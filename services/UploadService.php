<?php

class UploadService {
    private $storagePath;

    public function __construct($storagePath = __DIR__ . "/../storage") {
        $this->storagePath = $storagePath;

        if (!file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    public function saveFile($file) {
        if (!isset($file) || $file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload failed");
        }

        $filename = time() . "_" . basename($file["name"]);
        $target = $this->storagePath . "/" . $filename;

        if (move_uploaded_file($file["tmp_name"], $target)) {
            return $filename;
        } else {
            throw new Exception("Cannot move uploaded file");
        }
    }
}
