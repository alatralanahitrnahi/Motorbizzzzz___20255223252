import React from 'react';
import {
  Typography,
  Box,
  Card,
  CardContent,
  Grid
} from '@mui/material';
import {
  Category as CategoryIcon,
  Business as BusinessIcon,
  People as PeopleIcon,
  AccountBalance as FinancialIcon
} from '@mui/icons-material';

const RiskCategories = () => {
  // Mock data for demonstration
  const categories = [
    {
      title: 'Operational Risk',
      count: 24,
      icon: <CategoryIcon />,
      color: '#1976d2',
      description: 'Risks related to operations and processes'
    },
    {
      title: 'Financial Risk',
      count: 12,
      icon: <FinancialIcon />,
      color: '#388e3c',
      description: 'Risks related to financial operations'
    },
    {
      title: 'Strategic Risk',
      count: 8,
      icon: <BusinessIcon />,
      color: '#f57c00',
      description: 'Risks related to business strategy'
    },
    {
      title: 'Compliance Risk',
      count: 15,
      icon: <PeopleIcon />,
      color: '#7b1fa2',
      description: 'Risks related to regulatory compliance'
    }
  ];

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Risk Categories
      </Typography>
      
      <Box mb={3}>
        <Grid container spacing={2}>
          {categories.map((category, index) => (
            <Grid item xs={12} sm={6} md={3} key={index}>
              <Card>
                <CardContent>
                  <Box display="flex" alignItems="center">
                    <Box 
                      sx={{ 
                        backgroundColor: category.color, 
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
                      {category.icon}
                    </Box>
                    <div>
                      <Typography variant="h5">{category.count}</Typography>
                      <Typography variant="body2" color="text.secondary">{category.title}</Typography>
                    </div>
                  </Box>
                  <Box mt={2}>
                    <Typography variant="body2" color="text.secondary">
                      {category.description}
                    </Typography>
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
            Risk Category Management
          </Typography>
          <Typography variant="body1" color="text.secondary">
            This section will allow you to define and manage risk categories, assign owners, 
            and establish category-specific risk assessment methodologies.
          </Typography>
        </CardContent>
      </Card>
    </div>
  );
};

export default RiskCategories;