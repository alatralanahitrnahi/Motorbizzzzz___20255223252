import axios from 'axios';

// Create axios instance with default configuration
const apiClient = axios.create({
  baseURL: '/api', // This will be relative to the current domain
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('authToken');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle errors
apiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      localStorage.removeItem('authToken');
      window.location.href = '/login';
    }
    
    if (error.response?.status === 403) {
      // Handle forbidden access
      console.error('Access forbidden:', error.response.data.message);
    }
    
    if (error.response?.status === 500) {
      // Handle server errors
      console.error('Server error:', error.response.data.message);
    }
    
    return Promise.reject(error);
  }
);

// Generic API methods
export const apiService = {
  // GET request
  get: async (url, params = {}) => {
    try {
      const response = await apiClient.get(url, { params });
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to fetch data');
    }
  },

  // POST request
  post: async (url, data = {}) => {
    try {
      const response = await apiClient.post(url, data);
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to create data');
    }
  },

  // PUT request
  put: async (url, data = {}) => {
    try {
      const response = await apiClient.put(url, data);
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to update data');
    }
  },

  // DELETE request
  delete: async (url) => {
    try {
      const response = await apiClient.delete(url);
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to delete data');
    }
  },

  // PATCH request
  patch: async (url, data = {}) => {
    try {
      const response = await apiClient.patch(url, data);
      return response.data;
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to update data');
    }
  }
};

// Compliance Management API methods
export const complianceApi = {
  // Compliance Requirements
  getComplianceRequirements: (params) => apiService.get('/compliance-requirements', params),
  getComplianceRequirement: (id) => apiService.get(`/compliance-requirements/${id}`),
  createComplianceRequirement: (data) => apiService.post('/compliance-requirements', data),
  updateComplianceRequirement: (id, data) => apiService.put(`/compliance-requirements/${id}`, data),
  deleteComplianceRequirement: (id) => apiService.delete(`/compliance-requirements/${id}`),

  // Compliance Documents
  getComplianceDocuments: (params) => apiService.get('/compliance-documents', params),
  getComplianceDocument: (id) => apiService.get(`/compliance-documents/${id}`),
  createComplianceDocument: (data) => apiService.post('/compliance-documents', data),
  updateComplianceDocument: (id, data) => apiService.put(`/compliance-documents/${id}`, data),
  deleteComplianceDocument: (id) => apiService.delete(`/compliance-documents/${id}`),

  // Compliance Audits
  getComplianceAudits: (params) => apiService.get('/compliance-audits', params),
  getComplianceAudit: (id) => apiService.get(`/compliance-audits/${id}`),
  createComplianceAudit: (data) => apiService.post('/compliance-audits', data),
  updateComplianceAudit: (id, data) => apiService.put(`/compliance-audits/${id}`, data),
  deleteComplianceAudit: (id) => apiService.delete(`/compliance-audits/${id}`),

  // Compliance Audit Findings
  getComplianceAuditFindings: (params) => apiService.get('/compliance-audit-findings', params),
  getComplianceAuditFinding: (id) => apiService.get(`/compliance-audit-findings/${id}`),
  createComplianceAuditFinding: (data) => apiService.post('/compliance-audit-findings', data),
  updateComplianceAuditFinding: (id, data) => apiService.put(`/compliance-audit-findings/${id}`, data),
  deleteComplianceAuditFinding: (id) => apiService.delete(`/compliance-audit-findings/${id}`),

  // Certificates & Licenses
  getCertificatesLicenses: (params) => apiService.get('/certificates-licenses', params),
  getCertificateLicense: (id) => apiService.get(`/certificates-licenses/${id}`),
  createCertificateLicense: (data) => apiService.post('/certificates-licenses', data),
  updateCertificateLicense: (id, data) => apiService.put(`/certificates-licenses/${id}`, data),
  deleteCertificateLicense: (id) => apiService.delete(`/certificates-licenses/${id}`),
};

// Risk Management API methods
export const riskApi = {
  // Risk Categories
  getRiskCategories: (params) => apiService.get('/risk-categories', params),
  getRiskCategory: (id) => apiService.get(`/risk-categories/${id}`),
  createRiskCategory: (data) => apiService.post('/risk-categories', data),
  updateRiskCategory: (id, data) => apiService.put(`/risk-categories/${id}`, data),
  deleteRiskCategory: (id) => apiService.delete(`/risk-categories/${id}`),

  // Risks
  getRisks: (params) => apiService.get('/risks', params),
  getRisk: (id) => apiService.get(`/risks/${id}`),
  createRisk: (data) => apiService.post('/risks', data),
  updateRisk: (id, data) => apiService.put(`/risks/${id}`, data),
  deleteRisk: (id) => apiService.delete(`/risks/${id}`),

  // Risk Impact Assessments
  getRiskImpactAssessments: (params) => apiService.get('/risk-impact-assessments', params),
  getRiskImpactAssessment: (id) => apiService.get(`/risk-impact-assessments/${id}`),
  createRiskImpactAssessment: (data) => apiService.post('/risk-impact-assessments', data),
  updateRiskImpactAssessment: (id, data) => apiService.put(`/risk-impact-assessments/${id}`, data),
  deleteRiskImpactAssessment: (id) => apiService.delete(`/risk-impact-assessments/${id}`),

  // Risk Mitigation Strategies
  getRiskMitigationStrategies: (params) => apiService.get('/risk-mitigation-strategies', params),
  getRiskMitigationStrategy: (id) => apiService.get(`/risk-mitigation-strategies/${id}`),
  createRiskMitigationStrategy: (data) => apiService.post('/risk-mitigation-strategies', data),
  updateRiskMitigationStrategy: (id, data) => apiService.put(`/risk-mitigation-strategies/${id}`, data),
  deleteRiskMitigationStrategy: (id) => apiService.delete(`/risk-mitigation-strategies/${id}`),

  // Risk Incidents
  getRiskIncidents: (params) => apiService.get('/risk-incidents', params),
  getRiskIncident: (id) => apiService.get(`/risk-incidents/${id}`),
  createRiskIncident: (data) => apiService.post('/risk-incidents', data),
  updateRiskIncident: (id, data) => apiService.put(`/risk-incidents/${id}`, data),
  deleteRiskIncident: (id) => apiService.delete(`/risk-incidents/${id}`),

  // Business Continuity Plans
  getBusinessContinuityPlans: (params) => apiService.get('/business-continuity-plans', params),
  getBusinessContinuityPlan: (id) => apiService.get(`/business-continuity-plans/${id}`),
  createBusinessContinuityPlan: (data) => apiService.post('/business-continuity-plans', data),
  updateBusinessContinuityPlan: (id, data) => apiService.put(`/business-continuity-plans/${id}`, data),
  deleteBusinessContinuityPlan: (id) => apiService.delete(`/business-continuity-plans/${id}`),
};

export default apiService;