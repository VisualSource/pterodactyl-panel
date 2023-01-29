import { createRoot } from 'react-dom/client';
import { App } from '@/components/App';
import { StrictMode } from 'react';

// Enable language support.
import './i18n';

createRoot(document.getElementById('app')!).render(
    <StrictMode>
        <App />
    </StrictMode>,
);
