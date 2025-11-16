import React from 'react';
import {
  Typography,
  Box,
  Card,
  CardContent,
  Grid
} from '@mui/material';
import {
  Report as IncidentIcon,
  Error as CriticalIcon,
  Warning as WarningIcon,
  Info as InfoIcon
} from '@mui/icons-material';

const RiskIncidents = () => {
  // Mock data for demonstration
  const incidentStats = [
    {
      title: 'Total Incidents',
      value: 15,
      icon: <IncidentIcon />,
      color: '#1976d2'
    },
    {
      title: 'Critical',
      value: 2,
      icon: <CriticalIcon />,
      color: '#d32f2f'
    },
    {
      title: 'High',
      value: 5,
      icon: <WarningIcon />,
      color: '#f57c00'
    },
    {
      title: 'Medium/Low',
      value: 8,
      icon: <InfoIcon />,
      color: '#388e3c'
    }
  ];

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Risk Incidents
      </Typography>
      
      <Box mb={3}>
        <Grid container spacing={2}>
          {incidentStats.map((stat, index) => (
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
            Incident Management
          </Typography>
          <Typography variant="body1" color="text.secondary">
            This section will allow you to report risk incidents, investigate root causes, 
            track corrective actions, and analyze incident trends to prevent recurrence.
          </Typography>
        </CardContent>
      </Card>
    </div>
  );
};

export default RiskIncidents;