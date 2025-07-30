<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-newspaper text-danger"></i> Quản lý bài viết</h2>
        <a href="/5s-fashion/admin/post/create" class="btn btn-danger">
            <i class="fas fa-plus"></i> Thêm bài viết
        </a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:60px;">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Ngày tạo</th>
                            <th>Tác giả</th>
                            <th>Trạng thái</th>
                            <th style="width:120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $imagePath = $post['thumbnail'];
                                        if (strpos($imagePath, '/uploads/') === 0) {
                                            $cleanPath = substr($imagePath, 9); // Remove '/uploads/'
                                        } elseif (strpos($imagePath, 'uploads/') === 0) {
                                            $cleanPath = substr($imagePath, 8); // Remove 'uploads/'
                                        } else {
                                            $cleanPath = ltrim($imagePath, '/');
                                        }
                                        $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                                        ?>
                                        <img src="<?= htmlspecialchars($imageUrl) ?>"
                                             alt="<?= htmlspecialchars($post['title'] ?? '') ?>"
                                             style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($post['title']) ?></strong>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($post['author_name'] ?? 'Không rõ') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $post['status'] ?? 0;
                                        if ($status == 1) {
                                            echo '<span class="badge bg-success">Hiển thị</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">Ẩn</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="/5s-fashion/admin/post/edit/<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/5s-fashion/admin/post/toggleStatus/<?= $post['id'] ?>"
                                           class="btn btn-sm btn-outline-warning"
                                           onclick="return confirm('Bạn có muốn đổi trạng thái bài viết này?');">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                        <a href="/5s-fashion/admin/post/delete/<?= $post['id'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bạn có chắc muốn xóa bài viết này?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có bài viết nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
