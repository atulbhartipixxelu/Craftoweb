import { Link } from 'react-router-dom';
import { services, companyInfo } from '../../data/content';
import './Footer.css';

function Footer() {
  return (
    <footer className="site-footer">
      <div className="container footer-grid">
        <div>
          <Link to="/" className="brand" aria-label="CraftoWeb home">
            <img src="/logo.png" alt="CraftoWeb" />
          </Link>
          <p>Web design, development, and digital growth for teams that want work that ships.</p>
          <a href={`mailto:${companyInfo.email}`}>{companyInfo.email}</a>
          <a href={`tel:${companyInfo.phone.replace(/\s/g, '')}`}>{companyInfo.phone}</a>
        </div>
        <div>
          <h4>Company</h4>
          <ul>
            <li><Link to="/about">About Us</Link></li>
            <li><Link to="/services">Our Services</Link></li>
            <li><Link to="/work">Portfolio</Link></li>
            <li><Link to="/blog">Blogs</Link></li>
            <li><Link to="/reviews">Reviews</Link></li>
            <li><Link to="/contact">Contact Us</Link></li>
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
          <h4>Visit us</h4>
          <p>{companyInfo.address}</p>
          <Link to="/contact" className="btn btn-primary footer-btn">
            Get in touch
          </Link>
        </div>
      </div>
      <div className="footer-bottom">
        <div className="container">
          <p>© {new Date().getFullYear()} CraftoWeb. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
}

export default Footer;
