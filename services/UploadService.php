<?php

class UploadService {
    private $storageBasePath;
    private $publicBasePath;

    public function __construct($storagePath = __DIR__ . "/../storage") {
        // Lưu tất cả ảnh vào be/storage/uploads/
        $this->storageBasePath = $storagePath . '/uploads';
        $this->publicBasePath = '/storage/uploads';
        
        if (!file_exists($this->storageBasePath)) {
            mkdir($this->storageBasePath, 0777, true);
        }
    }

    public function saveFile($file, $folder = 'misc', $toAssets = true) {
        if (!isset($file) || $file["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload failed");
        }
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Invalid upload source");
        }

        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Check extension first
        if (!in_array($extension, $allowedExtensions, true)) {
            throw new Exception("Chỉ hỗ trợ upload ảnh (jpg, jpeg, png, gif, webp). File extension: " . $extension);
        }
        
        // Detect MIME type for additional security
        $detectedMime = $this->detectMimeType($file['tmp_name']);
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        
        // Log for debugging
        error_log("Upload - File: " . $file['name'] . ", Extension: " . $extension . ", MIME: " . $detectedMime);
        
        // Accept if either MIME is valid OR extension is valid (more permissive)
        $mimeValid = in_array($detectedMime, $allowedMime, true);
        $extValid = in_array($extension, $allowedExtensions, true);
        
        if (!$mimeValid && !$extValid) {
            throw new Exception("Invalid file type. Extension: " . $extension . ", MIME: " . $detectedMime);
        }

        $safeFolder = $this->sanitizeFolder($folder);
        $targetDir = $this->ensureStorageDirectory($safeFolder);

        $basename = $this->slugify(pathinfo($file['name'], PATHINFO_FILENAME));
        $filename = $this->uniqueFilename($targetDir, $basename, $extension);
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file["tmp_name"], $targetPath)) {
            throw new Exception("Cannot move uploaded file");
        }

        // Return relative path for API response: /storage/uploads/{folder}/{filename}
        return [
            'filename' => $filename,
            'relativePath' => $this->publicBasePath . '/' . $safeFolder . '/' . $filename,
            'mime' => $detectedMime
        ];
    }

    private function sanitizeFolder($folder) {
        $folder = strtolower($folder ?? '');
        $folder = preg_replace('/[^a-z0-9-_]/', '', $folder);
        return $folder ?: 'misc';
    }

    private function ensureStorageDirectory($folder) {
        $path = $this->storageBasePath . '/' . $folder;
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

    /**
     * Detect MIME type using multiple methods for compatibility
     * @param string $filePath Path to uploaded file
     * @return string MIME type
     */
    private function detectMimeType($filePath) {
        // Method 1: finfo (most reliable, PHP 5.3+)
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = finfo_file($finfo, $filePath);
                finfo_close($finfo);
                if ($mime) {
                    return $mime;
                }
            }
        }
        
        // Method 2: mime_content_type (deprecated but still works)
        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($filePath);
            if ($mime) {
                return $mime;
            }
        }
        
        // Method 3: Check file extension as fallback
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $extToMime = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        
        if (isset($extToMime[$extension])) {
            return $extToMime[$extension];
        }
        
        // Default fallback
        return 'application/octet-stream';
    }
}
