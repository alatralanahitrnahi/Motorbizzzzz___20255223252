import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import { complianceApi } from '../services/apiService';

// Async thunks for API calls
export const fetchComplianceRequirements = createAsyncThunk(
  'compliance/fetchRequirements',
  async (params = {}, { rejectWithValue }) => {
    try {
      const response = await complianceApi.getComplianceRequirements(params);
      return response;
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

export const createComplianceRequirement = createAsyncThunk(
  'compliance/createRequirement',
  async (requirementData, { rejectWithValue }) => {
    try {
      const response = await complianceApi.createComplianceRequirement(requirementData);
      return response;
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

export const updateComplianceRequirement = createAsyncThunk(
  'compliance/updateRequirement',
  async ({ id, ...requirementData }, { rejectWithValue }) => {
    try {
      const response = await complianceApi.updateComplianceRequirement(id, requirementData);
      return response;
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

export const deleteComplianceRequirement = createAsyncThunk(
  'compliance/deleteRequirement',
  async (id, { rejectWithValue }) => {
    try {
      await complianceApi.deleteComplianceRequirement(id);
      return id;
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

// Similar thunks for other compliance entities would go here
// For brevity, I'll just include the requirements ones

const complianceSlice = createSlice({
  name: 'compliance',
  initialState: {
    requirements: [],
    documents: [],
    audits: [],
    certificates: [],
    loading: false,
    error: null,
  },
  reducers: {
    clearError: (state) => {
      state.error = null;
    },
  },
  extraReducers: (builder) => {
    builder
      // Fetch requirements
      .addCase(fetchComplianceRequirements.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchComplianceRequirements.fulfilled, (state, action) => {
        state.loading = false;
        // Handle both paginated and non-paginated responses
        if (action.payload.data) {
          state.requirements = action.payload.data;
        } else {
          state.requirements = action.payload;
        }
      })
      .addCase(fetchComplianceRequirements.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload;
      })
      // Create requirement
      .addCase(createComplianceRequirement.fulfilled, (state, action) => {
        state.requirements.unshift(action.payload.compliance_requirement || action.payload);
      })
      .addCase(createComplianceRequirement.rejected, (state, action) => {
        state.error = action.payload;
      })
      // Update requirement
      .addCase(updateComplianceRequirement.fulfilled, (state, action) => {
        const updatedRequirement = action.payload.compliance_requirement || action.payload;
        const index = state.requirements.findIndex(req => req.id === updatedRequirement.id);
        if (index !== -1) {
          state.requirements[index] = updatedRequirement;
        }
      })
      .addCase(updateComplianceRequirement.rejected, (state, action) => {
        state.error = action.payload;
      })
      // Delete requirement
      .addCase(deleteComplianceRequirement.fulfilled, (state, action) => {
        state.requirements = state.requirements.filter(req => req.id !== action.payload);
      })
      .addCase(deleteComplianceRequirement.rejected, (state, action) => {
        state.error = action.payload;
      });
  },
});

export const { clearError } = complianceSlice.actions;
export default complianceSlice.reducer;