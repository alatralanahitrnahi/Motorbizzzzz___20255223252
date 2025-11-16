import React from 'react';
import {
  Card,
  CardContent,
  Typography,
  Box
} from '@mui/material';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Colors
} from 'chart.js';
import { Scatter } from 'react-chartjs-2';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Colors
);

const RiskHeatmap = ({ risks = [] }) => {
  // Map risk levels to numerical values for plotting
  const getRiskValue = (level) => {
    switch (level) {
      case 'low': return 1;
      case 'medium': return 2;
      case 'high': return 3;
      case 'critical': return 4;
      default: return 0;
    }
  };

  // Map likelihood to numerical values
  const getLikelihoodValue = (likelihood) => {
    switch (likelihood) {
      case 'low': return 1;
      case 'medium': return 2;
      case 'high': return 3;
      default: return 0;
    }
  };

  // Map impact to numerical values
  const getImpactValue = (impact) => {
    switch (impact) {
      case 'low': return 1;
      case 'medium': return 2;
      case 'high': return 3;
      default: return 0;
    }
  };

  // Prepare data for scatter plot
  const scatterData = {
    datasets: [
      {
        label: 'Low Risk',
        data: risks
          .filter(risk => risk.risk_level === 'low')
          .map(risk => ({
            x: getLikelihoodValue(risk.likelihood),
            y: getImpactValue(risk.impact),
            r: 8,
            risk: risk
          })),
        backgroundColor: 'rgba(76, 175, 80, 0.7)', // Green
      },
      {
        label: 'Medium Risk',
        data: risks
          .filter(risk => risk.risk_level === 'medium')
          .map(risk => ({
            x: getLikelihoodValue(risk.likelihood),
            y: getImpactValue(risk.impact),
            r: 12,
            risk: risk
          })),
        backgroundColor: 'rgba(255, 152, 0, 0.7)', // Orange
      },
      {
        label: 'High Risk',
        data: risks
          .filter(risk => risk.risk_level === 'high')
          .map(risk => ({
            x: getLikelihoodValue(risk.likelihood),
            y: getImpactValue(risk.impact),
            r: 16,
            risk: risk
          })),
        backgroundColor: 'rgba(244, 67, 54, 0.7)', // Red
      },
      {
        label: 'Critical Risk',
        data: risks
          .filter(risk => risk.risk_level === 'critical')
          .map(risk => ({
            x: getLikelihoodValue(risk.likelihood),
            y: getImpactValue(risk.impact),
            r: 20,
            risk: risk
          })),
        backgroundColor: 'rgba(158, 158, 158, 0.7)', // Dark Gray
      }
    ],
  };

  const options = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: {
        title: {
          display: true,
          text: 'Likelihood'
        },
        min: 0,
        max: 4,
        ticks: {
          callback: function(value) {
            if (value === 1) return 'Low';
            if (value === 2) return 'Medium';
            if (value === 3) return 'High';
            return '';
          }
        }
      },
      y: {
        title: {
          display: true,
          text: 'Impact'
        },
        min: 0,
        max: 4,
        ticks: {
          callback: function(value) {
            if (value === 1) return 'Low';
            if (value === 2) return 'Medium';
            if (value === 3) return 'High';
            return '';
          }
        }
      }
    },
    plugins: {
      tooltip: {
        callbacks: {
          label: function(context) {
            const risk = context.dataset.data[context.dataIndex].risk;
            return [
              `Title: ${risk.title}`,
              `Category: ${risk.risk_category?.name || 'Uncategorized'}`,
              `Status: ${risk.status}`,
              `Owner: ${risk.owner?.name || 'Unassigned'}`
            ];
          }
        }
      },
      legend: {
        position: 'top',
      },
    },
  };

  return (
    <Card>
      <CardContent>
        <Typography variant="h6" gutterBottom>
          Risk Heatmap
        </Typography>
        
        <Box position="relative" height={300}>
          <Scatter data={scatterData} options={options} />
        </Box>
        
        <Box display="flex" justifyContent="center" mt={2} flexWrap="wrap" gap={2}>
          <Box display="flex" alignItems="center">
            <Box sx={{ width: 12, height: 12, backgroundColor: 'rgba(76, 175, 80, 0.7)', mr: 1 }} />
            <Typography variant="body2">Low Risk ({risks.filter(r => r.risk_level === 'low').length})</Typography>
          </Box>
          <Box display="flex" alignItems="center">
            <Box sx={{ width: 12, height: 12, backgroundColor: 'rgba(255, 152, 0, 0.7)', mr: 1 }} />
            <Typography variant="body2">Medium Risk ({risks.filter(r => r.risk_level === 'medium').length})</Typography>
          </Box>
          <Box display="flex" alignItems="center">
            <Box sx={{ width: 12, height: 12, backgroundColor: 'rgba(244, 67, 54, 0.7)', mr: 1 }} />
            <Typography variant="body2">High Risk ({risks.filter(r => r.risk_level === 'high').length})</Typography>
          </Box>
          <Box display="flex" alignItems="center">
            <Box sx={{ width: 12, height: 12, backgroundColor: 'rgba(158, 158, 158, 0.7)', mr: 1 }} />
            <Typography variant="body2">Critical Risk ({risks.filter(r => r.risk_level === 'critical').length})</Typography>
          </Box>
        </Box>
      </CardContent>
    </Card>
  );
};

export default RiskHeatmap;