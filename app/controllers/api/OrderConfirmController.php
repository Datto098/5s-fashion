<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../core/ApiResponse.php';
require_once __DIR__ . '/../../models/Order.php';

class OrderConfirmController extends ApiController
{
    private $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * POST /api/orders/{id}/confirm
     * Customer confirms they received the order. Only the order owner may call this.
     */
    public function confirm($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if (!$order) {
                ApiResponse::error('Order not found', 404);
                return;
            }

            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $currentUserId = $_SESSION['user_id'] ?? null;
            if ($currentUserId !== null && $order['user_id'] && $order['user_id'] != $currentUserId) {
                ApiResponse::error('Not authorized to confirm this order', 403);
                return;
            }

            if ($order['status'] !== 'shipped') {
                ApiResponse::error('Only shipped orders can be confirmed as received');
                return;
            }

            $this->pdo->beginTransaction();

            // Update order status to delivered
            $update = $this->pdo->prepare("UPDATE orders SET status = 'delivered', delivered_at = NOW(), updated_at = NOW() WHERE id = :id");
            $update->execute([':id' => $id]);

            // If COD and payment not marked paid yet, mark as paid and finalize stock
            if (($order['payment_method'] ?? '') === 'cod' && ($order['payment_status'] ?? '') !== 'paid') {
                $updPay = $this->pdo->prepare("UPDATE orders SET payment_status = 'paid', updated_at = NOW() WHERE id = :id");
                $updPay->execute([':id' => $id]);

                // Finalize stock using Order model (handles variant/product modes)
                try {
                    $orderModel = new Order();
                    $orderModel->finalizeOrderStock($id);
                } catch (Exception $e) {
                    // log but don't abort the confirmation
                    error_log('[OrderConfirm] finalizeOrderStock failed: ' . $e->getMessage());
                }
            }

            // Log status change
            $log = $this->pdo->prepare("INSERT INTO order_logs (order_id, status_from, status_to, notes, created_by, created_at) VALUES (:order_id, :from, :to, :notes, :created_by, NOW())");
            $log->execute([
                ':order_id' => $id,
                ':from' => $order['status'],
                ':to' => 'delivered',
                ':notes' => 'Customer confirmed delivery',
                ':created_by' => $_SESSION['user_id'] ?? null
            ]);

            $this->pdo->commit();

            $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $updated = $stmt->fetch();

            ApiResponse::success([
                'message' => 'Order confirmed as received',
                'order' => $updated
            ]);

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            ApiResponse::error('Failed to confirm order: ' . $e->getMessage());
        }
    }
}

?>
