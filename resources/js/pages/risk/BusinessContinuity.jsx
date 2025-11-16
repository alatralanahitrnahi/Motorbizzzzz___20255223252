import React from 'react';
import {
  Typography,
  Box,
  Card,
  CardContent,
  Grid
} from '@mui/material';
import {
  Business as BusinessIcon,
  EventAvailable as PlannedIcon,
  EventRepeat as TestingIcon,
  GppGood as ProtectedIcon
} from '@mui/icons-material';

const BusinessContinuity = () => {
  // Mock data for demonstration
  const continuityStats = [
    {
      title: 'Total Plans',
      value: 6,
      icon: <BusinessIcon />,
      color: '#1976d2'
    },
    {
      title: 'Active',
      value: 5,
      icon: <ProtectedIcon />,
      color: '#388e3c'
    },
    {
      title: 'Testing Scheduled',
      value: 2,
      icon: <TestingIcon />,
      color: '#f57c00'
    },
    {
      title: 'Review Needed',
      value: 1,
      icon: <PlannedIcon />,
      color: '#7b1fa2'
    }
  ];

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Business Continuity Planning
      </Typography>
      
      <Box mb={3}>
        <Grid container spacing={2}>
          {continuityStats.map((stat, index) => (
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
            Business Continuity Management
          </Typography>
          <Typography variant="body1" color="text.secondary">
            This section will allow you to develop business continuity plans, schedule testing exercises, 
            track critical resources, and ensure organizational resilience during disruptions.
          </Typography>
        </CardContent>
      </Card>
    </div>
  );
};

export default BusinessContinuity;