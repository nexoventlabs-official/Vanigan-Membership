# API Documentation

## Base URL

```
https://vanigan.digital/api/vanigam
```

## Authentication

Most endpoints do not require authentication. Admin endpoints require session authentication.

## Rate Limiting

- OTP endpoints: 3 requests per 5 minutes per IP
- OTP cooldown: 60 seconds between requests for same mobile

---

## Endpoints

### 1. Health Check

Check API status.

**Endpoint:** `GET /api/health`

**Response:**
```json
{
  "status": "ok",
  "timestamp": "2026-03-19T10:30:00Z"
}
```

---

### 2. Send OTP

Send OTP via voice call to mobile number.

**Endpoint:** `POST /api/vanigam/send-otp`

**Request Body:**
```json
{
  "mobile": "9876543210"
}
```

**Validation:**
- `mobile`: Required, 10 digits, must start with 6-9

**Success Response (200):**
```json
{
  "success": true,
  "message": "OTP sent successfully to +919876543210",
  "mobile": "9876543210"
}
```

**Error Response (429 - Rate Limited):**
```json
{
  "success": false,
  "message": "Too many OTP requests. Please try after 5 minutes."
}
```

**Error Response (429 - Cooldown):**
```json
{
  "success": false,
  "message": "OTP already sent. Please wait before requesting again."
}
```

---

### 3. Verify OTP

Verify the OTP code sent to mobile.

**Endpoint:** `POST /api/vanigam/verify-otp`

**Request Body:**
```json
{
  "mobile": "9876543210",
  "code": "123456"
}
```

**Validation:**
- `mobile`: Required, 10 digits
- `code`: Required, 6 digits

**Success Response (200):**
```json
{
  "success": true,
  "message": "OTP verified successfully"
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Invalid OTP. Please try again."
}
```

**Error Response (400 - Expired):**
```json
{
  "success": false,
  "message": "OTP session expired. Please request a new OTP."
}
```

---

### 4. Check Member

Check if mobile number is already registered.

**Endpoint:** `POST /api/vanigam/check-member`

**Request Body:**
```json
{
  "mobile": "9876543210"
}
```

**Success Response (200 - Existing Member):**
```json
{
  "success": true,
  "exists": true,
  "member": {
    "unique_id": "VNG-A3B7C1D",
    "name": "John Doe",
    "mobile": "9876543210",
    "epic_no": "ABC1234567",
    "membership": "Member"
  }
}
```

**Success Response (200 - New Member):**
```json
{
  "success": true,
  "exists": false
}
```

---

### 5. Validate EPIC

Validate EPIC (Voter ID) number against voter database.

**Endpoint:** `POST /api/vanigam/validate-epic`

**Request Body:**
```json
{
  "epic_no": "IJB0768549"
}
```

**Validation:**
- `epic_no`: Required, 5-20 characters

**Success Response (200):**
```json
{
  "success": true,
  "voter": {
    "name": "Sharmela",
    "epic_no": "IJB0768549",
    "assembly_name": "Alandur",
    "district": "Kanchipuram"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "EPIC Number not found. Please check and try again."
}
```

---

### 6. Upload Photo

Upload member photo to Cloudinary.

**Endpoint:** `POST /api/vanigam/upload-photo`

**Request Body (multipart/form-data):**
```
photo: [file]
mobile: "9876543210"
```

**Validation:**
- `photo`: Required, image file (jpg, png), max 5MB
- `mobile`: Required, 10 digits

**Success Response (200):**
```json
{
  "success": true,
  "photo_url": "https://res.cloudinary.com/dqndhcmu2/image/upload/v1234567890/vanigan/photos/abc123.jpg",
  "message": "Photo uploaded successfully"
}
```

**Error Response (400 - No Face):**
```json
{
  "success": false,
  "message": "No face detected in photo. Please upload a clear photo."
}
```

---

### 7. Generate Card

Generate member ID card.

**Endpoint:** `POST /api/vanigam/generate-card`

**Request Body:**
```json
{
  "mobile": "9876543210",
  "epic_no": "IJB0768549",
  "photo_url": "https://...",
  "name": "Sharmela",
  "assembly": "Alandur",
  "district": "Kanchipuram",
  "age": "33",
  "blood_group": "O+",
  "address": "123 Main St, Chennai"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "unique_id": "VNG-A3B7C1D",
  "card_url": "https://vanigan.digital/member/card/VNG-A3B7C1D",
  "qr_url": "https://vanigan.digital/member/verify/VNG-A3B7C1D",
  "message": "Card generated successfully"
}
```

