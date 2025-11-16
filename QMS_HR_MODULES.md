# Quality Management System (QMS) and Human Resources (HR) Modules

## Overview

This document describes the implementation of the Quality Management System (QMS) and Human Resources (HR) modules for the Monitor Bizz application. These modules extend the existing ERP/CRM functionality to provide comprehensive quality control and human resource management capabilities.

## Quality Management System (QMS)

### Module Components

1. **Quality Standards**
   - Define and manage quality standards (e.g., ISO 9001, ISO 14001)
   - Version control for standards
   - Active/inactive status management

2. **Quality Checklists**
   - Create checklists for different inspection types (incoming, in-process, final)
   - Associate checklists with quality standards
   - Active/inactive status management

3. **Quality Checklist Items**
   - Define specific inspection criteria
   - Support for different criteria types (pass/fail, numeric, text)
   - Acceptable criteria ranges and values

4. **Quality Inspections**
   - Schedule and track inspections
   - Associate inspections with materials, products, or work orders
   - Track inspection status (pending, in progress, completed, rejected)
   - Record overall scores and pass/fail status

5. **Quality Inspection Results**
   - Record results for each checklist item
   - Capture remarks and attachments
   - Track pass/fail status for individual items

### Database Schema

The QMS module includes the following database tables:

1. `quality_standards` - Stores quality standards information
2. `quality_checklists` - Stores quality checklists
3. `quality_checklist_items` - Stores individual checklist items
4. `quality_inspections` - Stores inspection records
5. `quality_inspection_results` - Stores individual inspection results

### API Endpoints

The QMS module provides the following REST API endpoints:

1. **Quality Standards**
   - `GET /api/quality-standards` - List quality standards
   - `POST /api/quality-standards` - Create a quality standard
   - `GET /api/quality-standards/{id}` - Get a quality standard
   - `PUT /api/quality-standards/{id}` - Update a quality standard
   - `DELETE /api/quality-standards/{id}` - Delete a quality standard

2. **Quality Checklists**
   - `GET /api/quality-checklists` - List quality checklists
   - `POST /api/quality-checklists` - Create a quality checklist
   - `GET /api/quality-checklists/{id}` - Get a quality checklist
   - `PUT /api/quality-checklists/{id}` - Update a quality checklist
   - `DELETE /api/quality-checklists/{id}` - Delete a quality checklist

3. **Quality Checklist Items**
   - `GET /api/quality-checklist-items` - List quality checklist items
   - `POST /api/quality-checklist-items` - Create a quality checklist item
   - `GET /api/quality-checklist-items/{id}` - Get a quality checklist item
   - `PUT /api/quality-checklist-items/{id}` - Update a quality checklist item
   - `DELETE /api/quality-checklist-items/{id}` - Delete a quality checklist item

4. **Quality Inspections**
   - `GET /api/quality-inspections` - List quality inspections
   - `POST /api/quality-inspections` - Create a quality inspection
   - `GET /api/quality-inspections/{id}` - Get a quality inspection
   - `PUT /api/quality-inspections/{id}` - Update a quality inspection
   - `DELETE /api/quality-inspections/{id}` - Delete a quality inspection
   - `POST /api/quality-inspections/{id}/complete` - Complete a quality inspection

5. **Quality Inspection Results**
   - `GET /api/quality-inspection-results` - List quality inspection results
   - `POST /api/quality-inspection-results` - Create a quality inspection result
   - `GET /api/quality-inspection-results/{id}` - Get a quality inspection result
   - `PUT /api/quality-inspection-results/{id}` - Update a quality inspection result
   - `DELETE /api/quality-inspection-results/{id}` - Delete a quality inspection result

## Human Resources (HR) Module

### Module Components

1. **Departments**
   - Organize employees into departments
   - Assign department managers
   - Active/inactive status management

2. **Job Positions**
   - Define job positions within departments
   - Specify employment types (full-time, part-time, contract, intern)
   - Define salary ranges

3. **Training Programs**
   - Create and manage training programs
   - Define training objectives and duration
   - Assign trainers and difficulty levels

4. **Training Materials**
   - Attach training materials to programs
   - Organize materials with sort order
   - Support for different file types

5. **Employee Trainings**
   - Assign training programs to employees
   - Track training progress and completion
   - Record scores and feedback

6. **Skill Assessments**
   - Assess employee skills and proficiency levels
   - Record assessment scores and comments
   - Schedule next review dates

### Database Schema

The HR module includes the following database tables:

1. `departments` - Stores department information
2. `job_positions` - Stores job position information
3. `training_programs` - Stores training program information
4. `training_materials` - Stores training materials
5. `employee_trainings` - Stores employee training records
6. `skill_assessments` - Stores skill assessment records

### API Endpoints

The HR module provides the following REST API endpoints:

1. **Departments**
   - `GET /api/departments` - List departments
   - `POST /api/departments` - Create a department
   - `GET /api/departments/{id}` - Get a department
   - `PUT /api/departments/{id}` - Update a department
   - `DELETE /api/departments/{id}` - Delete a department

