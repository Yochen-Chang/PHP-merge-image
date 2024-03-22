<?php
if($_POST && $_FILES) {
    // 獲取使用者上傳資料
    // error_log(print_r($_POST, true));
    // error_log(print_r($_FILES, true));
    [$layout, $wide, $high] = explode("_", $_POST['layout']);
    $quality = $_POST['quality'];

    // 生成唯一資料夾名稱並確保資料夾存在
    $uniqueFolder = 'uploads/image-' . uniqid();
    if (!file_exists($uniqueFolder)) {
        mkdir($uniqueFolder, 0777, true);
    }
    
    // 圖片尺寸規格
    $imageSize = [
        'large'=>[
            'high' => [2500, 1686],
            'medium' => [1200, 810],
            'low' => [800, 540]
        ],
        'small'=>[
            'high' => [2500, 843],
            'medium' => [1200, 405],
            'low' => [800, 270]
        ]
    ];

    // 設定目標尺寸
    $targetWidth = $imageSize[$layout][$quality][0] / $wide; // 單張圖片的寬度
    $targetHeight = $imageSize[$layout][$quality][1] / $high; // 單張圖片的高度
    $mergedWidth = $targetWidth * $wide; // 合併後的寬度
    $mergedHeight = $targetHeight * $high; // 合併後的高度

    // 處理每張圖片
    $paths = []; // 儲存處理後的圖片路徑
    for ($i = 1; $i <= $wide; $i++) {
        $imagePath = $uniqueFolder . '/' . $_FILES['image'.$i]['name'];
        move_uploaded_file($_FILES['image'.$i]['tmp_name'], $imagePath);
        
        // 根據圖片類型讀取圖片
        $image = imagecreatefromstring(file_get_contents($imagePath));
        
        // 創建一個新的圖片並縮放
        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($image), imagesy($image));
        
        // 保存縮放後的圖片
        $resizedPath = $uniqueFolder . '/resized_' . $i . '.png';
        imagepng($resizedImage, $resizedPath);
        $paths[] = $resizedPath;
        
        // 釋放資源
        imagedestroy($image);
        imagedestroy($resizedImage);
    }

    // 創建一個新的圖片用於合併
    $mergedImage = imagecreatetruecolor($mergedWidth, $mergedHeight);

    // 將兩張縮放後的圖片合併到新圖片中
    foreach ($paths as $index => $path) {
        $image = imagecreatefrompng($path);
        imagecopy($mergedImage, $image, $index * $targetWidth, 0, 0, 0, $targetWidth, $targetHeight);
        imagedestroy($image);
    }

    // 保存合成後的圖片
    $outputPath = $uniqueFolder . '/merged_image.png';
    imagepng($mergedImage, $outputPath);

    // 釋放資源
    imagedestroy($mergedImage);

    // 提供下載連結
    // echo "合成成功：<a href='$outputPath'>下載圖片</a>";
    if ($outputPath) {
    // 操作成功
    $response = [
            'success' => true,
            'message' => '合成成功',
            'downloadUrl' => $outputPath
        ];
    } else {
        // 操作失敗
        $response = [
            'success' => false,
            'message' => '發生錯誤'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

// TODO:定期清理的功能
?>
