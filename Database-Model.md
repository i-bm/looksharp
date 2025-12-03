# Looksharp Database Model

**Version:** 1.0  
**Date:** December 2025  
**Based on:** Business Requirements Document v1.0

---

## Overview

This document outlines the core database schema for the Looksharp platform, capturing the essential entities and relationships needed to support Talent, Employers, Universities, and the various features described in the BRD.

---

## Core Entities

### 1. Users

Base authentication and user management table.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| email | String | Unique email address |
| phone_number | String | Ghana phone number (unique) |
| password_hash | String | Hashed password |
| auth_provider | Enum | Google, Apple, Phone, Email |
| auth_provider_id | String | External provider ID |
| user_type | Enum | talent, employer, university_admin, admin |
| language_preference | String | en, twi, ga, ewe, fr |
| is_verified | Boolean | Account verification status |
| is_active | Boolean | Account active status |
| last_login_at | Timestamp | Last login timestamp |
| created_at | Timestamp | Account creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- One-to-one with `talent_profiles` (if user_type = talent)
- One-to-one with `employers` (if user_type = employer)
- One-to-one with `university_admins` (if user_type = university_admin)

---

### 2. Talent Profiles

Extended profile information for Talent users.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| user_id | UUID | Foreign key to users |
| first_name | String | First name |
| last_name | String | Last name |
| date_of_birth | Date | Date of birth |
| gender | Enum | male, female, other, prefer_not_to_say |
| profile_photo | String | Profile photo URL |
| video_introduction | String | 30-sec video URL |
| bio | Text | Short biography |
| location | String | City/Region in Ghana |
| nss_status | Enum | awaiting, posted, completed, not_applicable |
| nss_posting_location | String | NSS posting location |
| nss_posting_number | String | NSS posting number |
| verification_status | Enum | pending, verified, rejected |
| verification_type | Enum | ghana_card, student_id, passport |
| verification_document_url | String | Verification document URL |
| verification_verified_at | Timestamp | When verification was completed |
| profile_completeness_score | Integer | 0-100 profile completeness |
| created_at | Timestamp | Profile creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- One-to-many with `talent_education`
- One-to-many with `talent_skills`
- One-to-many with `talent_portfolio_links`
- One-to-many with `applications`
- One-to-many with `saved_opportunities`
- One-to-many with `saved_searches`
- One-to-many with `reviews` (as reviewer)
- One-to-many with `resumes`

---

### 3. Talent Education

Education history for Talent.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| institution_id | UUID | Foreign key to institutions |
| degree_type | Enum | certificate, diploma, bachelors, masters, phd |
| field_of_study | String | Major/course name |
| start_date | Date | Enrollment start |
| end_date | Date | Graduation date (nullable) |
| is_current | Boolean | Currently enrolled |
| gpa | Decimal | GPA if applicable |
| is_primary | Boolean | Primary education record |
| created_at | Timestamp | Record creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `talent_profiles`
- Many-to-one with `institutions`

---

### 4. Talent Skills

Skills and competencies for Talent.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| skill_name | String | Skill name |
| proficiency_level | Enum | beginner, intermediate, advanced, expert |
| verified | Boolean | Skill verification status |
| created_at | Timestamp | Record creation |

**Relationships:**
- Many-to-one with `talent_profiles`

---

### 5. Talent Portfolio Links

Portfolio links (Behance, GitHub, Google Drive, etc.).

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| platform | Enum | behance, github, google_drive, website, other |
| url | String | Portfolio URL |
| is_primary | Boolean | Primary portfolio link |
| created_at | Timestamp | Record creation |

**Relationships:**
- Many-to-one with `talent_profiles`

---

### 6. Resumes

Resume/CV documents for Talent.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| template_type | String | Resume template identifier |
| file_url | String | PDF file URL |
| is_default | Boolean | Default resume for applications |
| created_at | Timestamp | Record creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `talent_profiles`

---

### 7. Employers

Company/Employer information.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| user_id | UUID | Foreign key to users |
| company_name | String | Company name |
| company_type | Enum | startup, sme, corporate, ngo, government |
| industry | String | Industry sector |
| company_size | Enum | micro, small, medium, large, enterprise |
| website | String | Company website |
| location | String | Primary location in Ghana |
| description | Text | Company description |
| logo_url | String | Company logo URL |
| cover_photo_url | String | Cover photo URL |
| company_video_url | String | 90-sec company video URL |
| verification_status | Enum | pending, verified, rejected |
| business_registration_number | String | Business registration number |
| business_registration_document_url | String | Registration document URL |
| owner_ghana_card_number | String | Owner Ghana Card number |
| owner_ghana_card_document_url | String | Owner Ghana Card document URL |
| verification_verified_at | Timestamp | When verification was completed |
| subscription_tier | Enum | free, starter, professional |
| subscription_expires_at | Timestamp | Subscription expiration |
| active_postings_count | Integer | Current active postings count |
| created_at | Timestamp | Company creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- One-to-many with `opportunities`
- One-to-many with `company_testimonials`
- One-to-many with `reviews` (as reviewed company)
- One-to-many with `subscriptions`
- One-to-many with `career_fair_booths`

