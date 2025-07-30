<?php
// app/controllers/PostController.php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Post.php';
class PostController extends BaseController
{
    // Trang danh sách bài viết
    public function index()
    {
        // Lấy 5-6 bài mới nhất
        $latestPosts = Post::getLatest(6);
        // Lấy tất cả bài viết
        $allPosts = Post::getAllForClient();

        $this->render('client/post/index', [
            'latestPosts' => $latestPosts,
            'allPosts' => $allPosts,
        ], 'client/layouts/app');
    }

    // Trang chi tiết bài viết
    public function show($id)
    {
        $post = (new Post())->find($id);
        if (!$post) {
            $this->render('errors/404');
            return;
        }
        $this->render('client/post/show', [
            'post' => $post
        ], 'client/layouts/app');
    }
}
