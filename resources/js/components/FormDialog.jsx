import React from 'react';
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Button,
  TextField,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  FormControlLabel,
  Checkbox,
  FormGroup,
  DatePicker
} from '@mui/material';
import { useForm, Controller } from 'react-hook-form';

const FormDialog = ({ open, onClose, title, fields, onSubmit, initialValues = {}, submitText = 'Save' }) => {
  const { control, handleSubmit, reset, formState: { errors } } = useForm({
    defaultValues: initialValues
  });

  const handleClose = () => {
    reset({});
    onClose();
  };

  const handleFormSubmit = (data) => {
    onSubmit(data);
    reset({});
    handleClose();
  };

  return (
    <Dialog open={open} onClose={handleClose} maxWidth="sm" fullWidth>
      <DialogTitle>{title}</DialogTitle>
      <DialogContent>
        <form onSubmit={handleSubmit(handleFormSubmit)} id="form-dialog-form">
          {fields.map((field, index) => (
            <div key={index} style={{ marginTop: '16px' }}>
              {field.type === 'text' && (
                <Controller
                  name={field.name}
                  control={control}
                  rules={field.rules}
                  render={({ field: controllerField }) => (
                    <TextField
                      {...controllerField}
                      fullWidth
                      label={field.label}
                      variant="outlined"
                      error={!!errors[field.name]}
                      helperText={errors[field.name]?.message}
                      type={field.inputType || 'text'}
                    />
                  )}
                />
              )}
              
              {field.type === 'select' && (
                <Controller
                  name={field.name}
                  control={control}
                  rules={field.rules}
                  render={({ field: controllerField }) => (
                    <FormControl fullWidth error={!!errors[field.name]}>
                      <InputLabel>{field.label}</InputLabel>
                      <Select
                        {...controllerField}
                        label={field.label}
                      >
                        {field.options.map((option, optionIndex) => (
                          <MenuItem key={optionIndex} value={option.value}>
                            {option.label}
                          </MenuItem>
                        ))}
                      </Select>
                    </FormControl>
                  )}
                />
              )}
              
              {field.type === 'checkbox' && (
                <Controller
                  name={field.name}
                  control={control}
                  rules={field.rules}
                  render={({ field: controllerField }) => (
                    <FormControlLabel
                      control={
                        <Checkbox
                          {...controllerField}
                          checked={!!controllerField.value}
                          onChange={(e) => controllerField.onChange(e.target.checked)}
                        />
                      }
                      label={field.label}
                    />
                  )}
                />
              )}
              
              {field.type === 'date' && (
                <Controller
                  name={field.name}
                  control={control}
                  rules={field.rules}
                  render={({ field: controllerField }) => (
                    <TextField
                      {...controllerField}
                      fullWidth
                      label={field.label}
                      variant="outlined"
                      type="date"
                      InputLabelProps={{
                        shrink: true,
                      }}
                      error={!!errors[field.name]}
                      helperText={errors[field.name]?.message}
                    />
                  )}
                />
              )}
            </div>
          ))}
        </form>
      </DialogContent>
      <DialogActions>
        <Button onClick={handleClose}>Cancel</Button>
        <Button type="submit" form="form-dialog-form" variant="contained" color="primary">
          {submitText}
        </Button>
      </DialogActions>
    </Dialog>
  );
};

export default FormDialog;