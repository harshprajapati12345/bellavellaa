# 🚀 BellaVella API — Master Postman Guide

This guide covers the entire API ecosystem of BellaVella, including authentication, environment setup, and module-specific documentation.

---

## 🛠️ 1. Environment Setup

To test efficiently, create a **Postman Environment** with these variables:

| Variable | Description | Example Value |
| :--- | :--- | :--- |
| `host` | Your local or live server URL | `http://127.0.0.1:8000` |
| `admin_jwt` | JWT Token for Admin APIs | *(Generated after Login)* |
| `cust_jwt` | JWT Token for Customer APIs | *(Generated after OTP Verify)* |

---

## 🔐 2. Authentication Flow

### 👨‍💼 Admin Panel (Email/Pass)
1. **Login**: `POST /api/admin/auth/login`
   - Body: `{ "email": "...", "password": "..." }`
2. **Setup**: Copy `access_token` from response to your `admin_jwt` environment variable.
3. **Usage**: Add Header `Authorization: Bearer {{admin_jwt}}` to all admin requests.

### 📱 Customer App (OTP)
1. **Send OTP**: `POST /api/flutter/auth/send-otp`
   - Body: `{ "mobile": "..." }`
2. **Verify OTP**: `POST /api/flutter/auth/verify-otp`
   - Body: `{ "mobile": "...", "otp": "..." }`
3. **Setup**: Copy `access_token` to your `cust_jwt` variable.

---

## 📦 3. Core Modules (Admin)

### 🎫 [Offers Guide](file:///c:/xampp/htdocs/bellavella/docs/api/admin/offers_postman_guide.md)
- `GET /api/admin/offers`: List/Search offers.
- `POST /api/admin/offers`: Create new coupon.

### ⚙️ [Settings Guide]
- `GET /api/admin/settings`: View grouped config.
- `POST /api/admin/settings`: Bulk update (e.g., app name, maintenance).

### 🔗 [Assignments Guide]
- `GET /api/admin/assignments`: View pending bookings & active pros.
- `POST /api/admin/assignments`: Link a professional to a booking.

### 📝 [Reviews Guide]
- `GET /api/admin/reviews`: Moderate customer feedback.
- `PUT /api/admin/reviews/{id}`: Approve/Reject or award points.

---

## 📥 4. Global Standards

- **Headers**: 
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Response Format**:
  ```json
  {
      "success": true,
      "message": "Human readable message",
      "data": { ... },
      "errors": null
  }
  ```
- **Common Error Codes**:
  - `401`: Unauthorized / Token Expired.
  - `403`: Forbidden / Account Blocked.
  - `422`: Validation Failed.
  - `404`: Resource not found.