---

### 8. Save Additional Details

Save additional member details (blood group, address, etc.).

**Endpoint:** `POST /api/vanigam/save-details`

**Request Body:**
```json
{
  "unique_id": "VNG-A3B7C1D",
  "blood_group": "O+",
  "address": "123 Main St, Chennai",
  "dob": "1990-04-16"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Details saved successfully"
}
```

---

### 9. Get Member

Get member details by unique ID.

**Endpoint:** `GET /api/vanigam/member/{uniqueId}`

**Example:** `GET /api/vanigam/member/VNG-A3B7C1D`

**Success Response (200):**
```json
{
  "success": true,
  "member": {
    "unique_id": "VNG-A3B7C1D",
    "name": "Sharmela",
    "epic_no": "IJB0768549",
    "mobile": "9876543210",
    "assembly": "Alandur",
    "district": "Kanchipuram",
    "photo_url": "https://...",
    "qr_url": "https://...",
    "membership": "Member",
    "created_at": "2026-03-19T10:30:00Z"
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Member not found"
}
```

---

### 10. Generate QR Code

Generate QR code image for member verification.

**Endpoint:** `GET /api/vanigam/qr/{uniqueId}`

**Example:** `GET /api/vanigam/qr/VNG-A3B7C1D`

**Response:** PNG image (QR code)

---

### 11. Get Referral

Get or create referral link for member.

**Endpoint:** `POST /api/vanigam/get-referral`

**Request Body:**
```json
{
  "unique_id": "VNG-A3B7C1D"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "referral_id": "REF-ABC12345",
  "referral_link": "https://vanigan.digital/refer/VNG-A3B7C1D/REF-ABC12345",
  "referral_count": 5
}
```

---

### 12. Increment Referral

Increment referral count when someone uses referral link.

**Endpoint:** `POST /api/vanigam/increment-referral`

**Request Body:**
```json
{
  "referral_id": "REF-ABC12345"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Referral count updated"
}
```

---

### 13. Loan Request

Submit a loan request.

**Endpoint:** `POST /api/vanigam/loan-request`

**Request Body:**
```json
{
  "unique_id": "VNG-A3B7C1D",
  "amount": "50000",
  "purpose": "Business",
  "mobile": "9876543210"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "loan_id": "LOAN-123456",
  "message": "Loan request submitted successfully"
}
```

---

### 14. Check Loan Status

Check status of loan request.

**Endpoint:** `POST /api/vanigam/check-loan-status`

**Request Body:**
```json
{
  "unique_id": "VNG-A3B7C1D"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "loans": [
    {
      "loan_id": "LOAN-123456",
      "amount": "50000",
      "status": "pending",
      "requested_at": "2026-03-19T10:30:00Z"
    }
  ]
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Bad Request (validation error) |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Unprocessable Entity (validation failed) |
| 429 | Too Many Requests (rate limited) |
| 500 | Internal Server Error |

---

## Example Usage

### JavaScript (Fetch API)

```javascript
// Send OTP
async function sendOTP(mobile) {
  const response = await fetch('https://vanigan.digital/api/vanigam/send-otp', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ mobile })
  });
  
  return await response.json();
}

// Validate EPIC
async function validateEPIC(epicNo) {
  const response = await fetch('https://vanigan.digital/api/vanigam/validate-epic', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ epic_no: epicNo })
  });
  
  return await response.json();
}
```

### cURL

```bash
# Send OTP
curl -X POST https://vanigan.digital/api/vanigam/send-otp \
  -H "Content-Type: application/json" \
  -d '{"mobile":"9876543210"}'

# Validate EPIC
curl -X POST https://vanigan.digital/api/vanigam/validate-epic \
  -H "Content-Type: application/json" \
  -d '{"epic_no":"IJB0768549"}'
```

---

## Notes

- All timestamps are in ISO 8601 format (UTC)
- Mobile numbers are Indian format (10 digits, starting with 6-9)
- EPIC numbers are uppercase alphanumeric
- Photo uploads must be JPEG or PNG format
- Maximum photo size: 5MB
- QR codes are 300x300 pixels PNG images

---

**Last Updated:** March 19, 2026