2. **Job Positions**
   - `GET /api/job-positions` - List job positions
   - `POST /api/job-positions` - Create a job position
   - `GET /api/job-positions/{id}` - Get a job position
   - `PUT /api/job-positions/{id}` - Update a job position
   - `DELETE /api/job-positions/{id}` - Delete a job position

3. **Training Programs**
   - `GET /api/training-programs` - List training programs
   - `POST /api/training-programs` - Create a training program
   - `GET /api/training-programs/{id}` - Get a training program
   - `PUT /api/training-programs/{id}` - Update a training program
   - `DELETE /api/training-programs/{id}` - Delete a training program

4. **Training Materials**
   - `GET /api/training-materials` - List training materials
   - `POST /api/training-materials` - Create a training material
   - `GET /api/training-materials/{id}` - Get a training material
   - `PUT /api/training-materials/{id}` - Update a training material
   - `DELETE /api/training-materials/{id}` - Delete a training material

5. **Employee Trainings**
   - `GET /api/employee-trainings` - List employee trainings
   - `POST /api/employee-trainings` - Create an employee training
   - `GET /api/employee-trainings/{id}` - Get an employee training
   - `PUT /api/employee-trainings/{id}` - Update an employee training
   - `DELETE /api/employee-trainings/{id}` - Delete an employee training
   - `POST /api/employee-trainings/{id}/complete` - Complete an employee training

6. **Skill Assessments**
   - `GET /api/skill-assessments` - List skill assessments
   - `POST /api/skill-assessments` - Create a skill assessment
   - `GET /api/skill-assessments/{id}` - Get a skill assessment
   - `PUT /api/skill-assessments/{id}` - Update a skill assessment
   - `DELETE /api/skill-assessments/{id}` - Delete a skill assessment

## Integration with Existing Modules

### QMS Integration

The QMS module integrates with existing modules in the following ways:

1. **Materials Module**
   - Quality inspections can be associated with incoming materials
   - Inspection results affect material acceptance/rejection

2. **Products Module**
   - Quality inspections can be associated with finished products
   - Final inspection results determine product release

3. **Work Orders Module**
   - In-process inspections can be associated with work orders
   - Quality results affect work order completion

### HR Integration

The HR module integrates with existing modules in the following ways:

1. **Users Module**
   - Employees are represented as users in the system
   - Department and job position information is stored in the users table

2. **Businesses Module**
   - All HR entities are scoped to specific businesses
   - Multi-tenant architecture ensures data isolation

## Implementation Files

The implementation includes the following files:

### Database Migrations
- `database/migrations/2025_11_16_140000_create_quality_standards_table.php`
- `database/migrations/2025_11_16_140001_create_quality_checklists_table.php`
- `database/migrations/2025_11_16_140002_create_quality_checklist_items_table.php`
- `database/migrations/2025_11_16_140003_create_quality_inspections_table.php`
- `database/migrations/2025_11_16_140004_create_quality_inspection_results_table.php`
- `database/migrations/2025_11_16_140005_create_departments_table.php`
- `database/migrations/2025_11_16_140006_create_job_positions_table.php`
- `database/migrations/2025_11_16_140007_create_training_programs_table.php`
- `database/migrations/2025_11_16_140008_create_training_materials_table.php`
- `database/migrations/2025_11_16_140009_create_employee_trainings_table.php`
- `database/migrations/2025_11_16_140010_create_skill_assessments_table.php`

### Models
- `app/Models/QualityStandard.php`
- `app/Models/QualityChecklist.php`
- `app/Models/QualityChecklistItem.php`
- `app/Models/QualityInspection.php`
- `app/Models/QualityInspectionResult.php`
- `app/Models/Department.php`
- `app/Models/JobPosition.php`
- `app/Models/TrainingProgram.php`
- `app/Models/TrainingMaterial.php`
- `app/Models/EmployeeTraining.php`
- `app/Models/SkillAssessment.php`

### API Controllers
- `app/Http/Controllers/Api/QualityStandardController.php`
- `app/Http/Controllers/Api/QualityChecklistController.php`
- `app/Http/Controllers/Api/QualityChecklistItemController.php`
- `app/Http/Controllers/Api/QualityInspectionController.php`
- `app/Http/Controllers/Api/QualityInspectionResultController.php`
- `app/Http/Controllers/Api/DepartmentController.php`
- `app/Http/Controllers/Api/JobPositionController.php`
- `app/Http/Controllers/Api/TrainingProgramController.php`
- `app/Http/Controllers/Api/TrainingMaterialController.php`
- `app/Http/Controllers/Api/EmployeeTrainingController.php`
- `app/Http/Controllers/Api/SkillAssessmentController.php`

### API Routes
The routes are defined in `routes/api.php` and include all endpoints listed above.

## Testing

To test the new modules, you can use the provided test script `test_qms_hr_modules.sh` which demonstrates how to interact with the API endpoints.

## Conclusion

The QMS and HR modules provide comprehensive functionality for quality management and human resource management within the Monitor Bizz application. These modules extend the existing ERP/CRM capabilities and integrate seamlessly with the current system architecture.