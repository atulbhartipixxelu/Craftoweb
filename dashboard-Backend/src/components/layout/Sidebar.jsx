import { NavLink } from 'react-router-dom';
import {
  HiOutlineViewGrid,
  HiOutlineChatAlt2,
  HiOutlineCalendar,
  HiOutlineChartBar,
  HiOutlineNewspaper,
  HiOutlineUserGroup,
  HiOutlineFolder,
  HiOutlineLightningBolt,
  HiOutlineShare,
  HiOutlineLockClosed,
  HiOutlineCog,
  HiOutlineQuestionMarkCircle,
  HiOutlineChat,
  HiOutlinePlus,
  HiOutlineBell,
  HiOutlineLogout,
  HiOutlineGlobe,
  HiOutlineDocumentText,
  HiOutlineAcademicCap,
  HiOutlineUsers,
  HiOutlineServer,
} from 'react-icons/hi';
import { useTheme } from '../../context/ThemeContext';
import { useAuth, useLogout } from '../../context/AuthContext';
import { navGroups, managementNav } from '../../data/mockData';
import './Sidebar.css';

const iconMap = {
  dashboard: HiOutlineViewGrid,
  message: HiOutlineChatAlt2,
  schedule: HiOutlineCalendar,
  analytics: HiOutlineChartBar,
  news: HiOutlineNewspaper,
  recruitment: HiOutlineUserGroup,
  project: HiOutlineFolder,
  activity: HiOutlineLightningBolt,
  shared: HiOutlineShare,
  privacy: HiOutlineLockClosed,
  settings: HiOutlineCog,
  help: HiOutlineQuestionMarkCircle,
  chat: HiOutlineChat,
  clients: HiOutlineUsers,
  users: HiOutlineUserGroup,
  domain: HiOutlineServer,
  updates: HiOutlineDocumentText,
  school: HiOutlineAcademicCap,
  reports: HiOutlineChartBar,
};

function NavIcon({ name }) {
  const Icon = iconMap[name] || HiOutlineViewGrid;
  return <Icon />;
}

function Sidebar() {
  const { theme, toggleTheme } = useTheme();
  const { user } = useAuth();
  const handleLogout = useLogout();

  return (
    <aside className="sidebar">
      <div className="sidebar-logo">
        <div className="logo-icon">
          <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="20" cy="20" r="18" stroke="url(#logoGrad)" strokeWidth="2.5" />
            <path d="M14 20 L20 14 L26 20 L20 26 Z" fill="url(#logoGrad)" />
            <defs>
              <linearGradient id="logoGrad" x1="0" y1="0" x2="40" y2="40">
                <stop stopColor="#ec4899" />
                <stop offset="1" stopColor="#f97316" />
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div className="logo-text">
          <span className="logo-title">CraftoWeb</span>
          <span className="logo-subtitle">Digital Agency </span>
        </div>
      </div>

      <nav className="sidebar-nav">
        {navGroups.map((group) => (
          <div key={group.title} className="nav-group">
            <span className="nav-group-title">{group.title}</span>
            <ul className="nav-list">
              {group.items.map((item) => (
                <li key={item.path}>
                  <NavLink
                    to={item.path}
                    end={item.path === '/'}
                    className={({ isActive }) =>
                      `nav-item ${isActive ? 'nav-item-active' : ''}`
                    }
                  >
                    <span className="nav-icon">
                      <NavIcon name={item.icon} />
                    </span>
                    <span className="nav-label">{item.label}</span>
                    {item.badge && <span className="badge badge-sm">{item.badge}</span>}
                    {item.hasAdd && (
                      <span className="nav-add">
                        <HiOutlinePlus />
                      </span>
                    )}
                  </NavLink>
                </li>
              ))}
            </ul>
          </div>
        ))}

        <div className="nav-group">
          <span className="nav-group-title">Management</span>
          <ul className="nav-list">
            {managementNav
              .filter(
                (item) =>
                  !item.superAdminOnly || user?.role === 'super_admin'
              )
              .map((item) => (
              <li key={item.path}>
                <NavLink
                  to={item.path}
                  className={({ isActive }) =>
                    `nav-item ${isActive ? 'nav-item-active' : ''}`
                  }
                >
                  <span className="nav-icon">
                    <NavIcon name={item.icon} />
                  </span>
                  <span className="nav-label">{item.label}</span>
                </NavLink>
              </li>
            ))}
          </ul>
        </div>
      </nav>

      <div className="sidebar-footer">
        <div className="darkmode-toggle">
          <span>Darkmode</span>
          <button
            className={`toggle-switch ${theme === 'dark' ? 'active' : ''}`}
            onClick={toggleTheme}
            aria-label="Toggle dark mode"
          >
            <span className="toggle-knob" />
          </button>
        </div>

        <div className="sidebar-user">
          <img
            src="https://i.pravatar.cc/150?img=8"
            alt="DashView"
            className="avatar avatar-sm"
          />
          <span className="user-name">{user?.name ?? 'Admin'}</span>
          <button className="user-action" aria-label="Notifications">
            <HiOutlineBell />
          </button>
          <button className="user-action" aria-label="Logout" onClick={handleLogout}>
            <HiOutlineLogout />
          </button>
        </div>
      </div>
    </aside>
  );
}

export default Sidebar;
