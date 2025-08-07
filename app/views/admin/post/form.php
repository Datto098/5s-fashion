<?php
?>
<div class="container py-0">
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h3 class="mb-3" style="margin-bottom: 16px !important;">
                <i class="fas fa-edit text-danger"></i>
                <?= isset($post) ? 'Sửa bài viết' : 'Thêm bài viết' ?>
            </h3>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" required
                        value="<?= htmlspecialchars($post['title'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ảnh đại diện</label>
                    <div class="mb-2">
                        <?php
                        if (!empty($post['thumbnail'])) {
                            $imagePath = $post['thumbnail'];
                            if (strpos($imagePath, '/uploads/') === 0) {
                                $cleanPath = substr($imagePath, 9); // Remove '/uploads/'
                            } elseif (strpos($imagePath, 'uploads/') === 0) {
                                $cleanPath = substr($imagePath, 8); // Remove 'uploads/'
                            } else {
                                $cleanPath = ltrim($imagePath, '/');
                            }
                            $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                        } else {
                            $imageUrl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRKFSgdhQvBlZO6I8s-jtKIYOED1NqEs4xEjA&s';
                        }
                        ?>
                        <img id="preview-img"
                             src="<?= htmlspecialchars($imageUrl) ?>"
                             style="max-width:120px;max-height:80px;border-radius:8px;">
                    </div>
                    <input type="file" name="thumbnail" class="form-control" <?= isset($post) ? '' : 'required' ?> onchange="previewImage(event)">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <div id="quill-editor" style="height: 300px;">
                        <?= isset($post['content']) ? $post['content'] : '' ?>
                    </div>
                    <input type="hidden" name="content" id="content-input">
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= (isset($post['status']) && $post['status'] == 1) ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= (isset($post['status']) && $post['status'] == 0) ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <a href="/5s-fashion/admin/post" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
</div>

<!-- QuillJS CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>
<script>
    // Khởi tạo Quill
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, false] }],
                ['bold', 'italic', 'underline'],
                ['image', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ],
            imageResize: {}
        }
    });

    // Nếu cần, set lại nội dung từ hidden input (nếu bạn dùng input ẩn để lưu content)
    var content = <?= json_encode($post['content'] ?? '') ?>;
    if (content) {
        quill.root.innerHTML = content;
    }

    // Khi submit form, lấy nội dung Quill cho vào input ẩn
    document.querySelector('form').onsubmit = function() {
        document.getElementById('content-input').value = quill.root.innerHTML;
    };

    function previewImage(event) {
        const [file] = event.target.files;
        if (file) {
            document.getElementById('preview-img').src = URL.createObjectURL(file);
        }
    }
</script>