# Monitorbizz - Improved Data Flow & Architecture

## Current State Analysis

### Current Multi-Tenancy
- ❌ Subdomain-based (complex setup)
- ✅ Business isolation via `business_id`
- ✅ Basic user-business relationship

### Current Features
- Materials management
- Vendors management
- Purchase Orders
- Machines
- Basic inventory tracking

---

## Proposed Improved Architecture

### 1. Multi-Tenant Platform (Single Domain)

**Access Model:**
- Single platform: `monitorbizz.com`
- Business selection after login
- No subdomain complexity
- Easy business switching for users with multiple businesses

**Business Registration Flow:**
```
1. User registers → Creates account
2. User creates business → Becomes owner
3. User can create multiple businesses
4. User can be invited to other businesses
5. Login → Select business → Work in that context
```

---

## 2. CRM Features Integration

### A. Customer Management
```
customers
├── id
├── business_id
├── customer_code (auto: CUST-001)
├── name
├── email
├── phone
├── company_name
├── gstin
├── billing_address
├── shipping_address
├── customer_type (retail/wholesale/distributor)
├── credit_limit
├── payment_terms (days)
├── status (active/inactive/blocked)
├── tags (JSON)
├── notes
└── timestamps
```

### B. Leads Management
```
leads
├── id
├── business_id
├── assigned_to (user_id)
├── lead_source (website/referral/cold_call)
├── company_name
├── contact_person
├── email
├── phone
├── status (new/contacted/qualified/converted/lost)
├── estimated_value
├── probability (%)
├── expected_close_date
├── notes
└── timestamps
```

### C. Quotations/Estimates
```
quotations
├── id
├── business_id
├── customer_id
├── lead_id (nullable)
├── quote_number (QT-2025-001)
├── quote_date
├── valid_until
├── status (draft/sent/accepted/rejected/expired)
├── subtotal
├── tax_amount
├── discount_amount
├── total_amount
├── terms_conditions
├── notes
└── timestamps

quotation_items
├── id
├── quotation_id
├── item_type (material/service/product)
├── item_id
├── description
├── quantity
├── unit_price
├── tax_rate
├── discount_percent
├── total_price
└── timestamps
```

### D. Sales Orders
```
sales_orders
├── id
├── business_id
├── customer_id
├── quotation_id (nullable)
├── order_number (SO-2025-001)
├── order_date
├── delivery_date
├── status (pending/confirmed/in_production/shipped/delivered/cancelled)
├── priority (low/medium/high/urgent)
├── payment_status (unpaid/partial/paid)
├── subtotal
├── tax_amount
├── shipping_cost
├── total_amount
├── notes
└── timestamps
```

---

## 3. ERP Features Integration

### A. Enhanced Inventory Management
```
products (finished goods)
├── id
├── business_id
├── product_code (PRD-001)
├── name
├── description
├── category_id
├── unit
├── selling_price
├── cost_price
├── reorder_level
├── current_stock
├── bom_id (bill of materials)
├── manufacturing_time (hours)
├── is_manufactured (boolean)
├── is_saleable (boolean)
├── images (JSON)
└── timestamps

stock_movements
├── id
├── business_id
├── item_type (material/product)
├── item_id
├── movement_type (in/out/transfer/adjustment)
├── quantity
├── from_location
├── to_location
├── reference_type (sales_order/work_order/purchase_order)
├── reference_id
├── notes
└── timestamps
```

### B. Bill of Materials (BOM)
```
boms
├── id
├── business_id
├── product_id
├── version
├── quantity (output quantity)
├── is_active
└── timestamps

bom_items
├── id
├── bom_id
├── material_id
├── quantity_required
├── unit
├── wastage_percent
└── timestamps
```

