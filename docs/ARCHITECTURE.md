# System Architecture

## Overview

The Tamil Nadu Vanigargalin Sangamam system is built on a modern, scalable architecture using Laravel 11, with a hybrid database approach combining MySQL for voter data and MongoDB for member storage.

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                         Frontend                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │  Chat UI     │  │  Admin Panel │  │  Card View   │     │
│  │ (Tailwind)   │  │  (Bootstrap) │  │   (HTML)     │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                       │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                    Controllers                        │  │
│  │  • VanigamController (Main API)                      │  │
│  │  • AdminPanelController (Admin)                      │  │
│  └──────────────────────────────────────────────────────┘  │
│                            │                                 │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                     Services                          │  │
│  │  • MongoService (Member CRUD)                        │  │
│  │  • TwoFactorOtpService (OTP)                         │  │
│  │  • CloudinaryService (Images)                        │  │
│  │  • VoterLookupService (EPIC)                         │  │
│  │  • CardGenerationService (Cards)                     │  │
│  └──────────────────────────────────────────────────────┘  │
│                            │                                 │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                      Helpers                          │  │
│  │  • VoterHelper (Database queries)                    │  │
│  │  • SecurityHelper (Validation)                       │  │
│  │  • StatisticsHelper (Analytics)                      │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        ▼                   ▼                   ▼
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│    MySQL     │  │   MongoDB    │  │  External    │
│  (Voters)    │  │  (Members)   │  │  Services    │
│              │  │              │  │              │
│ • 234 tables │  │ • members    │  │ • Cloudinary │
│ • 5M voters  │  │ • sessions   │  │ • 2Factor    │
│ • Read-only  │  │ • cache      │  │ • Redis      │
└──────────────┘  └──────────────┘  └──────────────┘
```

---

## Components

### 1. Frontend Layer

#### Chat Interface
- **Technology:** Vanilla JavaScript + Tailwind CSS
- **Style:** WhatsApp-inspired design
- **Features:**
  - Progressive form flow
  - Real-time validation
  - Mobile-first responsive
  - Smooth animations

#### Admin Panel
- **Technology:** Blade templates + Bootstrap
- **Features:**
  - Dashboard with statistics
  - Member management
  - Voter lookup
  - Secure authentication

#### Card View
- **Technology:** HTML + CSS
- **Features:**
  - Digital ID card display
  - QR code integration
  - Print-friendly layout
  - Downloadable format

### 2. Application Layer

#### Controllers

**VanigamController**
- Main application logic
- API endpoints
- Member registration flow
- Card generation
- OTP handling

**AdminPanelController**
- Admin authentication
- Dashboard data
- Member queries
- Voter searches
- Statistics generation

#### Services

**MongoService**
- MongoDB connection management
- CRUD operations for members
- Query optimization
- Data validation

**TwoFactorOtpService**
- OTP generation
- Voice call integration
- Session management
- Verification logic

**CloudinaryService**
- Image upload
- Photo optimization
- URL generation
- Storage management

**VoterLookupService**
- EPIC validation
- Voter data retrieval
- Cross-table search
- Cache management

**CardGenerationService**
- Card template rendering
- QR code generation
- Image composition
- PDF generation (future)

#### Helpers

**VoterHelper**
- Database query abstraction
- Table name management
- Data transformation
- Cache optimization

**SecurityHelper**
- Input validation
- XSS prevention
- CSRF protection
- Rate limiting

**StatisticsHelper**
- Data aggregation
- Report generation
- Analytics calculation

### 3. Data Layer

#### MySQL Database (Voters)
- **Purpose:** Read-only voter data
- **Structure:** 234 assembly tables
- **Size:** ~5 million records
- **Access:** Via VoterHelper

**Table Pattern:**
```
tbl_voters_[assembly_name]_[number]
Example: tbl_voters_alandur_28
```

**Columns:**
- EPIC_NO (Primary Key)
- FM_NAME_EN (First Name)
- LASTNAME_EN (Last Name)
- AC_NO (Assembly Number)
- ASSEMBLY_NAME
- DISTRICT_NAME
- AGE
- GENDER
- DOB
- MOBILE_NO
- PART_NO
- SECTION_NO
- C_HOUSE_NO
- RLN_TYPE (Relation Type)
- RLN_FM_NM_EN (Relation First Name)
- RLN_L_NM_EN (Relation Last Name)

#### MongoDB Database (Members)
- **Purpose:** Member data storage
- **Provider:** MongoDB Atlas
- **Database:** vanigan
- **Collection:** members

**Document Structure:**
```json
{
  "_id": ObjectId,
  "unique_id": "VNG-A3B7C1D",
  "epic_no": "IJB0768549",
  "name": "Sharmela",
  "mobile": "9876543210",
  "assembly": "Alandur",
  "district": "Kanchipuram",
  "photo_url": "https://...",
  "qr_url": "https://...",
  "membership": "Member",
  "age": "33",
  "blood_group": "O+",
  "address": "...",
  "dob": "1990-04-16",
  "ptc_code": "PTC-ABC1234",
  "referral_id": "REF-XYZ5678",
  "referral_count": 5,
  "created_at": ISODate,
  "updated_at": ISODate
}
```

### 4. External Services

#### Cloudinary
- **Purpose:** Image storage and optimization
- **Accounts:** 2 (photos + assets)
- **Features:**
  - Automatic optimization
  - CDN delivery
  - Transformation API
  - Secure URLs

#### 2Factor.in
- **Purpose:** OTP delivery
- **Method:** Voice call
- **Features:**
  - Auto-generated OTP
  - Session management
  - Verification API
  - Rate limiting

#### Upstash Redis (Optional)
- **Purpose:** Caching
- **Current:** File-based cache
- **Future:** Redis for better performance

---

## Data Flow

### Registration Flow

```
1. User enters mobile number
   ↓
