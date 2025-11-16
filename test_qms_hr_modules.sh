#!/bin/bash

# Test QMS and HR Modules API Endpoints

echo "Testing QMS and HR Modules API Endpoints"
echo "========================================"

# Set API base URL
API_BASE="http://localhost:8000/api"

# Login and get token (you'll need to replace with actual credentials)
echo "Logging in..."
LOGIN_RESPONSE=$(curl -s -X POST "$API_BASE/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}')

# Extract token from response
TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | grep -o '[^"]*$')

if [ -z "$TOKEN" ]; then
    echo "Login failed. Exiting."
    exit 1
fi

echo "Login successful. Token: $TOKEN"
echo ""

# Test QMS Endpoints
echo "Testing QMS Endpoints..."
echo "------------------------"

# Create Quality Standard
echo "Creating Quality Standard..."
curl -s -X POST "$API_BASE/quality-standards" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "ISO 9001:2015",
    "standard_code": "ISO9001",
    "description": "Quality management systems standard",
    "version": "2015"
  }' | jq '.'

echo ""

# Create Quality Checklist
echo "Creating Quality Checklist..."
curl -s -X POST "$API_BASE/quality-checklists" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Incoming Material Inspection",
    "checklist_type": "incoming",
    "description": "Checklist for incoming material inspection"
  }' | jq '.'

echo ""

# Test HR Endpoints
echo "Testing HR Endpoints..."
echo "-----------------------"

# Create Department
echo "Creating Department..."
curl -s -X POST "$API_BASE/departments" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Quality Assurance",
    "description": "Quality assurance department"
  }' | jq '.'

echo ""

# Create Job Position
echo "Creating Job Position..."
curl -s -X POST "$API_BASE/job-positions" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "department_id": 1,
    "title": "Quality Inspector",
    "description": "Responsible for quality inspections",
    "employment_type": "full_time",
    "min_salary": 40000,
    "max_salary": 60000
  }' | jq '.'

echo ""

# Create Training Program
echo "Creating Training Program..."
curl -s -X POST "$API_BASE/training-programs" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Quality Management Training",
    "description": "Training on quality management principles",
    "duration_hours": 16,
    "difficulty_level": "intermediate"
  }' | jq '.'

echo ""

echo "QMS and HR modules test completed!"