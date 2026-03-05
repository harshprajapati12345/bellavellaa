# Bellavella Professional API — Postman Guide

This guide provides a professional overview of the APIs used by the Bellavella Professionals Mobile Application.

## 1. Environment Configuration

Set up these variables in your Postman environment:

| Variable | Value | Description |
| :--- | :--- | :--- |
| `base_url` | `http://localhost:8000/api/professional` | The root URL for professional APIs |
| `token` | `eyJ0eXAiOiJKV1Q...` | JWT access token obtained after login |

### Global Headers
All protected requests MUST include:
- `Accept: application/json`
- `Authorization: Bearer {{token}}`

---

## 2. Authentication Flow

### A. Send OTP
Request a 4-digit OTP for login.
- **Method**: `POST`
- **URL**: `{{base_url}}/send-otp`
- **Body** (JSON):
```json
{
  "mobile": "9876543210"
}
```
- **Note**: In non-production environments, the OTP is returned in `otp_debug`.

### B. Verify OTP / Login
Verify the OTP to obtain a JWT token.
- **Method**: `POST`
- **URL**: `{{base_url}}/verify-otp`
- **Body** (JSON):
```json
{
  "mobile": "9876543210",
  "otp": "1234"
}
```
- **Response**: Returns `access_token` and `user` object. Set `{{token}}` variable from this response.

### C. Register
Register a new professional account (requires previous OTP verification).
- **Method**: `POST`
- **URL**: `{{base_url}}/register`
- **Body** (JSON):
```json
{
  "mobile": "9876543210",
  "name": "John Doe",
  "category": "Barber",
  "city": "Ahmedabad"
}
```

---

## 3. Dashboard & Status

### Get Dashboard Stats
Overview of today's jobs and earnings.
- **Method**: `GET`
- **URL**: `{{base_url}}/dashboard`

### Toggle Online Status
Switch between Online (Available) and Offline.
- **Method**: `POST`
- **URL**: `{{base_url}}/toggle-availability`

---

## 4. Booking Management

### View Booking Requests
List unassigned jobs available to accept.
- **Method**: `GET`
- **URL**: `{{base_url}}/booking-requests`

### Accept Booking
Claim a job for yourself.
- **Method**: `POST`
- **URL**: `{{base_url}}/bookings/{id}/accept`

### Start / Complete Job
- **Arrived**: `POST {{base_url}}/jobs/{id}/arrived`
- **Start Service**: `POST {{base_url}}/jobs/{id}/start-service`
- **Complete**: `POST {{base_url}}/jobs/{id}/complete`

---

## 5. Wallet & Earnings

### View Wallet
Check current balance and recent transactions.
- **Method**: `GET`
- **URL**: `{{base_url}}/wallet`

### Request Withdrawal
Withdraw funds to the bank account.
- **Method**: `POST`
- **URL**: `{{base_url}}/request-withdrawal`
- **Body** (JSON):
```json
{
  "amount": 500,
  "payment_method": "UPI"
}
```

---

## 6. Profile & Documents

### Get Profile
- **Method**: `GET`
- **URL**: `{{base_url}}/profile`

### Update Profile
Update text fields and upload files.
- **Method**: `POST`
- **URL**: `{{base_url}}/profile`
- **Body** (form-data):
  - `name`: "Updated Name"
  - `avatar`: (file)
  - `aadhaar_front`: (file)
  - `aadhaar_back`: (file)
  - `pan_img`: (file)

---

## 7. Notifications

### List Notifications
- **Method**: `GET`
- **URL**: `{{base_url}}/notifications`

### Mark All as Read
- **Method**: `POST`
- **URL**: `{{base_url}}/notifications/read-all`
