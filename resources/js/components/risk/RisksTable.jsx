import React, { useState, useEffect } from 'react';
import {
  Paper,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TablePagination,
  TableSortLabel,
  TextField,
  IconButton,
  Button,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Box,
  Chip,
  Tooltip,
  Typography,
  Select,
  MenuItem,
  FormControl,
  InputLabel
} from '@mui/material';
import {
  Search as SearchIcon,
  Edit as EditIcon,
  Delete as DeleteIcon,
  Add as AddIcon,
  TrendingUp as HighRiskIcon,
  TrendingDown as LowRiskIcon,
  Remove as MediumRiskIcon
} from '@mui/icons-material';
import { useSelector, useDispatch } from 'react-redux';
import { useForm, Controller } from 'react-hook-form';
import { 
  fetchRisks, 
  createRisk, 
  updateRisk, 
  deleteRisk
} from '../../store/riskSlice';

const RisksTable = () => {
  const dispatch = useDispatch();
  const { risks, loading } = useSelector(state => state.risk);
  
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [orderBy, setOrderBy] = useState('created_at');
  const [order, setOrder] = useState('desc');
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [riskLevelFilter, setRiskLevelFilter] = useState('');
  const [openDialog, setOpenDialog] = useState(false);
  const [editingRisk, setEditingRisk] = useState(null);
  const [filteredRisks, setFilteredRisks] = useState([]);

  const { control, handleSubmit, reset, formState: { errors } } = useForm();

  useEffect(() => {
    dispatch(fetchRisks());
  }, [dispatch]);

  useEffect(() => {
    let result = risks;
    
    // Apply search filter
    if (searchQuery) {
      result = result.filter(risk => 
        risk.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        risk.description?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        risk.cause?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        risk.effect?.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }
    
    // Apply status filter
    if (statusFilter) {
      result = result.filter(risk => risk.status === statusFilter);
    }
    
    // Apply risk level filter
    if (riskLevelFilter) {
      result = result.filter(risk => risk.risk_level === riskLevelFilter);
    }
    
    // Apply sorting
    result = [...result].sort((a, b) => {
      const aVal = a[orderBy];
      const bVal = b[orderBy];
      
      if (order === 'asc') {
        return aVal < bVal ? -1 : 1;
      } else {
        return aVal > bVal ? -1 : 1;
      }
    });
    
    setFilteredRisks(result);
  }, [risks, searchQuery, statusFilter, riskLevelFilter, orderBy, order]);

  const handleSort = (property) => {
    const isAsc = orderBy === property && order === 'asc';
    setOrderBy(property);
    setOrder(isAsc ? 'desc' : 'asc');
  };

  const handleChangePage = (event, newPage) => {
    setPage(newPage);
  };

  const handleChangeRowsPerPage = (event) => {
    setRowsPerPage(parseInt(event.target.value, 10));
    setPage(0);
  };

  const handleSearch = (event) => {
    setSearchQuery(event.target.value);
    setPage(0);
  };

  const handleStatusFilterChange = (event) => {
    setStatusFilter(event.target.value);
    setPage(0);
  };

  const handleRiskLevelFilterChange = (event) => {
    setRiskLevelFilter(event.target.value);
    setPage(0);
  };

  const handleEdit = (risk) => {
    setEditingRisk(risk);
    reset(risk);
    setOpenDialog(true);
  };

  const handleDelete = (risk) => {
    if (window.confirm(`Are you sure you want to delete "${risk.title}"?`)) {
      dispatch(deleteRisk(risk.id));
    }
  };

  const handleAddNew = () => {
    setEditingRisk(null);
    reset({
      title: '',
      description: '',
      cause: '',
      effect: '',
      risk_category_id: '',
      owner_id: '',
      likelihood: 'medium',
      impact: 'medium',
      risk_level: 'medium',
      status: 'identified',
      assessment_date: '',
      review_date: ''
    });
    setOpenDialog(true);
  };

  const handleCloseDialog = () => {
    setOpenDialog(false);
    setEditingRisk(null);
  };

  const onSubmit = (data) => {
    if (editingRisk) {
      dispatch(updateRisk({ id: editingRisk.id, ...data }));
    } else {
      dispatch(createRisk(data));
    }
    handleCloseDialog();
  };

  const getRiskLevelColor = (riskLevel) => {
    switch (riskLevel) {
      case 'low': return 'success';
      case 'medium': return 'warning';
      case 'high': return 'error';
      case 'critical': return 'error';
      default: return 'default';
    }
  };

  const getRiskLevelIcon = (riskLevel) => {
    switch (riskLevel) {
      case 'low': return <TrendingDown as LowRiskIcon />;
      case 'medium': return <Remove as MediumRiskIcon />;
      case 'high': return <TrendingUp as HighRiskIcon />;
      case 'critical': return <TrendingUp as HighRiskIcon />;
      default: return null;
    }
  };

  const getLikelihoodColor = (likelihood) => {
    switch (likelihood) {
      case 'low': return 'success';
      case 'medium': return 'warning';
      case 'high': return 'error';
      default: return 'default';
    }
  };

  const getImpactColor = (impact) => {
    switch (impact) {
      case 'low': return 'success';
      case 'medium': return 'warning';
      case 'high': return 'error';
      default: return 'default';
    }
  };

  return (
    <Box>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={2} gap={2}>
        <Box display="flex" gap={2} flex={1}>
          <TextField
            variant="outlined"
            size="small"
            placeholder="Search risks..."
            value={searchQuery}
            onChange={handleSearch}
            InputProps={{
              startAdornment: <SearchIcon sx={{ mr: 1, color: 'gray' }} />
            }}
            sx={{ minWidth: 200 }}
          />
          <FormControl size="small" sx={{ minWidth: 120 }}>
            <InputLabel>Status</InputLabel>
            <Select
              value={statusFilter}
              label="Status"
              onChange={handleStatusFilterChange}
            >
              <MenuItem value="">All</MenuItem>
              <MenuItem value="identified">Identified</MenuItem>
              <MenuItem value="assessed">Assessed</MenuItem>
              <MenuItem value="mitigated">Mitigated</MenuItem>
              <MenuItem value="monitored">Monitored</MenuItem>
              <MenuItem value="closed">Closed</MenuItem>
            </Select>
          </FormControl>
          <FormControl size="small" sx={{ minWidth: 120 }}>
            <InputLabel>Risk Level</InputLabel>
            <Select
              value={riskLevelFilter}
              label="Risk Level"
              onChange={handleRiskLevelFilterChange}
            >
              <MenuItem value="">All</MenuItem>
              <MenuItem value="low">Low</MenuItem>
              <MenuItem value="medium">Medium</MenuItem>
              <MenuItem value="high">High</MenuItem>
              <MenuItem value="critical">Critical</MenuItem>
            </Select>
          </FormControl>
        </Box>
        <Button
          variant="contained"
          startIcon={<AddIcon />}
          onClick={handleAddNew}
        >
          Add Risk
        </Button>
      </Box>

      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell sortDirection={orderBy === 'title' ? order : false}>
                <TableSortLabel
                  active={orderBy === 'title'}
                  direction={orderBy === 'title' ? order : 'asc'}
                  onClick={() => handleSort('title')}
                >
                  Title
                </TableSortLabel>
              </TableCell>
              <TableCell>Category</TableCell>
              <TableCell sortDirection={orderBy === 'likelihood' ? order : false}>
                <TableSortLabel
                  active={orderBy === 'likelihood'}
                  direction={orderBy === 'likelihood' ? order : 'asc'}
                  onClick={() => handleSort('likelihood')}
                >
                  Likelihood
                </TableSortLabel>
              </TableCell>
              <TableCell sortDirection={orderBy === 'impact' ? order : false}>
                <TableSortLabel
                  active={orderBy === 'impact'}
                  direction={orderBy === 'impact' ? order : 'asc'}
                  onClick={() => handleSort('impact')}
                >
                  Impact
                </TableSortLabel>
              </TableCell>
              <TableCell sortDirection={orderBy === 'risk_level' ? order : false}>
                <TableSortLabel
                  active={orderBy === 'risk_level'}
                  direction={orderBy === 'risk_level' ? order : 'asc'}
                  onClick={() => handleSort('risk_level')}
                >
                  Risk Level
                </TableSortLabel>
              </TableCell>
              <TableCell sortDirection={orderBy === 'status' ? order : false}>
                <TableSortLabel
                  active={orderBy === 'status'}
                  direction={orderBy === 'status' ? order : 'asc'}
                  onClick={() => handleSort('status')}
                >
                  Status
                </TableSortLabel>
              </TableCell>
              <TableCell>Owner</TableCell>
              <TableCell align="center">Actions</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {filteredRisks
              .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
              .map((risk) => (
                <TableRow key={risk.id} hover>
                  <TableCell>
                    <Typography variant="body2" fontWeight="medium">
                      {risk.title}
                    </Typography>
                    {risk.description && (
                      <Typography variant="caption" color="text.secondary">
                        {risk.description.substring(0, 50)}...
                      </Typography>
                    )}
                  </TableCell>
                  <TableCell>
                    <Chip 
                      label={risk.risk_category?.name || 'Uncategorized'} 
                      size="small" 
                      variant="outlined"
                    />
                  </TableCell>
                  <TableCell>
                    <Chip 
                      label={risk.likelihood} 
                      size="small" 
                      color={getLikelihoodColor(risk.likelihood)}
                    />
                  </TableCell>
                  <TableCell>
                    <Chip 
                      label={risk.impact} 
                      size="small" 
                      color={getImpactColor(risk.impact)}
                    />
                  </TableCell>
                  <TableCell>
                    <Chip 
                      label={risk.risk_level} 
                      size="small" 
                      color={getRiskLevelColor(risk.risk_level)}
                      icon={getRiskLevelIcon(risk.risk_level)}
                    />
                  </TableCell>
                  <TableCell>
                    <Chip 
                      label={risk.status} 
                      size="small" 
                      color={
                        risk.status === 'identified' ? 'info' :
                        risk.status === 'assessed' ? 'warning' :
                        risk.status === 'mitigated' ? 'success' :
                        risk.status === 'monitored' ? 'primary' : 'default'
                      }
                    />
                  </TableCell>
                  <TableCell>
                    {risk.owner?.name || 'Unassigned'}
                  </TableCell>
                  <TableCell align="center">
                    <IconButton size="small" onClick={() => handleEdit(risk)}>
                      <EditIcon />
                    </IconButton>
                    <IconButton size="small" onClick={() => handleDelete(risk)}>
                      <DeleteIcon />
                    </IconButton>
                  </TableCell>
                </TableRow>
              ))}
          </TableBody>
        </Table>
      </TableContainer>

      <TablePagination
        rowsPerPageOptions={[5, 10, 25, 50]}
        component="div"
        count={filteredRisks.length}
        rowsPerPage={rowsPerPage}
        page={page}
        onPageChange={handleChangePage}
        onRowsPerPageChange={handleChangeRowsPerPage}
      />

      <Dialog open={openDialog} onClose={handleCloseDialog} maxWidth="sm" fullWidth>
        <DialogTitle>
          {editingRisk ? 'Edit Risk Assessment' : 'Add Risk Assessment'}
        </DialogTitle>
        <DialogContent>
          <Box component="form" onSubmit={handleSubmit(onSubmit)} sx={{ mt: 2 }}>
            <Controller
              name="title"
              control={control}
              defaultValue=""
              rules={{ required: 'Title is required' }}
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Title"
                  fullWidth
                  margin="normal"
                  error={!!errors.title}
                  helperText={errors.title?.message}
                />
              )}
            />
            
            <Controller
              name="description"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Description"
                  fullWidth
                  margin="normal"
                  multiline
                  rows={3}
                />
              )}
            />
            
            <Controller
              name="cause"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Cause"
                  fullWidth
                  margin="normal"
                />
              )}
            />
            
            <Controller
              name="effect"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Effect"
                  fullWidth
                  margin="normal"
                />
              )}
            />
            
            <Controller
              name="risk_category_id"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Risk Category"
                  fullWidth
                  margin="normal"
                  select
                  SelectProps={{ native: true }}
                >
                  <option value="">Select Category</option>
                  <option value={1}>Operational Risk</option>
                  <option value={2}>Financial Risk</option>
                  <option value={3}>Strategic Risk</option>
                  <option value={4}>Compliance Risk</option>
                  <option value={5}>Reputational Risk</option>
                </TextField>
              )}
            />
            
            <Controller
              name="owner_id"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Owner"
                  fullWidth
                  margin="normal"
                  select
                  SelectProps={{ native: true }}
                >
                  <option value="">Select Owner</option>
                  <option value={1}>John Smith</option>
                  <option value={2}>Sarah Johnson</option>
                  <option value={3}>Mike Davis</option>
                </TextField>
              )}
            />
            
            <Controller
              name="likelihood"
              control={control}
              defaultValue="medium"
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Likelihood"
                  fullWidth
                  margin="normal"
                  select
                  SelectProps={{ native: true }}
                >
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </TextField>
              )}
            />
            
            <Controller
              name="impact"
              control={control}
              defaultValue="medium"
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Impact"
                  fullWidth
                  margin="normal"
                  select
                  SelectProps={{ native: true }}
                >
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </TextField>
              )}
            />
            
            <Controller
              name="risk_level"
              control={control}
              defaultValue="medium"
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Risk Level"
                  fullWidth
                  margin="normal"
                  select
                  SelectProps={{ native: true }}
                >
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="critical">Critical</option>
                </TextField>
              )}
            />
            
            <Controller
              name="status"
              control={control}
              defaultValue="identified"
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Status"
                  fullWidth
                  margin="normal"
                  select
                  SelectProps={{ native: true }}
                >
                  <option value="identified">Identified</option>
                  <option value="assessed">Assessed</option>
                  <option value="mitigated">Mitigated</option>
                  <option value="monitored">Monitored</option>
                  <option value="closed">Closed</option>
                </TextField>
              )}
            />
            
            <Controller
              name="assessment_date"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Assessment Date"
                  type="date"
                  fullWidth
                  margin="normal"
                  InputLabelProps={{ shrink: true }}
                />
              )}
            />
            
            <Controller
              name="review_date"
              control={control}
              defaultValue=""
              render={({ field }) => (
                <TextField
                  {...field}
                  label="Review Date"
                  type="date"
                  fullWidth
                  margin="normal"
                  InputLabelProps={{ shrink: true }}
                />
              )}
            />
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleCloseDialog}>Cancel</Button>
          <Button onClick={handleSubmit(onSubmit)} variant="contained" color="primary">
            {editingRisk ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default RisksTable;