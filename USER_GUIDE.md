# Monitor Bizz User Guide

## Table of Contents
1. Getting Started
2. Authentication
3. CRM Workflows
4. Inventory Management
5. Manufacturing Processes
6. System Administration

## Getting Started

### System Requirements
- Modern web browser (Chrome, Firefox, Safari, or Edge)
- Stable internet connection
- Minimum screen resolution of 1024x768

### Accessing the System
1. Open your web browser
2. Navigate to your Monitor Bizz instance URL
3. Log in with your credentials

### Navigation
The system uses a sidebar navigation menu with the following main sections:
- Dashboard - Overview of key metrics and recent activities
- CRM - Customer relationship management (Leads, Customers, Quotations, Sales Orders)
- Inventory - Materials, Products, Locations, and Stock Movements
- Manufacturing - Bills of Materials, Work Orders, and Machines
- Purchasing - Vendor management and purchase orders
- Reports - Analytics and business intelligence
- Settings - System configuration and user management

## Authentication

### User Registration
New businesses can register for Monitor Bizz by following these steps:

1. Click on the "Register" button on the login page
2. Fill in the registration form:
   - Business Name - Your company name
   - Your Name - Your full name
   - Email - Your email address (will be used for login)
   - Phone - Your contact number
   - Password - Choose a strong password
   - Address - Your business address (optional)
3. Click "Register" to create your account
4. You will be automatically logged in and redirected to the dashboard

### User Login
Existing users can log in to the system:

1. Enter your email address
2. Enter your password
3. Click "Login"
4. You will be redirected to the dashboard

### Password Reset
If you forget your password:

1. Click on "Forgot Password" on the login page
2. Enter your email address
3. Check your email for a password reset link
4. Click the link and follow instructions to set a new password

## CRM Workflows

### Managing Leads

#### Creating a New Lead
1. Navigate to CRM > Leads
2. Click the "Add Lead" button
3. Fill in the lead details:
   - Company Name - Name of the potential customer
   - Contact Person - Primary contact person
   - Email - Contact email address
   - Phone - Contact phone number
   - Lead Source - How you acquired this lead
   - Estimated Value - Potential revenue
   - Probability - Likelihood of conversion
   - Notes - Additional information
4. Click "Save" to create the lead

#### Converting a Lead to Customer
1. Navigate to CRM > Leads
2. Find the lead you want to convert
3. Click on the lead to open the details page
4. Click the "Convert to Customer" button
5. Fill in customer details:
   - Name - Customer name
   - Company Name - Legal business name
   - Email - Primary contact email
   - Phone - Primary contact phone
   - Customer Type - Retail, wholesale, or distributor
   - Credit Limit - Maximum credit allowed
   - Payment Terms - Days for payment
6. Click "Convert" to create the customer

### Managing Customers

#### Creating a New Customer
1. Navigate to CRM > Customers
2. Click the "Add Customer" button
3. Fill in customer details:
   - Name - Customer name or company name
   - Email - Primary contact email
   - Phone - Primary contact phone
   - Company Name - Legal business name
   - GSTIN - Tax identification number
   - Billing Address - Address for invoices
   - Shipping Address - Address for deliveries
   - Customer Type - Retail, wholesale, or distributor
   - Credit Limit - Maximum credit allowed
   - Payment Terms - Days for payment
4. Click "Save" to create the customer

#### Updating Customer Information
1. Navigate to CRM > Customers
2. Find the customer you want to update
3. Click on the customer to open the details page
4. Click the "Edit" button
5. Make the necessary changes
6. Click "Save" to update the customer

### Creating Quotations

#### Creating a New Quotation
1. Navigate to CRM > Quotations
2. Click the "Add Quotation" button
3. Select the customer from the dropdown
4. Enter quotation details:
   - Quote Date - Date of the quotation
   - Valid Until - Expiration date
   - Terms & Conditions - Payment and delivery terms
5. Add line items:
   - Click "Add Item"
   - Select product or material
   - Enter quantity and unit price
   - Apply tax rate and discount if needed
6. Review totals and adjust if necessary
7. Click "Save" to create the quotation