### C. Enhanced Work Orders (Manufacturing)
```
work_orders
├── id
├── business_id
├── sales_order_id (nullable)
├── work_order_number (WO-2025-001)
├── product_id
├── quantity_planned
├── quantity_produced
├── quantity_rejected
├── machine_id
├── assigned_to (user_id)
├── start_date
├── end_date
├── status (draft/scheduled/in_progress/completed/cancelled)
├── priority (low/medium/high)
├── actual_start_time
├── actual_end_time
├── notes
└── timestamps

work_order_operations
├── id
├── work_order_id
├── operation_name
├── machine_id
├── operator_id (user_id)
├── sequence
├── planned_duration (minutes)
├── actual_duration (minutes)
├── status (pending/in_progress/completed)
├── notes
└── timestamps

work_order_material_consumption
├── id
├── work_order_id
├── material_id
├── planned_quantity
├── actual_quantity
├── wastage_quantity
├── batch_number
└── timestamps
```

### D. Invoicing & Payments
```
invoices
├── id
├── business_id
├── customer_id
├── sales_order_id (nullable)
├── invoice_number (INV-2025-001)
├── invoice_date
├── due_date
├── status (draft/sent/paid/overdue/cancelled)
├── subtotal
├── tax_amount
├── discount_amount
├── total_amount
├── paid_amount
├── balance_amount
├── payment_terms
└── timestamps

payments
├── id
├── business_id
├── invoice_id
├── payment_number (PAY-2025-001)
├── payment_date
├── amount
├── payment_method (cash/upi/bank_transfer/cheque/card)
├── reference_number
├── notes
└── timestamps
```

### E. Expenses & Accounting
```
expenses
├── id
├── business_id
├── category (material/labor/overhead/utilities/maintenance)
├── expense_date
├── amount
├── vendor_id (nullable)
├── payment_method
├── reference_number
├── description
├── receipt_image
└── timestamps

accounts
├── id
├── business_id
├── account_type (asset/liability/income/expense/equity)
├── account_name
├── parent_account_id
├── balance
└── timestamps

transactions
├── id
├── business_id
├── transaction_date
├── transaction_type (debit/credit)
├── account_id
├── amount
├── reference_type (invoice/payment/expense)
├── reference_id
├── description
└── timestamps
```

---

## 4. Complete Data Flow

### Typical Workshop Flow

```
1. SALES PROCESS
   ├── Lead captured → leads
   ├── Lead qualified → quotation created
   ├── Quote accepted → sales_order created
   └── Payment received → invoice + payment

2. PROCUREMENT
   ├── Low stock alert → materials (reorder_level)
   ├── Create purchase_order → vendors
   ├── PO approved → awaiting delivery
   └── Materials received → inventory_batches + stock_movements

3. PRODUCTION
   ├── Sales order → work_order created
   ├── Check BOM → bom_items
   ├── Reserve materials → stock_movements (reserved)
   ├── Assign machine → machines
   ├── Start production → work_order_operations
   ├── Consume materials → work_order_material_consumption + stock_movements
   ├── Complete production → products stock increased
   └── Quality check → quantity_produced vs quantity_rejected

4. DELIVERY & INVOICING
   ├── Work order completed → update sales_order (ready_to_ship)
   ├── Ship product → stock_movements (out)
   ├── Generate invoice → invoices
   └── Receive payment → payments

5. REPORTING & ANALYTICS
   ├── Material consumption analysis
   ├── Machine utilization reports
   ├── Profitability by product
   ├── Customer payment trends
   └── Production efficiency metrics
```

---

## 5. Key Improvements Over Current System

### Multi-Tenancy
- ❌ Remove subdomain complexity
- ✅ Single domain with business context
- ✅ Easy business switching
- ✅ Better user experience

### CRM Integration
- ✅ Lead to customer conversion
- ✅ Quote to order workflow
- ✅ Customer credit management
- ✅ Payment tracking
- ✅ Customer analytics

### ERP Integration
- ✅ Complete inventory tracking
- ✅ Bill of Materials (BOM)
- ✅ Material consumption tracking
- ✅ Work order management
- ✅ Basic accounting
- ✅ Expense management
- ✅ Profitability analysis

