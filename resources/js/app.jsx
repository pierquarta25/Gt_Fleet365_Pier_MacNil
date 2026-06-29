import './bootstrap';
import '../css/app.css';

import React from 'react';
import { createRoot } from 'react-dom/client';
import VehicleForm from './components/VehicleForm';
import { LanguageProvider } from './i18n/LanguageContext';

const rootElement = document.getElementById('app');
if (rootElement) {
    const root = createRoot(rootElement);
    root.render(
        <LanguageProvider>
            <VehicleForm />
        </LanguageProvider>
    );
}
