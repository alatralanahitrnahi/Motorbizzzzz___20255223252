import React, { useEffect } from 'react';
import {
  Grid,
  Card,
  CardContent,
  Typography,
  Box,
  CircularProgress,
  Alert
} from '@mui/material';
import {
  Gavel as GavelIcon,
  Warning as WarningIcon,
  Description as DescriptionIcon,
  Assignment as AssignmentIcon,
  CardMembership as CardMembershipIcon,
  Category as CategoryIcon,
  Report as ReportIcon
} from '@mui/icons-material';
import { useSelector, useDispatch } from 'react-redux';
import { fetchComplianceRequirements } from '../store/complianceSlice';
import { fetchRisks } from '../store/riskSlice';
import ComplianceStatusChart from '../components/dashboard/ComplianceStatusChart';
import RiskHeatmap from '../components/dashboard/RiskHeatmap';

const Dashboard = () => {
  const dispatch = useDispatch();
  const { requirements, loading: complianceLoading, error: complianceError } = useSelector(state => state.compliance);
  const { risks, loading: riskLoading, error: riskError } = useSelector(state => state.risk);

  useEffect(() => {
    dispatch(fetchComplianceRequirements());
    dispatch(fetchRisks());
  }, [dispatch]);

  const stats = [
    {
      title: 'Compliance Requirements',
      value: requirements.length,
      icon: <GavelIcon />,
      color: '#1976d2'
    },
    {
      title: 'Active Risks',
      value: risks.filter(r => r.status !== 'closed').length,
      icon: <WarningIcon />,
      color: '#dc004e'
    },
    {
      title: 'Compliance Documents',
      value: 24,
      icon: <DescriptionIcon />,
      color: '#388e3c'
    },
    {
      title: 'Scheduled Audits',
      value: 8,
      icon: <AssignmentIcon />,
      color: '#f57c00'
    },
    {
      title: 'Certificates & Licenses',
      value: 12,
      icon: <CardMembershipIcon />,
      color: '#7b1fa2'
    },
    {
      title: 'Risk Categories',
      value: 5,
      icon: <CategoryIcon />,
      color: '#1976d2'
    },
    {
      title: 'Risk Incidents',
      value: 3,
      icon: <ReportIcon />,
      color: '#d32f2f'
    },
    {
      title: 'Business Continuity Plans',
      value: 4,
      icon: <AssignmentIcon />,
      color: '#388e3c'
    }
  ];

  if (complianceLoading || riskLoading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="400px">
        <CircularProgress />
      </Box>
    );
  }

  if (complianceError || riskError) {
    return (
      <Alert severity="error">
        Error loading dashboard data: {complianceError || riskError}
      </Alert>
    );
  }

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Compliance & Risk Management Dashboard
      </Typography>
      
      <Grid container spacing={3}>
        {stats.map((stat, index) => (
          <Grid item xs={12} sm={6} md={3} key={index}>
            <Card>
              <CardContent>
                <Box display="flex" alignItems="center">
                  <Box 
                    sx={{ 
                      backgroundColor: stat.color, 
                      borderRadius: '50%', 
                      width: 56, 
                      height: 56, 
                      display: 'flex', 
                      alignItems: 'center', 
                      justifyContent: 'center',
                      color: 'white'
                    }}
                  >
                    {stat.icon}
                  </Box>
                  <Box ml={2}>
                    <Typography variant="h4" component="div">
                      {stat.value}
                    </Typography>
                    <Typography variant="body2" color="text.secondary">
                      {stat.title}
                    </Typography>
                  </Box>
                </Box>
              </CardContent>
            </Card>
          </Grid>
        ))}
      </Grid>

      <Box mt={4}>
        <Grid container spacing={3}>
          <Grid item xs={12} md={6}>
            <ComplianceStatusChart requirements={requirements} />
          </Grid>
          
          <Grid item xs={12} md={6}>
            <RiskHeatmap risks={risks} />
          </Grid>
        </Grid>
      </Box>

      <Box mt={4}>
        <Grid container spacing={3}>
          <Grid item xs={12} md={6}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  Recent Compliance Requirements
                </Typography>
                {requirements.slice(0, 5).map((req, index) => (
                  <Box key={req.id} mb={2} pb={2} borderBottom={index < requirements.slice(0, 5).length - 1 ? 1 : 0} borderColor="divider">
                    <Typography variant="subtitle1">{req.name}</Typography>
                    <Typography variant="body2" color="text.secondary">
                      {req.category} • {req.status}
                    </Typography>
                  </Box>
                ))}
              </CardContent>
            </Card>
          </Grid>
          
          <Grid item xs={12} md={6}>
            <Card>
              <CardContent>
                <Typography variant="h6" gutterBottom>
                  Recent Risk Assessments
                </Typography>
                {risks.slice(0, 5).map((risk, index) => (
                  <Box key={risk.id} mb={2} pb={2} borderBottom={index < risks.slice(0, 5).length - 1 ? 1 : 0} borderColor="divider">
                    <Typography variant="subtitle1">{risk.title}</Typography>
                    <Typography variant="body2" color="text.secondary">
                      {risk.risk_category?.name || 'Uncategorized'} • {risk.risk_level} risk
                    </Typography>
                  </Box>
                ))}
              </CardContent>
            </Card>
          </Grid>
        </Grid>
      </Box>
    </div>
  );
};

export default Dashboard;