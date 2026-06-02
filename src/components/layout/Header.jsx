import { useState, useEffect } from 'react';
import { Link, NavLink, useLocation } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import { HiOutlineMenu, HiOutlineX, HiOutlineSun, HiOutlineMoon } from 'react-icons/hi';
import { useTheme } from '../../context/ThemeContext';
import { navLinks } from '../../data/content';
import './Header.css';

function Header() {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const { isDark, toggleTheme } = useTheme();
  const { pathname } = useLocation();

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 40);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  useEffect(() => setOpen(false), [pathname]);

  return (
    <header className={`site-header ${scrolled ? 'scrolled' : ''}`}>
      <div className="container header-row">
        <Link to="/" className="logo">
          Crafto<span>Web</span>
        </Link>

        <nav className="header-nav">
          {navLinks.map((link) => (
            <NavLink
              key={link.path}
              to={link.path}
              end={link.path === '/'}
              className={({ isActive }) => `nav-item ${isActive ? 'active' : ''}`}
            >
              {link.label}
            </NavLink>
          ))}
        </nav>

        <div className="header-actions">
          <button type="button" className="icon-btn" onClick={toggleTheme} aria-label="Toggle theme">
            {isDark ? <HiOutlineSun /> : <HiOutlineMoon />}
          </button>
          <Link to="/contact" className="btn btn-primary header-cta">
            Get in Touch
          </Link>
          <button type="button" className="icon-btn menu-btn" onClick={() => setOpen(!open)} aria-label="Menu">
            {open ? <HiOutlineX /> : <HiOutlineMenu />}
          </button>
        </div>
      </div>

      <AnimatePresence>
        {open && (
          <motion.nav
            className="mobile-menu"
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: 'auto' }}
            exit={{ opacity: 0, height: 0 }}
          >
            {navLinks.map((link) => (
              <NavLink key={link.path} to={link.path} end={link.path === '/'} className="mobile-link">
                {link.label}
              </NavLink>
            ))}
            <Link to="/contact" className="btn btn-primary mobile-cta">
              Get in Touch
            </Link>
          </motion.nav>
        )}
      </AnimatePresence>
    </header>
  );
}

export default Header;
