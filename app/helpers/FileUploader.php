<?php
/**
 * Secure File Upload Helper
 */

class FileUploader {

    private static $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private static $maxFileSize = 5 * 1024 * 1024; // 5MB

    public static function uploadImage($file, $destination = 'uploads/images/') {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No file uploaded or upload error');
        }

        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::$allowedImageTypes)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, WEBP allowed');
        }

        // Validate file size
        if ($file['size'] > self::$maxFileSize) {
            throw new Exception('File too large. Maximum size is 5MB');
        }

        // Generate secure filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;

        // Ensure destination directory exists
        $fullDestination = PUBLIC_PATH . '/' . ltrim($destination, '/');
        if (!is_dir($fullDestination)) {
            mkdir($fullDestination, 0755, true);
        }

        $targetPath = $fullDestination . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to move uploaded file');
        }

        return $destination . $filename;
    }

    public static function deleteFile($filePath) {
        $fullPath = PUBLIC_PATH . '/' . ltrim($filePath, '/');
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
?>
