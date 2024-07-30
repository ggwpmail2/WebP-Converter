<?php

class ToWebp {

    public function __construct() {}

    function convert($fullPath, $outPutQuality = 100, $deleteOriginal = false) {
        $this->fullPath = $fullPath;
        $this->outPutQuality = $outPutQuality;
        $this->deleteOriginal = $deleteOriginal;

        if (file_exists($this->fullPath)) {

            $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
            $this->extension = $ext;
            $this->newFilefullPath = str_replace('.'.$ext, '.webp', $fullPath);

            $isValidFormat = false;

            if ($this->extension === 'png' || $this->extension === 'PNG') {
                $img = imagecreatefrompng($this->fullPath);
                $isValidFormat = true;
            } else if (in_array($this->extension, ['jpg', 'JPG', 'jpeg', 'JPEG'])) {
                $img = imagecreatefromjpeg($this->fullPath);
                $isValidFormat = true;
            } else if ($this->extension === 'gif' || $this->extension === 'GIF') {
                $img = imagecreatefromgif($this->fullPath);
                $isValidFormat = true;
            }

            if ($isValidFormat) {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
                imagewebp($img, $this->newFilefullPath, $this->outPutQuality);
                imagedestroy($img);

                if ($this->deleteOriginal) {
                    unlink($this->fullPath);
                }

                return (object) [
                    'fullPath' => $this->newFilefullPath,
                    'status' => 1,
                ];
            } else {
                return (object) ['error' => 'Invalid file format', 'status' => 0];
            }
        } else {
            return (object) ['error' => 'File does not exist', 'status' => 0];
        }
    }
}

?>
