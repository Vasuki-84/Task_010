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
        //  IV + Binary Encrypted Data, then encode in base64 for storage
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data)
    {
        $key = $_ENV['ENCRYPTION_KEY'];
        // IV + Binary Encrypted Data
        $decoded = base64_decode($data);
        // find IV length based on the cipher method
        $ivLength = openssl_cipher_iv_length(
            self::$cipher
        );
        // extract IV = extracts 0-16 characters
        $iv = substr(
            $decoded,
            0,
            $ivLength
        );
        // cipher text = extracts characters after 16 to end
        $encrypted = substr(
            $decoded,
            $ivLength
        );

        return openssl_decrypt(
            $encrypted,
            self::$cipher,
            $key,
            OPENSSL_RAW_DATA,   // data is in raw binary format
            $iv
        );
    }
}