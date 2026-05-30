<?php

class Encryption
{
    private static $cipher = "AES-256-CBC";

    public static function encrypt($data)
    {
        $key = $_ENV['ENCRYPTION_KEY'];

        $ivLength = openssl_cipher_iv_length(
            self::$cipher
        );

        $iv = random_bytes($ivLength);

        $encrypted = openssl_encrypt(
            $data,
            self::$cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode(
            $iv . $encrypted
        );
    }

    public static function decrypt($data)
    {
        $key = $_ENV['ENCRYPTION_KEY'];

        $decoded = base64_decode($data);

        $ivLength = openssl_cipher_iv_length(
            self::$cipher
        );

        $iv = substr(
            $decoded,
            0,
            $ivLength
        );

        $encrypted = substr(
            $decoded,
            $ivLength
        );

        return openssl_decrypt(
            $encrypted,
            self::$cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}