import React from 'react';
import {
  Typography,
  Box,
  Card,
  CardContent,
  Grid
} from '@mui/material';
import {
  CardMembership as CertificateIcon,
  Event as ExpiryIcon,
  CheckCircle as ValidIcon,
  Error as ExpiredIcon
} from '@mui/icons-material';

const CertificatesLicenses = () => {
  // Mock data for demonstration
  const certStats = [
    {
      title: 'Total Certificates',
      value: 15,
      icon: <CertificateIcon />,
      color: '#1976d2'
    },
    {
      title: 'Valid',
      value: 12,
      icon: <ValidIcon />,
      color: '#388e3c'
    },
    {
      title: 'Expiring Soon',
      value: 2,
      icon: <ExpiryIcon />,
      color: '#f57c00'
    },
    {
      title: 'Expired',
      value: 1,
      icon: <ExpiredIcon />,
      color: '#d32f2f'
    }
  ];

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Certificates & Licenses
      </Typography>
      
      <Box mb={3}>
        <Grid container spacing={2}>
          {certStats.map((stat, index) => (
            <Grid item xs={12} sm={6} md={3} key={index}>
              <Card>
                <CardContent>
                  <Box display="flex" alignItems="center">
                    <Box 
                      sx={{ 
                        backgroundColor: stat.color, 
                        borderRadius: '50%', 
                        width: 48, 
                        height: 48, 
                        display: 'flex', 
                        alignItems: 'center', 
                        justifyContent: 'center',
                        color: 'white',
                        mr: 2
                      }}
                    >
                      {stat.icon}
                    </Box>
                    <div>
                      <Typography variant="h5">{stat.value}</Typography>
                      <Typography variant="body2" color="text.secondary">{stat.title}</Typography>
                    </div>
                  </Box>
                </CardContent>
              </Card>
            </Grid>
          ))}
        </Grid>
      </Box>
      
      <Card>
        <CardContent>
          <Typography variant="h6" gutterBottom>
            Certificate & License Management
          </Typography>
          <Typography variant="body1" color="text.secondary">
            This section will allow you to track certificates and licenses, monitor expiration dates, 
            set up renewal reminders, and store digital copies of documents.
          </Typography>
        </CardContent>
      </Card>
    </div>
  );
};

export default CertificatesLicenses;