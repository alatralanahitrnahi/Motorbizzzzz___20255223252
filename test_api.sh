#!/bin/bash

echo "=== Monitorbizz API Testing ==="
echo ""

echo "1. Login to get token..."
RESPONSE=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@motorbizz.com","password":"password"}')

echo "$RESPONSE"
echo ""

TOKEN=$(echo "$RESPONSE" | grep -o '"token":"[^"]*' | cut -d'"' -f4)
echo "Token extracted: $TOKEN"
echo ""

echo "2. Fetching materials..."
curl -s -X GET http://localhost:8000/api/materials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
echo ""
echo ""

echo "3. Creating new material..."
curl -s -X POST http://localhost:8000/api/materials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "API Test Material",
    "code": "API-001",
    "unit": "kg",
    "current_stock": 100,
    "reorder_level": 20,
    "description": "Created via API"
  }'
echo ""
echo ""

echo "4. Fetching vendors..."
curl -s -X GET http://localhost:8000/api/vendors \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
echo ""
echo ""

echo "5. Creating new vendor..."
curl -s -X POST http://localhost:8000/api/vendors \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "API Test Vendor",
    "email": "vendor@api-test.com",
    "phone": "1234567890",
    "address": "API Test Address"
  }'
echo ""
echo ""

echo "6. Fetching machines..."
curl -s -X GET http://localhost:8000/api/machines \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
echo ""
echo ""

echo "7. Creating new machine..."
curl -s -X POST http://localhost:8000/api/machines \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "API Test Machine",
    "type": "cnc",
    "status": "available"
  }'
echo ""
echo ""

echo "=== API Testing Complete ==="
