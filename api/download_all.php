<?php
 header("Access-Control-Allow-Origin: * ");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $images = $data['images'];

    if (!empty($images)) {
        $zip = new ZipArchive();
        $zipName = '../upload/' . uniqid() . '.zip';

        if ($zip->open($zipName, ZipArchive::CREATE) === TRUE) {
            foreach ($images as $image) {
                $filePath = str_replace('https://convert.wslx.ru/', '', $image);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . basename($zipName));
            header('Content-Length: ' . filesize($zipName));
            readfile($zipName);

            unlink($zipName);
        } else {
            echo json_encode(['error' => 'Failed to create zip archive']);
        }
    } else {
        echo json_encode(['error' => 'No images provided']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

?>
