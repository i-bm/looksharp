# Looksharp

**Connecting African Youth to Meaningful Work Experience**

Looksharp is a mobile-first digital talent marketplace that connects university students, polytechnic students, national service personnel, and recent graduates with employers seeking interns, attachments, industrial placements, entry-level hires, and graduate trainees in Ghana, with a clear roadmap to expand across Africa.

---

## 🎯 Vision

To make meaningful work experience accessible to every African youth, powering the continent's most talented generation into productive careers.

---

## ✨ Key Features

### For Talent (Students & Graduates)
- **One-click sign-up** with Google, Apple, phone number, or email
- **Rich profile builder** with education, skills, portfolio links, and video introductions
- **AI-powered opportunity matching** based on profile, location, and skills
- **Easy apply** with 1-click or max 3 questions
- **Application tracker** dashboard with real-time status updates
- **Resume builder** with Ghana-standard templates
- **Saved searches & job alerts** via SMS and email
- **Company insights & reviews** from previous interns
- **Virtual career fair attendance** with live chat and interview scheduling

### For Employers
- **Free & paid subscription tiers** (Free: 3 postings, Starter: 20 postings, Professional: unlimited)
- **Company branding pages** with logos, photos, testimonials, and videos
- **Opportunity builder** for internships, attachments, NSS positions, graduate trainees, and entry-level roles
- **Applicant dashboard** with filtering, bulk actions, and shortlisting
- **Interview scheduling** with Google/Outlook calendar sync
- **Offer letter templates** with e-signature integration
- **Diversity & inclusion reporting** for applicant tracking

### For Universities
- **Institutional dashboard** showing student registration and placement rates
- **Bulk student upload** via Excel or API
- **Exclusive employer events** and job postings for their students
- **Branded career fair hosting**

### Platform Features
- **Multi-language support** (English + future Twi, Ga, Ewe, French)
- **Mobile money integration** (MTN MoMo, Vodafone Cash, Telecel Cash, AirtelTigo Money)
- **Card payment support** via Flutterwave/Paystack
- **Ghana Card verification** for Talent and Employers
- **SMS & Email notifications** + Push notifications
- **Dark mode** & offline capability
- **National Service (NSS) module** for Ghana-specific placements

---

## 🛠️ Tech Stack

### Backend
- **PHP 8.2+**
- **Laravel 12** - Web application framework
- **MySQL/PostgreSQL** - Database (see [Database Model](./Database-Model.md))

### Frontend
- **Blade Templates** - Server-side templating
- **Tailwind CSS 4.0** - Utility-first CSS framework
- **Bootstrap 5.2** - Additional UI components
- **Vite 7.0** - Build tool and dev server
- **Sass** - CSS preprocessor

### Development Tools
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Testing framework
- **Laravel Sail** - Docker development environment
- **Laravel Pail** - Log viewer

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- **PHP 8.2** or higher
- **Composer** (PHP dependency manager)
- **Node.js 18+** and **npm**
- **MySQL 8.0+** or **PostgreSQL 13+**
- **Git**

Optional but recommended:
- **Docker** and **Docker Compose** (for Laravel Sail)

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-org/looksharp.git
cd looksharp
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database credentials and other environment variables:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=looksharp
DB_USERNAME=your_username
DB_PASSWORD=your_password

APP_NAME="Looksharp"
APP_URL=http://localhost:8000

# Email Service (Resend)
MAIL_MAILER=resend
RESEND_API_KEY=your_resend_api_key
MAIL_FROM_ADDRESS="hello@joinlooksharp.com"
MAIL_FROM_NAME="Looksharp"

# SMS Service (SMSOnlineGH)
SMSONLINEGH_API_KEY=your_smsonlinegh_api_key
SMSONLINEGH_API_URL=https://api.smsonlinegh.com/api/v1
SMSONLINEGH_SENDER_ID=your_sender_id
```

### 5. Database Setup

```bash
php artisan migrate
php artisan db:seed  # Optional: seed with sample data
```

### 6. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

### 7. Start Development Server

```bash
php artisan serve
```

Or use the combined dev command (server + queue + vite):
```bash
composer run dev
```

Visit `http://localhost:8000` in your browser.

