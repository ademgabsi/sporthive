
<div align="center">

# 🏋️ SportHive

**Connect People Through Sports**

[![Symfony](https://img.shields.io/badge/Symfony-6.4-000000?style=for-the-badge&logo=symfony&logoColor=white)](https://symfony.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge)](LICENSE)

*A comprehensive sports management platform for connecting athletes, managing competitions, and handling sports insurance*

[Features](#-features) • [Technologies](#️-technologies) • [Installation](#️-installation) • [Documentation](#-documentation) • [Contributing](#-contributing)

</div>

---

## 📖 About

**SportHive** is a modern web platform designed to **connect people through sports**. Whether you're traveling, exploring a new city, or simply looking for game partners, SportHive enables you to join sports activities with like-minded individuals — transforming simple games into lasting memories.

The platform provides a complete ecosystem for managing sports competitions, tournaments, teams, players, venues, and sports insurance with integrated payment processing and notification systems.

## ✨ Features

### 🏆 Competition & Tournament Management
- **Competition Creation**: Create and manage sports competitions with customizable settings
- **Tournament Organization**: Full tournament lifecycle management with brackets and scheduling
- **Team Management**: Register teams, manage rosters, and track performance
- **Match Scheduling**: Automated match scheduling with QR code generation for easy check-in
- **Player Registration**: Individual player profiles and statistics tracking

### 🏟️ Venue & Reservation System
- **Venue Management**: Register and manage sports venues (terrains)
- **Reservation System**: Book venues for training sessions, matches, and events
- **Availability Tracking**: Real-time venue availability and conflict prevention

### 🛡️ Sports Insurance Platform
- **Insurance Policies**: Comprehensive sports insurance coverage management
  - Accident coverage
  - Illness coverage
  - Civil liability coverage
  - Disability coverage
- **Claims Management**: File and track insurance claims with automated workflows
- **Policy Analytics**: Advanced analytics dashboard for insurance metrics
- **Payment Processing**: Secure payment integration via Stripe
- **PDF Generation**: Automated policy and claim document generation

### 💳 Payment & Financial Management
- **Stripe Integration**: Secure payment processing for insurance premiums
- **Checkout Sessions**: Dedicated payment flows for insurance purchases
- **Transaction Tracking**: Complete payment history and receipt management

### 📱 Communication & Notifications
- **SMS Notifications**: Real-time SMS alerts via Twilio for claims and important updates
- **Email Notifications**: Automated email notifications for events and updates
- **Multi-channel Alerts**: Integrated notification system across SMS and email

### 🔐 Security & Authentication
- **Two-Factor Authentication (2FA)**: Google Authenticator integration for enhanced security
- **OTP Verification**: One-time password verification during registration and login
- **Role-Based Access Control**: Granular permissions for users, admins, and moderators
- **Secure Password Reset**: Email-based password recovery system

### 🛠️ Content Moderation
- **Profanity Filtering**: Automatic content moderation using PurgoMalum API
- **Custom Word Lists**: Configurable prohibited words and phrases
- **Real-time Validation**: Instant feedback on user-generated content

### 📊 Analytics & Reporting
- **Insurance Analytics**: Comprehensive dashboards for insurance metrics
  - Claims by status
  - Policy distribution
  - Revenue analytics
  - Coverage type analysis
- **Chart Visualization**: Interactive charts using Chart.js
- **Data Export**: Export reports and analytics data

### 🎨 User Experience
- **Responsive Design**: Mobile-friendly interface with Bootstrap
- **Modern UI**: Clean and intuitive user interface
- **Stimulus.js Integration**: Enhanced interactivity with minimal JavaScript
- **Turbo Drive**: Fast page navigation with Hotwired Turbo
- **Asset Mapping**: Optimized asset delivery

### 🤖 Additional Features
- **Sponsorship Management**: Manage sponsors for competitions and events
- **Chatbot Support**: Integrated chatbot for user assistance
- **QR Code Generation**: Generate QR codes for match tickets and event access
- **File Uploads**: Support for images and documents (avatars, venue photos, etc.)
- **Pagination**: Efficient data browsing with KnpPaginator

## 🛠️ Technologies

### Backend
- **Framework**: Symfony 6.4 (PHP 8.1+)
- **ORM**: Doctrine ORM 3.3 with DBAL
- **Database**: MySQL/MariaDB or PostgreSQL
- **Migrations**: Doctrine Migrations
- **Templating**: Twig 3.0
- **Validation**: Symfony Validator component
- **Security**: Symfony Security Bundle with 2FA support

### Frontend
- **JavaScript**: Stimulus.js, Hotwired Turbo
- **CSS**: Bootstrap (via Asset Mapper)
- **Charts**: Chart.js 4.4
- **Asset Management**: Symfony Asset Mapper
- **UI Components**: Symfony UX components

### Third-Party Integrations
- **Payment Processing**: Stripe API (stripe-php 17.2)
- **SMS Notifications**: Twilio REST API
- **Content Moderation**: PurgoMalum API
- **2FA/OTP**: OTPHP library (Spomky Labs)
- **QR Codes**: Endroid QR Code Bundle
- **PDF Generation**: Dompdf 3.1

### Development Tools
- **Testing**: PHPUnit 9.5, Symfony PHPUnit Bridge
- **Debugging**: Symfony Web Profiler, Debug Bundle
- **Code Generation**: Symfony Maker Bundle
- **Package Management**: Composer, npm

### DevOps
- **Containerization**: Docker (docker-compose.yaml included)
- **Web Server**: Symfony CLI or traditional web servers
- **Environment Management**: Symfony Dotenv

## 🖥️ System Architecture

```
SportHive/
├── assets/              # Frontend assets (JS, CSS)
├── bin/                 # Executable scripts (console)
├── config/              # Application configuration
│   ├── packages/        # Package configurations
│   └── routes/          # Routing definitions
├── migrations/          # Database migrations
├── public/              # Web root
│   ├── css/            # Stylesheets
│   ├── img/            # Images
│   └── uploads/        # User-uploaded files
├── src/
│   ├── Command/        # Console commands
│   ├── Controller/     # HTTP controllers
│   ├── Entity/         # Doctrine entities
│   ├── EventListener/  # Event subscribers
│   ├── Form/           # Form types
│   ├── Repository/     # Data repositories
│   ├── Security/       # Authentication & authorization
│   ├── Service/        # Business logic services
│   └── Validator/      # Custom validators
├── templates/          # Twig templates
├── tests/              # Test suite
└── translations/       # i18n files
```

## ⚙️ Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL 8.0+ or PostgreSQL 16+
- Symfony CLI (recommended)

### Step 1: Clone the Repository
```bash
git clone https://github.com/ademgabsi/sporthive.git
cd sporthive
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies (if using npm)
npm install
```

### Step 3: Configure Environment Variables
Create a `.env.local` file and configure the following variables:

```env
# Application Environment
APP_ENV=dev
APP_SECRET=your-app-secret-here

# Database Configuration
DATABASE_URL="mysql://username:password@127.0.0.1:3306/sporthive?serverVersion=8.0"
# Or for PostgreSQL:
# DATABASE_URL="postgresql://username:password@127.0.0.1:5432/sporthive?serverVersion=16&charset=utf8"

# Stripe Configuration
STRIPE_PUBLIC_KEY=pk_test_your_public_key
STRIPE_SECRET_KEY=sk_test_your_secret_key

# Twilio Configuration
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=your_twilio_auth_token
TWILIO_FROM=+1234567890

# Mailer Configuration (optional)
MAILER_DSN=smtp://localhost:1025
```

### Step 4: Setup Database
```bash
# Create the database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# (Optional) Load sample data
php bin/console doctrine:fixtures:load
```

### Step 5: Install Assets
```bash
# Install JavaScript dependencies via importmap
php bin/console importmap:install

# Install public assets
php bin/console assets:install public
```

### Step 6: Start the Development Server
```bash
# Using Symfony CLI (recommended)
symfony server:start

# Or using PHP built-in server
php -S localhost:8000 -t public/
```

Visit `http://localhost:8000` in your browser.

## 🐳 Docker Installation

For a containerized setup using Docker:

```bash
# Start all services
docker-compose up -d

# Run migrations
docker-compose exec php bin/console doctrine:migrations:migrate

# Access the application
# Visit http://localhost:8000
```

## 📚 Documentation

### Core Entities

- **Utilisateur (User)**: User accounts with roles and 2FA support
- **Competition**: Sports competitions with teams and matches
- **Tournoi (Tournament)**: Tournament organization and bracket management
- **Equipe (Team)**: Team registration and roster management
- **Joueur (Player)**: Individual player profiles
- **GestionMatch (Match)**: Match scheduling and QR code generation
- **Terrain (Venue)**: Sports venue registration
- **Reservation**: Venue booking system
- **Assurance (Insurance)**: Insurance policy management
- **AssuranceUtilisateur**: User-insurance associations
- **Reclamation (Claim)**: Insurance claim processing
- **Sponsor**: Sponsorship management

### Key Services

- **StripeService**: Payment processing integration
- **SmsService**: SMS notifications via Twilio
- **NotificationService**: Multi-channel notification system
- **PurgoMalumService**: Content moderation
- **GoogleAuthenticatorService**: 2FA/OTP implementation
- **AssuranceAnalyticsService**: Insurance metrics and analytics
- **ReclamationService**: Claims workflow management
- **PricingService**: Dynamic pricing calculations

### API Integrations

#### Stripe Payment Flow
```php
// Create a checkout session
$session = $stripeService->createCheckoutSession($assurance, $user);
header("Location: " . $session->url);
```

#### SMS Notifications
```php
// Send SMS notification
$smsService->send($phoneNumber, $message);
```

#### Content Moderation
```php
// Filter inappropriate content
$cleanText = $purgoMalumService->filterProfanity($userInput);
```

## 🧪 Testing

```bash
# Run all tests
php bin/phpunit

# Run specific test suite
php bin/phpunit tests/Unit/

# Run with coverage
php bin/phpunit --coverage-html coverage/
```

## 🔒 Security

### Best Practices Implemented
- ✅ Environment variables for sensitive data (API keys, database credentials)
- ✅ Symfony Security component with role-based access control
- ✅ Two-factor authentication (2FA) with Google Authenticator
- ✅ CSRF protection on all forms
- ✅ Content Security Policy (CSP) headers
- ✅ Input validation and sanitization
- ✅ Secure password hashing (bcrypt/argon2)
- ✅ SQL injection prevention via Doctrine ORM
- ✅ XSS protection via Twig auto-escaping
- ✅ Stripe PCI-compliant payment processing

### Security Checklist
- [ ] Never commit `.env.local` or sensitive credentials
- [ ] Use HTTPS in production
- [ ] Regularly update dependencies (`composer update`)
- [ ] Enable production mode (`APP_ENV=prod`)
- [ ] Set strong `APP_SECRET`
- [ ] Configure proper file permissions
- [ ] Enable rate limiting for API endpoints
- [ ] Regular security audits

## 🚀 Deployment

### Production Checklist
```bash
# Set production environment
APP_ENV=prod

# Clear and warm up cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# Install optimized dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Install and build assets
php bin/console importmap:install
php bin/console assets:install public
```

### Performance Optimization
- Enable OPcache in PHP
- Use APCu for application caching
- Configure HTTP cache headers
- Enable Symfony cache pools
- Use CDN for static assets
- Database query optimization with indexes

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add some amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Development Guidelines
- Follow Symfony coding standards
- Write unit tests for new features
- Update documentation as needed
- Keep commits atomic and meaningful
- Use meaningful branch names

## 📝 License

This project is proprietary software. All rights reserved.

**© ESPRIT - Higher School of Private Engineering and Technology**

Unauthorized copying, modification, distribution, or use of this software, via any medium, is strictly prohibited.

## 👥 Authors & Acknowledgments

- **Development Team**: ESPRIT Students
- **Academic Institution**: ESPRIT (École Supérieure Privée d'Ingénierie et de Technologies)

### Special Thanks
- Symfony framework community
- All open-source contributors whose libraries make this project possible

## 📞 Support

For questions, issues, or suggestions:
- **GitHub Issues**: [Submit an issue](https://github.com/ademgabsi/sporthive/issues)
- **Academic Contact**: Contact via ESPRIT official channels

---

<div align="center">

**Built with ❤️ using Symfony**

[⬆ Back to Top](#-sporthive)

</div>
