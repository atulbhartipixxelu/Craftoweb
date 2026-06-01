import { Outlet, useLocation } from 'react-router-dom';
import { useEffect } from 'react';
import AOS from 'aos';
import SmoothScroll from '../effects/SmoothScroll';
import CustomCursor from '../effects/CustomCursor';
import PageLoader from '../common/PageLoader';
import Header from './Header';
import Footer from './Footer';
import FloatingContact from './FloatingContact';
import { useGsapScroll } from '../../hooks/useGsapScroll';

function Layout() {
  const { pathname } = useLocation();
  useGsapScroll(pathname);

  useEffect(() => {
    AOS.init({ duration: 650, once: true, offset: 48, easing: 'ease-out-cubic' });
  }, []);

  useEffect(() => {
    window.scrollTo(0, 0);
    const t = setTimeout(() => AOS.refresh(), 100);
    return () => clearTimeout(t);
  }, [pathname]);

  return (
    <SmoothScroll>
      <PageLoader />
      <CustomCursor />
      <Header />
      <main className="main-content">
        <Outlet />
      </main>
      <Footer />
      <FloatingContact />
    </SmoothScroll>
  );
}

export default Layout;
