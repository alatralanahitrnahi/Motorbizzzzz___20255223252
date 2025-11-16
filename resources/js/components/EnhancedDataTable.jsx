import React, { useState, useEffect } from 'react';
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  TablePagination,
  TableSortLabel,
  TextField,
  IconButton,
  Box,
  CircularProgress,
  Alert,
  Chip,
  Tooltip,
  Typography
} from '@mui/material';
import { 
  Search as SearchIcon, 
  Edit as EditIcon, 
  Delete as DeleteIcon,
  FilterList as FilterIcon,
  Clear as ClearIcon
} from '@mui/icons-material';
import AdvancedFilter from './AdvancedFilter';

const EnhancedDataTable = ({
  columns,
  data,
  loading,
  error,
  page,
  rowsPerPage,
  totalCount,
  onPageChange,
  onRowsPerPageChange,
  onSort,
  orderBy,
  order,
  onSearch,
  searchQuery,
  onEdit,
  onDelete,
  actions = true,
  onFilterChange,
  currentFilters = {},
  onClearFilters,
  title = "Data Table"
}) => {
  const [localSearchQuery, setLocalSearchQuery] = useState(searchQuery || '');

  useEffect(() => {
    setLocalSearchQuery(searchQuery || '');
  }, [searchQuery]);

  const handleChangePage = (event, newPage) => {
    onPageChange(newPage);
  };

  const handleChangeRowsPerPage = (event) => {
    onRowsPerPageChange(parseInt(event.target.value, 10));
  };

  const handleSort = (property) => {
    const isAsc = orderBy === property && order === 'asc';
    onSort(property, isAsc ? 'desc' : 'asc');
  };

  const handleSearch = (event) => {
    const query = event.target.value;
    setLocalSearchQuery(query);
    onSearch(query);
  };

  const handleSearchSubmit = (event) => {
    if (event.key === 'Enter') {
      onSearch(localSearchQuery);
    }
  };

  const hasActiveFilters = Object.keys(currentFilters).some(
    key => currentFilters[key] !== '' && currentFilters[key] !== null && currentFilters[key] !== undefined
  );

  if (loading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="200px">
        <CircularProgress />
      </Box>
    );
  }

  if (error) {
    return (
      <Alert severity="error" sx={{ mb: 2 }}>
        {error}
      </Alert>
    );
  }

  return (
    <Paper sx={{ width: '100%', overflow: 'hidden' }}>
      <Box sx={{ p: 2 }}>
        <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
          <Typography variant="h6">{title}</Typography>
          <Box>
            {hasActiveFilters && (
              <Tooltip title="Clear all filters">
                <IconButton onClick={onClearFilters} size="small">
                  <ClearIcon />
                </IconButton>
              </Tooltip>
            )}
          </Box>
        </Box>
        
        <Box display="flex" gap={2} mb={2} flexWrap="wrap">
          <TextField
            variant="outlined"
            size="small"
            placeholder="Search..."
            value={localSearchQuery}
            onChange={handleSearch}
            onKeyDown={handleSearchSubmit}
            InputProps={{
              startAdornment: (
                <IconButton size="small">
                  <SearchIcon />
                </IconButton>
              ),
            }}
            sx={{ minWidth: 200 }}
          />
          
          <AdvancedFilter
            columns={columns}
            onFilterChange={onFilterChange}
            currentFilters={currentFilters}
            onClearFilters={onClearFilters}
          />
        </Box>
      </Box>
      
      <TableContainer>
        <Table stickyHeader aria-label="enhanced data table">
          <TableHead>
            <TableRow>
              {columns.map((column) => (
                <TableCell
                  key={column.id}
                  align={column.align || 'left'}
                  style={{ minWidth: column.minWidth }}
                  sortDirection={orderBy === column.id ? order : false}
                >
                  {column.sortable !== false ? (
                    <TableSortLabel
                      active={orderBy === column.id}
                      direction={orderBy === column.id ? order : 'asc'}
                      onClick={() => handleSort(column.id)}
                    >
                      {column.label}
                    </TableSortLabel>
                  ) : (
                    column.label
                  )}
                </TableCell>
              ))}
              {actions && (
                <TableCell align="center">
                  Actions
                </TableCell>
              )}
            </TableRow>
          </TableHead>
          <TableBody>
            {data
              .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
              .map((row, index) => (
                <TableRow hover role="checkbox" tabIndex={-1} key={row.id || index}>
                  {columns.map((column) => {
                    const value = row[column.id];
                    return (
                      <TableCell key={column.id} align={column.align || 'left'}>
                        {column.format ? column.format(value, row) : value}
                      </TableCell>
                    );
                  })}
                  {actions && (
                    <TableCell align="center">
                      <IconButton 
                        size="small" 
                        onClick={() => onEdit(row)}
                        sx={{ mr: 1 }}
                      >
                        <EditIcon />
                      </IconButton>
                      <IconButton 
                        size="small" 
                        onClick={() => onDelete(row)}
                      >
                        <DeleteIcon />
                      </IconButton>
                    </TableCell>
                  )}
                </TableRow>
              ))}
          </TableBody>
        </Table>
      </TableContainer>
      
      <TablePagination
        rowsPerPageOptions={[5, 10, 25, 50, 100]}
        component="div"
        count={totalCount}
        rowsPerPage={rowsPerPage}
        page={page}
        onPageChange={handleChangePage}
        onRowsPerPageChange={handleChangeRowsPerPage}
      />
    </Paper>
  );
};

export default EnhancedDataTable;