---

### 8. Company Testimonials

Employee testimonials for company pages.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| employer_id | UUID | Foreign key to employers |
| employee_name | String | Employee name |
| employee_role | String | Employee role/title |
| testimonial_text | Text | Testimonial content |
| employee_photo_url | String | Employee photo URL |
| is_featured | Boolean | Featured on company page |
| created_at | Timestamp | Record creation |

**Relationships:**
- Many-to-one with `employers`

---

### 9. Institutions

Universities, Polytechnics, and educational institutions.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| name | String | Institution name |
| type | Enum | university, polytechnic, college, other |
| location | String | Location in Ghana |
| website | String | Institution website |
| logo_url | String | Institution logo URL |
| is_partner | Boolean | Partner institution status |
| partnership_tier | Enum | basic, premium | Nullable |
| created_at | Timestamp | Record creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- One-to-many with `talent_education`
- One-to-many with `university_admins`
- One-to-many with `career_fairs` (as host)

---

### 10. University Admins

Career services officers and university administrators.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| user_id | UUID | Foreign key to users |
| institution_id | UUID | Foreign key to institutions |
| name | String | Admin name |
| role | String | Role/title |
| email | String | Contact email |
| phone | String | Contact phone |
| created_at | Timestamp | Record creation |

**Relationships:**
- Many-to-one with `users`
- Many-to-one with `institutions`

---

### 11. Opportunities

Job postings (internships, attachments, NSS positions, etc.).

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| employer_id | UUID | Foreign key to employers |
| title | String | Opportunity title |
| opportunity_type | Enum | internship, attachment, national_service, graduate_trainee, entry_level |
| description | Text | Full job description |
| requirements | Text | Requirements and qualifications |
| stipend_amount | Decimal | Stipend amount (0 if unpaid) |
| stipend_currency | String | GHS (default) |
| is_unpaid | Boolean | Unpaid opportunity flag |
| duration_months | Integer | Duration in months |
| location_type | Enum | remote, hybrid, on_site |
| location | String | Physical location if applicable |
| application_deadline | Date | Application deadline |
| max_applications | Integer | Maximum number of applications (nullable) |
| is_featured | Boolean | Featured/boosted posting |
| featured_until | Timestamp | Featured expiration |
| status | Enum | draft, active, closed, expired |
| nss_auto_expire_date | Date | Auto-expire date for NSS positions (12-18 months) |
| unpaid_justification | Text | Justification for unpaid >3 months |
| university_approval_flag | Boolean | University approval for unpaid >3 months |
| views_count | Integer | View count |
| applications_count | Integer | Application count |
| created_at | Timestamp | Posting creation |
| updated_at | Timestamp | Last update |
| published_at | Timestamp | When published |

**Relationships:**
- Many-to-one with `employers`
- One-to-many with `applications`
- One-to-many with `saved_opportunities`
- Many-to-many with `institutions` (exclusive postings)
- Many-to-many with `skills` (required skills)

---

### 12. Applications

Talent applications to opportunities.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| opportunity_id | UUID | Foreign key to opportunities |
| resume_id | UUID | Foreign key to resumes |
| cover_letter_text | Text | Cover letter text |
| cover_letter_file_url | String | Uploaded cover letter file URL |
| application_questions | JSON | Answers to custom questions |
| status | Enum | applied, under_review, shortlisted, interview, offer, accepted, rejected, withdrawn |
| is_shortlisted | Boolean | Shortlisted flag |
| rejection_reason | Text | Rejection reason (if rejected) |
| applied_at | Timestamp | Application submission |
| status_updated_at | Timestamp | Last status change |
| created_at | Timestamp | Record creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `talent_profiles`
- Many-to-one with `opportunities`
- Many-to-one with `resumes`
- One-to-many with `interviews`
- One-to-one with `offers` (if status = offer or accepted)

---

### 13. Interviews

