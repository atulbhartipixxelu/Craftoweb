import { Outlet } from 'react-router-dom';
import { useProjects } from '../../context/ProjectsContext';
import Sidebar from './Sidebar';
import '../../styles/layout.css';

function Layout() {
  const { loading, error, refreshData } = useProjects();

  return (
    <div className="app-layout">
      <Sidebar />
      <div className="main-wrapper">
        {error && (
          <div
            style={{
              background: '#fef2f2',
              color: '#b91c1c',
              padding: '12px 20px',
              borderBottom: '1px solid #fecaca',
              display: 'flex',
              justifyContent: 'space-between',
              alignItems: 'center',
              fontSize: '0.875rem',
            }}
          >
            <span>API Error: {error}</span>
            <button type="button" className="btn-secondary btn-sm" onClick={refreshData}>
              Retry
            </button>
          </div>
        )}
        {loading ? (
          <div
            style={{
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              minHeight: '60vh',
              color: 'var(--text-muted)',
            }}
          >
            Loading dashboard data...
          </div>
        ) : (
          <Outlet />
        )}
      </div>
    </div>
  );
}

export default Layout;
