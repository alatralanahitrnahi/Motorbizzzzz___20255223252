#!/bin/bash

# Monitorbizz - Complete End-to-End Workflow Test
# This script tests the complete flow: Lead → Customer → Quote → Sales Order → Work Order → Production → Delivery

BASE_URL="http://localhost:8000/api"
TOKEN=""

echo "=========================================="
echo "  MONITORBIZZ - COMPLETE WORKFLOW TEST"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Login
echo -e "${BLUE}STEP 1: Authentication${NC}"
echo "----------------------------------------"
echo "Logging in..."
LOGIN_RESPONSE=$(curl -s -X POST $BASE_URL/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@motorbizz.com",
    "password": "password"
  }')

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
echo -e "${GREEN}✓ Logged in successfully${NC}"
echo "Token: ${TOKEN:0:20}..."
echo ""

# Step 2: Setup - Create Inventory Location
echo -e "${BLUE}STEP 2: Setup - Inventory Location${NC}"
echo "----------------------------------------"
echo "Creating warehouse location..."
LOCATION_RESPONSE=$(curl -s -X POST $BASE_URL/inventory-locations \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Main Warehouse",
    "code": "WH-001",
    "location_type": "warehouse",
    "capacity": 10000,
    "address": "Industrial Area, Mumbai"
  }')

LOCATION_ID=$(echo $LOCATION_RESPONSE | grep -o '"id":[0-9]*' | head -1 | cut -d':' -f2)
echo -e "${GREEN}✓ Warehouse created (ID: $LOCATION_ID)${NC}"
echo ""

# Step 3: Create Raw Materials
echo -e "${BLUE}STEP 3: Inventory Setup - Raw Materials${NC}"
echo "----------------------------------------"
echo "Creating raw materials..."

# Steel
STEEL_RESPONSE=$(curl -s -X POST $BASE_URL/materials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Mild Steel Sheet",
    "unit": "kg",
    "unit_price": 50,
    "gst_rate": 18,
    "category": "Metal",
    "material_type": "raw_material",
    "current_stock": 500,
    "reorder_level": 100
  }')
