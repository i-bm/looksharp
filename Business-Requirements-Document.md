# Business Requirements Document (BRD)

**Product Name:** Looksharp  
**Project:** Looksharp - Internship & Early Career Talent Platform for Africa (Phase 1: Ghana Launch)  
**Version:** 1.0  
**Date:** December 01, 2025  
**Prepared by:** Senior Business Analyst Team (on behalf of Looksharp Founding Team)

---

## 1. Executive Summary

Looksharp is a mobile-first digital talent marketplace that connects university students, polytechnic students, national service personnel, and recent graduates (collectively "Talent") with employers seeking interns, attachments, industrial placements, entry-level hires, and graduate trainees in Ghana, with a clear roadmap to expand across Africa.

The platform solves the acute "experience paradox" faced by African youth: employers demand experience, but young people cannot get experience without opportunities. Looksharp will become the single trusted gateway for verified early-career opportunities in Ghana, replicating and exceeding the success of Handshake (USA) while being purpose-built for the African context.

**Target launch:** Q3 2026 in Ghana.  
**Target users in Year 1:** 150,000+ Talent and 4,000+ registered employers.

---

## 2. Vision & Objectives

### Vision

To make meaningful work experience accessible to every African youth, powering the continent's most talented generation into productive careers.

### Business Objectives

- Achieve 200,000 registered Talent and 5,000 active employers in Ghana within 24 months of launch.
- Facilitate at least 50,000 successful internship/attachment placements in the first 36 months.
- Become the #1 recruitment source for companies hiring interns and fresh graduates in Ghana by 2028.
- Generate sustainable revenue through employer-paid models while remaining 100% free for Talent.

---

## 3. Scope

### In Scope (MVP + Post-MVP Phases 1-3)

- Talent registration, profile creation, opportunity search & applications
- Employer registration, company page, opportunity posting & applicant management
- University/Institutions partnership portal
- AI-powered matching & recommendations
- Mobile apps (iOS & Android) + responsive web
- Mobile money & card payment integration (MTN MoMo, Telecel Cash, Vodafone Cash, Visa/Mastercard)
- In-app messaging & interview scheduling
- Virtual career fairs & events
- National Service placement module (Ghana-specific)
- Reviews & ratings system
- Analytics dashboards for all user types

### Out of Scope (for now)

- Full-time experienced hire recruitment (will be added in Phase 4)
- Executive search or headhunting services
- Physical career fair ticketing/logistics

---

## 4. User Personas

| Persona | Description | Goals | Pain Points |
|---------|-------------|-------|-------------|
| **Akosua - Final Year Student (University of Ghana)** | 22 years old, Computer Science, needs mandatory internship | Find reputable companies offering real learning experiences, apply easily on phone | Too many fake postings on WhatsApp/Telegram, long unresponsive email applications |
| **Kwame - National Service Personnel** | Just completed service, looking for graduate trainee roles | Convert service experience into permanent job | Companies rarely respond after service |
| **HR Manager - MTN Ghana** | Recruits 150 interns/graduates annually | Reduce time-to-hire, improve quality of applicants, comply with internal diversity goals | High volume of unqualified CVs via email |
| **Career Services Officer - Ashesi University** | Manages 800+ students | Track student placements, promote university to employers | No centralized data on where alumni interned |
| **Small Business Owner - Tech Startup in Accra** | 15 employees, needs 2 marketing interns | Find hungry, talented students at zero or low cost | Cannot afford LinkedIn Premium or recruitment agencies |

---

## 5. Functional Requirements

### 5.1 Common Features

| ID | Requirement | Priority |
|----|-------------|----------|
| COM-01 | Multi-language support (English + future Twi, Ga, Ewe, French) | High |
| COM-02 | SMS & Email + Push notifications | High |
| COM-03 | Ghana Card / Student ID / Passport verification for Talent | High |
| COM-04 | BVN-style employer verification (Ghana Card for owner + Business Registration documents) | High |
| COM-05 | Dark mode & offline capability for key features | Medium |

### 5.2 Talent Features

| ID | Requirement | Details |
|----|-------------|---------|
| TAL-01 | One-click sign-up with Google, Apple, phone number or email | Must support all Ghanaian phone networks |
| TAL-02 | Rich profile builder | Education, skills, portfolio links (Behance, GitHub, Google Drive), video introduction (30-sec max), NSS posting info |
| TAL-03 | Resume builder with Ghana-standard templates | Export to PDF |
| TAL-04 | Smart opportunity feed | AI-recommended internships based on profile completeness, location, course, skills |
| TAL-05 | Saved searches & job alerts | Daily/weekly summary via SMS if no app opens |
| TAL-06 | Easy apply (1-click or max 3 questions) | Support for cover letter upload or text |
| TAL-07 | Application tracker dashboard | Status: Applied → Under Review → Interview → Offer → Accepted/Rejected |
| TAL-08 | Company insights & reviews | Anonymous reviews of internship experience |
| TAL-09 | Virtual career fair attendance | Browse booths, drop CV, live chat, schedule interviews |

### 5.3 Employer Features

