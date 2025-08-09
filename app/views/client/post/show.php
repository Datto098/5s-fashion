<?php
function getPostImageUrl($thumbnail) {
    if (!empty($thumbnail)) {
        if (strpos($thumbnail, '/uploads/') === 0) {
            $cleanPath = substr($thumbnail, 9);
        } elseif (strpos($thumbnail, 'uploads/') === 0) {
            $cleanPath = substr($thumbnail, 8);
        } else {
            $cleanPath = ltrim($thumbnail, '/');
        }
        return '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
    }
    return '/5s-fashion/public/assets/images/default-post.jpg';
}
?>
<!-- Banner/Header -->
<div class="blog-header position-relative mb-4" style="background: linear-gradient(90deg, #e53935 0%, #e35d5b 100%); min-height: 180px; border-radius: 0 0 32px 32px;">
    <div class="container h-100 d-flex flex-column justify-content-center" style="min-height: 180px;">
        <nav aria-label="breadcrumb" class="pt-3">
            <ol class="breadcrumb bg-transparent px-0 mb-2">
                <li class="breadcrumb-item"><a href="/5s-fashion" class="text-white-50">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/5s-fashion/blog" class="text-white-50">Blog</a></li>
                <li class="breadcrumb-item active text-white"><?= htmlspecialchars($post['title']) ?></li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold text-white mb-2"><?= htmlspecialchars($post['title']) ?></h1>
        <p class="lead text-white-50 mb-0"><?= date('d/m/Y', strtotime($post['created_at'])) ?></p>
    </div>
</div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <img src="<?= htmlspecialchars(getPostImageUrl($post['thumbnail'] ?? null)) ?>"
                     class="card-img-top"
                     alt="<?= htmlspecialchars($post['title']) ?>"
                     style="max-height:320px;object-fit:cover;">
                <div class="card-body">
                    <div class="mb-3 text-muted" style="font-size: 0.95rem;">
                        <i class="far fa-clock"></i> <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                    </div>
                    <div class="post-content" style="font-size:1.1rem;">
                        <?= $post['content'] ?>
                    </div>
                </div>
            </div>
            <a href="/5s-fashion/blog" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Quay lại Blog</a>
        </div>
    </div>
</div>
