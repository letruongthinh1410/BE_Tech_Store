<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/AuthService.php';

class AuthMiddleware {
    
    /**
     * Xác thực user từ JWT token
     * Trả về user data nếu hợp lệ, exit nếu không hợp lệ
     */
    public static function authenticate() {
        // Get Authorization header
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        // Extract token
        if (!preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Token không được cung cấp'
            ]);
            exit;
        }

        $token = $matches[1];

        // Validate token
        $authService = new AuthService();
        $user = $authService->getUserFromToken($token);

        if (!$user) {
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Token không hợp lệ hoặc đã hết hạn'
            ]);
            exit;
        }

        return $user;
    }
    
    /**
     * Optional authentication - không bắt buộc phải có token
     * Trả về user data nếu có token hợp lệ, null nếu không có token
     */
    public static function optionalAuth() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
            return null;
        }

        $token = $matches[1];
        $authService = new AuthService();
        return $authService->getUserFromToken($token);
    }
}
