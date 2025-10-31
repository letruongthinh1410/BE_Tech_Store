# PHP JWT Authentication API

## Authentication
Tất cả các endpoint có đánh dấu **[Protected]** yêu cầu gửi JWT token trong header:

```
Authorization: Bearer YOUR_JWT_TOKEN
```

---

## API Endpoints

### 1. Register – Đăng ký tài khoản

**Endpoint:**
```
POST /api/register.php
```

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "Test123456",
  "full_name": "John Doe"
}
```

**Validation Rules:**
- `email`: Bắt buộc, hợp lệ định dạng email  
- `password`: Bắt buộc, tối thiểu 8 ký tự, chứa chữ hoa, chữ thường và số  
- `full_name`: Tùy chọn  

**Response – 201 Created**
```json
{
  "success": true,
  "message": "Đăng ký thành công. Vui lòng kiểm tra email để xác thực tài khoản",
  "user_id": 1,
  "verification_url": "http://localhost/TechStore/api/verify-email.php?token=abc123..."
}
```

**Error – 400 Bad Request**
```json
{
  "success": false,
  "message": "Email đã được sử dụng"
}
```

---

### 2. Verify Email – Xác thực email

**Endpoint:**
```
GET /api/verify-email.php?token={verification_token}
```

**Parameters:**
- `token`: Token được gửi qua email hoặc trả về sau đăng ký  

**Response – 200 OK**
```json
{
  "success": true,
  "message": "Email đã được xác thực thành công"
}
```

**Error – 400 Bad Request**
```json
{
  "success": false,
  "message": "Token không hợp lệ hoặc đã được sử dụng"
}
```

---

### 3. Login – Đăng nhập

**Endpoint:**
```
POST /api/login.php
```

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "Test123456"
}
```

**Response – 200 OK**
```json
{
  "success": true,
  "message": "Đăng nhập thành công",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "full_name": "John Doe",
    "email_verified": 1
  }
}
```

**Token Details:**
- Loại: JWT  
- Expiration: 3600s (1 giờ)  
- Algorithm: HS256  

**Error – 401 Unauthorized**
```json
{
  "success": false,
  "message": "Email hoặc mật khẩu không đúng"
}
```

**Error – 400 Bad Request**
```json
{
  "success": false,
  "message": "Vui lòng xác thực email trước khi đăng nhập"
}
```

---

### 4. Get Current User – Lấy thông tin user hiện tại [Protected]

**Endpoint:**
```
GET /api/me.php
```

**Headers:**
```
Authorization: Bearer YOUR_JWT_TOKEN
```

**Response – 200 OK**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "user@example.com",
    "full_name": "John Doe",
    "email_verified": 1,
    "created_at": "2025-10-30 10:30:00",
    "updated_at": "2025-10-30 15:45:00"
  }
}
```

**Error – 401 Unauthorized**
```json
{
  "success": false,
  "message": "Token không hợp lệ hoặc đã hết hạn"
}
```

---

### 5. Change Password – Đổi mật khẩu [Protected]

**Endpoint:**
```
POST /api/change-password.php
```

**Headers:**
```
Content-Type: application/json
Authorization: Bearer YOUR_JWT_TOKEN
```

**Request Body:**
```json
{
  "old_password": "Test123456",
  "new_password": "NewTest123456"
}
```

**Response – 200 OK**
```json
{
  "success": true,
  "message": "Đổi mật khẩu thành công"
}
```

**Error – 400 Bad Request**
```json
{
  "success": false,
  "message": "Mật khẩu cũ không đúng"
}
```

---

## Error Codes

| HTTP Code | Ý nghĩa | Gợi ý xử lý |
|-----------|----------|-------------|
| 200 | OK | Hiển thị kết quả |
| 201 | Created | Đăng ký thành công |
| 400 | Bad Request | Lỗi validation hoặc thiếu field |
| 401 | Unauthorized | Token không hợp lệ hoặc hết hạn |
| 405 | Method Not Allowed | Sai method HTTP |
| 500 | Internal Server Error | Lỗi hệ thống, thử lại hoặc báo admin |