| ID | Requirement | Details |
|----|-------------|---------|
| EMP-01 | Free & paid tiers | Free: 3 active postings; Starter: 20 postings + branding; Professional: unlimited + ATS integration. Pricing to be finalized after market testing |
| EMP-02 | Company page with branding | Logo, photos, employee testimonials, video (max 90 sec) |
| EMP-03 | Opportunity builder | Internship, Attachment, National Service, Graduate Trainee, Entry-level full-time. Fields: stipend (or "unpaid"), duration, location/hybrid/remote, application deadline |
| EMP-04 | Applicant dashboard with filtering & bulk actions | Shortlist, reject, message, schedule interviews |
| EMP-05 | Interview scheduling with Google/Outlook calendar sync | Auto-timezone handling |
| EMP-06 | Offer letter template & e-signature | Integration with HelloSign or DocuSign |
| EMP-07 | Diversity & inclusion reporting | Track gender, university, region of applicants |

### 5.4 University / Career Services Features

| ID | Requirement |
|----|-------------|
| UNI-01 | Institutional dashboard showing student registration & placement rates |
| UNI-02 | Bulk student upload via Excel or API |
| UNI-03 | Exclusive employer events & job postings visible only to their students |
| UNI-04 | Branded career fair hosting |

### 5.5 National Service Specific Module (Ghana-only for now)

| ID | Requirement |
|----|-------------|
| NSS-01 | Talent can indicate they are awaiting/posted for National Service |
| NSS-02 | Employers can post "National Service positions" that auto-expire after 12-18 months |
| NSS-03 | Integration roadmap with National Service Authority API (when available) |

---

## 6. Non-Functional Requirements

| Category | Requirement |
|----------|-------------|
| Performance | <2 sec page load on 3G networks (95th percentile) |
| Scalability | Support 500,000 concurrent users by Year 3 |
| Availability | 99.9% uptime |
| Security | GDPR + Ghana Data Protection Act 2012 compliance, SSL everywhere, 2FA for employers |
| Accessibility | WCAG 2.1 AA compliant |
| Platforms | Native Android (min v8.0), iOS (min 14), Responsive web |
| Offline | Talent can browse saved jobs & draft applications offline |

---

## 7. Payment & Monetization

| Revenue Stream | Description |
|----------------|-------------|
| Featured Postings | Employer pays to boost posting to top of feed |
| Subscription Plans | Monthly/annual plans for unlimited postings + branding |
| Sponsored Career Fairs | Companies pay for premium booths |
| Premium Talent Features (future) | CV review, mock interviews, certification badges (not in MVP) |

All payments via mobile money, cards, or USSD for feature phones.

---

## 8. Key Business Rules

| Rule | Description |
|------|-------------|
| BR-01 | Talent accounts are free forever |
| BR-02 | Employers must verify business before posting paid opportunities |
| BR-03 | Stipend amount must be clearly shown (even if GHS 0) to avoid exploitation |
| BR-04 | Unpaid internships longer than 3 months require justification and university approval flag |
| BR-05 | Maximum 2 interview rescheduling requests per party |
| BR-06 | Reviews only allowed after placement ends or 3 months after application rejection |

---

## 9. Integrations

| Integration | Purpose |
|-------------|---------|
| MTN MoMo, Vodafone Cash, Telecel Cash, AirtelTigo Money | Payments |
| Flutterwave or Paystack | Card payments & payout to employers (refunds) |
| Google Workspace / Microsoft 365 | Calendar sync |
| WhatsApp Business API | Application alerts & chat fallback |
| Ghana.GOV APIs (future) | National Service status verification |
| University LMS (e.g., Sakai, Moodle) | Single sign-on for students |

---

## 10. Assumptions & Constraints

### Assumptions

- Internet penetration and smartphone adoption will continue growing at current rates in Ghana.
- Universities will partner and encourage student sign-ups.
- The National Service Authority will eventually provide API access.

### Constraints

- Must comply with Ghana's Labour Act 2003 (Act 651) regarding internships.
- Budget for Year 1 marketing: GHS 1.2 m (to be raised).
- The development team will be hybrid (Ghana + remote).

---

## 11. Risks & Mitigation

| Risk | Likelihood | Impact | Mitigation |
|------|------------|--------|------------|
| Low employer adoption | Medium | High | Seed with large corporates (MTN, Ecobank, Unilever, Nestlé) via enterprise sales team |
| Fake accounts / CV fraud | High | High | Mandatory Ghana Card verification + AI fraud detection |
| Exploitation (unpaid long internships) | Medium | High | Transparency rules + reporting mechanism + partnership with NUGS & labour unions |
| Competitor response (LinkedIn, Jobberman) | High | Medium | Differentiate with student-first experience, mobile money, local focus |

---

## 12. Success Metrics (KPIs)

| Metric | Year 1 Target | Year 2 Target |
|--------|---------------|---------------|
| Registered Talent | 150,000 | 500,000 |
| Active Employers | 4,000 | 12,000 |
| Applications submitted | 300,000 | 1,200,000 |
| Placements facilitated | 15,000 | 60,000 |
| Talent NPS | >70 | >80 |
| Employer NPS | >60 | >75 |
| Monthly Active Users (MAU) | 80,000 | 300,000 |
| Revenue | GHS 2.4 m | GHS 18 m |

---

This Business Requirements Document is approved as the single source of truth for product development decisions. All future features will be evaluated against the vision and objectives outlined here.

