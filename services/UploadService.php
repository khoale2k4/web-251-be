<?php

class UploadService {
    private $defaultStoragePath;
    private $assetsBasePath;
    private $publicBasePath;

    public function __construct($storagePath = __DIR__ . "/../storage") {
        $this->defaultStoragePath = $storagePath;
        if (!file_exists($this->defaultStoragePath)) {
            mkdir($this->defaultStoragePath, 0777, true);
        }

        $projectRoot = realpath(dirname(__DIR__)); // .../web-251-be
        $baseRoot = realpath($projectRoot . '/..'); // .../btl-251-web
        $this->assetsBasePath = $baseRoot . '/web-251-fe/assets/uploads';
        $this->publicBasePath = 'assets/uploads';
        if (!file_exists($this->assetsBasePath)) {
            mkdir($this->assetsBasePath, 0777, true);
        }
    }

    public function saveFile($file, $folder = 'misc', $toAssets = true) {
        if (!isset($file) || $file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload failed");
        }
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Invalid upload source");
        }

        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $detectedMime = mime_content_type($file['tmp_name']);
        if (!in_array($detectedMime, $allowedMime, true)) {
            throw new Exception("Chỉ hỗ trợ upload ảnh (jpg, png, gif, webp)");
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $extension = $this->mimeToExtension($detectedMime) ?? 'jpg';
        }

        $safeFolder = $this->sanitizeFolder($folder);
        $targetDir = $toAssets
            ? $this->ensureAssetsDirectory($safeFolder)
            : $this->defaultStoragePath;

        $basename = $this->slugify(pathinfo($file['name'], PATHINFO_FILENAME));
        $filename = $this->uniqueFilename($targetDir, $basename, $extension);
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            throw new Exception("Cannot move uploaded file");
        }

        return [
            'filename' => $filename,
            'relativePath' => $toAssets ? $this->publicBasePath . '/' . $safeFolder . '/' . $filename : $filename,
            'mime' => $detectedMime
        ];
    }

    private function sanitizeFolder($folder) {
        $folder = strtolower($folder ?? '');
        $folder = preg_replace('/[^a-z0-9-_]/', '', $folder);
        return $folder ?: 'misc';
    }

    private function ensureAssetsDirectory($folder) {
        $path = $this->assetsBasePath . '/' . $folder;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    private function slugify($value) {
        $value = strtolower($value ?? '');
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        $value = trim($value, '-');
        if ($value === '') {
            $value = 'image';
        }
        return $value;
    }

    private function uniqueFilename($dir, $basename, $extension) {
        $candidate = $basename . '.' . $extension;
        $counter = 1;
        while (file_exists($dir . DIRECTORY_SEPARATOR . $candidate)) {
            $candidate = $basename . '-' . $counter . '.' . $extension;
            $counter++;
        }
        return $candidate;
    }

    private function mimeToExtension($mime) {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        return $map[$mime] ?? null;
    }
}
