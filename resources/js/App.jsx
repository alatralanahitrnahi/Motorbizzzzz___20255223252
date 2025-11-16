import React, { useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Provider } from 'react-redux';
import { store } from './store/store';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import { AuthProvider } from './contexts/AuthContext';
import ProtectedRoute from './components/ProtectedRoute';
import Login from './pages/auth/Login';
import Register from './pages/auth/Register';
import Dashboard from './pages/Dashboard';
import ComplianceRequirements from './pages/compliance/ComplianceRequirements';
import ComplianceDocuments from './pages/compliance/ComplianceDocuments';
import ComplianceAudits from './pages/compliance/ComplianceAudits';
import CertificatesLicenses from './pages/compliance/CertificatesLicenses';
import RiskCategories from './pages/risk/RiskCategories';
import RiskAssessments from './pages/risk/RiskAssessments';
import RiskIncidents from './pages/risk/RiskIncidents';
import BusinessContinuity from './pages/risk/BusinessContinuity';
import ResponsiveDashboard from './components/dashboard/ResponsiveDashboard';
import websocketService from './services/websocketService';

const theme = createTheme({
  palette: {
    primary: {
      main: '#1976d2',
    },
    secondary: {
      main: '#dc004e',
    },
    background: {
      default: '#f5f5f5',
    },
  },
  components: {
    MuiCard: {
      styleOverrides: {
        root: {
          boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
          borderRadius: '8px',
        },
      },
    },
  },
});

function App() {
  useEffect(() => {
    // Initialize WebSocket connection
    websocketService.connect();
    
    // Cleanup on unmount
    return () => {
      websocketService.disconnect();
    };
  }, []);

  return (
    <Provider store={store}>
      <AuthProvider>
        <ThemeProvider theme={theme}>
          <CssBaseline />
          <Router>
            <Routes>
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route path="/" element={
                <ProtectedRoute>
                  <ResponsiveDashboard>
                    <Routes>
                      <Route index element={<Dashboard />} />
                      <Route path="/compliance/requirements" element={
                        <ProtectedRoute requiredPermission="view-compliance">
                          <ComplianceRequirements />
                        </ProtectedRoute>
                      } />
                      <Route path="/compliance/documents" element={
                        <ProtectedRoute requiredPermission="view-compliance">
                          <ComplianceDocuments />
                        </ProtectedRoute>
                      } />
                      <Route path="/compliance/audits" element={
                        <ProtectedRoute requiredPermission="view-compliance">
                          <ComplianceAudits />
                        </ProtectedRoute>
                      } />
                      <Route path="/compliance/certificates" element={
                        <ProtectedRoute requiredPermission="view-compliance">
                          <CertificatesLicenses />
                        </ProtectedRoute>
                      } />
                      <Route path="/risk/categories" element={
                        <ProtectedRoute requiredPermission="view-risk">
                          <RiskCategories />
                        </ProtectedRoute>
                      } />
                      <Route path="/risk/assessments" element={
                        <ProtectedRoute requiredPermission="view-risk">
                          <RiskAssessments />
                        </ProtectedRoute>
                      } />
                      <Route path="/risk/incidents" element={
                        <ProtectedRoute requiredPermission="view-risk">
                          <RiskIncidents />
                        </ProtectedRoute>
                      } />
                      <Route path="/risk/continuity" element={
                        <ProtectedRoute requiredPermission="view-risk">
                          <BusinessContinuity />
                        </ProtectedRoute>
                      } />
                    </Routes>
                  </ResponsiveDashboard>
                </ProtectedRoute>
              } />
            </Routes>
          </Router>
        </ThemeProvider>
      </AuthProvider>
    </Provider>
  );
}

export default App;