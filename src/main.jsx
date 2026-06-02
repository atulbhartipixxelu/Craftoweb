import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import ErrorBoundary from './components/common/ErrorBoundary';
import './styles/global.css';
import './styles/animations.css';
import './components/effects/CustomCursor.css';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-coverflow';

const rootEl = document.getElementById('root');

if (!rootEl) {
  document.body.innerHTML =
    '<p style="padding:2rem;font-family:sans-serif">Root element missing. Deploy the <strong>dist</strong> folder, not source code.</p>';
} else {
  createRoot(rootEl).render(
    <StrictMode>
      <ErrorBoundary>
        <App />
      </ErrorBoundary>
    </StrictMode>
  );
}
