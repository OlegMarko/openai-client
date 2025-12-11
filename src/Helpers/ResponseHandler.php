<?php

namespace Fixik\OpenAI\Helpers;

class ResponseHandler
{
    public static function get(array $data, string $path, $default = null)
    {
        $keys = explode('.', $path);
        foreach ($keys as $k) {
            if (!isset($data[$k])) return $default;
            $data = $data[$k];
        }

        return $data;
    }
}