#### Sending a Quotation
1. Navigate to CRM > Quotations
2. Find the quotation you want to send
3. Click on the quotation to open the details page
4. Click the "Send" button
5. The quotation status will change to "Sent"

#### Converting Quotation to Sales Order
1. Navigate to CRM > Quotations
2. Find the accepted quotation
3. Click on the quotation to open the details page
4. Click the "Convert to Order" button
5. Review the sales order details
6. Click "Convert" to create the sales order

### Managing Sales Orders

#### Creating a Sales Order Directly
1. Navigate to CRM > Sales Orders
2. Click the "Add Order" button
3. Select the customer from the dropdown
4. Enter order details:
   - Order Date - Date of the order
   - Delivery Date - Expected delivery date
   - Priority - Low, medium, or high
5. Add line items:
   - Click "Add Item"
   - Select product
   - Enter quantity and unit price
   - Apply tax rate and discount if needed
6. Review totals
7. Click "Save" to create the sales order

#### Processing a Sales Order
1. Navigate to CRM > Sales Orders
2. Find the order to process
3. Click on the order to open the details page
4. Review order details and ensure accuracy
5. Click "Confirm" to proceed with fulfillment
6. The order status will change to "Confirmed"

## Inventory Management

### Managing Materials

#### Creating a New Material
1. Navigate to Inventory > Materials
2. Click the "Add Material" button
3. Fill in material details:
   - Name - Descriptive name
   - Unit - Measurement unit (kg, liter, pieces, etc.)
   - Unit Price - Cost per unit
   - GST Rate - Tax rate
   - Category - Material category
   - Material Type - Raw material, component, consumable, or spare part
   - Current Stock - Initial stock level
   - Reorder Level - Minimum stock threshold
4. Click "Save" to create the material

#### Updating Material Stock
1. Navigate to Inventory > Materials
2. Find the material to update
3. Click on the material to open the details page
4. Click the "Adjust Stock" button
5. Enter the quantity to add or remove
6. Select the movement type (in, out, adjustment)
7. Add notes if needed
8. Click "Save" to update stock levels

### Managing Products

#### Creating a New Product
1. Navigate to Inventory > Products
2. Click the "Add Product" button
3. Fill in product details:
   - Name - Product name
   - Unit - Measurement unit (pieces, sets, etc.)
   - Selling Price - Price to customers
   - Cost Price - Manufacturing cost
   - Category - Product category
   - Product Type - Finished good, semi-finished, component, or assembly
   - Manufacturing Time - Time required to produce (minutes)
   - Is Manufactured - Whether product is manufactured in-house
   - Is Saleable - Whether product can be sold
   - Current Stock - Initial stock level
   - Reorder Level - Minimum stock threshold
4. Click "Save" to create the product

### Managing Inventory Locations

#### Creating a New Location
1. Navigate to Inventory > Locations
2. Click the "Add Location" button
3. Fill in location details:
   - Name - Descriptive name
   - Code - Unique identifier
   - Location Type - Warehouse, shop floor, or storage area
   - Capacity - Maximum storage capacity
   - Address - Physical address
4. Click "Save" to create the location

### Stock Transfers

#### Transferring Stock Between Locations
1. Navigate to Inventory > Stock Movements
2. Click the "Transfer Stock" button
3. Select the source location
4. Select the destination location
5. Select the material or product to transfer
6. Enter the quantity to transfer
7. Add notes if needed
8. Click "Transfer" to move the stock

## Manufacturing Processes

### Creating Bills of Materials (BOMs)

#### Creating a New BOM
1. Navigate to Manufacturing > BOMs
2. Click the "Add BOM" button
3. Select the product this BOM is for
4. Enter BOM details:
   - Version - Version number
   - Quantity - Base quantity for which BOM is defined
5. Add BOM items:
   - Click "Add Item"
   - Select material
   - Enter quantity required per base unit
   - Enter wastage percentage
6. Click "Save" to create the BOM

### Managing Work Orders