2. System sends OTP via 2Factor.in
   ↓
3. User enters OTP
   ↓
4. System verifies OTP
   ↓
5. User enters EPIC number
   ↓
6. System validates against MySQL (234 tables)
   ↓
7. User uploads photo
   ↓
8. System uploads to Cloudinary
   ↓
9. System generates unique ID
   ↓
10. System creates member card
    ↓
11. System saves to MongoDB
    ↓
12. User receives card with QR code
```

### EPIC Validation Flow

```
1. Receive EPIC number
   ↓
2. Check cache for EPIC
   ├─ Found → Return cached data
   └─ Not found ↓
3. Query 234 voter tables in batches
   ├─ Found → Cache result → Return data
   └─ Not found → Cache negative result → Return error
```

### Card Generation Flow

```
1. Collect member data
   ↓
2. Generate unique ID (VNG-XXXXXXX)
   ↓
3. Generate PTC code (PTC-XXXXXXX)
   ↓
4. Create QR code with verification URL
   ↓
5. Compose card with photo
   ↓
6. Save to MongoDB
   ↓
7. Return card URL and QR URL
```

---

## Security Architecture

### Authentication
- Admin: Session-based
- API: Rate-limited, no auth required
- Future: JWT tokens for mobile app

### Data Protection
- Passwords: bcrypt hashing
- Sessions: Encrypted cookies
- CSRF: Token validation
- XSS: Input sanitization

### Network Security
- HTTPS: SSL/TLS encryption
- CORS: Configured headers
- Rate Limiting: IP-based
- Firewall: Server-level

---

## Scalability Considerations

### Current Capacity
- **Concurrent Users:** ~1000
- **Database:** 5M voters, unlimited members
- **Storage:** Cloudinary (unlimited)
- **OTP:** 2Factor.in (API limits)

### Scaling Strategy

**Horizontal Scaling:**
- Add more application servers
- Load balancer (Nginx/HAProxy)
- Database read replicas

**Vertical Scaling:**
- Increase server resources
- Optimize database queries
- Enable Redis caching

**Database Optimization:**
- Index optimization
- Query caching
- Connection pooling
- Batch processing

---

## Performance Optimization

### Current Optimizations
1. **Caching:**
   - Voter table list (1 hour)
   - EPIC lookups (10 minutes)
   - Config caching (production)

2. **Database:**
   - Batch queries (30 tables at a time)
   - Indexed EPIC_NO columns
   - Connection reuse

3. **Assets:**
   - CDN delivery (Cloudinary)
   - Image optimization
   - Lazy loading

### Future Optimizations
1. Redis caching
2. Queue workers for OTP
3. Database sharding
4. CDN for static assets
5. API response caching

---

## Monitoring & Logging

### Application Logs
- **Location:** `storage/logs/laravel.log`
- **Level:** Error (production), Debug (development)
- **Rotation:** Daily

### Error Tracking
- Laravel exception handler
- Custom error pages
- Stack traces (development only)

### Performance Monitoring
- Response times
- Database query times
- External API latency
- Memory usage

---

## Deployment Architecture

### Production Environment
```
┌─────────────────────────────────────┐
│         Load Balancer (Optional)    │
└─────────────────────────────────────┘
                  │
┌─────────────────────────────────────┐
│         Web Server (Apache/Nginx)   │
│         SSL Certificate             │
└─────────────────────────────────────┘
                  │
┌─────────────────────────────────────┐
│         PHP-FPM 8.2                 │
│         Laravel Application         │
└─────────────────────────────────────┘
                  │
        ┌─────────┴─────────┐
        ▼                   ▼
┌──────────────┐  ┌──────────────┐
│ MySQL Server │  │ MongoDB Atlas│
│ (Local/RDS)  │  │   (Cloud)    │
└──────────────┘  └──────────────┘
```

### Backup Strategy
- **MongoDB:** Daily automated backups
- **MySQL:** Weekly snapshots
- **Files:** Cloudinary (automatic)
- **Code:** Git repository

---

## Technology Stack Summary

| Layer | Technology | Version |
|-------|-----------|---------|
| Framework | Laravel | 11.x |
| Language | PHP | 8.2+ |
| Frontend | Tailwind CSS | 3.4 |
| Database (Voters) | MySQL | 5.7+ |
| Database (Members) | MongoDB | 6.0+ |
| Cache | File/Redis | - |
| Image Storage | Cloudinary | - |
| OTP Service | 2Factor.in | - |
| Web Server | Apache/Nginx | - |
| SSL | Let's Encrypt | - |

---

**Last Updated:** March 19, 2026
