import React from 'react';
import {
  Card,
  CardContent,
  Typography,
  Box
} from '@mui/material';
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  BarElement,
  Title
} from 'chart.js';
import { Pie, Bar } from 'react-chartjs-2';

ChartJS.register(
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  BarElement,
  Title
);

const ComplianceStatusChart = ({ requirements = [] }) => {
  // Pie chart data for compliance status
  const statusData = {
    labels: ['Active', 'Inactive', 'Archived'],
    datasets: [
      {
        data: [
          requirements.filter(r => r.status === 'active').length,
          requirements.filter(r => r.status === 'inactive').length,
          requirements.filter(r => r.status === 'archived').length
        ],
        backgroundColor: [
          '#4caf50', // Green for active
          '#ff9800', // Orange for inactive
          '#9e9e9e'  // Gray for archived
        ],
        borderWidth: 1,
      },
    ],
  };

  // Bar chart data for compliance by category
  const categories = [...new Set(requirements.map(r => r.category || 'Uncategorized'))];
  const categoryData = {
    labels: categories,
    datasets: [
      {
        label: 'Compliance Requirements',
        data: categories.map(category => 
          requirements.filter(r => (r.category || 'Uncategorized') === category).length
        ),
        backgroundColor: 'rgba(25, 118, 210, 0.7)',
        borderColor: 'rgba(25, 118, 210, 1)',
        borderWidth: 1,
      },
    ],
  };

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
      },
    },
  };

  return (
    <Card>
      <CardContent>
        <Typography variant="h6" gutterBottom>
          Compliance Status Overview
        </Typography>
        
        <Box display="flex" flexDirection={{ xs: 'column', md: 'row' }} gap={3} height={300}>
          <Box flex={1} position="relative">
            <Pie data={statusData} options={chartOptions} />
          </Box>
          
          <Box flex={2} position="relative">
            <Bar 
              data={categoryData} 
              options={{
                ...chartOptions,
                scales: {
                  y: {
                    beginAtZero: true,
                    ticks: {
                      stepSize: 1
                    }
                  }
                }
              }} 
            />
          </Box>
        </Box>
        
        <Box display="flex" justifyContent="center" mt={2} gap={3}>
          <Box display="flex" alignItems="center">
            <Box sx={{ width: 12, height: 12, backgroundColor: '#4caf50', mr: 1 }} />
            <Typography variant="body2">Active ({requirements.filter(r => r.status === 'active').length})</Typography>
          </Box>
          <Box display="flex" alignItems="center">
            <Box sx={{ width: 12, height: 12, backgroundColor: '#ff9800', mr: 1 }} />
            <Typography variant="body2">Inactive ({requirements.filter(r => r.status === 'inactive').length})</Typography>
          </Box>
          <Box display="flex" alignItems="center">
            <Box sx={{ width: 12, height: 12, backgroundColor: '#9e9e9e', mr: 1 }} />
            <Typography variant="body2">Archived ({requirements.filter(r => r.status === 'archived').length})</Typography>
          </Box>
        </Box>
      </CardContent>
    </Card>
  );
};

export default ComplianceStatusChart;