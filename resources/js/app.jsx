import './bootstrap';
import '../css/app.css';

import React from 'react';
import { createRoot } from 'react-dom/client';
import VehicleForm from './components/VehicleForm';

// Cerco l'elemento con id "app" nel file Blade e inizializzo React
const rootElement = document.getElementById('app');
if (rootElement) {
    const root = createRoot(rootElement);
    root.render(<VehicleForm />);
}
