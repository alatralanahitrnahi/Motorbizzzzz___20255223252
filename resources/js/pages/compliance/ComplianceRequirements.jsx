import React from 'react';
import {
  Typography,
  Box,
  Grid,
  Card,
  CardContent
} from '@mui/material';
import RequirementsTable from '../../components/compliance/RequirementsTable';

const ComplianceRequirements = () => {
  return (
    <div>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h4">
          Compliance Requirements
        </Typography>
      </Box>

      <Grid container spacing={3}>
        <Grid item xs={12}>
          <Card>
            <CardContent>
              <RequirementsTable />
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </div>
  );
};

export default ComplianceRequirements;