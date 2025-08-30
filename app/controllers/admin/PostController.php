<?php

require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../models/Post.php';

class PostController extends BaseController
{
    // Danh sách bài viết (admin)
    public function index()
    {
        $posts = Post::getAll();
        $this->render('admin/post/index', [
            'posts' => $posts
        ], 'admin/layouts/main-inline');
    }

    // Thêm mới
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $thumbnailName = null;
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../public/uploads/posts/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['thumbnail']['name']);
                $uploadFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadFile)) {
                    // Lưu đường dẫn bắt đầu từ /uploads/posts/
                    $thumbnailName = '/uploads/posts/' . $fileName;
                }
            }

            $data = [
                'title'      => $_POST['title'],
                'content'    => $_POST['content'],
                'status'     => $_POST['status'],
                'author_id'  => $_SESSION['user']['id'],
                'thumbnail'  => $thumbnailName,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $post = new Post();
            $post->create($data);
            header('Location: /zone-fashion/admin/post');
            exit;
        }
        $this->render('admin/post/form', [], 'admin/layouts/main-inline');
    }

    // Sửa
    public function edit($id)
    {
        $post = (new Post())->find($id);
        if (!$post) {
            header('Location: /zone-fashion/admin/post');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $thumbnailName = $post['thumbnail']; // Giữ ảnh cũ nếu không upload mới
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../../public/uploads/posts/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['thumbnail']['name']);
                $uploadFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadFile)) {
                    // Xóa ảnh cũ nếu có
                    if (!empty($post['thumbnail'])) {
                        $oldImagePath = __DIR__ . '/../../../public' . $post['thumbnail'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    $thumbnailName = '/uploads/posts/' . $fileName;
                }
            }

            $data = [
                'title'     => $_POST['title'],
                'content'   => $_POST['content'],
                'status'    => $_POST['status'],
                'thumbnail' => $thumbnailName,
            ];
            (new Post())->update($id, $data);
            header('Location: /zone-fashion/admin/post');
            exit;
        }

        $this->render('admin/post/form', ['post' => $post], 'admin/layouts/main-inline');
    }
    public function delete($id)
    {
        $post = (new Post())->find($id);
        if ($post) {
            // Xóa file ảnh vật lý nếu có
            if (!empty($post['thumbnail'])) {
                $imagePath = __DIR__ . '/../../../public' . $post['thumbnail'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            // Xóa bài viết trong DB
            (new Post())->delete($id);
        }
        header('Location: /zone-fashion/admin/post');
        exit;
    }

    public function toggleStatus($id)
    {
        $post = (new Post())->find($id);
        if ($post) {
            $newStatus = ($post['status'] == 1) ? 0 : 1;
            (new Post())->update($id, ['status' => $newStatus]);
        }
        header('Location: /zone-fashion/admin/post');
        exit;
    }
}