---

## 🐳 Using Docker (Laravel Sail)

If you prefer using Docker:

```bash
# Start containers
./vendor/bin/sail up -d

# Install dependencies
./vendor/bin/sail composer install
./vendor/bin/sail npm install

# Setup environment
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

# Build assets
./vendor/bin/sail npm run dev
```

Access the application at `http://localhost`

---

## 📁 Project Structure

```
looksharp/
├── app/
│   ├── Http/
│   │   └── Controllers/     # Application controllers
│   ├── Models/               # Eloquent models
│   ├── Helpers/              # Helper functions
│   └── Providers/            # Service providers
├── bootstrap/                # Application bootstrap files
├── config/                   # Configuration files
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── docs/                     # Documentation and design files
│   ├── architecture designs/ # System architecture diagrams
│   └── UIs/                  # UI mockups and designs
├── public/                   # Public assets (CSS, JS, images)
│   └── assets/               # Static assets
├── resources/
│   ├── views/                # Blade templates
│   │   ├── layouts/          # Layout templates
│   │   └── pages/            # Page templates
│   ├── css/                  # CSS source files
│   └── js/                   # JavaScript source files
├── routes/
│   └── web.php               # Web routes
├── storage/                   # Storage for logs, cache, files
├── tests/                     # Test files
├── vendor/                    # Composer dependencies
├── Business-Requirements-Document.md  # Product requirements
└── Database-Model.md          # Database schema documentation
```

---

## 🧪 Testing

Run the test suite:

```bash
composer run test
```

Or with PHPUnit directly:

```bash
php artisan test
```

---

## 📝 Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) for code style fixing:

```bash
./vendor/bin/pint
```

---

## 📚 Documentation

### Core Documentation
- **[Business Requirements Document](./Business-Requirements-Document.md)** - Complete product requirements and specifications
- **[Database Model](./Database-Model.md)** - Database schema and entity relationships

### Design & Architecture
- **[Architecture Designs](./docs/architecture%20designs/)** - High-level system architecture diagrams
  - `looksharp_highlevel-architecture.png` - Complete system architecture overview
  - `Summarized_Looksharp_HighLevel_Architecture.png` - Simplified architecture diagram
- **[UI Designs](./docs/UIs/)** - User interface mockups and designs
  - `landing_page.jpg` - Landing page design
  - `student_dashboard.jpg` - Student dashboard interface
  - `student_login.jpg` - Student login page design

---

## 🔐 Security

- All user data is encrypted at rest
- SSL/TLS required for all connections
- GDPR and Ghana Data Protection Act 2012 compliant
- Two-factor authentication (2FA) for employers
- Ghana Card verification for Talent and Employers

If you discover any security vulnerabilities, please email security@looksharp.com instead of using the issue tracker.

---

## 🎯 Roadmap

### Phase 1: MVP (Q3 2026)
- Core Talent and Employer features
- Basic matching and application system
- Payment integration
- Mobile-responsive web application

### Phase 2: Mobile Apps
- Native iOS and Android applications
- Push notifications
- Offline capability

### Phase 3: Advanced Features
- AI-powered matching improvements
- Virtual career fairs
- University partnership portal
- Advanced analytics

### Phase 4: Expansion
- Full-time experienced hire recruitment
- Multi-country expansion across Africa

---

## 📊 Key Metrics (Year 1 Targets)

- **150,000+** registered Talent
- **4,000+** active Employers
- **300,000+** applications submitted
- **15,000+** placements facilitated
- **80,000+** Monthly Active Users (MAU)

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure your code follows the project's coding standards and includes appropriate tests.

---

## 📄 License

This project is proprietary software. All rights reserved.

---

## 📞 Contact & Support

- **Website:** [Coming Soon]
- **Email:** support@looksharp.com
- **Documentation:** See [Business Requirements Document](./Business-Requirements-Document.md)

---

## 🙏 Acknowledgments

Built with ❤️ for African youth, powered by Laravel and the open-source community.

---

**Status:** 🚧 In Active Development

**Target Launch:** Q3 2026 (Ghana)
