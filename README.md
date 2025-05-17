# Vendor Management System (VMS)

A comprehensive Vendor Management System built on CodeIgniter 3 framework, designed to streamline vendor registration, qualification, and management processes.

## Core Features

### Vendor Registration & Onboarding
- Multi-step vendor registration process
- Document verification workflow
- Automated email notifications
- Vendor profile management
- Legal document management (NPWP, NPPKP, etc.)

### Vendor Qualification
- Vendor assessment and evaluation
- Document verification system
- Compliance tracking
- Blacklist management
- Vendor categorization and classification

### Document Management
- Legal document verification (Akta, SIUP, TDP, etc.)
- Document expiration tracking
- Automated renewal notifications
- Document version control
- Digital document storage

### Vendor Assessment
- K3 (Health & Safety) assessment
- CSMS (Contractor Safety Management System) evaluation
- Performance tracking
- Risk assessment
- Vendor scoring system

### Approval Workflow
- Multi-level approval system
- Role-based access control
- Approval tracking
- Automated notifications
- Audit trail

### Vendor Communication
- Automated email notifications
- System alerts
- Document status updates
- Vendor portal access
- Communication history

## Technical Requirements

- PHP >= 7.4.0
- MySQL/MariaDB
- Apache/Nginx web server
- Composer for dependency management
- Required PHP extensions:
  - mysqli
  - json
  - mbstring
  - xml

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
```

2. Install dependencies:
```bash
composer install
```

3. Configure the database:
   - Create a new database
   - Import the database schema
   - Update database configuration in `application/config/database.php`

4. Configure the application:
   - Set base URL in `application/config/config.php`
   - Configure email settings in `application/config/email.php`
   - Update company information in system settings

5. Web server configuration:
   - Point document root to the project's public directory
   - Ensure proper permissions for upload directories
   - Configure URL rewriting

6. Initial setup:
   - Create admin user
   - Configure initial system settings
   - Set up email templates

## Security Considerations

- Change default credentials immediately after installation
- Enable HTTPS
- Regular security updates
- Implement proper access controls
- Regular backup of vendor data
- Secure document storage
- Audit logging

## Support and Contact

For technical support or inquiries, please contact:
- Email: revanza.raytama@gmail.com
- LinkedIn: [linkedin.com/in/revanzaraytama](https://linkedin.com/in/
revanzaraytama)

## License

[Specify License]

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## Important Notes

- The system is production-ready but requires proper configuration
- Built using HMVC architecture for better code organization
- Regular maintenance and updates are recommended
- Vendor data should be regularly backed up
- System performance should be monitored regularly
- Regular security audits are recommended

## Vendor Portal Features

- Self-service registration
- Document upload and management
- Profile updates
- Status tracking
- Communication center
- Document expiration notifications
- Assessment results viewing
- Performance metrics access

## Admin Features

- Vendor approval workflow
- Document verification
- Assessment management
- Blacklist management
- System configuration
- User management
- Report generation
- Audit trail access 