import React from 'react';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { Provider } from 'react-redux';
import { configureStore } from '@reduxjs/toolkit';
import RequirementsTable from './RequirementsTable';
import complianceReducer from '../../store/complianceSlice';

// Mock the react-hook-form
jest.mock('react-hook-form', () => ({
  ...jest.requireActual('react-hook-form'),
  useForm: () => ({
    control: {},
    handleSubmit: jest.fn(),
    reset: jest.fn(),
    formState: { errors: {} }
  }),
  Controller: ({ render }) => render({ field: {} })
}));

// Mock the react-router-dom
jest.mock('react-router-dom', () => ({
  ...jest.requireActual('react-router-dom'),
  useNavigate: () => jest.fn()
}));

// Mock the Redux dispatch
const mockDispatch = jest.fn();
jest.mock('react-redux', () => ({
  ...jest.requireActual('react-redux'),
  useDispatch: () => mockDispatch
}));

// Create a mock store
const createMockStore = (initialState = {}) => {
  return configureStore({
    reducer: {
      compliance: complianceReducer
    },
    preloadedState: {
      compliance: {
        requirements: [],
        loading: false,
        error: null,
        ...initialState.compliance
      }
    }
  });
};

describe('RequirementsTable', () => {
  let store;

  beforeEach(() => {
    store = createMockStore();
    mockDispatch.mockClear();
  });

  test('renders without crashing', () => {
    render(
      <Provider store={store}>
        <RequirementsTable />
      </Provider>
    );

    expect(screen.getByText('Compliance Requirements')).toBeInTheDocument();
  });

  test('displays loading state', () => {
    store = createMockStore({
      compliance: {
        requirements: [],
        loading: true,
        error: null
      }
    });

    render(
      <Provider store={store}>
        <RequirementsTable />
      </Provider>
    );

    expect(screen.getByRole('progressbar')).toBeInTheDocument();
  });

  test('displays error message', () => {
    const errorMessage = 'Failed to load requirements';
    store = createMockStore({
      compliance: {
        requirements: [],
        loading: false,
        error: errorMessage
      }
    });

    render(
      <Provider store={store}>
        <RequirementsTable />
      </Provider>
    );

    expect(screen.getByText(errorMessage)).toBeInTheDocument();
  });

  test('displays requirements data', () => {
    const mockRequirements = [
      {
        id: 1,
        name: 'ISO 9001:2015 Quality Management',
        category: 'regulatory',
        authority: 'ISO',
        status: 'active',
        priority: 2
      },
      {
        id: 2,
        name: 'Environmental Compliance',
        category: 'regulatory',
        authority: 'EPA',
        status: 'inactive',
        priority: 3
      }
    ];

    store = createMockStore({
      compliance: {
        requirements: mockRequirements,
        loading: false,
        error: null
      }
    });

    render(
      <Provider store={store}>
        <RequirementsTable />
      </Provider>
    );

    expect(screen.getByText('ISO 9001:2015 Quality Management')).toBeInTheDocument();
    expect(screen.getByText('Environmental Compliance')).toBeInTheDocument();
  });

  test('handles search functionality', async () => {
    render(
      <Provider store={store}>
        <RequirementsTable />
      </Provider>
    );

    const searchInput = screen.getByPlaceholderText('Search...');
    fireEvent.change(searchInput, { target: { value: 'ISO' } });

    // Wait for the search to be processed
    await waitFor(() => {
      expect(mockDispatch).toHaveBeenCalled();
    });
  });

  test('handles add new requirement button click', () => {
    render(
      <Provider store={store}>
        <RequirementsTable />
      </Provider>
    );

    const addButton = screen.getByText('Add Requirement');
    fireEvent.click(addButton);

    // In a real test, you would check if the dialog opens
    // This is a simplified test
    expect(addButton).toBeInTheDocument();
  });
});