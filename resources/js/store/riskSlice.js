import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import { riskApi } from '../services/apiService';

// Async thunks for API calls
export const fetchRisks = createAsyncThunk(
  'risk/fetchRisks',
  async (params = {}, { rejectWithValue }) => {
    try {
      const response = await riskApi.getRisks(params);
      return response;
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

export const createRisk = createAsyncThunk(
  'risk/createRisk',
  async (riskData, { rejectWithValue }) => {
    try {
      const response = await riskApi.createRisk(riskData);
      return response;
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

export const updateRisk = createAsyncThunk(
  'risk/updateRisk',
  async ({ id, ...riskData }, { rejectWithValue }) => {
    try {
      const response = await riskApi.updateRisk(id, riskData);
      return response;
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

export const deleteRisk = createAsyncThunk(
  'risk/deleteRisk',
  async (id, { rejectWithValue }) => {
    try {
      await riskApi.deleteRisk(id);
      return id;
    } catch (error) {
      return rejectWithValue(error.message);
    }
  }
);

// Similar thunks for other risk entities would go here
// For brevity, I'll just include the risks ones

const riskSlice = createSlice({
  name: 'risk',
  initialState: {
    risks: [],
    categories: [],
    incidents: [],
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
      // Fetch risks
      .addCase(fetchRisks.pending, (state) => {
        state.loading = true;
        state.error = null;
      })
      .addCase(fetchRisks.fulfilled, (state, action) => {
        state.loading = false;
        // Handle both paginated and non-paginated responses
        if (action.payload.data) {
          state.risks = action.payload.data;
        } else {
          state.risks = action.payload;
        }
      })
      .addCase(fetchRisks.rejected, (state, action) => {
        state.loading = false;
        state.error = action.payload;
      })
      // Create risk
      .addCase(createRisk.fulfilled, (state, action) => {
        state.risks.unshift(action.payload.risk || action.payload);
      })
      .addCase(createRisk.rejected, (state, action) => {
        state.error = action.payload;
      })
      // Update risk
      .addCase(updateRisk.fulfilled, (state, action) => {
        const updatedRisk = action.payload.risk || action.payload;
        const index = state.risks.findIndex(risk => risk.id === updatedRisk.id);
        if (index !== -1) {
          state.risks[index] = updatedRisk;
        }
      })
      .addCase(updateRisk.rejected, (state, action) => {
        state.error = action.payload;
      })
      // Delete risk
      .addCase(deleteRisk.fulfilled, (state, action) => {
        state.risks = state.risks.filter(risk => risk.id !== action.payload);
      })
      .addCase(deleteRisk.rejected, (state, action) => {
        state.error = action.payload;
      });
  },
});

export const { clearError } = riskSlice.actions;
export default riskSlice.reducer;