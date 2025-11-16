import React from 'react';
import {
  Typography,
  Box,
  Card,
  CardContent,
  Grid
} from '@mui/material';
import {
  Description as DocumentIcon,
  CloudUpload as UploadIcon,
  Search as SearchIcon
} from '@mui/icons-material';

const ComplianceDocuments = () => {
  // Mock data for demonstration
  const documents = [
    {
      id: 1,
      title: 'Quality Manual',
      type: 'Policy',
      version: '2.1',
      status: 'Approved',
      lastUpdated: '2025-11-15',
      owner: 'John Smith'
    },
    {
      id: 2,
      title: 'Environmental Policy',
      type: 'Policy',
      version: '1.0',
      status: 'Draft',
      lastUpdated: '2025-11-10',
      owner: 'Sarah Johnson'
    },
    {
      id: 3,
      title: 'Safety Procedures',
      type: 'Procedure',
      version: '3.2',
      status: 'Approved',
      lastUpdated: '2025-11-05',
      owner: 'Mike Davis'
    }
  ];

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Compliance Documents
      </Typography>
      
      <Box mb={3}>
        <Grid container spacing={2}>
          <Grid item xs={12} md={4}>
            <Card>
              <CardContent>
                <Box display="flex" alignItems="center">
                  <DocumentIcon sx={{ fontSize: 40, color: '#1976d2', mr: 2 }} />
                  <div>
                    <Typography variant="h5">24</Typography>
                    <Typography variant="body2" color="text.secondary">Total Documents</Typography>
                  </div>
                </Box>
              </CardContent>
            </Card>
          </Grid>
          
          <Grid item xs={12} md={4}>
            <Card>
              <CardContent>
                <Box display="flex" alignItems="center">
                  <UploadIcon sx={{ fontSize: 40, color: '#388e3c', mr: 2 }} />
                  <div>
                    <Typography variant="h5">8</Typography>
                    <Typography variant="body2" color="text.secondary">Pending Review</Typography>
                  </div>
                </Box>
              </CardContent>
            </Card>
          </Grid>
          
          <Grid item xs={12} md={4}>
            <Card>
              <CardContent>
                <Box display="flex" alignItems="center">
                  <SearchIcon sx={{ fontSize: 40, color: '#f57c00', mr: 2 }} />
                  <div>
                    <Typography variant="h5">3</Typography>
                    <Typography variant="body2" color="text.secondary">Expiring Soon</Typography>
                  </div>
                </Box>
              </CardContent>
            </Card>
          </Grid>
        </Grid>
      </Box>
      
      <Card>
        <CardContent>
          <Typography variant="h6" gutterBottom>
            Document Management
          </Typography>
          <Typography variant="body1" color="text.secondary">
            This section will allow you to manage compliance documents, track versions, 
            assign reviewers, and monitor approval workflows.
          </Typography>
        </CardContent>
      </Card>
    </div>
  );
};

export default ComplianceDocuments;