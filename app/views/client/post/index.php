
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
                <li class="breadcrumb-item active text-white">Blog</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold text-white mb-2">Blog 5S Fashion</h1>
        <p class="lead text-white-50 mb-0">Khám phá xu hướng, mẹo phối đồ và tin tức thời trang mới nhất từ 5S Fashion</p>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Cột trái: Bài viết mới nhất -->
        <div class="col-lg-4 mb-4">
            <h3 class="mb-4 text-primary"><i class="fas fa-bolt"></i> Bài viết mới</h3>
            <div class="list-group list-group-flush">
                <?php if (!empty($latestPosts)): ?>
                    <?php foreach ($latestPosts as $post): ?>
                        <a href="/5s-fashion/blog/<?= $post['id'] ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 shadow-sm rounded mb-3" style="background: #f8f9fa; border: none;">
                            <img src="<?= htmlspecialchars(getPostImageUrl($post['thumbnail'] ?? null)) ?>"
                                alt="<?= htmlspecialchars($post['title']) ?>"
                                class="rounded"
                                style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-dark mb-1" style="font-size: 1rem; line-height: 1.2;">
                                    <?= htmlspecialchars($post['title']) ?>
                                </div>
                                <div class="text-muted" style="font-size: 0.85rem;">
                                    <i class="far fa-clock"></i> <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                    <?php if (!empty($post['author_name'])): ?>
                                        &nbsp;|&nbsp; <i class="fas fa-user"></i> <?= htmlspecialchars($post['author_name']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">Chưa có bài viết mới.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột phải: Danh sách tất cả bài viết -->
        <div class="col-lg-8">
            <h3 class="mb-4 text-primary"><i class="fas fa-list"></i> Tất cả bài viết</h3>
            <div class="row">
                <?php if (!empty($allPosts)): ?>
                    <?php foreach ($allPosts as $post): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0 post-card">
                                <a href="/5s-fashion/blog/<?= $post['id'] ?>">
                                    <img src="<?= htmlspecialchars(getPostImageUrl($post['thumbnail'] ?? null)) ?>" class="card-img-top" alt="<?= htmlspecialchars($post['title']) ?>" style="height: 180px; object-fit: cover; border-radius: 12px 12px 0 0;">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-2 text-dark" style="font-size: 1.1rem;">
                                        <a href="/5s-fashion/post/<?= $post['id'] ?>" class="text-decoration-none text-dark">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h5>
                                    <div class="mb-2 text-muted" style="font-size: 0.9rem;">
                                        <i class="far fa-clock"></i> <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                        <?php if (!empty($post['author_name'])): ?>
                                            &nbsp;|&nbsp; <i class="fas fa-user"></i> <?= htmlspecialchars($post['author_name']) ?>
                                        <?php endif; ?>
                                    </div>
                                    <p class="card-text text-secondary" style="font-size: 0.97rem; flex-grow: 1;">
                                        <?= htmlspecialchars(mb_substr(strip_tags($post['content'] ?? ''), 0, 100)) ?>...
                                    </p>
                                    <a href="/5s-fashion/post/<?= $post['id'] ?>" class="btn btn-outline-primary btn-sm mt-auto align-self-start">Đọc tiếp <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">Chưa có bài viết nào.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .post-card {
        border-radius: 12px;
        transition: box-shadow 0.2s, transform 0.2s;
        background: #fff;
    }

    .post-card:hover {
        box-shadow: 0 8px 24px rgba(0, 123, 255, 0.10);
        transform: translateY(-4px) scale(1.01);
    }

    .list-group-item-action:hover {
        background: #e3f0ff !important;
    }
</style>

<!-- Responsive cho header -->
<style>
    .blog-header {
        box-shadow: 0 4px 24px rgba(229, 57, 53, 0.08);
    }

    .blog-header .breadcrumb a {
        color: #fff !important;
        text-decoration: underline;
    }

    .blog-header .breadcrumb .active {
        color: #fff !important;
    }

    @media (max-width: 768px) {
        .blog-header {
            min-height: 120px;
            border-radius: 0 0 18px 18px;
        }

        .blog-header h1 {
            font-size: 1.5rem;
        }

        .blog-header p {
            font-size: 1rem;
        }
    }
</style>