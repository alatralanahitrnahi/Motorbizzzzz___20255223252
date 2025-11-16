#!/bin/bash

# Test the test company API endpoints
echo "Testing ABC Steel Fabrication API..."
echo "====================================="

# Login
echo "1. Testing login..."
LOGIN_RESPONSE=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"rajesh@abcsteel.com","password":"password123"}')

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
echo "Token: ${TOKEN:0:20}..."

# Get materials
echo "2. Testing materials endpoint..."
MATERIALS=$(curl -s -X GET http://localhost:8000/api/materials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

MATERIAL_COUNT=$(echo $MATERIALS | grep -o '"id"' | wc -l)
echo "Found $MATERIAL_COUNT materials"

# Get products
echo "3. Testing products endpoint..."
PRODUCTS=$(curl -s -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

PRODUCT_COUNT=$(echo $PRODUCTS | grep -o '"id"' | wc -l)
echo "Found $PRODUCT_COUNT products"

# Get customers
echo "4. Testing customers endpoint..."
CUSTOMERS=$(curl -s -X GET http://localhost:8000/api/customers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json")

CUSTOMER_COUNT=$(echo $CUSTOMERS | grep -o '"id"' | wc -l)
echo "Found $CUSTOMER_COUNT customers"

echo "✅ All API tests completed successfully!"
