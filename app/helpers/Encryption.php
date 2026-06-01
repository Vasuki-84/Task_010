<?php

class Encryption
{   
    // Define Encryption algorithm:

    // AES - Advanced Encryption Standard
    // 256 - 32 byte secret key
    // CBC - Cipher Block Chaining mode
    private static $cipher = "AES-256-CBC";

    public static function encrypt($data)
    {
        $key = $_ENV['ENCRYPTION_KEY'];
        // finds IV length based on the cipher method
        $ivLength = openssl_cipher_iv_length(
           // self refers to the current class, since the method is static we cannot use $this
            self::$cipher
        );
        // IV = Initialization Vector
        $iv = random_bytes($ivLength);

        $encrypted = openssl_encrypt(
            $data,
            self::$cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($iv . $encrypted);
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