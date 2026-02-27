# Professional API Reference

**Base URL:** `http://127.0.0.1:8000/api/professionals`

This document details the API endpoints strictly within the Professional mobile app backend (`app/Http/Controllers/Api/Professionals`). These endpoints are designed to handle authentication (via mobile & OTP), profile management, booking fulfillment, wallet transactions, and push notifications for Bellavella's active workforce.

All endpoints adhere to the standard JSON response envelope:
```json
{
  "success": true,
  "message": "Human-readable message",
  "data": { ... },
  "errors": null
}
```

---

## 1. Authentication & Onboarding (`AuthController.php`)
Handles Mobile + OTP login flows and account verification status.

- `POST /auth/send-otp`: Request a 4-digit OTP code to be sent to a mobile number. Invalidates any previous pending OTPs.
- `POST /auth/verify-otp`: Validate an OTP. Returns a JWT if the user exists, or prompts for signup if it's a new mobile number.
- `POST /auth/signup`: Finalize onboarding for a newly verified mobile number by providing name, city, and category. Returns a JWT.
- `GET /auth/status`: Check the professional's verification status (e.g., `Pending`, `Verified`, `Rejected`) and if document uploads are complete (`docs` boolean).
- `GET /auth/me`: Retrieve the currently logged-in professional's basic profile details.
- `POST /auth/refresh`: Refresh the current valid JWT token.
- `POST /auth/logout`: Invalidate the current JWT token.

---

## 2. Profile & Documents (`ProfileController.php`)
Allows the professional to manage their details and compliance documents.

- `GET /profile`: View comprehensive profile details.
- `POST /profile/update`: Update text fields (bio, experience, category, city) and securely upload associated documents (`avatar`, `aadhaar_front`, `aadhaar_back`, `pan_img`). Once all documents are uploaded, the system automatically flags the professional as ready for Admin verification.

---

## 3. Operations & Assignments (`BookingController.php`)
Manages the professional's job queue and active order fulfillment.

- `GET /bookings/requests`: View a list of all unassigned/pending bookings matching the professional's city. *(Requires `Verified` account status)*
- `GET /bookings`: View the professional's own assigned upcoming and actively ongoing bookings.
- `GET /bookings/{id}`: View the full details, services, and customer notes for a specific assigned booking.
- `POST /bookings/{id}/accept`: Claim an unassigned booking request from the pool.
- `POST /bookings/{id}/reject`: Dismiss/hide a booking request from the professional's pool view.
- `POST /bookings/{id}/status`: Progress a booking through its lifecycle (`Assigned` -> `Started` -> `In Progress` -> `Completed`). Marking a booking as `Completed` automatically triggers the platform's commission calculations and credits the professional's total earnings.

---

## 4. Homepage & Availability (`DashboardController.php`)
Powers the professional dashboard and manages visibility to customers.

- `GET /dashboard`: Fast holistic overview containing today's active bookings list, counts of available pending requests, and high-level earnings/rating summaries.
- `GET /schedule`: View the professional's upcoming confirmed schedule (today and future bookings).
- `GET /availability`: Check if the professional is currently visible/online for new assignments.
- `POST /availability`: Toggle the `is_online` status boolean.

---

## 5. Finances & Payouts (`EarningsController.php`)
Handles dynamic earning calculations, job history, and wallet management.

- `GET /earnings`: View calculated earnings breakdowns specifically for today, the current week, and the current month based on completed bookings minus commission.
- `GET /jobs/history`: View a paginated list of historically completed or cancelled bookings.
- `GET /wallet`: Retrieve the professional's current cash wallet balance and the 20 most recent transactional Ledger entries (credits and debits).
- `POST /wallet/withdraw`: Request a cash payout to a linked bank account. This uses a pessimistic database lock (`DB::transaction` with `lockForUpdate()`) to securely deduct the balance and log a transactional `debit`.

---

## 6. Inventory & Supplies (`KitController.php`)
Allows the professional to restock essential supplies from the central admin.

- `GET /kit-store`: Browse the active catalog of available kit products and current inventory levels.
- `POST /kit-orders`: Place an order requesting a specific quantity of a kit product. Deducts from the central `total_stock` and creates a `KitOrder` assignment.

---

## 7. Push Notifications (`NotificationController.php`)
Serves native alert payloads.

- `GET /notifications`: Retrieve a paginated list of all past notifications (e.g., new order assignments, wallet deposits) sent to the professional.
- `POST /notifications/read`: Mark an array of notification IDs as `read_at` to clear badge counts in the flutter app.
