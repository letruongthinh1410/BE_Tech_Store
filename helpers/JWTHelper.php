<?php
require_once __DIR__ . '/../config/config.php';

class JWTHelper {
    
    // Base64 URL encode
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Base64 URL decode
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    // Encode JWT token
    public static function encode($payload, $secretKey = JWT_SECRET_KEY) {
        // Header
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => JWT_ALGORITHM
        ]);

        // Payload with timestamps
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRATION;
        
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        
        // Signature
        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            $secretKey,
            true
        );
        
        $base64UrlSignature = self::base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    // Decode JWT token
    public static function decode($jwt, $secretKey = JWT_SECRET_KEY) {
        $tokenParts = explode('.', $jwt);
        
        if (count($tokenParts) !== 3) {
            throw new Exception('Invalid token structure');
        }

        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $tokenParts;
        
        // Verify signature
        $signature = self::base64UrlEncode(
            hash_hmac(
                'sha256',
                $base64UrlHeader . "." . $base64UrlPayload,
                $secretKey,
                true
            )
        );

        if ($signature !== $base64UrlSignature) {
            throw new Exception('Invalid token signature');
        }

        $payload = json_decode(self::base64UrlDecode($base64UrlPayload), true);

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token has expired');
        }

        return $payload;
    }

    // Validate token
    public static function validateToken($jwt) {
        try {
            $payload = self::decode($jwt);
            return [
                'valid' => true,
                'payload' => $payload
            ];
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // Refresh token 
    public static function refreshToken($jwt) {
        try {
            $payload = self::decode($jwt);
            
            // Remove old timestamps
            unset($payload['iat']);
            unset($payload['exp']);
            
            // Create new token
            return self::encode($payload);
            
        } catch (Exception $e) {
            throw new Exception('Cannot refresh token: ' . $e->getMessage());
        }
    }
    
}
