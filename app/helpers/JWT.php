<?php

class JWT
{
    // Generate JWT
    public static function generate($user)
    {   
        // Converts JSON into encoded string.
        $header = base64_encode(json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]));

        $payload = base64_encode(json_encode([
            "user_id" => $user['id'],
            "email" => $user['email'],
            "iat" => time(),
            "exp" => time() + $_ENV['JWT_EXPIRY']
        ]));

        // Hash-based Message Authentication Code
        // use secret key to create signature
        $signature = hash_hmac(
            'sha256',
            "$header.$payload",
            $_ENV['JWT_SECRET'],
            true
        );
        // convert into readble string
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    // Validate JWT
    public static function validate($token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;

        $validSignature = base64_encode(
            hash_hmac(
                'sha256',  // To create secure signature
                "$header.$payload",
                $_ENV['JWT_SECRET'],
                true
            )
        );

        // Compare signatures
        if (
            !hash_equals(
                $validSignature,
                $signature
            )
        ) {
            return false;
        }

        $payloadData = json_decode(
            base64_decode($payload),
            true
        );

        // Check expiry
        if ($payloadData['exp'] < time()) {
            return false;
        }

        return $payloadData;
    }
}