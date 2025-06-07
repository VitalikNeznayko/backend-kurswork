<?php

namespace core;

class Cache
{
    private $cacheDir;

    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function get($key)
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            $data = unserialize(file_get_contents($file));
            if ($data['expire'] > time()) {
                return $data['value'];
            } else {
                unlink($file);
            }
        }
        return null;
    }

    public function set($key, $value, $ttl = 3600)
    {
        $statusCode = http_response_code();
        if ($statusCode !== 200) {
            return false; 
        }
        $file = $this->getCacheFile($key);
        $data = [
            'value' => $value,
            'expire' => time() + $ttl
        ];
        file_put_contents($file, serialize($data));
    }

    public function delete($key)
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    private function getCacheFile($key)
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }
}
