import { configureStore } from '@reduxjs/toolkit';
import complianceReducer from './complianceSlice';
import riskReducer from './riskSlice';

export const store = configureStore({
  reducer: {
    compliance: complianceReducer,
    risk: riskReducer,
  },
});

export default store;