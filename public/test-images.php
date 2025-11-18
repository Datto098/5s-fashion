<?php
// Test file để kiểm tra baseUrl và ảnh
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Images</title>
</head>
<body>
    <h1>Test Images</h1>
    
    <h2>Base URL Test:</h2>
    <script>
        // Set baseUrl giống như trong layout
        window.baseUrl = '<?= rtrim(url(), '/') ?>';
        document.write('<p>Base URL: ' + window.baseUrl + '</p>');
    </script>
    
    <h2>Direct Image Test:</h2>
    <img src="/zone-fashion/assets/images/no-image.jpg" alt="Direct path" style="width: 60px; height: 60px; border: 1px solid red;">
    <img src="<?= url() ?>/assets/images/no-image.jpg" alt="URL helper" style="width: 60px; height: 60px; border: 1px solid blue;">
    
    <h2>Product Images Test:</h2>
    <?php
    // Get a sample product image
    $sampleImages = [
        '6884887e20c9d_1753516158.webp',
        '6886434ae7a37_1753629514.webp',
        '6886435667f65_1753629526.webp'
    ];
    
    foreach ($sampleImages as $img) {
        echo "<img src='/zone-fashion/uploads/products/{$img}' alt='{$img}' style='width: 60px; height: 60px; border: 1px solid green; margin: 5px;'>";
    }
    ?>
    
    <script>
        // Test JavaScript image loading
        function testImageLoad(src) {
            const img = new Image();
            img.onload = () => console.log('✅ Image loaded:', src);
            img.onerror = () => console.log('❌ Image failed:', src);
            img.src = src;
        }
        
        testImageLoad(window.baseUrl + '/assets/images/no-image.jpg');
        testImageLoad(window.baseUrl + '/uploads/products/6884887e20c9d_1753516158.webp');
    </script>
</body>
</html>