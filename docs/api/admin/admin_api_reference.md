# Admin API Reference

**Base URL:** `http://127.0.0.1:8000/api/admin`

This document outlines the API endpoints available in the Admin panel backend. Nearly all endpoints under this namespace require an authenticated Admin JWT. They are designed to manage the entire Bellavella platform, from customers and professionals to services, packages, and offers.

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

## 1. Authentication (`AuthController.php`)
Manages Admin user logins and session lifecycles using Email and Password.

- `POST /auth/login`: Authenticate an admin and return a JWT access token.
- `GET /auth/me`: Retrieve the authenticated admin's profile details.
- `POST /auth/refresh`: Refresh the current valid JWT token.
- `POST /auth/logout`: Invalidate the current JWT token.

---

## 2. Core Entities

### Customers (`CustomerController.php`)
Manage customer profiles.
- `GET /customers`: List all registered customers.
- `POST /customers`: Create a new customer profile.
- `GET /customers/{id}`: View a specific customer's details and booking history.
- `PUT/PATCH /customers/{id}`: Update a customer's information.
- `DELETE /customers/{id}`: Remove a customer profile.

### Services (`ServiceController.php`)
Manage individual salon/beauty services available on the platform.
- `GET /services`: List all available services.
- `POST /services`: Create a new service (with associated image, price, duration, and category).
- `GET /services/{id}`: View specific service details.
- `PUT/PATCH /services/{id}`: Update a service's information.
- `DELETE /services/{id}`: Remove a service.

### Packages (`PackageController.php`)
Manage bundled service packages.
- `GET /packages`: List all active/inactive packages.
- `POST /packages`: Create a new package containing multiple services.
- `GET /packages/{id}`: View package details.
- `PUT/PATCH /packages/{id}`: Update a package.
- `DELETE /packages/{id}`: Remove a package.

---

## 3. CRM & Marketing

### Offers (`OfferController.php`)
Manage promotional codes, discounts, and active offers.
- `GET /offers`: List all offers.
- `POST /offers`: Create a new promotional offer/coupon.
- `GET /offers/{id}`: View specific offer rules/details.
- `PUT/PATCH /offers/{id}`: Update an offer.
- `DELETE /offers/{id}`: Delete an offer.

### Reviews (`ReviewController.php`)
Manage customer reviews and ratings for professionals and services.
- `GET /reviews`: List all submitted reviews.
- `GET /reviews/{id}`: View details of a specific review.
- `PUT/PATCH /reviews/{id}`: Update a review (e.g., moderate/approve it).
- `DELETE /reviews/{id}`: Delete a review.
> Note: Admin API cannot create (`POST`) reviews directly.

---

## 4. Platform Management

### Banners (`BannerController.php`)
Manage promotional banners displayed on the mobile app home screen.
- `GET /banners` / `POST /banners` / `GET /banners/{id}` / `PUT /banners/{id}` / `DELETE /banners/{id}`

### Videos (`VideoController.php`)
Manage video content (tutorials or promotional) displayed in the apps.
- `GET /videos` / `POST /videos` / `GET /videos/{id}` / `PUT /videos/{id}` / `DELETE /videos/{id}`

### Media Library (`MediaController.php`)
Manage uploaded assets (images, PDFs, generic media files).
- `GET /media` / `POST /media` / `GET /media/{id}` / `PUT /media/{id}` / `DELETE /media/{id}`

### Homepage Content (`HomepageController.php`)
Manage dynamic sections shown on the mobile app homepage.
- `GET /homepage`: List dynamic sections and their contents.
- `POST /homepage/reorder`: Special endpoint to update the display order of homepage sections.
- And standard CRUD operations.

### App Settings (`SettingController.php`)
Manage global platform configurations (fees, limits, toggles).
- `GET /settings`: Retrieve all system settings.
- `GET /settings/{key}`: Retrieve a specific setting by its key.
- `POST /settings`: Bulk update an array of settings.

---

## 5. Operations & Logistics

### Order Assignments (`AssignmentController.php`)
Manage manual assignment of bookings to specific professionals.
- `GET /assignments`: List all bookings needing assignments or tracking existing assignments.
- `POST /assignments`: Create a new assignment pairing a professional with an unassigned booking.

---

## 6. Staff & Workforce Management

### Professionals (`ProfessionalController.php`)
Manage the workforce (beauticians, stylists, etc.).
- `GET /professionals`: List all registered professionals.
- `POST /professionals`: Onboard a new professional account offline.
- `GET /professionals/{id}`: View a professional's comprehensive profile.
- `PUT/PATCH /professionals/{id}`: Update professional details.
- `DELETE /professionals/{id}`: Remove a professional.

### Professional Verification (`ProfessionalVerificationController.php`)
Handle KYC and document approvals for professionals.
- `GET /professionals-verification`: List professionals awaiting document verification.
- `POST /professionals/{id}/verify`: Approve or Reject a professional's submitted documents.

### Professional History & Activity (`ProfessionalOrderController.php`)
Track an individual professional's performance.
- `GET /professionals/{id}/orders`: View current active/assigned orders for a specific professional.
- `GET /professionals/{id}/history`: View completed/historic orders for a specific professional.

### Leave Requests (`LeaveRequestController.php`)
Manage time-off requests submitted by professionals.
- `GET /leave-requests`: List all pending leave requests.
- `GET /leave-requests/{id}` / `POST /leave-requests` / `PUT /leave-requests/{id}` / `DELETE /leave-requests/{id}`: Approve, deny, or adjust specific leave requests.

---

## 7. Inventory & Kit Management

### Kit Products (`KitProductController.php`)
Manage the central inventory catalog of supplies and kits provided to professionals.
- `GET /kit/products`: View inventory levels and catalog.
- `POST /kit/products`: Add a new supply product to inventory.
- And standard CRUD operations.

### Kit Orders (`KitOrderController.php`)
Manage supply requests placed by professionals or assign kits directly from admin.
- `GET /kit/orders`: List kit/supply orders.
- `POST /kit/orders`: Fulfill or create a new kit assignment.
- And standard CRUD operations.
