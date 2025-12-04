<?php

class StorageController {
    private $storagePath;

    public function __construct($storagePath = __DIR__ . "/../storage") {
        $this->storagePath = $storagePath;
    }

    public function handleRequest($request) {
        // Parse URL: /storage/{folder}/{filename} hoặc /storage/{filename}
        // Loại bỏ /storage từ đầu request
        $path = preg_replace('#^/storage/?#', '', $request);
        
        if (empty($path)) {
            http_response_code(404);
            echo json_encode(["error" => "File not found"]);
            exit;
        }

        // Xây dựng đường dẫn file
        // Kiểm tra nếu có subfolder (uploads/news/file.jpg) hay không
        $filePath = $this->storagePath . '/' . $path;
        
        // Security: Ngăn chặn path traversal
        $realPath = realpath($filePath);
        $realStoragePath = realpath($this->storagePath);
        
        if ($realPath === false || strpos($realPath, $realStoragePath) !== 0) {
            http_response_code(403);
            echo json_encode(["error" => "Access denied"]);
            exit;
        }

        if (!file_exists($realPath) || !is_file($realPath)) {
            http_response_code(404);
            echo json_encode(["error" => "File not found"]);
            exit;
        }

        // Xác định MIME type
        $mimeType = $this->getMimeType($realPath);
        
        // Set headers
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($realPath));
        
        // Cache headers (1 năm)
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        
        // Output file
        readfile($realPath);
        exit;
    }

    private function getMimeType($filePath) {
        // Sử dụng finfo nếu có
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
        
        // Fallback: dựa vào extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];
        
        return $mimeMap[$extension] ?? 'application/octet-stream';
    }
}