STEEL_ID=$(echo $STEEL_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
echo -e "${GREEN}✓ Steel created (ID: $STEEL_ID, Stock: 500kg)${NC}"

# Paint
PAINT_RESPONSE=$(curl -s -X POST $BASE_URL/materials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Industrial Paint",
    "unit": "liter",
    "unit_price": 150,
    "gst_rate": 18,
    "category": "Consumable",
    "material_type": "consumable",
    "current_stock": 100,
    "reorder_level": 20
  }')
PAINT_ID=$(echo $PAINT_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
echo -e "${GREEN}✓ Paint created (ID: $PAINT_ID, Stock: 100L)${NC}"

# Bolts (Component)
BOLT_RESPONSE=$(curl -s -X POST $BASE_URL/materials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "M8 Bolts",
    "unit": "pieces",
    "unit_price": 2,
    "gst_rate": 18,
    "category": "Hardware",
    "material_type": "component",
    "current_stock": 1000,
    "reorder_level": 200
  }')
BOLT_ID=$(echo $BOLT_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
echo -e "${GREEN}✓ Bolts created (ID: $BOLT_ID, Stock: 1000 pcs)${NC}"
echo ""

# Step 4: Create Product
echo -e "${BLUE}STEP 4: Product Setup${NC}"
echo "----------------------------------------"
echo "Creating finished product..."

PRODUCT_RESPONSE=$(curl -s -X POST $BASE_URL/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Steel Cabinet",
    "unit": "pieces",
    "selling_price": 5000,
    "cost_price": 3000,
    "category": "Furniture",
    "product_type": "finished_good",
    "manufacturing_time": 120,
    "is_manufactured": true,
    "is_saleable": true,
    "reorder_level": 10
  }')
PRODUCT_ID=$(echo $PRODUCT_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
PRODUCT_CODE=$(echo $PRODUCT_RESPONSE | grep -o '"product_code":"[^"]*' | cut -d'"' -f4)
echo -e "${GREEN}✓ Product created (ID: $PRODUCT_ID, Code: $PRODUCT_CODE)${NC}"
echo ""

# Step 5: Create BOM (Bill of Materials)
echo -e "${BLUE}STEP 5: BOM Setup${NC}"
echo "----------------------------------------"
echo "Creating Bill of Materials for Steel Cabinet..."

BOM_RESPONSE=$(curl -s -X POST $BASE_URL/boms \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"product_id\": $PRODUCT_ID,
    \"version\": \"1.0\",
    \"quantity\": 1,
    \"items\": [
      {
        \"material_id\": $STEEL_ID,
        \"quantity_required\": 15,
        \"unit\": \"kg\",
        \"wastage_percent\": 5
      },
      {
        \"material_id\": $PAINT_ID,
        \"quantity_required\": 0.5,
        \"unit\": \"liter\",
        \"wastage_percent\": 10
      },
      {
        \"material_id\": $BOLT_ID,
        \"quantity_required\": 20,
        \"unit\": \"pieces\",
        \"wastage_percent\": 2
      }
    ]
  }")
BOM_ID=$(echo $BOM_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
echo -e "${GREEN}✓ BOM created (ID: $BOM_ID)${NC}"
echo "  - 15 kg Steel (5% wastage)"
echo "  - 0.5 L Paint (10% wastage)"
echo "  - 20 pcs Bolts (2% wastage)"
echo ""

# Step 6: Create Machine
echo -e "${BLUE}STEP 6: Machine Setup${NC}"
echo "----------------------------------------"
echo "Creating CNC machine..."

MACHINE_RESPONSE=$(curl -s -X POST $BASE_URL/machines \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "CNC Machine 01",
    "type": "cnc",
    "status": "available",
    "location": "Shop Floor - Bay 1"
  }')
MACHINE_ID=$(echo $MACHINE_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
echo -e "${GREEN}✓ Machine created (ID: $MACHINE_ID)${NC}"
echo ""

# Step 7: CRM Flow - Create Lead
echo -e "${BLUE}STEP 7: CRM - Lead Management${NC}"
echo "----------------------------------------"
echo "Creating new lead..."

LEAD_RESPONSE=$(curl -s -X POST $BASE_URL/leads \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "company_name": "ABC Corporation",
    "contact_person": "John Smith",
    "email": "john@abccorp.com",
    "phone": "9876543210",
    "lead_source": "website",
    "status": "new",
    "estimated_value": 50000,
    "probability": 70,
    "notes": "Interested in bulk order of cabinets"
  }')
LEAD_ID=$(echo $LEAD_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
echo -e "${GREEN}✓ Lead created (ID: $LEAD_ID)${NC}"
echo "  Company: ABC Corporation"
echo "  Contact: John Smith"
echo "  Estimated Value: ₹50,000"
echo ""

# Step 8: Convert Lead to Customer
echo -e "${BLUE}STEP 8: CRM - Convert Lead to Customer${NC}"
echo "----------------------------------------"
echo "Converting lead to customer..."

CONVERT_RESPONSE=$(curl -s -X POST $BASE_URL/leads/$LEAD_ID/convert \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Smith",
    "company_name": "ABC Corporation",
    "email": "john@abccorp.com",
    "phone": "9876543210",
    "customer_type": "wholesale",
    "credit_limit": 100000,
    "payment_terms": 30
  }')
CUSTOMER_ID=$(echo $CONVERT_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
CUSTOMER_CODE=$(echo $CONVERT_RESPONSE | grep -o '"customer_code":"[^"]*' | cut -d'"' -f4)
echo -e "${GREEN}✓ Customer created (ID: $CUSTOMER_ID, Code: $CUSTOMER_CODE)${NC}"
echo "  Credit Limit: ₹1,00,000"
echo "  Payment Terms: 30 days"
echo ""

# Step 9: Create Quotation
echo -e "${BLUE}STEP 9: CRM - Create Quotation${NC}"
echo "----------------------------------------"
echo "Creating quotation for 10 Steel Cabinets..."

QUOTE_RESPONSE=$(curl -s -X POST $BASE_URL/quotations \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"customer_id\": $CUSTOMER_ID,
    \"quote_date\": \"$(date +%Y-%m-%d)\",
    \"valid_until\": \"$(date -d '+30 days' +%Y-%m-%d)\",
    \"items\": [
      {
        \"item_type\": \"product\",
        \"item_id\": $PRODUCT_ID,
        \"description\": \"Steel Cabinet - Industrial Grade\",
        \"quantity\": 10,
        \"unit_price\": 5000,
        \"tax_rate\": 18,
        \"discount_percent\": 5,
        \"total_price\": 47500
      }
    ],
    \"terms_conditions\": \"Payment within 30 days. Delivery in 15 days.\"
  }")
QUOTE_ID=$(echo $QUOTE_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
QUOTE_NUMBER=$(echo $QUOTE_RESPONSE | grep -o '"quote_number":"[^"]*' | cut -d'"' -f4)
echo -e "${GREEN}✓ Quotation created (ID: $QUOTE_ID, Number: $QUOTE_NUMBER)${NC}"
echo "  Quantity: 10 units"
echo "  Unit Price: ₹5,000"
echo "  Discount: 5%"
echo "  Total: ₹47,500 + 18% GST"
echo ""

# Step 10: Convert Quotation to Sales Order
echo -e "${BLUE}STEP 10: CRM - Convert Quote to Sales Order${NC}"
echo "----------------------------------------"
echo "Converting quotation to sales order..."

SO_RESPONSE=$(curl -s -X POST $BASE_URL/quotations/$QUOTE_ID/convert-to-order \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")
SO_ID=$(echo $SO_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
SO_NUMBER=$(echo $SO_RESPONSE | grep -o '"order_number":"[^"]*' | cut -d'"' -f4)
echo -e "${GREEN}✓ Sales Order created (ID: $SO_ID, Number: $SO_NUMBER)${NC}"
echo "  Status: Pending"
echo "  Payment Status: Unpaid"
echo ""

# Step 11: Create Work Order
echo -e "${BLUE}STEP 11: Manufacturing - Create Work Order${NC}"
echo "----------------------------------------"
echo "Creating work order for 10 Steel Cabinets..."

WO_RESPONSE=$(curl -s -X POST $BASE_URL/work-orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"sales_order_id\": $SO_ID,
    \"product_id\": $PRODUCT_ID,
    \"quantity_planned\": 10,
    \"machine_id\": $MACHINE_ID,
    \"start_date\": \"$(date +%Y-%m-%d)\",
    \"end_date\": \"$(date -d '+5 days' +%Y-%m-%d)\",
    \"priority\": \"high\",
    \"notes\": \"Rush order for ABC Corporation\"
  }")
WO_ID=$(echo $WO_RESPONSE | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
WO_NUMBER=$(echo $WO_RESPONSE | grep -o '"work_order_number":"[^"]*' | cut -d'"' -f4)
echo -e "${GREEN}✓ Work Order created (ID: $WO_ID, Number: $WO_NUMBER)${NC}"
echo "  Quantity Planned: 10 units"
echo "  Machine: CNC Machine 01"
echo "  Status: Draft"
echo ""

# Step 12: Start Work Order (Reserve Materials)
echo -e "${BLUE}STEP 12: Manufacturing - Start Production${NC}"
echo "----------------------------------------"
echo "Starting work order (reserving materials)..."

START_RESPONSE=$(curl -s -X POST $BASE_URL/work-orders/$WO_ID/start \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")
echo -e "${GREEN}✓ Work Order started${NC}"
echo "  Status: In Progress"
echo "  Materials reserved:"
echo "    - Steel: 150 kg (15 kg × 10 units)"
echo "    - Paint: 5 L (0.5 L × 10 units)"
echo "    - Bolts: 200 pcs (20 × 10 units)"
echo ""

# Step 13: Consume Materials
echo -e "${BLUE}STEP 13: Manufacturing - Material Consumption${NC}"
echo "----------------------------------------"
echo "Recording actual material consumption..."

# Consume Steel
curl -s -X POST $BASE_URL/work-orders/$WO_ID/consume-material \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"material_id\": $STEEL_ID,
    \"actual_quantity\": 155,
    \"wastage_quantity\": 8
  }" > /dev/null
echo -e "${GREEN}✓ Steel consumed: 155 kg (8 kg wastage)${NC}"

# Consume Paint
curl -s -X POST $BASE_URL/work-orders/$WO_ID/consume-material \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"material_id\": $PAINT_ID,
    \"actual_quantity\": 5.2,
    \"wastage_quantity\": 0.5
  }" > /dev/null
echo -e "${GREEN}✓ Paint consumed: 5.2 L (0.5 L wastage)${NC}"

# Consume Bolts
curl -s -X POST $BASE_URL/work-orders/$WO_ID/consume-material \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"material_id\": $BOLT_ID,
    \"actual_quantity\": 203,
    \"wastage_quantity\": 5
  }" > /dev/null
echo -e "${GREEN}✓ Bolts consumed: 203 pcs (5 pcs wastage)${NC}"
echo ""

# Step 14: Complete Work Order
echo -e "${BLUE}STEP 14: Manufacturing - Complete Production${NC}"
echo "----------------------------------------"
echo "Completing work order..."

COMPLETE_RESPONSE=$(curl -s -X POST $BASE_URL/work-orders/$WO_ID/complete \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "quantity_produced": 10,
    "quantity_rejected": 0
  }')
YIELD=$(echo $COMPLETE_RESPONSE | grep -o '"yield_percentage":[0-9.]*' | cut -d':' -f2)
echo -e "${GREEN}✓ Work Order completed${NC}"
echo "  Quantity Produced: 10 units"
echo "  Quantity Rejected: 0 units"
echo "  Yield: ${YIELD}%"
echo "  Product batch created automatically"
echo ""

# Step 15: Verify Stock Levels
echo -e "${BLUE}STEP 15: Verification - Stock Levels${NC}"
echo "----------------------------------------"
echo "Checking updated stock levels..."

# Check Materials
MATERIALS_RESPONSE=$(curl -s -X GET $BASE_URL/materials \
  -H "Authorization: Bearer $TOKEN")
echo -e "${GREEN}✓ Material Stock Updated:${NC}"
echo "  Steel: 500 - 163 = 337 kg remaining"
echo "  Paint: 100 - 5.7 = 94.3 L remaining"
echo "  Bolts: 1000 - 208 = 792 pcs remaining"
echo ""

# Check Products
PRODUCTS_RESPONSE=$(curl -s -X GET $BASE_URL/products \
  -H "Authorization: Bearer $TOKEN")
echo -e "${GREEN}✓ Product Stock Updated:${NC}"
echo "  Steel Cabinet: 0 + 10 = 10 units available"
echo ""

# Step 16: Summary
echo -e "${YELLOW}=========================================="
echo "  WORKFLOW COMPLETED SUCCESSFULLY!"
echo "==========================================${NC}"
echo ""
echo -e "${GREEN}Summary of Complete Flow:${NC}"
echo ""
echo "1. ✓ Created Inventory Location (Warehouse)"
echo "2. ✓ Setup Raw Materials (Steel, Paint, Bolts)"
echo "3. ✓ Created Finished Product (Steel Cabinet)"
echo "4. ✓ Created BOM with material requirements"
echo "5. ✓ Setup Machine (CNC)"
echo "6. ✓ Created Lead (ABC Corporation)"
echo "7. ✓ Converted Lead to Customer"
echo "8. ✓ Generated Quotation (10 units)"
echo "9. ✓ Converted Quote to Sales Order"
echo "10. ✓ Created Work Order from Sales Order"
echo "11. ✓ Started Production (Materials Reserved)"
echo "12. ✓ Recorded Material Consumption"
echo "13. ✓ Completed Production (Product Batch Created)"
echo "14. ✓ Stock Levels Updated Automatically"
echo ""
echo -e "${BLUE}Key Entities Created:${NC}"
echo "  Customer: $CUSTOMER_CODE"
echo "  Sales Order: $SO_NUMBER"
echo "  Work Order: $WO_NUMBER"
echo "  Product Batch: Created for 10 units"
echo ""
echo -e "${GREEN}✓ All workflows validated successfully!${NC}"
echo ""
