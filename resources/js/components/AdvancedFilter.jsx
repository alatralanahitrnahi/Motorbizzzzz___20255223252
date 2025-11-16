import React, { useState } from 'react';
import {
  Box,
  Button,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  TextField,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Checkbox,
  FormControlLabel,
  FormGroup,
  Divider,
  Chip,
  IconButton,
  Tooltip
} from '@mui/material';
import {
  FilterList as FilterIcon,
  Clear as ClearIcon,
  Add as AddIcon
} from '@mui/icons-material';

const AdvancedFilter = ({ 
  columns = [], 
  onFilterChange, 
  currentFilters = {}, 
  onClearFilters 
}) => {
  const [open, setOpen] = useState(false);
  const [filters, setFilters] = useState(currentFilters);
  const [activeFilters, setActiveFilters] = useState([]);

  const handleOpen = () => {
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
  };

  const handleApply = () => {
    onFilterChange(filters);
    
    // Update active filters for display
    const active = Object.entries(filters)
      .filter(([key, value]) => value !== '' && value !== null && value !== undefined)
      .map(([key, value]) => ({
        column: key,
        value: value,
        label: columns.find(col => col.id === key)?.label || key
      }));
    
    setActiveFilters(active);
    handleClose();
  };

  const handleClear = () => {
    setFilters({});
    setActiveFilters([]);
    onClearFilters();
    handleClose();
  };

  const handleFilterChange = (columnId, value) => {
    setFilters(prev => ({
      ...prev,
      [columnId]: value
    }));
  };

  const removeActiveFilter = (columnId) => {
    const newFilters = { ...filters };
    delete newFilters[columnId];
    setFilters(newFilters);
    
    const newActiveFilters = activeFilters.filter(f => f.column !== columnId);
    setActiveFilters(newActiveFilters);
    
    onFilterChange(newFilters);
  };

  const getColumnOptions = (column) => {
    // This would typically come from the data or be predefined
    if (column.id === 'status') {
      return [
        { value: 'active', label: 'Active' },
        { value: 'inactive', label: 'Inactive' },
        { value: 'archived', label: 'Archived' }
      ];
    }
    
    if (column.id === 'risk_level') {
      return [
        { value: 'low', label: 'Low' },
        { value: 'medium', label: 'Medium' },
        { value: 'high', label: 'High' },
        { value: 'critical', label: 'Critical' }
      ];
    }
    
    if (column.id === 'priority') {
      return [
        { value: 1, label: 'P1 - Critical' },
        { value: 2, label: 'P2 - High' },
        { value: 3, label: 'P3 - Medium' },
        { value: 4, label: 'P4 - Low' },
        { value: 5, label: 'P5 - Lowest' }
      ];
    }
    
    return [];
  };

  return (
    <Box>
      <Box display="flex" alignItems="center" gap={1} mb={2} flexWrap="wrap">
        <Button
          variant="outlined"
          startIcon={<FilterIcon />}
          onClick={handleOpen}
        >
          Advanced Filter
        </Button>
        
        {activeFilters.map((filter, index) => (
          <Chip
            key={index}
            label={`${filter.label}: ${filter.value}`}
            onDelete={() => removeActiveFilter(filter.column)}
            size="small"
          />
        ))}
        
        {activeFilters.length > 0 && (
          <Tooltip title="Clear all filters">
            <IconButton size="small" onClick={handleClear}>
              <ClearIcon />
            </IconButton>
          </Tooltip>
        )}
      </Box>

      <Dialog open={open} onClose={handleClose} maxWidth="sm" fullWidth>
        <DialogTitle>Advanced Filter</DialogTitle>
        <DialogContent>
          <Box sx={{ mt: 2 }}>
            {columns
              .filter(column => column.filterable !== false)
              .map((column, index) => (
                <Box key={column.id} mb={2}>
                  {index > 0 && <Divider sx={{ my: 2 }} />}
                  
                  <FormControl fullWidth>
                    <InputLabel>{column.label}</InputLabel>
                    
                    {column.type === 'text' && (
                      <TextField
                        value={filters[column.id] || ''}
                        onChange={(e) => handleFilterChange(column.id, e.target.value)}
                        placeholder={`Filter by ${column.label}`}
                      />
                    )}
                    
                    {column.type === 'select' && (
                      <Select
                        value={filters[column.id] || ''}
                        onChange={(e) => handleFilterChange(column.id, e.target.value)}
                      >
                        <MenuItem value="">All</MenuItem>
                        {getColumnOptions(column).map((option, optionIndex) => (
                          <MenuItem key={optionIndex} value={option.value}>
                            {option.label}
                          </MenuItem>
                        ))}
                      </Select>
                    )}
                    
                    {column.type === 'boolean' && (
                      <FormGroup>
                        <FormControlLabel
                          control={
                            <Checkbox
                              checked={filters[column.id] === true}
                              onChange={(e) => handleFilterChange(column.id, e.target.checked ? true : '')}
                            />
                          }
                          label="Yes"
                        />
                        <FormControlLabel
                          control={
                            <Checkbox
                              checked={filters[column.id] === false}
                              onChange={(e) => handleFilterChange(column.id, e.target.checked ? false : '')}
                            />
                          }
                          label="No"
                        />
                      </FormGroup>
                    )}
                    
                    {column.type === 'date' && (
                      <TextField
                        type="date"
                        value={filters[column.id] || ''}
                        onChange={(e) => handleFilterChange(column.id, e.target.value)}
                        InputLabelProps={{ shrink: true }}
                      />
                    )}
                  </FormControl>
                </Box>
              ))}
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleClear} color="secondary">
            Clear All
          </Button>
          <Button onClick={handleClose}>
            Cancel
          </Button>
          <Button onClick={handleApply} variant="contained" color="primary">
            Apply Filters
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default AdvancedFilter;