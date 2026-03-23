# Admin Panel Guide

## Accessing the Admin Panel

**URL:** https://vanigan.digital/admin/login

**Default Credentials:**
- Username: `admin`
- Password: `admin`

⚠️ **Important:** Change the default password after first login!

---

## Dashboard

The dashboard provides an overview of the system:

### Statistics Displayed
- Total members registered
- Recent registrations (last 7 days)
- Total voters in database
- System status

### Quick Actions
- View all members
- Search voters
- View recent activity

---

## Member Management

### View All Members

**Path:** Admin Panel → Users

**Features:**
- List of all registered members
- Search by name, EPIC, or unique ID
- Filter by registration date
- Pagination (50 members per page)

**Information Displayed:**
- Unique ID (VNG-XXXXXXX)
- Name
- EPIC Number
- Mobile Number
- Assembly
- District
- Registration Date
- Status

### View Member Details

Click on any member to view full details:

**Member Information:**
- Personal details (name, age, gender)
- Contact information (mobile)
- Voter details (EPIC, assembly, district)
- Photo
- Member card preview
- QR code
- Registration timestamp
- Additional details (blood group, address)

**Actions Available:**
- View member card
- Download card
- View QR code
- Check referrals

---

## Voter Lookup

### Search Voters

**Path:** Admin Panel → Voters

**Search Options:**
- By EPIC number
- By name
- By assembly
- By district

**Search Results:**
- Voter name
- EPIC number
- Assembly constituency
- District
- Age
- Gender
- Part number
- Section number

### View Voter Details

Click on any voter to see complete information:

**Voter Information:**
- Full name
- EPIC number
- Assembly name and number
- District
- Age and date of birth
- Gender
- Relation type and name
- House number
- Part and section numbers
- Mobile number (if available)

**Additional Features:**
- Check if voter is registered as member
- View member card if registered
- Generate new member card

---

## User Actions

### Verify Member

1. Go to Members list
2. Click on member unique ID
3. View member details
4. Verify information matches voter data
5. Check photo authenticity

### Search Member

**By Unique ID:**
1. Go to Users
2. Enter unique ID in search box
3. Click search

**By EPIC Number:**
1. Go to Voters
2. Enter EPIC number
3. Click search
4. Check if member exists

### Download Member Card

1. View member details
2. Click "View Card" button
3. Right-click on card
4. Select "Save image as"

---

## Reports & Statistics

### Member Statistics

**Available Metrics:**
- Total members
- Members by assembly
- Members by district
- Registration trends
- Daily/weekly/monthly signups

### Voter Statistics

**Available Metrics:**
- Total voters in database
- Voters by assembly
- Voters by district
- Age distribution
- Gender distribution

---

## Security Features

### Session Management

- Auto-logout after 2 hours of inactivity
- Secure session cookies
- CSRF protection

### Access Control

- Password-protected access
- Single admin account
- Activity logging

### Data Protection

- Read-only voter database
- Encrypted passwords
- Secure API endpoints

---

## Common Tasks

### Task 1: Verify New Registration

1. Go to Dashboard
2. Check "Recent Registrations"
3. Click on member unique ID
4. Verify:
   - Photo matches EPIC data
   - EPIC number is valid
   - Personal details are correct
   - Contact information is accurate

### Task 2: Search for Duplicate

1. Go to Voters
2. Search by EPIC number
3. Check if already registered
4. If duplicate found, contact member

### Task 3: Generate Statistics Report

1. Go to Dashboard
2. Note down statistics
3. Export data (if needed)
4. Generate report

### Task 4: Troubleshoot Member Issue

1. Get member unique ID or mobile
2. Search in Members list
3. View member details
4. Check:
   - Registration status
   - Card generation status
   - Photo upload status
   - EPIC validation status

---

## Troubleshooting

### Cannot Login

**Problem:** Invalid username or password

**Solution:**
1. Verify credentials
2. Check caps lock
3. Clear browser cache
4. Try different browser

### Member Not Found

**Problem:** Cannot find member in list

**Solution:**
1. Check spelling of search term
2. Try searching by unique ID
3. Try searching by EPIC number
4. Check if member completed registration

### Card Not Displaying

**Problem:** Member card not showing

**Solution:**
1. Check if photo was uploaded
2. Verify EPIC validation completed
3. Check MongoDB connection
4. View Laravel logs

### Slow Performance

**Problem:** Admin panel loading slowly

**Solution:**
1. Clear browser cache
2. Check server resources
3. Optimize database queries
4. Enable caching

---

## Best Practices

### Security

1. **Change Default Password**
   - Use strong password (12+ characters)
   - Include uppercase, lowercase, numbers, symbols
   - Don't share password

2. **Logout After Use**
   - Always logout when done
   - Don't leave session open
   - Use private browsing if on shared computer

3. **Regular Monitoring**
   - Check for suspicious activity
   - Monitor registration patterns
   - Review error logs

### Data Management

1. **Regular Backups**
   - Backup MongoDB weekly
   - Export member list monthly
   - Keep backup of voter database

2. **Data Verification**
   - Spot-check new registrations
   - Verify photo quality
   - Validate EPIC numbers

3. **Performance Monitoring**
   - Check response times
   - Monitor database queries
   - Review error rates

---

## Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| Ctrl + F | Search |
| Esc | Close modal |
| Enter | Submit form |

---

## FAQ

### Q: How do I change admin password?

A: Contact system administrator to update `ADMIN_PASSWORD_HASH` in `.env` file.

### Q: Can I add more admin users?

A: Currently supports single admin. Contact developer for multi-admin setup.

### Q: How do I export member list?

A: Use MongoDB export tools or contact developer for export feature.

### Q: What if voter database is outdated?

A: Contact database administrator to update voter tables.

### Q: How do I delete a member?

A: Currently no delete feature. Contact developer if needed.

---

## Support

For technical issues:
1. Check [Troubleshooting Guide](TROUBLESHOOTING.md)
2. Review Laravel logs: `storage/logs/laravel.log`
3. Contact system administrator

---

**Last Updated:** March 19, 2026
