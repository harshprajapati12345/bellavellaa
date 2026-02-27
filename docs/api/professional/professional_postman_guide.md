# Professional App API - Postman Guide

**Base URL:** `http://127.0.0.1:8000/api/professionals`

All successful responses return a 200 OK status code. Errors return 4xx or 500 status codes.

---

## 1. Authentication

### Send OTP
Generates and sends an OTP to the professional's mobile number.
- **Method:** `POST`
- **URL:** `/auth/send-otp`
- **Body (`raw` JSON):**
  ```json
  {
      "mobile": "9876543210"
  }
  ```

### Verify OTP
Verifies the OTP and logs the professional in. If the mobile number doesn't exist, it prompts them to sign up.
- **Method:** `POST`
- **URL:** `/auth/verify-otp`
- **Body (`raw` JSON):**
  ```json
  {
      "mobile": "9876543210",
      "otp": "1234"
  }
  ```
- **Response:** If it's a new user, `is_new_user` will be true and no `access_token` is provided. If it's an existing user, it returns the `access_token`.

### Signup (New Professional Registration)
Completes the registration process for a verified mobile number.
- **Method:** `POST`
- **URL:** `/auth/signup`
- **Body (`raw` JSON):**
  ```json
  {
      "mobile": "9876543210",
      "name": "Jane Does",
      "category": "Hair Stylist",
      "city": "Mumbai"
  }
  ```
- **Response:** Returns the professional's data and an `access_token`.

### Get Current User Profile (Me)
- **Method:** `GET`
- **URL:** `/auth/me`
- **Headers:** `Authorization: Bearer <your_access_token>`

### Get Verification Status
Returns the verification status of the currently authenticated professional (`Pending`, `Verified`, `Rejected`).
- **Method:** `GET`
- **URL:** `/auth/status`
- **Headers:** `Authorization: Bearer <your_access_token>`

### Refresh Token
Refresh the JWT token.
- **Method:** `POST`
- **URL:** `/auth/refresh`
- **Headers:** `Authorization: Bearer <your_access_token>`

### Logout
Invalidate the current token.
- **Method:** `POST`
- **URL:** `/auth/logout`
- **Headers:** `Authorization: Bearer <your_access_token>`

---

## 2. Profile Management

All endpoints require: `Authorization: Bearer <your_access_token>`

### Get Profile
Retrieve the professional's full profile details.
- **Method:** `GET`
- **URL:** `/profile`

### Update Profile & Upload Documents
Update text details and optionally upload files (`avatar`, `aadhaar_front`, `aadhaar_back`, `pan_img`).
- **Method:** `POST`
- **URL:** `/profile/update`
- **Headers:** *Do not manually set Content-Type; let Postman handle boundary for form-data.*
- **Body (`form-data`):**
  - `name`: (Text) "Jane Doe"
  - `email`: (Text) "jane@example.com"
  - `phone`: (Text) "9876543210"
  - `city`: (Text) "Mumbai"
  - `category`: (Text) "Beautician"
  - `experience`: (Text) "5 Years"
  - `bio`: (Text) "Experienced beautician specializing in bridal makeup."
  - `aadhaar`: (Text) "123412341234"
  - `pan`: (Text) "ABCDE1234F"
  - `avatar`: (File) *Select image*
  - `aadhaar_front`: (File) *Select image/pdf*
  - `aadhaar_back`: (File) *Select image/pdf*
  - `pan_img`: (File) *Select image/pdf*

---

## 3. Booking and Order Management

All endpoints require: `Authorization: Bearer <your_access_token>`

### Get Booking Requests
List pending/unassigned bookings in the professional's city available to be accepted. **(Professional must be 'Verified')**
- **Method:** `GET`
- **URL:** `/bookings/requests`

### Get My Assigned Bookings
List all bookings currently assigned to the professional.
- **Method:** `GET`
- **URL:** `/bookings`

### Get Booking Details
Retrieve comprehensive details of a specific assigned booking.
- **Method:** `GET`
- **URL:** `/bookings/{id}`
  - Example: `/bookings/5`

### Accept a Booking Request
Assign an unassigned booking to this professional. **(Professional must be 'Verified')**
- **Method:** `POST`
- **URL:** `/bookings/{id}/accept`

### Reject/Dismiss a Booking Request
Dismiss an unassigned booking from the professional's list.
- **Method:** `POST`
- **URL:** `/bookings/{id}/reject`

### Update Booking Status
Update the status of an assigned job.
- **Method:** `POST`
- **URL:** `/bookings/{id}/status`
- **Body (`raw` JSON):**
  ```json
  {
      "status": "In Progress" // Valid: "Assigned", "Started", "In Progress", "Completed", "Cancelled"
  }
  ```
> Setting status to `Completed` automatically calculates commission and credits earnings to the professional.

---

## 4. Dashboard and Schedule

All endpoints require: `Authorization: Bearer <your_access_token>`

### Get Dashboard Summary
Provides an overview of today's bookings, total earnings, today's earnings, and counts of pending requests.
- **Method:** `GET`
- **URL:** `/dashboard`

### Get Upcoming Schedule
Retrieves all non-cancelled, assigned bookings from today onwards.
- **Method:** `GET`
- **URL:** `/schedule`

### Get Online/Availability Status
Returns if the professional is currently accepting requests (`is_online`).
- **Method:** `GET`
- **URL:** `/availability`

### Toggle Online/Availability Status
Set the professional as online (`true`) or offline (`false`).
- **Method:** `POST`
- **URL:** `/availability`
- **Body (`raw` JSON):**
  ```json
  {
      "is_online": true
  }
  ```

---

## 5. Earnings and Wallet

All endpoints require: `Authorization: Bearer <your_access_token>`

### Get Earnings Overview
Get daily, weekly, monthly, and lifetime earnings summary.
- **Method:** `GET`
- **URL:** `/earnings`

### Get Completed Job History (Payouts)
Get a paginated list of all historically completed bookings.
- **Method:** `GET`
- **URL:** `/jobs/history`

### Get Wallet Balance & Transactions
Retrieve the current cash balance (in Rupees) and recent wallet transactions (debits/credits).
- **Method:** `GET`
- **URL:** `/wallet`

### Request a Withdrawal
Withdraw funds from the wallet (Minimum Rs. 100).
- **Method:** `POST`
- **URL:** `/wallet/withdraw`
- **Body (`raw` JSON):**
  ```json
  {
      "amount": 500
  }
  ```

---

## 6. Kit Management

All endpoints require: `Authorization: Bearer <your_access_token>`

### Get Kit Store Inventory
Retrieve an inventory list of active `KitProducts` available to order.
- **Method:** `GET`
- **URL:** `/kit-store`

### Place a Kit Order
Request a product from the kit store inventory.
- **Method:** `POST`
- **URL:** `/kit-orders`
- **Body (`raw` JSON):**
  ```json
  {
      "kit_product_id": 1,
      "quantity": 2,
      "notes": "Please include standard nozzle sizes."
  }
  ```

---

## 7. Notifications

All endpoints require: `Authorization: Bearer <your_access_token>`

### Get Recent Notifications
Retrieve a paginated list of professional push/in-app notifications.
- **Method:** `GET`
- **URL:** `/notifications`

### Mark Notifications as Read
Mark an array of notification IDs as read.
- **Method:** `POST`
- **URL:** `/notifications/read`
- **Body (`raw` JSON):**
  ```json
  {
      "notification_ids": [1, 2, 3]
  }
  ```
