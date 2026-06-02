import { lazy, Suspense } from 'react';
import { BrowserRouter, Routes, Route, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import { ThemeProvider } from './context/ThemeContext';
import Layout from './components/layout/Layout';
import PageTransition from './components/common/PageTransition';

const Home = lazy(() => import('./pages/Home'));
const About = lazy(() => import('./pages/About'));
const Services = lazy(() => import('./pages/Services'));
const Blog = lazy(() => import('./pages/Blog'));
const BlogDetail = lazy(() => import('./pages/BlogDetail'));
const Work = lazy(() => import('./pages/Work'));
const Reviews = lazy(() => import('./pages/Reviews'));
const Contact = lazy(() => import('./pages/Contact'));

function PageFallback() {
  return (
    <div style={{ minHeight: '50vh', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      <p style={{ color: 'var(--text-muted)' }}>Loading…</p>
    </div>
  );
}

function AnimatedRoutes() {
  const location = useLocation();

  return (
    <AnimatePresence mode="wait">
      <Routes location={location} key={location.pathname}>
        <Route path="/" element={<Layout />}>
          <Route
            index
            element={
              <PageTransition>
                <Suspense fallback={<PageFallback />}>
                  <Home />
                </Suspense>
              </PageTransition>
            }
          />
          <Route
            path="about"
            element={
              <PageTransition>
                <Suspense fallback={<PageFallback />}>
                  <About />
                </Suspense>
              </PageTransition>
            }
          />
          <Route
            path="services"
            element={
              <PageTransition>
                <Suspense fallback={<PageFallback />}>
                  <Services />
                </Suspense>
              </PageTransition>
            }
          />
          <Route
            path="work"
            element={
              <PageTransition>
                <Suspense fallback={<PageFallback />}>
                  <Work />
                </Suspense>
              </PageTransition>
            }
          />
          <Route
            path="blog"
            element={
              <PageTransition>
                <Suspense fallback={<PageFallback />}>
                  <Blog />
                </Suspense>
              </PageTransition>
            }
          />
          <Route
            path="blog/:slug"
            element={
              <PageTransition>
                <Suspense fallback={<PageFallback />}>
                  <BlogDetail />
                </Suspense>
              </PageTransition>
            }
          />
          <Route
            path="reviews"
            element={
              <PageTransition>
                <Suspense fallback={<PageFallback />}>
                  <Reviews />
                </Suspense>
              </PageTransition>
            }
          />
          <Route
            path="contact"
            element={
              <PageTransition>
                <Suspense fallback={<PageFallback />}>
                  <Contact />
                </Suspense>
              </PageTransition>
            }
          />
        </Route>
      </Routes>
    </AnimatePresence>
  );
}

function App() {
  const basename = import.meta.env.BASE_URL.replace(/\/$/, '') || undefined;

  return (
    <ThemeProvider>
      <BrowserRouter basename={basename}>
        <AnimatedRoutes />
      </BrowserRouter>
    </ThemeProvider>
  );
}

export default App;
