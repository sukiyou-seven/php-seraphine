<?php

class Upload
{
    private $save_url;
    private $allowed_types;
    private $max_size;
    private $error;

    public function __construct()
    {
        $this->save_url = __DIR__ . "/../../../static/upload/";
        $this->allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        $this->max_size = 10 * 1024 * 1024; // 10MB
        $this->error = null;

        if (!is_dir($this->save_url)) {
            mkdir($this->save_url, 0755, true);
        }
    }

    public function upload($file, $subdir = '')
    {
        if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->setError('文件上传失败');
            return false;
        }

        if ($file['size'] > $this->max_size) {
            $this->setError('文件大小超出限制');
            return false;
        }

        if (!in_array($file['type'], $this->allowed_types)) {
            $this->setError('不支持的文件类型');
            return false;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $this->generateFilename($extension);

        if ($subdir) {
            $subdir = trim($subdir, '/') . '/';
            $targetDir = $this->save_url . $subdir;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
        } else {
            $targetDir = $this->save_url;
        }

        $targetPath = $targetDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->setError('文件保存失败');
            return false;
        }

        chmod($targetPath, 0644);

        return [
            'filename' => $filename,
            'path' => $subdir . $filename,
            'full_path' => $targetPath,
            'size' => $file['size'],
            'type' => $file['type']
        ];
    }

    public function uploadBase64($base64Data, $filename = '', $subdir = '')
    {
        if (preg_match('/^data:(\w+\/\w+);base64,(.+)$/', $base64Data, $matches)) {
            $mimeType = $matches[1];
            $data = $matches[2];
        } else {
            $this->setError('无效的 Base64 数据');
            return false;
        }

        if (!in_array($mimeType, $this->allowed_types)) {
            $this->setError('不支持的文件类型');
            return false;
        }

        $fileData = base64_decode($data);
        if ($fileData === false) {
            $this->setError('Base64 解码失败');
            return false;
        }

        if (strlen($fileData) > $this->max_size) {
            $this->setError('文件大小超出限制');
            return false;
        }

        $extension = $this->getExtensionFromMimeType($mimeType);
        if (!$filename) {
            $filename = $this->generateFilename($extension);
        }

        if ($subdir) {
            $subdir = trim($subdir, '/') . '/';
            $targetDir = $this->save_url . $subdir;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
        } else {
            $targetDir = $this->save_url;
        }

        $targetPath = $targetDir . $filename;

        if (file_put_contents($targetPath, $fileData) === false) {
            $this->setError('文件保存失败');
            return false;
        }

        chmod($targetPath, 0644);

        return [
            'filename' => $filename,
            'path' => $subdir . $filename,
            'full_path' => $targetPath,
            'size' => strlen($fileData),
            'type' => $mimeType
        ];
    }

    public function setAllowedTypes($types)
    {
        $this->allowed_types = $types;
        return $this;
    }

    public function setMaxSize($bytes)
    {
        $this->max_size = $bytes;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    private function setError($message)
    {
        $this->error = $message;
    }

    private function generateFilename($extension)
    {
        return uniqid('upload_', true) . '.' . $extension;
    }

    private function getExtensionFromMimeType($mimeType)
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf'
        ];
        return $map[$mimeType] ?? 'bin';
    }
}
