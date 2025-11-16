# Monitorbizz API Testing Guide

## Base URL
```
http://localhost:8000/api
```

## Authentication

### 1. Register New Business & User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "business_name": "Johns Workshop",
    "phone": "9876543210",
    "address": "123 Main Street"
  }'
```

**Response:**
```json
{
  "message": "Registration successful",
  "user": {...},
  "business": {...},
  "token": "1|xxxxxxxxxxxxx"
}
```

### 2. Login (Get Token)
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@motorbizz.com",
    "password": "password"
  }'
```

**Response:**
```json
{
  "message": "Login successful",
  "user": {...},
  "token": "2|xxxxxxxxxxxxx"
}
```

**Save the token for subsequent requests!**

### 3. Get Current User
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 4. Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Materials API

### List All Materials
```bash
curl -X GET http://localhost:8000/api/materials \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Material
```bash
curl -X POST http://localhost:8000/api/materials \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Mild Steel",
    "code": "MS-001",
    "unit": "kg",
    "current_stock": 100,
    "reorder_level": 20,
    "description": "High quality mild steel"
  }'
```

### Get Single Material
```bash
curl -X GET http://localhost:8000/api/materials/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Update Material
```bash
curl -X PUT http://localhost:8000/api/materials/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Mild Steel Updated",
    "code": "MS-001",
    "unit": "kg",
    "current_stock": 150,
    "reorder_level": 25,
    "description": "Updated description"
  }'
```

### Delete Material
```bash
curl -X DELETE http://localhost:8000/api/materials/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Vendors API

### List All Vendors
```bash
curl -X GET http://localhost:8000/api/vendors \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Vendor
```bash
curl -X POST http://localhost:8000/api/vendors \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "ABC Steel Suppliers",
    "email": "abc@steelsuppliers.com",
    "phone": "9876543210",
    "address": "Industrial Area, Mumbai",
    "gstin": "27XXXXX1234X1Z5"
  }'
```

### Get Single Vendor
```bash
curl -X GET http://localhost:8000/api/vendors/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Update Vendor
```bash
curl -X PUT http://localhost:8000/api/vendors/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "ABC Steel Suppliers Updated",
    "email": "abc@steelsuppliers.com",
    "phone": "9876543211",
    "address": "New Industrial Area, Mumbai",
    "gstin": "27XXXXX1234X1Z5"
  }'
```

### Delete Vendor
```bash
curl -X DELETE http://localhost:8000/api/vendors/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Machines API

### List All Machines
```bash
curl -X GET http://localhost:8000/api/machines \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Machine
```bash
curl -X POST http://localhost:8000/api/machines \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "CNC Machine 01",
    "machine_type": "CNC",
    "status": "operational",
    "purchase_date": "2024-01-15",
    "last_maintenance_date": "2025-01-01",
    "next_maintenance_date": "2025-04-01"
  }'
```

**Status values:** `operational`, `maintenance`, `down`

### Get Single Machine
```bash
curl -X GET http://localhost:8000/api/machines/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Update Machine
```bash
curl -X PUT http://localhost:8000/api/machines/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "CNC Machine 01 Updated",
    "machine_type": "CNC",
    "status": "maintenance",
    "purchase_date": "2024-01-15",
    "last_maintenance_date": "2025-01-15",
    "next_maintenance_date": "2025-04-15"
  }'
```

### Delete Machine
```bash
curl -X DELETE http://localhost:8000/api/machines/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Complete Testing Flow

### Step 1: Login and Get Token
```bash
TOKEN=$(curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@motorbizz.com","password":"password"}' \
  -s | grep -o '"token":"[^"]*' | cut -d'"' -f4)

echo "Your token: $TOKEN"
```

### Step 2: Create a Material
```bash
curl -X POST http://localhost:8000/api/materials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Material",
    "code": "TM-001",
    "unit": "pcs",
    "current_stock": 50,
    "reorder_level": 10,
    "description": "Test material via API"
  }'
```

### Step 3: List All Materials
```bash
curl -X GET http://localhost:8000/api/materials \
  -H "Authorization: Bearer $TOKEN"
```

### Step 4: Create a Vendor
```bash
curl -X POST http://localhost:8000/api/vendors \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Vendor",
    "email": "vendor@test.com",
    "phone": "1234567890",
    "address": "Test Address"
  }'
```

### Step 5: Create a Machine
```bash
curl -X POST http://localhost:8000/api/machines \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Machine",
    "machine_type": "Lathe",
    "status": "operational",
    "purchase_date": "2025-01-01"
  }'
```

---

## Testing with jq (Pretty JSON)

If you have `jq` installed, you can format responses:

```bash
curl -X GET http://localhost:8000/api/materials \
  -H "Authorization: Bearer $TOKEN" -s | jq
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 422 Validation Error
```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

### 404 Not Found
```json
{
  "message": "No query results for model..."
}
```

---

## Quick Test Script

Save this as `test_api.sh`:

```bash
#!/bin/bash

# Login and get token
echo "Logging in..."
TOKEN=$(curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@motorbizz.com","password":"password"}' \
  -s | grep -o '"token":"[^"]*' | cut -d'"' -f4)

echo "Token: $TOKEN"
echo ""

# List materials
echo "Fetching materials..."
curl -X GET http://localhost:8000/api/materials \
  -H "Authorization: Bearer $TOKEN" -s | jq

echo ""

# Create material
echo "Creating new material..."
curl -X POST http://localhost:8000/api/materials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "API Test Material",
    "code": "API-001",
    "unit": "kg",
    "current_stock": 100,
    "reorder_level": 20
  }' -s | jq
```

Run: `chmod +x test_api.sh && ./test_api.sh`