#### Creating a New Work Order
1. Navigate to Manufacturing > Work Orders
2. Click the "Add Work Order" button
3. Fill in work order details:
   - Sales Order - Associated customer order (optional)
   - Product - Product to manufacture
   - Quantity Planned - Number of units to produce
   - Machine - Equipment to use
   - Assigned To - Personnel responsible
   - Start Date - Planned start date
   - End Date - Planned completion date
   - Priority - Importance level
4. Click "Save" to create the work order

#### Starting a Work Order
1. Navigate to Manufacturing > Work Orders
2. Find the work order to start
3. Click on the work order to open the details page
4. Click the "Start" button
5. Confirm material reservation
6. The work order status will change to "In Progress"

#### Recording Material Consumption
1. Navigate to Manufacturing > Work Orders
2. Find the work order in progress
3. Click on the work order to open the details page
4. Scroll to the "Material Consumption" section
5. Click "Add Consumption"
6. Select the material
7. Enter actual quantity used
8. Enter wastage quantity if applicable
9. Click "Save" to record consumption

#### Completing a Work Order
1. Navigate to Manufacturing > Work Orders
2. Find the work order to complete
3. Click on the work order to open the details page
4. Click the "Complete" button
5. Enter the quantity produced
6. Enter the quantity rejected (if any)
7. Click "Complete" to finish the work order
8. The system will automatically:
   - Update inventory with finished goods
   - Release material reservations
   - Calculate yield percentage

### Managing Machines

#### Creating a New Machine
1. Navigate to Manufacturing > Machines
2. Click the "Add Machine" button
3. Fill in machine details:
   - Name - Descriptive name
   - Code - Unique identifier
   - Type - Machine type classification
   - Status - Current availability
   - Location - Physical location
4. Click "Save" to create the machine

## System Administration

### User Management

#### Creating New Users
1. Navigate to Settings > Users
2. Click the "Add User" button
3. Fill in user details:
   - Name - Full name
   - Email - Email address (used for login)
   - Role - User permissions level
   - Password - Initial password
4. Click "Save" to create the user

#### Assigning Roles
1. Navigate to Settings > Users
2. Find the user to modify
3. Click on the user to open the details page
4. Click the "Edit" button
5. Select the appropriate role from the dropdown
6. Click "Save" to update the user

### Business Settings

#### Updating Business Information
1. Navigate to Settings > Business
2. Click the "Edit" button
3. Update business details:
   - Name - Business name
   - Email - Primary business email
   - Phone - Primary business phone
   - Address - Business address
4. Click "Save" to update business information

### System Configuration

#### Configuring Notifications
1. Navigate to Settings > Notifications
2. Configure notification preferences:
   - Email notifications
   - System alerts
   - Reminder settings
3. Click "Save" to apply changes

## Troubleshooting

### Common Issues and Solutions

#### Login Problems
- Issue: Cannot log in with correct credentials
- Solution: Check that Caps Lock is off and try resetting your password

#### Slow Performance
- Issue: System is running slowly
- Solution: Clear browser cache and try a different browser

#### Data Not Loading
- Issue: Pages are not loading or showing errors
- Solution: Check internet connection and refresh the page

#### Missing Features
- Issue: Cannot find a specific feature
- Solution: Check user permissions or contact system administrator

### Contact Support
If you encounter issues not covered in this guide:
1. Document the problem with screenshots if possible
2. Note the steps you took before the issue occurred
3. Contact your system administrator or support team
4. Provide detailed information about the problem

## Glossary

- BOM: Bill of Materials - A list of materials required to manufacture a product
- CRM: Customer Relationship Management - Tools for managing customer interactions
- GSTIN: Goods and Services Tax Identification Number - Indian tax ID
- SKU: Stock Keeping Unit - Unique identifier for inventory items
- WIP: Work in Progress - Products that are partially completed
- Yield: The percentage of good products produced compared to total production

## Feedback and Suggestions

We welcome your feedback to improve Monitor Bizz. To provide feedback:
1. Navigate to Settings > Feedback
2. Fill in your comments and suggestions
3. Click "Submit" to send your feedback

Your input helps us make Monitor Bizz better for everyone.