Interview scheduling and management.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| application_id | UUID | Foreign key to applications |
| interview_type | Enum | phone, video, in_person |
| scheduled_at | Timestamp | Scheduled date/time |
| timezone | String | Timezone |
| location | String | Location (for in-person) |
| video_link | String | Video call link (for video) |
| status | Enum | scheduled, confirmed, completed, cancelled, rescheduled |
| reschedule_count_talent | Integer | Talent reschedule count (max 2) |
| reschedule_count_employer | Integer | Employer reschedule count (max 2) |
| calendar_event_id | String | Google/Outlook calendar event ID |
| notes | Text | Interview notes |
| created_at | Timestamp | Record creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `applications`

---

### 14. Offers

Job offers and offer letters.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| application_id | UUID | Foreign key to applications |
| offer_letter_url | String | Offer letter document URL |
| offer_letter_signed_url | String | Signed offer letter URL |
| e_signature_status | Enum | pending, signed, declined |
| signed_at | Timestamp | When offer was signed |
| expires_at | Timestamp | Offer expiration date |
| created_at | Timestamp | Offer creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- One-to-one with `applications`

---

### 15. Reviews

Reviews and ratings of internship experiences.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| employer_id | UUID | Foreign key to employers |
| opportunity_id | UUID | Foreign key to opportunities |
| application_id | UUID | Foreign key to applications |
| rating | Integer | Rating 1-5 |
| review_text | Text | Review content |
| is_anonymous | Boolean | Anonymous review flag |
| is_verified | Boolean | Verified placement review |
| can_review_at | Timestamp | Earliest review date (placement end or 3 months after rejection) |
| created_at | Timestamp | Review creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `talent_profiles`
- Many-to-one with `employers`
- Many-to-one with `opportunities`
- Many-to-one with `applications`

---

### 16. Saved Opportunities

Talent saved/bookmarked opportunities.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| opportunity_id | UUID | Foreign key to opportunities |
| saved_at | Timestamp | When saved |

**Relationships:**
- Many-to-one with `talent_profiles`
- Many-to-one with `opportunities`

---

### 17. Saved Searches

Talent saved search criteria and alerts.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| search_name | String | Custom search name |
| search_criteria | JSON | Search filters and criteria |
| alert_frequency | Enum | daily, weekly, none |
| last_alert_sent_at | Timestamp | Last alert sent |
| is_active | Boolean | Active search flag |
| created_at | Timestamp | Search creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `talent_profiles`

---

### 18. Messages

In-app messaging between Talent and Employers.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| conversation_id | UUID | Foreign key to conversations |
| sender_id | UUID | Foreign key to users (sender) |
| receiver_id | UUID | Foreign key to users (receiver) |
| message_text | Text | Message content |
| attachment_url | String | Attachment file URL |
| is_read | Boolean | Read status |
| read_at | Timestamp | When read |
| created_at | Timestamp | Message creation |

**Relationships:**
- Many-to-one with `conversations`
- Many-to-one with `users` (sender)
- Many-to-one with `users` (receiver)

---

### 19. Conversations

Message conversation threads.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| talent_id | UUID | Foreign key to talent_profiles |
| employer_id | UUID | Foreign key to employers |
| application_id | UUID | Foreign key to applications (nullable) |
| last_message_at | Timestamp | Last message timestamp |
| created_at | Timestamp | Conversation creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `talent_profiles`
- Many-to-one with `employers`
- Many-to-one with `applications`
- One-to-many with `messages`

---

### 20. Career Fairs

Virtual career fair events.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| name | String | Career fair name |
| description | Text | Event description |
| host_institution_id | UUID | Foreign key to institutions (nullable) |
| start_date | Date | Event start date |
| end_date | Date | Event end date |
| is_active | Boolean | Active event flag |
| registration_deadline | Date | Registration deadline |
| created_at | Timestamp | Event creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `institutions`
- One-to-many with `career_fair_booths`
- Many-to-many with `talent_profiles` (attendees)

---

### 21. Career Fair Booths

Employer booths at career fairs.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| career_fair_id | UUID | Foreign key to career_fairs |
| employer_id | UUID | Foreign key to employers |
| booth_name | String | Booth name/title |
| is_premium | Boolean | Premium/sponsored booth |
| live_chat_enabled | Boolean | Live chat availability |
| created_at | Timestamp | Booth creation |

**Relationships:**
- Many-to-one with `career_fairs`
- Many-to-one with `employers`

---

### 22. Subscriptions

Employer subscription plans and payments.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| employer_id | UUID | Foreign key to employers |
| plan_type | Enum | free, starter, professional |
| billing_cycle | Enum | monthly, annual |
| amount | Decimal | Subscription amount |
| currency | String | GHS (default) |
| status | Enum | active, cancelled, expired, trial |
| starts_at | Timestamp | Subscription start |
| expires_at | Timestamp | Subscription expiration |
| auto_renew | Boolean | Auto-renewal flag |
| created_at | Timestamp | Subscription creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `employers`
- One-to-many with `payments`

