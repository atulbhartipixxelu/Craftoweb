import { Link } from 'react-router-dom';
import { navLinks, services, companyInfo, socialLinks } from '../../data/content';
import './Footer.css';

function Footer() {
  return (
    <footer className="site-footer">
      <div className="container footer-top">
        <div className="footer-brand">
          <Link to="/" className="logo">
            Crafto<span>Web</span>
          </Link>
          <p>
            We craft premium websites, apps, and digital experiences that help ambitious brands grow faster.
          </p>
          <Link to="/contact" className="btn btn-primary">
            Start a Project
          </Link>
        </div>

        <div className="footer-links">
          <div>
            <h4>Navigate</h4>
            <ul>
              {navLinks.map((l) => (
                <li key={l.path}>
                  <Link to={l.path}>{l.label}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4>Services</h4>
            <ul>
              {services.map((s) => (
                <li key={s.id}>
                  <Link to={`/services#${s.id}`}>{s.title}</Link>
                </li>
              ))}
            </ul>
          </div>
          <div>
            <h4>Contact</h4>
            <ul className="footer-contact">
              <li>
                <a href={`mailto:${companyInfo.email}`}>{companyInfo.email}</a>
              </li>
              <li>
                <a href={`tel:${companyInfo.phone.replace(/\s/g, '')}`}>{companyInfo.phone}</a>
              </li>
              <li>{companyInfo.address}</li>
            </ul>
            <div className="footer-social">
              {socialLinks.map((s) => (
                <a key={s.name} href={s.url} target="_blank" rel="noreferrer">
                  {s.name}
                </a>
              ))}
            </div>
          </div>
        </div>
      </div>

      <div className="container footer-bottom">
        <p>© {new Date().getFullYear()} CraftoWeb. All rights reserved.</p>
        <div className="footer-legal">
          <Link to="/contact">Privacy</Link>
          <Link to="/contact">Terms</Link>
        </div>
      </div>
    </footer>
  );
}

export default Footer;