### Manufacturing Focus
- ✅ Machine-centric operations
- ✅ Real-time production tracking
- ✅ Wastage monitoring
- ✅ Quality control
- ✅ Batch traceability

---

## 6. API Endpoints Structure

```
/api/auth
├── POST /register
├── POST /login
├── POST /logout
└── GET /me

/api/businesses
├── GET / (user's businesses)
├── POST / (create business)
├── GET /{id}
├── PUT /{id}
└── POST /{id}/switch (switch context)

/api/crm/customers
├── GET /
├── POST /
├── GET /{id}
├── PUT /{id}
└── DELETE /{id}

/api/crm/leads
├── GET /
├── POST /
├── GET /{id}
├── PUT /{id}
├── POST /{id}/convert (to customer)
└── DELETE /{id}

/api/crm/quotations
├── GET /
├── POST /
├── GET /{id}
├── PUT /{id}
├── POST /{id}/send
├── POST /{id}/accept
└── POST /{id}/convert-to-order

/api/sales/orders
├── GET /
├── POST /
├── GET /{id}
├── PUT /{id}
└── POST /{id}/confirm

/api/inventory/materials
├── (existing endpoints)
└── GET /{id}/stock-movements

/api/inventory/products
├── GET /
├── POST /
├── GET /{id}
├── PUT /{id}
└── GET /{id}/bom

/api/manufacturing/work-orders
├── GET /
├── POST /
├── GET /{id}
├── PUT /{id}
├── POST /{id}/start
├── POST /{id}/complete
└── POST /{id}/consume-material

/api/manufacturing/machines
├── (existing endpoints)
├── GET /{id}/utilization
└── GET /{id}/work-orders

/api/finance/invoices
├── GET /
├── POST /
├── GET /{id}
├── PUT /{id}
├── POST /{id}/send
└── POST /{id}/record-payment

/api/finance/expenses
├── GET /
├── POST /
├── GET /{id}
├── PUT /{id}
└── DELETE /{id}

/api/reports
├── GET /sales-summary
├── GET /production-efficiency
├── GET /inventory-valuation
├── GET /customer-payments
└── GET /profitability
```

---

## 7. Priority Implementation Order

### Phase 1: Core CRM (Week 1-2)
1. ✅ Remove subdomain requirement
2. ✅ Business context switching
3. ✅ Customer management
4. ✅ Quotation system
5. ✅ Sales orders

### Phase 2: Enhanced Manufacturing (Week 3-4)
1. ✅ Products & BOM
2. ✅ Enhanced work orders
3. ✅ Material consumption tracking
4. ✅ Stock movements
5. ✅ Quality tracking (rejected qty)

### Phase 3: Finance & Invoicing (Week 5-6)
1. ✅ Invoice generation
2. ✅ Payment tracking
3. ✅ Expense management
4. ✅ Basic accounting ledger

### Phase 4: Analytics & Reporting (Week 7-8)
1. ✅ Production reports
2. ✅ Sales analytics
3. ✅ Inventory reports
4. ✅ Financial summaries

---

## 8. Reference Apps for Features

### CRM Features (inspired by)
- **Zoho CRM**: Lead management, pipeline
- **HubSpot**: Contact management, deals
- **Salesforce**: Opportunity tracking

### ERP Features (inspired by)
- **Odoo**: Manufacturing, inventory
- **ERPNext**: BOM, work orders
- **SAP Business One**: Integrated workflows

### Manufacturing Focus (inspired by)
- **Katana MRP**: Production planning
- **MRPeasy**: Shop floor control
- **Fishbowl**: Inventory + manufacturing

---

## Next Steps

1. Confirm this data flow approach
2. Create database migrations for new tables
3. Update API controllers
4. Build UI workflows
5. Add comprehensive API documentation

**What should we prioritize first?**
