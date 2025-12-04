# User Process Flows - Looksharp Platform

**Version:** 1.0  
**Date:** December 2025  
**Based on:** Business Requirements Document v1.0 & Database Model v1.0

---

## Overview

This document outlines the complete user process flows for all user types on the Looksharp platform. Each flow includes decision points, business rules, and system interactions to guide implementation and ensure a seamless user experience.

---

## Table of Contents

1. [Talent (Student/Graduate) Flows](#1-talent-studentgraduate-flows)
2. [Employer Flows](#2-employer-flows)
3. [University Admin Flows](#3-university-admin-flows)
4. [Cross-Cutting Flows](#4-cross-cutting-flows)

---

## 1. Talent (Student/Graduate) Flows

### 1.1 Registration & Onboarding Flow

**Goal:** Get Talent registered and verified quickly with minimal friction

**Steps:**
1. **Landing Page** → User clicks "Sign Up as Student/Talent"
2. **Registration Method Selection**
   - Options: Google, Apple, Phone Number, Email
   - **Business Rule:** Must support all Ghanaian phone networks (MTN, Vodafone, Telecel, AirtelTigo)
3. **Account Creation**
   - If Phone: OTP verification sent via SMS
   - If Email: Email verification link sent
   - If Google/Apple: OAuth redirect and callback
4. **User Type Confirmation**
   - System confirms: `user_type = 'talent'`
   - Creates base `users` record
5. **Profile Initialization**
   - Creates `talent_profiles` record linked to `user_id`
   - Sets `profile_completeness_score = 0`
   - Sets `verification_status = 'pending'`
6. **Welcome Screen**
   - Shows profile completion progress
   - Highlights benefits of completing profile
   - Prompts to start profile building

**Exit Points:**
- User can skip profile completion (but will have limited access)
- User can complete profile later from dashboard

**Notifications:**
- Welcome email/SMS with next steps
- Profile completion reminders (if incomplete after 24 hours)

---

### 1.2 Profile Building Flow

**Goal:** Help Talent create a complete, compelling profile for better matching

**Steps:**
1. **Profile Builder Entry**
   - From welcome screen or dashboard "Complete Profile" CTA
   - Shows progress indicator (0-100%)
2. **Basic Information** (Required for verification)
   - First Name, Last Name
   - Date of Birth
   - Gender
   - Location (City/Region in Ghana)
   - Profile Photo (optional but recommended)
3. **Education Information** (Required)
   - Add Education Record:
     - Select Institution (from `institutions` table or add new)
     - Degree Type (Certificate, Diploma, Bachelor's, Master's, PhD)
     - Field of Study
     - Start Date, End Date (or "Currently Enrolled")
     - GPA (optional)
     - Mark as Primary Education
   - Can add multiple education records
4. **Skills Section** (Recommended)
   - Add skills from master `skills` table or custom entry
   - Set proficiency level (Beginner, Intermediate, Advanced, Expert)
   - Can add multiple skills
5. **Portfolio Links** (Optional but valuable)
   - Add portfolio links:
     - Platform: Behance, GitHub, Google Drive, Website, Other
     - URL
     - Mark as Primary
6. **Video Introduction** (Optional)
   - Upload 30-second max video introduction
   - Stored in `talent_profiles.video_introduction`
7. **National Service Status** (Ghana-specific)
   - Select NSS Status:
     - Awaiting
     - Posted (requires posting location and number)
     - Completed
     - Not Applicable
8. **Resume Builder** (Optional)
   - Use Ghana-standard templates
   - Build resume with profile data
   - Export to PDF
   - Save to `resumes` table
   - Mark as default if first resume
9. **Verification Upload** (Required for paid opportunities)
   - Upload verification document:
     - Ghana Card (preferred)
     - Student ID
     - Passport
   - Document stored in `talent_profiles.verification_document_url`
   - Sets `verification_status = 'pending'`
   - Triggers admin review process
10. **Profile Completion**
    - System calculates `profile_completeness_score`
    - Updates score based on completed sections
    - Shows completion percentage

**Business Rules:**
- Verification required before applying to paid opportunities (BR-02)
- Profile completeness affects AI matching quality
- Can save and continue later (draft state)

**Notifications:**
- Profile completion milestone emails (25%, 50%, 75%, 100%)
- Verification status updates (pending → verified/rejected)

---

### 1.3 Opportunity Discovery & Search Flow

**Goal:** Help Talent find relevant opportunities through search and AI recommendations

**Steps:**
1. **Opportunity Feed Entry**
   - From dashboard or "Browse Opportunities" menu
   - Shows personalized feed if profile complete
   - Shows general feed if profile incomplete
2. **AI-Powered Feed** (If profile complete)
   - System generates recommendations based on:
     - Education (field of study, institution)
     - Skills match
     - Location preferences
     - NSS status (if applicable)
     - Profile completeness score
   - Featured opportunities shown first (if `is_featured = true`)
   - Then AI-recommended opportunities
   - Then recent postings
3. **Search & Filter**
   - Search by keywords (title, company, description)
   - Filters:
     - Opportunity Type (Internship, Attachment, NSS, Graduate Trainee, Entry-level)
     - Location (City/Region, Remote, Hybrid, On-site)
     - Stipend Range (including "Unpaid" filter)
     - Duration (months)
     - Application Deadline
     - Industry
     - Required Skills
     - Company Size
   - Can save search criteria
4. **Opportunity Details View**
   - Full job description
   - Company information and rating
   - Stipend amount (clearly displayed, even if GHS 0)
   - Duration and location
   - Application deadline
   - Required skills and qualifications
   - Company reviews (if any)
   - "Apply Now" or "Save for Later" buttons
5. **Save Search**
   - User can save search criteria
   - Set alert frequency (Daily, Weekly, None)
   - Saved to `saved_searches` table
   - System sends alerts when new opportunities match

**Business Rules:**
- Stipend amount must be clearly shown (BR-03)
- Unpaid internships >3 months require justification (BR-04)
- NSS positions auto-expire after 12-18 months (NSS-02)

**Notifications:**
- Daily/Weekly job alerts (if saved searches configured)
- SMS summary if user hasn't opened app in 7 days

---

### 1.4 Application Submission Flow

**Goal:** Enable Talent to apply to opportunities quickly and easily

**Steps:**
1. **Application Initiation**
   - From opportunity details page, click "Apply Now"
   - System checks:
     - Is opportunity still active? (`status = 'active'`)
     - Has deadline passed? (`application_deadline >= today`)
     - Has max applications been reached? (`applications_count < max_applications`)
     - Is user verified? (if paid opportunity, `verification_status = 'verified'`)
2. **Application Form**
   - Pre-fills with profile data
   - Resume Selection:
     - Shows saved resumes from `resumes` table
     - Can select default resume or choose another
     - Option to upload new resume
   - Cover Letter:
     - Option 1: Upload cover letter file
     - Option 2: Write cover letter text
     - Option 3: Skip (if employer allows)
   - Custom Questions (if employer configured):
     - Up to 3 custom questions
     - Answers stored in `application_questions` JSON field
3. **Application Review**
   - Shows summary of application
   - Highlights key information
   - Confirms opportunity details
4. **Application Submission**
   - Creates `applications` record:
     - `status = 'applied'`
     - `applied_at = now()`
     - Links to `talent_id`, `opportunity_id`, `resume_id`
   - Increments `opportunities.applications_count`
   - Updates `talent_profiles` (for analytics)
5. **Confirmation**
   - Success message
   - Shows application ID
   - Option to view in Application Tracker
   - Option to apply to similar opportunities

**Business Rules:**
- Easy apply: 1-click or max 3 questions (TAL-06)
- Verification required for paid opportunities (BR-02)
- Cannot apply twice to same opportunity (check existing applications)

**Notifications:**
- Confirmation email/SMS
- Application received notification to employer
- Status update notifications (when employer changes status)

---

### 1.5 Application Tracking Flow

**Goal:** Keep Talent informed about application status and next steps

**Steps:**
1. **Application Tracker Dashboard**
   - From main menu or dashboard
   - Shows all applications with status:
     - Applied (blue)
     - Under Review (yellow)
     - Shortlisted (green)
     - Interview Scheduled (purple)
     - Offer (gold)
     - Accepted (green)
     - Rejected (red)
     - Withdrawn (gray)
2. **Application Details View**
   - Application information
   - Current status and timeline
   - Company information
   - Opportunity details
   - Resume and cover letter used
   - Interview information (if scheduled)
   - Offer details (if received)
3. **Status Updates**
   - Real-time status changes
   - Status history timeline
   - Notes from employer (if provided)
4. **Interview Management** (If status = 'interview')
   - View interview details:
     - Date and time
     - Type (Phone, Video, In-person)
     - Location or video link
     - Timezone
   - Calendar sync option (Google/Outlook)
   - Reschedule request (max 2 times per BR-05)
   - Confirm attendance
5. **Offer Management** (If status = 'offer')
   - View offer letter
   - Download offer document
   - E-signature option (HelloSign/DocuSign integration)
   - Accept or Decline offer
   - View offer expiration date
6. **Bulk Actions**
   - Filter by status
   - Sort by date, company, status
   - Export application history

**Business Rules:**
- Max 2 interview rescheduling requests per party (BR-05)
- Offer expiration enforced (`offers.expires_at`)
- Status changes trigger notifications

**Notifications:**
- Status change notifications (SMS + Email + Push)
- Interview reminders (24 hours before, 1 hour before)
- Offer expiration reminders

---

### 1.6 Messaging Flow

**Goal:** Enable direct communication between Talent and Employers

**Steps:**
1. **Message Initiation**
   - From application details page: "Message Employer"
   - From company page: "Contact Company"
   - Creates `conversations` record if first message
2. **Conversation View**
   - Shows message thread
   - Displays sender/receiver information
   - Shows application context (if linked)
   - Message timestamps
   - Read receipts
3. **Sending Messages**
   - Type message text
   - Attach files (optional)
   - Send message
   - Creates `messages` record
   - Updates `conversations.last_message_at`
4. **Message Notifications**
   - Real-time push notification (if app open)
   - Email notification (if not active)
   - SMS fallback (if configured)
5. **Conversation Management**
   - View all conversations
   - Filter by unread
   - Search messages
   - Archive conversations

**Business Rules:**
- Messages linked to application context
- WhatsApp Business API fallback for users without app

**Notifications:**
- New message notifications (immediate)
- Unread message reminders (if unread after 24 hours)

---

### 1.7 Career Fair Attendance Flow

**Goal:** Enable Talent to attend virtual career fairs and interact with employers

**Steps:**
1. **Career Fair Discovery**
   - Browse upcoming career fairs
   - Filter by date, host institution, industry
   - View fair details and participating employers
2. **Registration**
   - Register for career fair
   - Creates attendance record
   - Adds to `career_fairs` ↔ `talent_profiles` relationship
3. **Career Fair Entry** (On event day)
   - Access virtual fair platform
   - Browse employer booths
   - View booth information and job postings
4. **Booth Interaction**
   - View company information
   - Browse exclusive job postings
   - Drop CV (creates application)
   - Live chat with employer (if enabled)
   - Schedule interview (if available)
5. **Post-Fair Follow-up**
   - View applications submitted at fair
   - Continue conversations started at fair
   - Access fair resources and recordings

**Business Rules:**
- Exclusive postings visible only to fair attendees
- Live chat availability depends on employer settings

**Notifications:**
- Fair registration confirmation
- Fair reminder (24 hours before, 1 hour before)
- New booth messages/alerts during fair

---

### 1.8 Review & Rating Flow

**Goal:** Enable Talent to review internship experiences (post-placement)

**Steps:**
1. **Review Eligibility Check**
   - System checks if Talent can review:
     - Placement has ended, OR
     - 3 months after application rejection (BR-06)
   - Shows eligible reviews in dashboard
2. **Review Creation**
   - Select company/opportunity to review
   - Rate 1-5 stars
   - Write review text (optional)
   - Choose anonymous or public
   - Submit review
3. **Review Submission**
   - Creates `reviews` record
   - Sets `is_verified = true` if placement completed
   - Updates company rating (aggregate)
   - Review visible on company page (if public)

**Business Rules:**
- Reviews only allowed after `can_review_at` timestamp (BR-06)
- Anonymous reviews still count toward ratings
- Verified reviews (from completed placements) weighted higher

**Notifications:**
- Review eligibility notification
- Review submission confirmation

---

## 2. Employer Flows

### 2.1 Registration & Company Setup Flow

**Goal:** Get employers registered and verified to start posting opportunities

**Steps:**
1. **Registration Initiation**
   - From landing page: "Sign Up as Employer"
   - Or from "For Employers" page
2. **Registration Method**
   - Options: Google, Apple, Email, Phone
   - Company email preferred for verification
3. **Account Creation**
   - Creates `users` record with `user_type = 'employer'`
   - Email/Phone verification required
4. **Company Information**
   - Company Name (required)
   - Company Type (Startup, SME, Corporate, NGO, Government)
   - Industry
   - Company Size (Micro, Small, Medium, Large, Enterprise)
   - Website
   - Primary Location in Ghana
   - Company Description
   - Logo Upload
   - Cover Photo Upload
   - Company Video (90-sec max, optional)
5. **Company Profile Creation**
   - Creates `employers` record
   - Links to `user_id`
   - Sets `subscription_tier = 'free'`
   - Sets `verification_status = 'pending'`
6. **Verification Documents** (Required for paid postings)
   - Upload Business Registration Document
   - Enter Business Registration Number
   - Upload Owner Ghana Card
   - Enter Owner Ghana Card Number
   - Documents stored and submitted for review
7. **Verification Review**
   - Admin reviews documents
   - Sets `verification_status = 'verified'` or `'rejected'`
   - If rejected, provides reason and allows resubmission
8. **Subscription Selection** (Optional initially)
   - Free Tier: 3 active postings
   - Starter Tier: 20 postings + branding (paid)
   - Professional Tier: Unlimited + ATS integration (paid)
   - Can upgrade later
9. **Company Page Preview**
   - Shows how company page will appear
   - Option to add testimonials
   - Option to add employee photos
10. **Onboarding Complete**
    - Welcome message
    - Quick start guide
    - Link to post first opportunity

**Business Rules:**
- Verification required before posting paid opportunities (BR-02)
- Free tier limited to 3 active postings (EMP-01)
- BVN-style verification (Ghana Card + Business Registration) (COM-04)

**Notifications:**
- Welcome email with next steps
- Verification status updates
- Subscription expiration reminders

---

### 2.2 Opportunity Posting Flow

**Goal:** Enable employers to create compelling opportunity postings

**Steps:**
1. **Post Opportunity Entry**
   - From dashboard: "Post New Opportunity"
   - Or from opportunities list: "Create Posting"
2. **Opportunity Type Selection**
   - Internship
   - Attachment
   - National Service
   - Graduate Trainee
   - Entry-level Full-time
3. **Basic Information**
   - Title (required)
   - Description (rich text editor)
   - Requirements and Qualifications
   - Location Type (Remote, Hybrid, On-site)
   - Physical Location (if applicable)
4. **Compensation & Duration**
   - Stipend Amount (GHS, required, can be 0)
   - Is Unpaid checkbox (if stipend = 0)
   - Duration in Months
   - **Business Rule Check:**
     - If unpaid AND duration > 3 months:
       - Require justification text
       - Require university approval flag (BR-04)
5. **Application Settings**
   - Application Deadline
   - Maximum Applications (optional, null = unlimited)
   - Custom Application Questions (max 3)
   - Required Documents (Resume, Cover Letter, Portfolio, etc.)
6. **Targeting & Visibility**
   - Required Skills (from master `skills` table)
   - Preferred Education (Field of Study, Degree Type)
   - Exclusive to Specific Institutions (if university partner)
   - Location Preferences
7. **NSS-Specific Settings** (If opportunity_type = 'national_service')
   - Auto-expire Date (12-18 months from posting)
   - Sets `nss_auto_expire_date`
8. **Preview & Review**
   - Shows opportunity as it will appear to Talent
   - Highlights key information
   - Shows compliance checks (stipend visibility, etc.)
9. **Publishing Options**
   - Save as Draft (for later editing)
   - Publish Now (if within posting limits)
   - Feature/Boost Posting (paid option, if available)
10. **Publishing**
    - Creates `opportunities` record
    - Sets `status = 'active'` or `'draft'`
    - Sets `published_at = now()` if published
    - Increments `employers.active_postings_count`
    - Checks subscription limits
11. **Post-Publish**
    - Success confirmation
    - Shows opportunity URL
    - Option to share on social media
    - Option to feature/boost posting

**Business Rules:**
- Free tier: max 3 active postings (EMP-01)
- Starter tier: max 20 active postings
- Professional tier: unlimited
- Stipend must be clearly shown (BR-03)
- Unpaid >3 months requires justification (BR-04)
- NSS positions auto-expire (NSS-02)

**Notifications:**
- Posting published confirmation
- Application received notifications (real-time)
- Posting expiration reminders
- Limit reached warnings (if approaching posting limit)

---

### 2.3 Applicant Management Flow

**Goal:** Help employers efficiently review, filter, and manage applicants

**Steps:**
1. **Applicant Dashboard Entry**
   - From dashboard: "View Applicants"
   - Or from opportunity: "View Applications"
2. **Application List View**
   - Shows all applications for selected opportunity (or all opportunities)
   - Default sort: Most recent first
   - Shows key information:
     - Talent name and photo
     - Application date
     - Status
     - Match score (if AI matching enabled)
     - Education summary
     - Skills match
3. **Filtering & Search**
   - Filter by:
     - Status (Applied, Under Review, Shortlisted, Interview, Offer, Accepted, Rejected)
     - Education (Institution, Field of Study, Degree Type)
     - Skills
     - Location
     - NSS Status
     - Application Date Range
   - Search by name, email, or keywords
4. **Application Review**
   - Click application to view details:
     - Full Talent profile
     - Resume (download/view)
     - Cover letter
     - Answers to custom questions
     - Portfolio links
     - Video introduction (if available)
     - Education history
     - Skills and proficiency
5. **Bulk Actions**
   - Select multiple applications
   - Bulk actions:
     - Shortlist
     - Reject (with reason)
     - Send message
     - Export to CSV
6. **Individual Actions**
   - Shortlist Application
     - Sets `is_shortlisted = true`
     - Sets `status = 'shortlisted'`
     - Sends notification to Talent
   - Reject Application
     - Sets `status = 'rejected'`
     - Option to provide rejection reason
     - Sends notification to Talent
   - Move to Interview
     - Sets `status = 'interview'`
     - Triggers interview scheduling flow
   - Send Message
     - Opens messaging interface
     - Creates conversation if first message
7. **Diversity & Inclusion Reporting**
   - View analytics dashboard:
     - Gender distribution
     - University distribution
     - Region distribution
     - Skills distribution
   - Export reports for internal use

**Business Rules:**
- Status changes trigger notifications
- Rejection reasons stored for analytics
- Bulk actions logged for audit

**Notifications:**
- New application received (real-time)
- Application status change confirmations
- Weekly application summary (if configured)

---

### 2.4 Interview Scheduling Flow

**Goal:** Streamline interview scheduling with calendar integration

**Steps:**
1. **Schedule Interview Entry**
   - From application details: "Schedule Interview"
   - Or from applicant dashboard: "Schedule Interview" action
2. **Interview Details**
   - Interview Type:
     - Phone
     - Video (provides video link field)
     - In-person (provides location field)
   - Date and Time Selection
   - Timezone Selection (auto-detected, can override)
   - Duration (default: 1 hour)
   - Interviewer Name/Email (optional)
   - Notes/Instructions for candidate
3. **Calendar Integration**
   - Option to sync with:
     - Google Calendar
     - Outlook Calendar
   - Creates calendar event
   - Stores `calendar_event_id` in `interviews` table
4. **Interview Creation**
   - Creates `interviews` record
   - Links to `application_id`
   - Sets `status = 'scheduled'`
   - Sets `scheduled_at` with timezone
5. **Interview Invitation**
   - Sends invitation to Talent:
     - Email with calendar invite
     - SMS reminder
     - In-app notification
   - Includes:
     - Interview details
     - Calendar link (if video)
     - Location (if in-person)
     - Interviewer information
     - Notes/instructions
6. **Interview Confirmation**
   - Talent confirms attendance
   - Sets `status = 'confirmed'`
   - Sends confirmation to employer
7. **Reschedule Management**
   - Either party can request reschedule
   - System tracks:
     - `reschedule_count_talent` (max 2)
     - `reschedule_count_employer` (max 2)
   - If limit reached, shows warning
   - Updates `scheduled_at` and sends notifications
8. **Interview Completion**
   - After interview, employer can:
     - Mark as completed
     - Add interview notes
     - Move to next stage (Offer, Reject, Additional Interview)
   - Sets `status = 'completed'`

**Business Rules:**
- Max 2 rescheduling requests per party (BR-05)
- Auto-timezone handling (EMP-05)
- Calendar sync required for video interviews

**Notifications:**
- Interview scheduled (immediate)
- Interview reminder (24 hours before)
- Interview reminder (1 hour before)
- Reschedule requests
- Interview confirmations

---

### 2.5 Offer Management Flow

**Goal:** Create, send, and track offer letters with e-signature

**Steps:**
1. **Create Offer Entry**
   - From application details: "Make Offer"
   - Or from interview completion: "Extend Offer"
2. **Offer Details**
   - Pre-fills with opportunity information
   - Stipend/Salary amount
   - Start date
   - Duration
   - Location
   - Other terms and conditions
3. **Offer Letter Generation**
   - Uses template
   - Generates offer letter document
   - Stores in `offers.offer_letter_url`
   - Sets `e_signature_status = 'pending'`
4. **Offer Expiration**
   - Set expiration date (default: 7 days)
   - Stores in `offers.expires_at`
5. **Offer Submission**
   - Creates `offers` record
   - Links to `application_id`
   - Sets `applications.status = 'offer'`
   - Sends offer to Talent
6. **Offer Delivery**
   - Email with offer letter PDF
   - In-app notification
   - SMS alert
   - Includes e-signature link
7. **E-Signature Process**
   - Talent clicks e-signature link
   - Redirects to HelloSign/DocuSign
   - Talent signs document
   - Signed document stored in `offers.offer_letter_signed_url`
   - Sets `e_signature_status = 'signed'`
   - Sets `signed_at = now()`
8. **Offer Acceptance**
   - When signed, sets `applications.status = 'accepted'`
   - Sends confirmation to both parties
   - Updates opportunity (marks position as filled)
9. **Offer Decline**
   - Talent can decline offer
   - Sets `e_signature_status = 'declined'`
   - Sets `applications.status = 'rejected'`
   - Sends notification to employer
10. **Offer Expiration**
    - If not signed by expiration:
      - Sets `e_signature_status = 'expired'`
      - Sends expiration notification
      - Option to extend offer

**Business Rules:**
- Offer expiration enforced
- E-signature required for acceptance
- Signed offers are legally binding

**Notifications:**
- Offer sent (immediate)
- Offer expiration reminder (24 hours before)
- Offer signed confirmation
- Offer declined notification

---

### 2.6 Company Page Management Flow

**Goal:** Help employers build compelling company pages to attract Talent

**Steps:**
1. **Company Page Entry**
   - From dashboard: "Edit Company Page"
   - Or from company page: "Edit" (if owner)
2. **Company Information**
   - Edit basic information (name, description, industry, etc.)
   - Upload/update logo
   - Upload/update cover photo
   - Upload/update company video (90-sec max)
3. **Testimonials Management**
   - Add employee testimonials:
     - Employee name
     - Employee role/title
     - Testimonial text
     - Employee photo (optional)
     - Mark as featured
   - Edit/delete existing testimonials
   - Reorder testimonials
4. **Company Page Preview**
   - Preview how page appears to Talent
   - See company rating and reviews
   - Test mobile view
5. **Publishing**
   - Save changes
   - Updates `employers` record
   - Updates `company_testimonials` records
   - Company page live immediately

**Business Rules:**
- Company video max 90 seconds (EMP-02)
- Featured testimonials shown first
- Reviews visible on company page (if public)

**Notifications:**
- Company page updated confirmation
- New review notification (when Talent reviews company)

---

### 2.7 Subscription & Payment Flow

**Goal:** Enable employers to upgrade subscriptions and make payments

**Steps:**
1. **Subscription Management Entry**
   - From dashboard: "Manage Subscription"
   - Or from posting limit warning: "Upgrade Now"
2. **Current Subscription Display**
   - Shows current tier (Free, Starter, Professional)
   - Shows active postings count vs. limit
   - Shows subscription expiration (if paid)
   - Shows features included
3. **Upgrade Options**
   - Compare tiers:
     - Free: 3 postings, basic features
     - Starter: 20 postings, branding, GHS X/month
     - Professional: Unlimited, ATS integration, GHS Y/month
   - Annual vs. Monthly billing options
4. **Payment Method Selection**
   - Mobile Money:
     - MTN MoMo
     - Vodafone Cash
     - Telecel Cash
     - AirtelTigo Money
   - Card Payment (Visa/Mastercard via Flutterwave/Paystack)
   - USSD (for feature phones)
5. **Payment Processing**
   - Enter payment details
   - Process payment via selected provider
   - Creates `payments` record
   - Creates/updates `subscriptions` record
6. **Subscription Activation**
   - Updates `employers.subscription_tier`
   - Sets `subscription_expires_at`
   - Sets `subscription.status = 'active'`
   - Enables tier features immediately
7. **Payment Confirmation**
   - Payment receipt sent
   - Subscription confirmation
   - Updated limits displayed

**Business Rules:**
- Free tier: 3 active postings (EMP-01)
- Starter tier: 20 active postings
- Professional: unlimited
- Auto-renewal option available
- Payment required before tier activation

**Notifications:**
- Payment receipt
- Subscription activated confirmation
- Subscription expiration reminders (7 days, 1 day before)
- Payment failed notifications

---

## 3. University Admin Flows

### 3.1 University Registration & Setup Flow

**Goal:** Enable universities to partner with Looksharp and manage student placements

**Steps:**
1. **Registration Initiation**
   - From "For Universities" page
   - Or direct partnership inquiry
2. **Institution Registration**
   - University/Institution name
   - Type (University, Polytechnic, College, Other)
   - Location
   - Website
   - Logo upload
3. **Admin Account Creation**
   - Admin name and contact information
   - Email and phone
   - Role/Title (Career Services Officer, etc.)
   - Creates `users` record with `user_type = 'university_admin'`
   - Creates `institutions` record
   - Creates `university_admins` record
4. **Partnership Tier Selection**
   - Basic Partnership (free)
   - Premium Partnership (paid, with additional features)
5. **Verification**
   - Admin reviews and verifies institution
   - Sets `institutions.is_partner = true`
   - Sets `institutions.partnership_tier`
6. **Onboarding**
   - Welcome message
   - Dashboard access
   - Student upload instructions
   - API documentation (if applicable)

**Business Rules:**
- Partnership required for exclusive features
- Institution must be verified before student upload

**Notifications:**
- Registration confirmation
- Verification status updates

---

### 3.2 Student Management Flow

**Goal:** Help universities track student registrations and placements

**Steps:**
1. **Dashboard Entry**
   - University admin dashboard
   - Shows key metrics:
     - Total registered students
     - Active students
     - Placement rate
     - Applications submitted
     - Placements secured
2. **Student Registration View**
   - List of students from institution
   - Filter by:
     - Year of study
     - Field of study
     - Registration status
     - Placement status
   - Search by name, email, student ID
3. **Bulk Student Upload**
   - Upload Excel file with student data:
     - Name
     - Email
     - Phone
     - Student ID
     - Year of study
     - Field of study
   - System validates and imports
   - Creates `users` and `talent_profiles` records
   - Sends invitation emails to students
4. **Individual Student Management**
   - View student profile
   - View applications
   - View placement status
   - Send messages (if enabled)
5. **Placement Tracking**
   - View placement statistics
   - Filter by:
     - Company
     - Opportunity type
     - Date range
   - Export placement reports

**Business Rules:**
- Bulk upload via Excel or API (UNI-02)
- Students must accept invitation to activate account
- Placement data aggregated for reporting

**Notifications:**
- Student registration milestones
- Placement achievement notifications
- Weekly placement summary reports

---

### 3.3 Career Fair Hosting Flow

**Goal:** Enable universities to host branded career fairs

**Steps:**
1. **Create Career Fair**
   - From dashboard: "Host Career Fair"
   - Enter fair details:
     - Fair name
     - Description
     - Start date and end date
     - Registration deadline
2. **Fair Configuration**
   - Set fair visibility (public or institution-only)
   - Configure fair features:
     - Live chat enabled
     - Interview scheduling
     - CV drop functionality
   - Upload fair branding materials
3. **Employer Invitations**
   - Invite employers to participate
   - Employers register for booths
   - Approve/reject employer applications
4. **Fair Management**
   - View registered employers
   - View registered students
   - Monitor fair activity
   - Manage fair settings
5. **Fair Analytics**
   - View fair statistics:
     - Number of attendees
     - Applications submitted
     - Interviews scheduled
     - Employer participation
   - Export fair reports

**Business Rules:**
- Branded career fair hosting (UNI-04)
- Exclusive employer events visible only to institution students (UNI-03)

**Notifications:**
- Fair created confirmation
- Employer registration notifications
- Fair reminder to students (before event)
- Post-fair summary reports

---

## 4. Cross-Cutting Flows

### 4.1 Verification Flow (Talent & Employer)

**Goal:** Verify user identity and documents to ensure platform trust

**Steps:**
1. **Document Upload**
   - User uploads verification documents
   - Documents stored securely
2. **Admin Review**
   - Admin reviews documents
   - Validates against requirements
3. **Verification Decision**
   - Approved: Sets `verification_status = 'verified'`
   - Rejected: Sets `verification_status = 'rejected'` with reason
4. **Notification**
   - User notified of verification status
   - If rejected, can resubmit with corrections

**Business Rules:**
- Talent: Ghana Card, Student ID, or Passport (COM-03)
- Employer: Ghana Card + Business Registration (COM-04)
- Verification required for paid opportunities (BR-02)

---

### 4.2 Notification Management Flow

**Goal:** Deliver timely, relevant notifications across all channels

**Steps:**
1. **Notification Trigger**
   - System event occurs (application, status change, message, etc.)
   - Creates `notifications` record
2. **Channel Selection**
   - Determines notification channels:
     - Push (if app installed)
     - Email (always)
     - SMS (for critical events)
   - Respects user preferences
3. **Notification Delivery**
   - Sends via selected channels
   - Updates `notifications.status = 'sent'`
   - Records `sent_at` timestamp
4. **Notification Tracking**
   - Tracks delivery status
   - Tracks read status
   - Updates `read_at` when user views notification

**Business Rules:**
- SMS & Email + Push notifications (COM-02)
- Critical events always include SMS
- User can manage notification preferences

---

### 4.3 AI Matching & Recommendations Flow

**Goal:** Provide personalized opportunity recommendations to Talent

**Steps:**
1. **Profile Analysis**
   - Analyzes `talent_profiles` data:
     - Education (field of study, institution)
     - Skills and proficiency
     - Location preferences
     - NSS status
     - Profile completeness
2. **Opportunity Matching**
   - Matches against `opportunities`:
     - Required skills overlap
     - Education alignment
     - Location compatibility
     - Stipend preferences
     - Opportunity type preferences
3. **Score Calculation**
   - Calculates match score (0-100)
   - Factors:
     - Skills match percentage
     - Education relevance
     - Location match
     - Profile completeness bonus
4. **Recommendation Generation**
   - Generates ranked list of opportunities
   - Featured opportunities boosted
   - Recent postings prioritized
5. **Feed Personalization**
   - Displays recommendations in feed
   - Updates as profile changes
   - Learns from application behavior

**Business Rules:**
- AI-powered matching (TAL-04)
- Recommendations improve with profile completeness
- Featured postings always shown first

---

### 4.4 Payment Processing Flow

**Goal:** Process payments securely via multiple payment methods

**Steps:**
1. **Payment Initiation**
   - User selects payment option (subscription, featured posting, etc.)
   - Enters payment amount
2. **Payment Method Selection**
   - Mobile Money (MTN MoMo, Vodafone Cash, Telecel Cash, AirtelTigo Money)
   - Card (Visa/Mastercard)
   - USSD (for feature phones)
3. **Payment Processing**
   - Redirects to payment provider (Flutterwave/Paystack)
   - User completes payment
   - Provider returns transaction ID
4. **Payment Verification**
   - Verifies payment with provider
   - Creates `payments` record
   - Sets `status = 'completed'` or `'failed'`
5. **Service Activation**
   - If successful, activates service (subscription, featured posting, etc.)
   - Sends payment receipt
6. **Payment Failure Handling**
   - If failed, notifies user
   - Allows retry
   - Logs failure reason

**Business Rules:**
- All payments via mobile money, cards, or USSD (Section 7)
- Payment required before service activation
- Refunds processed via payout to employers

---

## Key Decision Points & Business Rules Summary

### Talent Decision Points
- **Verification:** Required before applying to paid opportunities
- **Profile Completeness:** Affects AI matching quality
- **Application Limits:** Cannot apply twice to same opportunity
- **Interview Rescheduling:** Max 2 requests per party
- **Review Eligibility:** Only after placement ends or 3 months after rejection

### Employer Decision Points
- **Verification:** Required before posting paid opportunities
- **Subscription Limits:** Free (3), Starter (20), Professional (unlimited)
- **Unpaid Internships:** >3 months require justification + university approval
- **Stipend Display:** Must be clearly shown (even if GHS 0)
- **Interview Rescheduling:** Max 2 requests per party

### System Decision Points
- **NSS Positions:** Auto-expire after 12-18 months
- **Application Deadlines:** Enforced strictly
- **Max Applications:** Enforced if set by employer
- **Featured Postings:** Always shown first in feed
- **Notification Channels:** Push + Email + SMS (for critical events)

---

## Implementation Recommendations

### Phase 1 (MVP) - Core Flows
1. Talent registration and profile building
2. Employer registration and company setup
3. Opportunity posting and discovery
4. Application submission and tracking
5. Basic messaging
6. Payment processing (mobile money + cards)

### Phase 2 - Enhanced Features
1. AI matching and recommendations
2. Interview scheduling with calendar sync
3. Offer management with e-signature
4. Career fair hosting
5. University admin portal
6. Reviews and ratings

### Phase 3 - Advanced Features
1. Advanced analytics and reporting
2. ATS integration
3. WhatsApp Business API integration
4. National Service Authority API integration
5. Multi-language support (Twi, Ga, Ewe, French)

---

## Success Metrics per Flow

### Talent Flows
- **Registration:** < 5 minutes to complete
- **Profile Building:** 70% completion rate within 7 days
- **Application Submission:** < 2 minutes per application
- **Application Response:** 80% receive status update within 7 days

### Employer Flows
- **Registration:** < 10 minutes to complete
- **Verification:** < 48 hours approval time
- **Opportunity Posting:** < 15 minutes to create and publish
- **Applicant Response:** 70% respond to applications within 3 days

### University Flows
- **Student Upload:** 1000+ students uploaded in < 1 hour
- **Placement Tracking:** Real-time dashboard updates
- **Career Fair:** 50+ employers per fair, 500+ student attendees

---

This document should be used as a reference for:
- UI/UX design decisions
- Feature prioritization
- Development sprint planning
- Quality assurance testing
- User training and documentation

