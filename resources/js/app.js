import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';

// Get the root element
const rootElement = document.getElementById('app');

// Create a root and render the App component
if (rootElement) {
  const root = createRoot(rootElement);
  root.render(<App />);
}