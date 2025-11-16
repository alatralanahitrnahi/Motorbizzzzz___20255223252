# Compliance & Risk Management API Documentation

## Overview

This document describes the REST API endpoints for the Compliance & Risk Management modules of the Monitor Bizz application. These modules provide comprehensive compliance tracking and risk management capabilities.

## Base URL

```
http://localhost:8000/api
```

## Authentication

All API endpoints require a Bearer token for authentication.

**Headers:**
```
Authorization: Bearer <your_access_token>
Content-Type: application/json
```

## Compliance Management Endpoints

### Compliance Requirements

#### Get All Compliance Requirements
```
GET /compliance-requirements
```

**Query Parameters:**
- `search` (optional): Search term to filter requirements by name or description
- `category` (optional): Filter by category (e.g., 'regulatory', 'industry', 'internal')
- `status` (optional): Filter by status ('active', 'inactive', 'archived')
- `expiring_soon` (optional): Filter for requirements expiring within 30 days (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "name": "ISO 9001:2015 Quality Management",
      "description": "Quality management system requirements",
      "category": "regulatory",
      "authority": "ISO",
      "reference_number": "ISO 9001:2015",
      "effective_date": "2025-01-01",
      "expiry_date": "2028-12-31",
      "status": "active",
      "priority": 3,
      "notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Compliance Requirement
```
POST /compliance-requirements
```

**Request Body:**
```json
{
  "name": "string",
  "description": "string (optional)",
  "category": "string (optional)",
  "authority": "string (optional)",
  "reference_number": "string (optional)",
  "effective_date": "date (optional)",
  "expiry_date": "date (optional)",
  "status": "string (optional, default: 'active')",
  "priority": "integer (optional, 1-5, default: 1)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Compliance requirement created",
  "compliance_requirement": {...}
}
```

#### Get Compliance Requirement
```
GET /compliance-requirements/{id}
```

**Response:`
```json
{
  "compliance_requirement": {
    "id": 1,
    "business_id": 1,
    "name": "ISO 9001:2015 Quality Management",
    "description": "Quality management system requirements",
    "category": "regulatory",
    "authority": "ISO",
    "reference_number": "ISO 9001:2015",
    "effective_date": "2025-01-01",
    "expiry_date": "2028-12-31",
    "status": "active",
    "priority": 3,
    "notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "documents": [...],
    "audit_findings": [...],
    "responsible_users": [...]
  }
}
```

#### Update Compliance Requirement
```
PUT /compliance-requirements/{id}
```

**Request Body:**
```json
{
  "name": "string (optional)",
  "description": "string (optional)",
  "category": "string (optional)",
  "authority": "string (optional)",
  "reference_number": "string (optional)",
  "effective_date": "date (optional)",
  "expiry_date": "date (optional)",
  "status": "string (optional)",
  "priority": "integer (optional, 1-5)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Compliance requirement updated",
  "compliance_requirement": {...}
}
```

#### Delete Compliance Requirement
```
DELETE /compliance-requirements/{id}
```

**Response:**
```json
{
  "message": "Compliance requirement deleted"
}
```

### Compliance Documents

#### Get All Compliance Documents
```
GET /compliance-documents
```

**Query Parameters:**
- `search` (optional): Search term to filter documents by title or description
- `document_type` (optional): Filter by document type
- `status` (optional): Filter by status ('draft', 'approved', 'archived')
- `needs_review` (optional): Filter for documents needing review (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "compliance_requirement_id": 1,
      "title": "Quality Manual",
      "description": "Company quality management manual",
      "document_type": "policy",
      "file_path": "/storage/documents/quality_manual.pdf",
      "file_name": "quality_manual.pdf",
      "file_type": "application/pdf",
      "file_size": 1024000,
      "version": 2,
      "approved_by": 1,
      "approval_date": "2025-11-15",
      "effective_date": "2025-11-15",
      "review_date": "2026-11-15",
      "status": "approved",
      "notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Compliance Document
```
POST /compliance-documents
```

**Request Body:**
```json
{
  "compliance_requirement_id": "integer (optional)",
  "title": "string",
  "description": "string (optional)",
  "document_type": "string (optional)",
  "file_path": "string (optional)",
  "file_name": "string (optional)",
  "file_type": "string (optional)",
  "file_size": "integer (optional)",
  "version": "integer (optional, default: 1)",
  "approved_by": "integer (optional)",
  "approval_date": "date (optional)",
  "effective_date": "date (optional)",
  "review_date": "date (optional)",
  "status": "string (optional, default: 'draft')",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Compliance document created",
  "compliance_document": {...}
}
```

#### Get Compliance Document
```
GET /compliance-documents/{id}
```

**Response:**
```json
{
  "compliance_document": {
    "id": 1,
    "business_id": 1,
    "compliance_requirement_id": 1,
    "title": "Quality Manual",
    "description": "Company quality management manual",
    "document_type": "policy",
    "file_path": "/storage/documents/quality_manual.pdf",
    "file_name": "quality_manual.pdf",
    "file_type": "application/pdf",
    "file_size": 1024000,
    "version": 2,
    "approved_by": 1,
    "approval_date": "2025-11-15",
    "effective_date": "2025-11-15",
    "review_date": "2026-11-15",
    "status": "approved",
    "notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "compliance_requirement": {...},
    "approved_by_user": {...}
  }
}
```

#### Update Compliance Document
```
PUT /compliance-documents/{id}
```

**Request Body:**
```json
{
  "compliance_requirement_id": "integer (optional)",
  "title": "string (optional)",
  "description": "string (optional)",
  "document_type": "string (optional)",
  "file_path": "string (optional)",
  "file_name": "string (optional)",
  "file_type": "string (optional)",
  "file_size": "integer (optional)",
  "version": "integer (optional)",
  "approved_by": "integer (optional)",
  "approval_date": "date (optional)",
  "effective_date": "date (optional)",
  "review_date": "date (optional)",
  "status": "string (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Compliance document updated",
  "compliance_document": {...}
}
```

#### Delete Compliance Document
```
DELETE /compliance-documents/{id}
```

**Response:**
```json
{
  "message": "Compliance document deleted"
}
```

### Compliance Audits

#### Get All Compliance Audits
```
GET /compliance-audits
```

**Query Parameters:**
- `search` (optional): Search term to filter audits by title or description
- `audit_type` (optional): Filter by audit type ('internal', 'external', 'regulatory')
- `status` (optional): Filter by status ('planned', 'in_progress', 'completed', 'cancelled')
- `upcoming` (optional): Filter for audits planned within 7 days (boolean)
- `overdue` (optional): Filter for overdue audits (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "title": "Annual ISO 9001 Audit",
      "description": "Annual quality management system audit",
      "audit_type": "internal",
      "auditor_id": 2,
      "planned_date": "2025-12-15",
      "actual_date": null,
      "start_time": "09:00:00",
      "end_time": "17:00:00",
      "status": "planned",
      "scope": "All departments",
      "objectives": "Verify compliance with ISO 9001:2015",
      "findings_summary": null,
      "recommendations": null,
      "action_items": null,
      "follow_up_date": null,
      "notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Compliance Audit
```
POST /compliance-audits
```

**Request Body:**
```json
{
  "title": "string",
  "description": "string (optional)",
  "audit_type": "string (optional, 'internal', 'external', 'regulatory')",
  "auditor_id": "integer (optional)",
  "planned_date": "date (optional)",
  "actual_date": "date (optional)",
  "start_time": "time (optional)",
  "end_time": "time (optional)",
  "status": "string (optional, default: 'planned')",
  "scope": "string (optional)",
  "objectives": "string (optional)",
  "findings_summary": "string (optional)",
  "recommendations": "string (optional)",
  "action_items": "string (optional)",
  "follow_up_date": "date (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Compliance audit created",
  "compliance_audit": {...}
}
```

#### Get Compliance Audit
```
GET /compliance-audits/{id}
```

**Response:**
```json
{
  "compliance_audit": {
    "id": 1,
    "business_id": 1,
    "title": "Annual ISO 9001 Audit",
    "description": "Annual quality management system audit",
    "audit_type": "internal",
    "auditor_id": 2,
    "planned_date": "2025-12-15",
    "actual_date": null,
    "start_time": "09:00:00",
    "end_time": "17:00:00",
    "status": "planned",
    "scope": "All departments",
    "objectives": "Verify compliance with ISO 9001:2015",
    "findings_summary": null,
    "recommendations": null,
    "action_items": null,
    "follow_up_date": null,
    "notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "auditor": {...},
    "findings": [...]
  }
}
```

#### Update Compliance Audit
```
PUT /compliance-audits/{id}
```

**Request Body:**
```json
{
  "title": "string (optional)",
  "description": "string (optional)",
  "audit_type": "string (optional, 'internal', 'external', 'regulatory')",
  "auditor_id": "integer (optional)",
  "planned_date": "date (optional)",
  "actual_date": "date (optional)",
  "start_time": "time (optional)",
  "end_time": "time (optional)",
  "status": "string (optional)",
  "scope": "string (optional)",
  "objectives": "string (optional)",
  "findings_summary": "string (optional)",
  "recommendations": "string (optional)",
  "action_items": "string (optional)",
  "follow_up_date": "date (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Compliance audit updated",
  "compliance_audit": {...}
}
```

#### Delete Compliance Audit
```
DELETE /compliance-audits/{id}
```

**Response:**
```json
{
  "message": "Compliance audit deleted"
}
```

### Compliance Audit Findings

#### Get All Compliance Audit Findings
```
GET /compliance-audit-findings
```

**Query Parameters:**
- `search` (optional): Search term to filter findings by description
- `severity` (optional): Filter by severity ('low', 'medium', 'high', 'critical')
- `status` (optional): Filter by status ('open', 'in_progress', 'resolved', 'closed')
- `open` (optional): Filter for open findings (boolean)
- `overdue` (optional): Filter for overdue findings (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "compliance_audit_id": 1,
      "compliance_requirement_id": 1,
      "description": "Document control procedure not followed",
      "evidence": "Non-conforming documents found in file cabinet",
      "severity": "medium",
      "status": "open",
      "assigned_to": 3,
      "due_date": "2025-12-31",
      "corrective_action": "Implement document control training",
      "resolution_date": null,
      "resolution_notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Compliance Audit Finding
```
POST /compliance-audit-findings
```

**Request Body:**
```json
{
  "compliance_audit_id": "integer",
  "compliance_requirement_id": "integer (optional)",
  "description": "string",
  "evidence": "string (optional)",
  "severity": "string (optional, default: 'low')",
  "status": "string (optional, default: 'open')",
  "assigned_to": "integer (optional)",
  "due_date": "date (optional)",
  "corrective_action": "string (optional)",
  "resolution_date": "date (optional)",
  "resolution_notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Compliance audit finding created",
  "compliance_audit_finding": {...}
}
```

#### Get Compliance Audit Finding
```
GET /compliance-audit-findings/{id}
```

**Response:**
```json
{
  "compliance_audit_finding": {
    "id": 1,
    "business_id": 1,
    "compliance_audit_id": 1,
    "compliance_requirement_id": 1,
    "description": "Document control procedure not followed",
    "evidence": "Non-conforming documents found in file cabinet",
    "severity": "medium",
    "status": "open",
    "assigned_to": 3,
    "due_date": "2025-12-31",
    "corrective_action": "Implement document control training",
    "resolution_date": null,
    "resolution_notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "compliance_audit": {...},
    "compliance_requirement": {...},
    "assigned_to_user": {...}
  }
}
```

#### Update Compliance Audit Finding
```
PUT /compliance-audit-findings/{id}
```

**Request Body:**
```json
{
  "compliance_audit_id": "integer (optional)",
  "compliance_requirement_id": "integer (optional)",
  "description": "string (optional)",
  "evidence": "string (optional)",
  "severity": "string (optional)",
  "status": "string (optional)",
  "assigned_to": "integer (optional)",
  "due_date": "date (optional)",
  "corrective_action": "string (optional)",
  "resolution_date": "date (optional)",
  "resolution_notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Compliance audit finding updated",
  "compliance_audit_finding": {...}
}
```

#### Delete Compliance Audit Finding
```
DELETE /compliance-audit-findings/{id}
```

**Response:**
```json
{
  "message": "Compliance audit finding deleted"
}
```

### Certificates & Licenses

#### Get All Certificates & Licenses
```
GET /certificates-licenses
```

**Query Parameters:**
- `search` (optional): Search term to filter certificates/licenses by name or description
- `status` (optional): Filter by status ('active', 'expired', 'revoked', 'pending_renewal')
- `expiring_soon` (optional): Filter for certificates/licenses expiring within 30 days (boolean)
- `expired` (optional): Filter for expired certificates/licenses (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "name": "ISO 9001:2015 Certificate",
      "description": "Quality management system certification",
      "certificate_number": "ISO-2025-001",
      "issuing_authority": "BSI Group",
      "issue_date": "2025-01-15",
      "expiry_date": "2028-01-14",
      "status": "active",
      "file_path": "/storage/certificates/iso9001.pdf",
      "file_name": "iso9001.pdf",
      "file_type": "application/pdf",
      "file_size": 512000,
      "responsible_person_id": 1,
      "notes": null,
      "reminder_sent": false,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Certificate/License
```
POST /certificates-licenses
```

**Request Body:**
```json
{
  "name": "string",
  "description": "string (optional)",
  "certificate_number": "string (optional)",
  "issuing_authority": "string (optional)",
  "issue_date": "date (optional)",
  "expiry_date": "date (optional)",
  "status": "string (optional, default: 'active')",
  "file_path": "string (optional)",
  "file_name": "string (optional)",
  "file_type": "string (optional)",
  "file_size": "integer (optional)",
  "responsible_person_id": "integer (optional)",
  "notes": "string (optional)",
  "reminder_sent": "boolean (optional, default: false)"
}
```

**Response:**
```json
{
  "message": "Certificate/License created",
  "certificate_license": {...}
}
```

#### Get Certificate/License
```
GET /certificates-licenses/{id}
```

**Response:**
```json
{
  "certificate_license": {
    "id": 1,
    "business_id": 1,
    "name": "ISO 9001:2015 Certificate",
    "description": "Quality management system certification",
    "certificate_number": "ISO-2025-001",
    "issuing_authority": "BSI Group",
    "issue_date": "2025-01-15",
    "expiry_date": "2028-01-14",
    "status": "active",
    "file_path": "/storage/certificates/iso9001.pdf",
    "file_name": "iso9001.pdf",
    "file_type": "application/pdf",
    "file_size": 512000,
    "responsible_person_id": 1,
    "notes": null,
    "reminder_sent": false,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "responsible_person": {...}
  }
}
```

#### Update Certificate/License
```
PUT /certificates-licenses/{id}
```

**Request Body:**
```json
{
  "name": "string (optional)",
  "description": "string (optional)",
  "certificate_number": "string (optional)",
  "issuing_authority": "string (optional)",
  "issue_date": "date (optional)",
  "expiry_date": "date (optional)",
  "status": "string (optional)",
  "file_path": "string (optional)",
  "file_name": "string (optional)",
  "file_type": "string (optional)",
  "file_size": "integer (optional)",
  "responsible_person_id": "integer (optional)",
  "notes": "string (optional)",
  "reminder_sent": "boolean (optional)"
}
```

**Response:**
```json
{
  "message": "Certificate/License updated",
  "certificate_license": {...}
}
```

#### Delete Certificate/License
```
DELETE /certificates-licenses/{id}
```

**Response:**
```json
{
  "message": "Certificate/License deleted"
}
```

## Risk Management Endpoints

### Risk Categories

#### Get All Risk Categories
```
GET /risk-categories
```

**Query Parameters:**
- `search` (optional): Search term to filter categories by name or description
- `category_type` (optional): Filter by category type
- `status` (optional): Filter by status ('active', 'inactive')

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "name": "Operational Risk",
      "description": "Risks related to operations and processes",
      "category_type": "operational",
      "owner_id": 1,
      "status": "active",
      "notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Risk Category
```
POST /risk-categories
```

**Request Body:**
```json
{
  "name": "string",
  "description": "string (optional)",
  "category_type": "string (optional)",
  "owner_id": "integer (optional)",
  "status": "string (optional, default: 'active')",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk category created",
  "risk_category": {...}
}
```

#### Get Risk Category
```
GET /risk-categories/{id}
```

**Response:**
```json
{
  "risk_category": {
    "id": 1,
    "business_id": 1,
    "name": "Operational Risk",
    "description": "Risks related to operations and processes",
    "category_type": "operational",
    "owner_id": 1,
    "status": "active",
    "notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "owner": {...},
    "risks": [...]
  }
}
```

#### Update Risk Category
```
PUT /risk-categories/{id}
```

**Request Body:**
```json
{
  "name": "string (optional)",
  "description": "string (optional)",
  "category_type": "string (optional)",
  "owner_id": "integer (optional)",
  "status": "string (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk category updated",
  "risk_category": {...}
}
```

#### Delete Risk Category
```
DELETE /risk-categories/{id}
```

**Response:**
```json
{
  "message": "Risk category deleted"
}
```

### Risks

#### Get All Risks
```
GET /risks
```

**Query Parameters:**
- `search` (optional): Search term to filter risks by title or description
- `risk_category_id` (optional): Filter by risk category
- `likelihood` (optional): Filter by likelihood ('low', 'medium', 'high')
- `impact` (optional): Filter by impact ('low', 'medium', 'high')
- `risk_level` (optional): Filter by risk level ('low', 'medium', 'high', 'critical')
- `status` (optional): Filter by status ('identified', 'assessed', 'mitigated', 'monitored', 'closed')
- `needs_review` (optional): Filter for risks needing review (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "risk_category_id": 1,
      "title": "Supply Chain Disruption",
      "description": "Risk of supplier failure affecting production",
      "cause": "Single source supplier dependency",
      "effect": "Production delays and increased costs",
      "owner_id": 2,
      "likelihood": "medium",
      "impact": "high",
      "risk_level": "high",
      "status": "mitigated",
      "assessment_date": "2025-11-15",
      "review_date": "2026-05-15",
      "notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Risk
```
POST /risks
```

**Request Body:**
```json
{
  "risk_category_id": "integer (optional)",
  "title": "string",
  "description": "string (optional)",
  "cause": "string (optional)",
  "effect": "string (optional)",
  "owner_id": "integer (optional)",
  "likelihood": "string (optional, default: 'medium')",
  "impact": "string (optional, default: 'medium')",
  "risk_level": "string (optional, default: 'medium')",
  "status": "string (optional, default: 'identified')",
  "assessment_date": "date (optional)",
  "review_date": "date (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk created",
  "risk": {...}
}
```

#### Get Risk
```
GET /risks/{id}
```

**Response:**
```json
{
  "risk": {
    "id": 1,
    "business_id": 1,
    "risk_category_id": 1,
    "title": "Supply Chain Disruption",
    "description": "Risk of supplier failure affecting production",
    "cause": "Single source supplier dependency",
    "effect": "Production delays and increased costs",
    "owner_id": 2,
    "likelihood": "medium",
    "impact": "high",
    "risk_level": "high",
    "status": "mitigated",
    "assessment_date": "2025-11-15",
    "review_date": "2026-05-15",
    "notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "risk_category": {...},
    "owner": {...},
    "impact_assessments": [...],
    "mitigation_strategies": [...],
    "incidents": [...]
  }
}
```

#### Update Risk
```
PUT /risks/{id}
```

**Request Body:**
```json
{
  "risk_category_id": "integer (optional)",
  "title": "string (optional)",
  "description": "string (optional)",
  "cause": "string (optional)",
  "effect": "string (optional)",
  "owner_id": "integer (optional)",
  "likelihood": "string (optional)",
  "impact": "string (optional)",
  "risk_level": "string (optional)",
  "status": "string (optional)",
  "assessment_date": "date (optional)",
  "review_date": "date (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk updated",
  "risk": {...}
}
```

#### Delete Risk
```
DELETE /risks/{id}
```

**Response:**
```json
{
  "message": "Risk deleted"
}
```

### Risk Impact Assessments

#### Get All Risk Impact Assessments
```
GET /risk-impact-assessments
```

**Query Parameters:**
- `risk_id` (optional): Filter by risk
- `assessed_by` (optional): Filter by assessor

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "risk_id": 1,
      "financial_impact": 50000.00,
      "operational_impact": 75.00,
      "reputational_impact": 60.00,
      "legal_impact": 40.00,
      "safety_impact": 30.00,
      "assessment_details": "Detailed impact analysis",
      "assessed_by": 1,
      "assessment_date": "2025-11-15",
      "methodology": "Qualitative assessment methodology",
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Risk Impact Assessment
```
POST /risk-impact-assessments
```

**Request Body:**
```json
{
  "risk_id": "integer",
  "financial_impact": "numeric (optional)",
  "operational_impact": "numeric (optional, 0-100)",
  "reputational_impact": "numeric (optional, 0-100)",
  "legal_impact": "numeric (optional, 0-100)",
  "safety_impact": "numeric (optional, 0-100)",
  "assessment_details": "string (optional)",
  "assessed_by": "integer (optional)",
  "assessment_date": "date (optional)",
  "methodology": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk impact assessment created",
  "risk_impact_assessment": {...}
}
```

#### Get Risk Impact Assessment
```
GET /risk-impact-assessments/{id}
```

**Response:**
```json
{
  "risk_impact_assessment": {
    "id": 1,
    "business_id": 1,
    "risk_id": 1,
    "financial_impact": 50000.00,
    "operational_impact": 75.00,
    "reputational_impact": 60.00,
    "legal_impact": 40.00,
    "safety_impact": 30.00,
    "assessment_details": "Detailed impact analysis",
    "assessed_by": 1,
    "assessment_date": "2025-11-15",
    "methodology": "Qualitative assessment methodology",
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "risk": {...},
    "assessed_by_user": {...}
  }
}
```

#### Update Risk Impact Assessment
```
PUT /risk-impact-assessments/{id}
```

**Request Body:**
```json
{
  "risk_id": "integer (optional)",
  "financial_impact": "numeric (optional)",
  "operational_impact": "numeric (optional, 0-100)",
  "reputational_impact": "numeric (optional, 0-100)",
  "legal_impact": "numeric (optional, 0-100)",
  "safety_impact": "numeric (optional, 0-100)",
  "assessment_details": "string (optional)",
  "assessed_by": "integer (optional)",
  "assessment_date": "date (optional)",
  "methodology": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk impact assessment updated",
  "risk_impact_assessment": {...}
}
```

#### Delete Risk Impact Assessment
```
DELETE /risk-impact-assessments/{id}
```

**Response:**
```json
{
  "message": "Risk impact assessment deleted"
}
```

### Risk Mitigation Strategies

#### Get All Risk Mitigation Strategies
```
GET /risk-mitigation-strategies
```

**Query Parameters:**
- `risk_id` (optional): Filter by risk
- `status` (optional): Filter by status ('planned', 'in_progress', 'implemented', 'completed')
- `upcoming` (optional): Filter for strategies starting within 7 days (boolean)
- `overdue` (optional): Filter for overdue strategies (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "risk_id": 1,
      "strategy_name": "Diversify Supplier Base",
      "description": "Add alternative suppliers to reduce dependency",
      "actions": "Identify and qualify 2 additional suppliers",
      "responsible_person_id": 3,
      "cost": 25000.00,
      "start_date": "2025-12-01",
      "end_date": "2026-03-01",
      "status": "in_progress",
      "effectiveness": 75.00,
      "notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Risk Mitigation Strategy
```
POST /risk-mitigation-strategies
```

**Request Body:**
```json
{
  "risk_id": "integer",
  "strategy_name": "string",
  "description": "string (optional)",
  "actions": "string (optional)",
  "responsible_person_id": "integer (optional)",
  "cost": "numeric (optional)",
  "start_date": "date (optional)",
  "end_date": "date (optional)",
  "status": "string (optional, default: 'planned')",
  "effectiveness": "numeric (optional, 0-100)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk mitigation strategy created",
  "risk_mitigation_strategy": {...}
}
```

#### Get Risk Mitigation Strategy
```
GET /risk-mitigation-strategies/{id}
```

**Response:**
```json
{
  "risk_mitigation_strategy": {
    "id": 1,
    "business_id": 1,
    "risk_id": 1,
    "strategy_name": "Diversify Supplier Base",
    "description": "Add alternative suppliers to reduce dependency",
    "actions": "Identify and qualify 2 additional suppliers",
    "responsible_person_id": 3,
    "cost": 25000.00,
    "start_date": "2025-12-01",
    "end_date": "2026-03-01",
    "status": "in_progress",
    "effectiveness": 75.00,
    "notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "risk": {...},
    "responsible_person": {...}
  }
}
```

#### Update Risk Mitigation Strategy
```
PUT /risk-mitigation-strategies/{id}
```

**Request Body:**
```json
{
  "risk_id": "integer (optional)",
  "strategy_name": "string (optional)",
  "description": "string (optional)",
  "actions": "string (optional)",
  "responsible_person_id": "integer (optional)",
  "cost": "numeric (optional)",
  "start_date": "date (optional)",
  "end_date": "date (optional)",
  "status": "string (optional)",
  "effectiveness": "numeric (optional, 0-100)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk mitigation strategy updated",
  "risk_mitigation_strategy": {...}
}
```

#### Delete Risk Mitigation Strategy
```
DELETE /risk-mitigation-strategies/{id}
```

**Response:**
```json
{
  "message": "Risk mitigation strategy deleted"
}
```

### Risk Incidents

#### Get All Risk Incidents
```
GET /risk-incidents
```

**Query Parameters:**
- `search` (optional): Search term to filter incidents by title or description
- `risk_id` (optional): Filter by risk
- `severity` (optional): Filter by severity ('low', 'medium', 'high', 'critical')
- `status` (optional): Filter by status ('reported', 'investigated', 'resolved', 'closed')
- `high_severity` (optional): Filter for high severity incidents (boolean)
- `open` (optional): Filter for open incidents (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "risk_id": 1,
      "title": "Supplier Delivery Failure",
      "description": "Key supplier failed to deliver critical components",
      "incident_date": "2025-11-10T09:30:00.000000Z",
      "incident_type": "supply_chain",
      "reported_by": 4,
      "affected_areas": "Production line 1, Production line 2",
      "financial_loss": 15000.00,
      "affected_people": 25,
      "severity": "high",
      "status": "investigated",
      "immediate_actions": "Switched to backup supplier",
      "root_cause": "Supplier financial difficulties",
      "corrective_actions": "Implement supplier financial monitoring",
      "resolution_date": "2025-11-12",
      "lessons_learned": "Need better supplier risk assessment",
      "notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Risk Incident
```
POST /risk-incidents
```

**Request Body:**
```json
{
  "risk_id": "integer (optional)",
  "title": "string",
  "description": "string (optional)",
  "incident_date": "datetime",
  "incident_type": "string (optional)",
  "reported_by": "integer (optional)",
  "affected_areas": "string (optional)",
  "financial_loss": "numeric (optional)",
  "affected_people": "integer (optional)",
  "severity": "string (optional, default: 'medium')",
  "status": "string (optional, default: 'reported')",
  "immediate_actions": "string (optional)",
  "root_cause": "string (optional)",
  "corrective_actions": "string (optional)",
  "resolution_date": "date (optional)",
  "lessons_learned": "string (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk incident created",
  "risk_incident": {...}
}
```

#### Get Risk Incident
```
GET /risk-incidents/{id}
```

**Response:**
```json
{
  "risk_incident": {
    "id": 1,
    "business_id": 1,
    "risk_id": 1,
    "title": "Supplier Delivery Failure",
    "description": "Key supplier failed to deliver critical components",
    "incident_date": "2025-11-10T09:30:00.000000Z",
    "incident_type": "supply_chain",
    "reported_by": 4,
    "affected_areas": "Production line 1, Production line 2",
    "financial_loss": 15000.00,
    "affected_people": 25,
    "severity": "high",
    "status": "investigated",
    "immediate_actions": "Switched to backup supplier",
    "root_cause": "Supplier financial difficulties",
    "corrective_actions": "Implement supplier financial monitoring",
    "resolution_date": "2025-11-12",
    "lessons_learned": "Need better supplier risk assessment",
    "notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "risk": {...},
    "reported_by_user": {...}
  }
}
```

#### Update Risk Incident
```
PUT /risk-incidents/{id}
```

**Request Body:**
```json
{
  "risk_id": "integer (optional)",
  "title": "string (optional)",
  "description": "string (optional)",
  "incident_date": "datetime (optional)",
  "incident_type": "string (optional)",
  "reported_by": "integer (optional)",
  "affected_areas": "string (optional)",
  "financial_loss": "numeric (optional)",
  "affected_people": "integer (optional)",
  "severity": "string (optional)",
  "status": "string (optional)",
  "immediate_actions": "string (optional)",
  "root_cause": "string (optional)",
  "corrective_actions": "string (optional)",
  "resolution_date": "date (optional)",
  "lessons_learned": "string (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Risk incident updated",
  "risk_incident": {...}
}
```

#### Delete Risk Incident
```
DELETE /risk-incidents/{id}
```

**Response:**
```json
{
  "message": "Risk incident deleted"
}
```

### Business Continuity Plans

#### Get All Business Continuity Plans
```
GET /business-continuity-plans
```

**Query Parameters:**
- `search` (optional): Search term to filter plans by title or description
- `status` (optional): Filter by status ('active', 'inactive', 'testing', 'review_required')
- `due_for_testing` (optional): Filter for plans due for testing (boolean)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "business_id": 1,
      "title": "IT System Failure Continuity Plan",
      "description": "Plan for handling critical IT system failures",
      "scope": "All business operations dependent on IT systems",
      "objectives": "Ensure business continuity during IT outages",
      "owner_id": 1,
      "critical_functions": "Order processing, inventory management, customer service",
      "recovery_strategies": "Cloud backup systems, redundant servers",
      "resource_requirements": "Backup servers, cloud storage, IT personnel",
      "contact_information": "IT Manager: it.manager@company.com, Phone: 555-0123",
      "communication_plan": "Notify stakeholders via email and SMS within 1 hour",
      "last_tested_date": "2025-06-15",
      "next_test_date": "2025-12-15",
      "status": "active",
      "notes": null,
      "created_at": "2025-11-16T10:00:00.000000Z",
      "updated_at": "2025-11-16T10:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Create Business Continuity Plan
```
POST /business-continuity-plans
```

**Request Body:**
```json
{
  "title": "string",
  "description": "string (optional)",
  "scope": "string (optional)",
  "objectives": "string (optional)",
  "owner_id": "integer (optional)",
  "critical_functions": "string (optional)",
  "recovery_strategies": "string (optional)",
  "resource_requirements": "string (optional)",
  "contact_information": "string (optional)",
  "communication_plan": "string (optional)",
  "last_tested_date": "date (optional)",
  "next_test_date": "date (optional)",
  "status": "string (optional, default: 'active')",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Business continuity plan created",
  "business_continuity_plan": {...}
}
```

#### Get Business Continuity Plan
```
GET /business-continuity-plans/{id}
```

**Response:**
```json
{
  "business_continuity_plan": {
    "id": 1,
    "business_id": 1,
    "title": "IT System Failure Continuity Plan",
    "description": "Plan for handling critical IT system failures",
    "scope": "All business operations dependent on IT systems",
    "objectives": "Ensure business continuity during IT outages",
    "owner_id": 1,
    "critical_functions": "Order processing, inventory management, customer service",
    "recovery_strategies": "Cloud backup systems, redundant servers",
    "resource_requirements": "Backup servers, cloud storage, IT personnel",
    "contact_information": "IT Manager: it.manager@company.com, Phone: 555-0123",
    "communication_plan": "Notify stakeholders via email and SMS within 1 hour",
    "last_tested_date": "2025-06-15",
    "next_test_date": "2025-12-15",
    "status": "active",
    "notes": null,
    "created_at": "2025-11-16T10:00:00.000000Z",
    "updated_at": "2025-11-16T10:00:00.000000Z",
    "owner": {...}
  }
}
```

#### Update Business Continuity Plan
```
PUT /business-continuity-plans/{id}
```

**Request Body:**
```json
{
  "title": "string (optional)",
  "description": "string (optional)",
  "scope": "string (optional)",
  "objectives": "string (optional)",
  "owner_id": "integer (optional)",
  "critical_functions": "string (optional)",
  "recovery_strategies": "string (optional)",
  "resource_requirements": "string (optional)",
  "contact_information": "string (optional)",
  "communication_plan": "string (optional)",
  "last_tested_date": "date (optional)",
  "next_test_date": "date (optional)",
  "status": "string (optional)",
  "notes": "string (optional)"
}
```

**Response:**
```json
{
  "message": "Business continuity plan updated",
  "business_continuity_plan": {...}
}
```

#### Delete Business Continuity Plan
```
DELETE /business-continuity-plans/{id}
```

**Response:**
```json
{
  "message": "Business continuity plan deleted"
}
```