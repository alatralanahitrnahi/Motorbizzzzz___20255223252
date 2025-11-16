# Monitor Bizz - Manufacturing ERP/CRM System

## Overview

Monitor Bizz is a comprehensive manufacturing ERP/CRM system designed for small to medium-sized manufacturing businesses. The system provides end-to-end functionality for managing customer relationships, inventory, purchasing, manufacturing, and sales processes.

## Documentation Files

This repository contains the following comprehensive documentation files:

1. **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Complete RESTful API documentation with endpoints and examples
2. **[CRM_MODULE.md](CRM_MODULE.md)** - Detailed documentation of the Customer Relationship Management module
3. **[INVENTORY_MANAGEMENT.md](INVENTORY_MANAGEMENT.md)** - Comprehensive guide to inventory management features
4. **[MANUFACTURING_MODULE.md](MANUFACTURING_MODULE.md)** - Detailed documentation of the manufacturing processes and workflows
5. **[USER_GUIDE.md](USER_GUIDE.md)** - Step-by-step user guide with practical workflows
6. **[SYSTEM_ARCHITECTURE.md](SYSTEM_ARCHITECTURE.md)** - Technical architecture and multi-tenant implementation details

## Key Features

### CRM (Customer Relationship Management)
- Lead management and conversion
- Customer profile management
- Quotation creation and management
- Sales order processing
- Relationship tracking between leads, customers, quotations, and orders

### Inventory Management
- Multi-type inventory classification (raw materials, components, consumables, spare parts)
- Multi-location tracking (warehouse, shop floor, storage areas)
- Stock reservation system to prevent overselling
- Batch/lot tracking for quality control
- Real-time stock updates and movement history

### Manufacturing
- Bill of Materials (BOM) management with wastage tracking
- Work order planning and execution
- Material consumption tracking with wastage analysis
- Production scheduling and resource allocation
- Quality control with yield percentage calculation
- Batch production management

### Multi-Tenant Architecture
- Shared database, shared schema approach
- Complete data isolation between businesses
- Row-level business ID filtering
- Scalable and cost-effective implementation

## System Requirements

### Backend
- PHP 8.1 or higher
- Laravel 10.x framework
- SQLite, MySQL 5.7+, or PostgreSQL 10+
- Redis (optional for caching and queues)

### Frontend
- Modern web browser (Chrome, Firefox, Safari, or Edge)
- JavaScript enabled
- Minimum screen resolution of 1024x768

### Infrastructure
- Web server (Apache or Nginx)
- Database server
- SSL certificate for production deployment

## API Endpoints

The system provides a comprehensive RESTful API for integration with external systems. Key endpoint categories include:

- Authentication: User registration, login, and session management
- CRM: Leads, customers, quotations, and sales orders
- Inventory: Materials, products, and location management
- Manufacturing: BOMs, work orders, and machine management
- Vendors: Supplier information and purchasing

For detailed API documentation, refer to [API_DOCUMENTATION.md](API_DOCUMENTATION.md).

## Getting Started

1. Review the [SYSTEM_ARCHITECTURE.md](SYSTEM_ARCHITECTURE.md) for technical implementation details
2. Follow the installation guide in the main application repository
3. Use the [USER_GUIDE.md](USER_GUIDE.md) for day-to-day operations
4. Refer to module-specific documentation for detailed feature information

## Support and Maintenance

For support and maintenance information, please contact your system administrator or refer to the support documentation in the main repository.

## Contributing

If you would like to contribute to the Monitor Bizz system or documentation, please follow the contribution guidelines in the main repository.

## License

This documentation is part of the Monitor Bizz system and is subject to the same licensing terms as the main application.
