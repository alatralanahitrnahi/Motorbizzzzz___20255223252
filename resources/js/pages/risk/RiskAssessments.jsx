import React from 'react';
import {
  Typography,
  Box,
  Grid,
  Card,
  CardContent
} from '@mui/material';
import RisksTable from '../../components/risk/RisksTable';

const RiskAssessments = () => {
  return (
    <div>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h4">
          Risk Assessments
        </Typography>
      </Box>

      <Grid container spacing={3}>
        <Grid item xs={12}>
          <Card>
            <CardContent>
              <RisksTable />
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </div>
  );
};

export default RiskAssessments;