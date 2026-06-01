import { HiOutlineSearch, HiOutlineCog } from 'react-icons/hi';
import './Header.css';

function Header({ title = 'Dashboard Overview' }) {
  return (
    <header className="header">
      <h1 className="header-title">{title}</h1>
      <div className="header-actions">
        <div className="search-bar">
          <HiOutlineSearch className="search-icon" />
          <input type="text" placeholder="Search" className="search-input" />
        </div>
        <button className="settings-btn" aria-label="Settings">
          <HiOutlineCog />
        </button>
      </div>
    </header>
  );
}

export default Header;