---

### 23. Payments

Payment transactions (subscriptions, featured postings, etc.).

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| employer_id | UUID | Foreign key to employers |
| subscription_id | UUID | Foreign key to subscriptions (nullable) |
| payment_type | Enum | subscription, featured_posting, career_fair_booth |
| amount | Decimal | Payment amount |
| currency | String | GHS (default) |
| payment_method | Enum | mtn_momo, vodafone_cash, telecel_cash, airteltigo_money, card, ussd |
| payment_provider | String | Flutterwave, Paystack, etc. |
| transaction_id | String | External transaction ID |
| status | Enum | pending, completed, failed, refunded |
| payment_data | JSON | Additional payment metadata |
| created_at | Timestamp | Payment creation |
| updated_at | Timestamp | Last update |

**Relationships:**
- Many-to-one with `employers`
- Many-to-one with `subscriptions`

---

### 24. Notifications

System notifications (SMS, Email, Push).

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| user_id | UUID | Foreign key to users |
| notification_type | Enum | sms, email, push |
| channel | Enum | application, interview, message, alert, system |
| title | String | Notification title |
| message | Text | Notification content |
| data | JSON | Additional notification data |
| status | Enum | pending, sent, failed, read |
| sent_at | Timestamp | When sent |
| read_at | Timestamp | When read |
| created_at | Timestamp | Notification creation |

**Relationships:**
- Many-to-one with `users`

---

### 25. Skills (Master Data)

Master list of skills for matching and filtering.

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| name | String | Skill name |
| category | String | Skill category |
| is_active | Boolean | Active skill flag |
| created_at | Timestamp | Record creation |

**Relationships:**
- Many-to-many with `talent_skills`
- Many-to-many with `opportunities` (required skills)

---

## Key Relationships Summary

### Talent Flow
- `users` → `talent_profiles` → `talent_education`, `talent_skills`, `talent_portfolio_links`
- `talent_profiles` → `applications` → `interviews` → `offers`
- `talent_profiles` → `saved_opportunities`, `saved_searches`
- `talent_profiles` → `reviews` (as reviewer)

### Employer Flow
- `users` → `employers` → `opportunities` → `applications`
- `employers` → `subscriptions` → `payments`
- `employers` → `reviews` (as reviewed company)
- `employers` → `career_fair_booths`

### University Flow
- `users` → `university_admins` → `institutions`
- `institutions` → `talent_education`
- `institutions` → `career_fairs` (as host)

### Communication Flow
- `talent_profiles` ↔ `employers` via `conversations` → `messages`
- `applications` → `interviews` (scheduling)
- `notifications` → `users` (alerts)

---

## Indexes & Performance Considerations

### Critical Indexes
- `users.email`, `users.phone_number` (unique indexes)
- `talent_profiles.user_id` (unique index)
- `employers.user_id` (unique index)
- `opportunities.employer_id`, `opportunities.status`, `opportunities.opportunity_type`
- `applications.talent_id`, `applications.opportunity_id`, `applications.status`
- `opportunities.published_at`, `opportunities.is_featured` (for feed queries)
- `notifications.user_id`, `notifications.status` (for notification queries)

### Composite Indexes
- `opportunities(status, is_featured, published_at)` - for opportunity feed
- `applications(talent_id, status)` - for application tracker
- `applications(opportunity_id, status)` - for employer dashboard

---

## Data Integrity Rules

1. **Verification Requirements:**
   - Talent must have verification document before applying to paid opportunities
   - Employers must be verified before posting paid opportunities

2. **Application Limits:**
   - Track `max_applications` per opportunity
   - Enforce application deadline

3. **Interview Rescheduling:**
   - Max 2 reschedules per party (tracked in `interviews` table)

4. **Review Eligibility:**
   - Reviews only allowed after `can_review_at` timestamp

5. **Subscription Limits:**
   - Free tier: max 3 active postings
   - Starter tier: max 20 active postings
   - Professional: unlimited

6. **NSS Position Expiry:**
   - Auto-expire NSS positions after 12-18 months

---

## Notes

- All timestamps use UTC timezone
- All monetary amounts stored in GHS (Ghana Cedis)
- UUIDs used for all primary keys for security and scalability
- Soft deletes may be implemented for critical entities (users, opportunities, applications)
- JSON fields used for flexible data storage (application_questions, search_criteria, payment_data)
- Consider partitioning large tables (notifications, messages) by date for performance

