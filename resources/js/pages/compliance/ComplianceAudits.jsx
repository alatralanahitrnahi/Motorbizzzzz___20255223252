import React from 'react';
import {
  Typography,
  Box,
  Card,
  CardContent,
  Grid
} from '@mui/material';
import {
  Assignment as AuditIcon,
  EventAvailable as ScheduledIcon,
  CheckCircle as CompletedIcon,
  Warning as WarningIcon
} from '@mui/icons-material';

const ComplianceAudits = () => {
  // Mock data for demonstration
  const auditStats = [
    {
      title: 'Total Audits',
      value: 12,
      icon: <AuditIcon />,
      color: '#1976d2'
    },
    {
      title: 'Scheduled',
      value: 4,
      icon: <ScheduledIcon />,
      color: '#f57c00'
    },
    {
      title: 'Completed',
      value: 7,
      icon: <CompletedIcon />,
      color: '#388e3c'
    },
    {
      title: 'Overdue',
      value: 1,
      icon: <WarningIcon />,
      color: '#d32f2f'
    }
  ];

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Compliance Audits
      </Typography>
      
      <Box mb={3}>
        <Grid container spacing={2}>
          {auditStats.map((stat, index) => (
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
            Audit Management
          </Typography>
          <Typography variant="body1" color="text.secondary">
            This section will allow you to schedule compliance audits, track audit progress, 
            record findings, and manage corrective actions.
          </Typography>
        </CardContent>
      </Card>
    </div>
  );
};

export default ComplianceAudits;