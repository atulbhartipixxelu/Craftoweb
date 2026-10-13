import { useEffect, useRef, useState } from 'react';
import { Link, NavLink, useLocation } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import { HiOutlineChevronDown, HiOutlineMenu, HiOutlineX } from 'react-icons/hi';
import { projects, services } from '../../data/content';
import './Header.css';

const links = [
  { path: '/', label: 'Home', end: true },
  { path: '/about', label: 'About' },
  { path: '/services', label: 'Services', menu: 'services' },
  { path: '/work', label: 'Portfolio', menu: 'portfolio' },
  { path: '/blog', label: 'Blogs' },
  { path: '/reviews', label: 'Reviews' },
  { path: '/contact', label: 'Contact Us' },
];

const latestProjects = projects.slice(0, 4);

function Header() {
  const [open, setOpen] = useState(false);
  const [mega, setMega] = useState(null);
  const [scrolled, setScrolled] = useState(false);
  const closeTimer = useRef(null);
  const { pathname } = useLocation();

  const showMega = (key) => {
    window.clearTimeout(closeTimer.current);
    setMega(key);
  };

  const hideMega = () => {
    closeTimer.current = window.setTimeout(() => setMega(null), 140);
  };

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  useEffect(() => {
    setOpen(false);
    setMega(null);
  }, [pathname]);

  return (
    <header className={`site-header ${scrolled ? 'scrolled' : ''}`}>
      <div className="container header-row">
        <Link to="/" className="brand" aria-label="CraftoWeb home">
          <img src="/logo.png" alt="CraftoWeb" />
        </Link>

        <nav className="header-nav">
          {links.map((link) =>
            link.menu ? (
              <div
                key={link.path}
                className="nav-mega-wrap"
                onMouseEnter={() => showMega(link.menu)}
                onMouseLeave={hideMega}
              >
                <NavLink to={link.path} className={({ isActive }) => `nav-item ${isActive || mega === link.menu ? 'active' : ''}`}>
                  {link.label}
                  <HiOutlineChevronDown className={mega === link.menu ? 'open' : ''} />
                </NavLink>
              </div>
            ) : (
              <NavLink
                key={link.path}
                to={link.path}
                end={link.end}
                className={({ isActive }) => `nav-item ${isActive ? 'active' : ''}`}
              >
                {link.label}
              </NavLink>
            )
          )}
        </nav>

        <div className="header-actions">
          <Link to="/contact" className="btn btn-primary header-quote">
            Get a quote
          </Link>
          <button type="button" className="menu-btn" onClick={() => setOpen((v) => !v)} aria-label="Menu">
            {open ? <HiOutlineX /> : <HiOutlineMenu />}
          </button>
        </div>
      </div>

      <AnimatePresence>
        {mega && (
          <motion.button
            type="button"
            className="mega-overlay"
            aria-label="Close menu"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.22 }}
            onClick={() => setMega(null)}
          />
        )}
      </AnimatePresence>

      <AnimatePresence>
        {mega && (
          <motion.div
            className={`mega-panel ${mega === 'portfolio' ? 'mega-panel-work' : ''}`}
            initial={{ opacity: 0, y: -8 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -6 }}
            transition={{ duration: 0.22 }}
            onMouseEnter={() => showMega(mega)}
            onMouseLeave={hideMega}
          >
            {mega === 'services' ? (
              <>
                <div className="container mega-top">
                  <div className="mega-intro">
                    <p>Our services</p>
                    <h2>Everything you need to design, build, and grow.</h2>
                    <Link to="/services" onClick={() => setMega(null)}>
                      View all services
                    </Link>
                  </div>
                  <div className="mega-grid">
                    {services.map((service) => (
                      <Link key={service.id} to={`/services#${service.id}`} className="mega-card" onClick={() => setMega(null)}>
                        <img src={service.image} alt="" />
                        <strong>{service.title}</strong>
                        <span>{service.items.slice(0, 3).join(' · ')}</span>
                      </Link>
                    ))}
                  </div>
                </div>
                <MegaMarquee items={services.map((service) => ({ id: service.id, title: service.title, image: service.image }))} />
              </>
            ) : (
              <div className="work-mega">
                <Link to="/work" className="work-feature" onClick={() => setMega(null)}>
                  <img src={latestProjects[0].image} alt="" />
                  <div>
                    <span>Latest · {latestProjects[0].category}</span>
                    <strong>{latestProjects[0].title}</strong>
                    <p>{latestProjects[0].subtitle}</p>
                  </div>
                </Link>
                <div className="work-side">
                  <div className="work-side-head">
                    <p>More recent work</p>
                    <Link to="/work" onClick={() => setMega(null)}>
                      View all
                    </Link>
                  </div>
                  {latestProjects.slice(1).map((project) => (
                    <Link key={project.id} to="/work" className="work-row" onClick={() => setMega(null)}>
                      <img src={project.image} alt="" />
                      <div>
                        <span>{project.category}</span>
                        <strong>{project.title}</strong>
                      </div>
                    </Link>
                  ))}
                </div>
                <div className="work-film" aria-hidden="true">
                  <div className="work-film-track">
                    {[...projects, ...projects].map((project, index) => (
                      <span key={`${project.id}-${index}`}>
                        <img src={project.image} alt="" />
                        <em>{project.title}</em>
                      </span>
                    ))}
                  </div>
                </div>
              </div>
            )}
          </motion.div>
        )}
      </AnimatePresence>

      <AnimatePresence>
        {open && (
          <motion.nav className="mobile-menu" initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}>
            {links.map((link) => (
              <NavLink key={link.path} to={link.path} end={link.end} className="mobile-link" onClick={() => setOpen(false)}>
                {link.label}
              </NavLink>
            ))}
            <Link to="/contact" className="btn btn-primary mobile-cta" onClick={() => setOpen(false)}>
              Get a quote
            </Link>
          </motion.nav>
        )}
      </AnimatePresence>
    </header>
  );
}

function MegaMarquee({ items }) {
  const loop = [...items, ...items, ...items];

  return (
    <div className="mega-marquee" aria-hidden="true">
      <div className="mega-track">
        {loop.map((item, index) => (
          <span key={`${item.id}-${index}`}>
            <img src={item.image} alt="" />
            {item.title}
          </span>
        ))}
      </div>
    </div>
  );
}

export default Header;
