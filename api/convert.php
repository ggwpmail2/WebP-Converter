<?php
 header("Access-Control-Allow-Origin: *");

include 'toWebp.php';

// Базовый URL для генерации корректных ссылок
$baseUrl = 'https://convert.wslx.ru/upload/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = '../upload/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $image = $_FILES['image'];
    $imagePath = $uploadDir . basename($image['name']);

    if (move_uploaded_file($image['tmp_name'], $imagePath)) {
        $outputQuality = 75; // можно изменить это значение

        $webp = new ToWebp();
        $result = $webp->convert($imagePath, $outputQuality, false);

        if ($result->status === 1) {
            $originalSize = filesize($imagePath) / 1024; // Размер в КБ
            $convertedSize = filesize($result->fullPath) / 1024; // Размер в КБ
            $compression = round((1 - $convertedSize / $originalSize) * 100, 2);

            // Генерация ссылки для скачивания с учетом базового URL
            $downloadUrl = $baseUrl . basename($result->fullPath);

            echo json_encode([
                'id' => uniqid(),
                'name' => basename($result->fullPath),
                'thumbnail' => $downloadUrl,
                'originalSize' => $originalSize,
                'convertedSize' => $convertedSize,
                'compression' => $compression,
                'downloadUrl' => $downloadUrl,
            ]);
        } else {
            echo json_encode(['error' => $result->error]);
        }
    } else {
        echo json_encode(['error' => 'Failed to upload file']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

?>
