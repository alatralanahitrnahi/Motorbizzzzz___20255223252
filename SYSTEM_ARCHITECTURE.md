# Monitor Bizz System Architecture Documentation

## Overview

Monitor Bizz is a comprehensive manufacturing ERP/CRM system built on the Laravel PHP framework with a modern RESTful API architecture. The system follows a multi-tenant approach without subdomains, allowing multiple businesses to operate on the same platform with complete data isolation.

## Technical Architecture

### Technology Stack

#### Backend
- Framework: Laravel 10.x PHP Framework
- Database: SQLite (development), MySQL/PostgreSQL (production)
- Authentication: Laravel Sanctum for API token authentication
- API: RESTful API architecture with JSON responses

#### Frontend
- Framework: Laravel Blade templates with modern JavaScript
- Styling: Tailwind CSS
- JavaScript: Alpine.js for interactive components

#### Infrastructure
- Web Server: Apache or Nginx
- PHP Version: 8.1 or higher
- Database: SQLite, MySQL 5.7+, or PostgreSQL 10+

## Multi-Tenant Architecture

### Approach
Monitor Bizz implements a shared database, shared schema multi-tenant approach with row-level isolation.

### Implementation Details

#### Business ID Pattern
All business entities include a business_id foreign key that references the businesses table.

#### Global Scopes
Each model uses a global scope to automatically filter records by the current business.

#### Middleware Protection
API routes are protected by middleware that validates business access.

## Database Schema

### Core Tables
- Businesses - Business information
- Users - User accounts and roles
- Customers - Customer profiles and details
- Materials - Raw materials and components
- Products - Finished goods and products
- Work Orders - Production work orders

## Security Architecture

### Authentication
- Password Hashing - Bcrypt algorithm
- Token Security - Laravel Sanctum personal access tokens

### Authorization
- Role-Based Access Control - Admin, manager, and user roles
- Business Isolation - Row-level data separation

## Performance Architecture

### Caching Strategy
- Configuration Cache - Cached application configuration
- Route Cache - Cached route definitions
- View Cache - Cached compiled views

### Database Optimization
- Indexing - Strategic indexes on business_id and frequently queried columns
- Query Optimization - Eager loading to prevent N+1 queries

This architecture provides a solid foundation for a scalable, secure, and maintainable manufacturing ERP/CRM system.
