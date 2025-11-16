# Monitor Bizz API Documentation

## Overview

Monitor Bizz is a comprehensive manufacturing ERP/CRM system with a RESTful API that provides endpoints for managing all aspects of a manufacturing business including CRM, inventory, purchasing, manufacturing, and sales.

## Authentication

All API endpoints (except authentication) require a Bearer token for authentication.

### Register


Registers a new business and user account.

**Request Body:**


**Response:**


### Login


Authenticate a user and receive an access token.

**Request Body:**


**Response:**


### Logout


Logout the current user (invalidate token).

**Headers:**


**Response:**


### Get Current User


Get information about the currently authenticated user.

**Headers:**


**Response:**


## CRM Module

### Customers

#### Get All Customers

Get a paginated list of customers for the current business.

Headers:
- Authorization: Bearer <token>

Query Parameters:
- search (optional): Search term to filter customers by name or email

#### Create Customer

Create a new customer.

Headers:
- Authorization: Bearer <token>
- Content-Type: application/json

Request Body:
{
  "name": "string",
  "email": "string (optional)",
  "phone": "string (optional)",
  "company_name": "string (optional)",
  "gstin": "string (optional)",
  "billing_address": "string (optional)",
  "shipping_address": "string (optional)",
  "customer_type": "retail
