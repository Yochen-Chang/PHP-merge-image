<?php
if (function_exists('gd_info')) {
    echo "GD Library is installed.";
    print_r(gd_info());
} else {
    echo "GD Library is not installed.";
}

// 加载第一张图片（假设是红色图片）
$image1 = imagecreatefromjpeg('red.jpg');  // 修改为实际路径

// 加载第二张图片（假设是白色图片）
$image2 = imagecreatefromjpeg('white.jpg'); // 修改为实际路径

// 创建一张新的图像，宽度为 200px, 高度为 400px
$combinedImage = imagecreatetruecolor(200, 400);

// 保证合成图片背景透明
imagealphablending($combinedImage, false);
imagesavealpha($combinedImage, true);
$transparent = imagecolorallocatealpha($combinedImage, 0, 0, 0, 127);
imagefilledrectangle($combinedImage, 0, 0, 200, 400, $transparent);

// 将第一张图片复制到新图像的顶部
imagecopy($combinedImage, $image1, 0, 0, 0, 0, 200, 200);

// 将第二张图片复制到新图像的底部
imagecopy($combinedImage, $image2, 0, 200, 0, 0, 200, 200);

// 设置要保存图像的路径
$outputPath = 'output.png'; // 修改为你希望保存图像的实际路径

// 将图像保存到指定路径
imagepng($combinedImage, $outputPath);

// 释放资源
imagedestroy($image1);
imagedestroy($image2);
imagedestroy($combinedImage);
?>