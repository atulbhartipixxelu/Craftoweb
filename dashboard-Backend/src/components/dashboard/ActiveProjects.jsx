import { Link } from 'react-router-dom';
import { useProjects } from '../../context/ProjectsContext';
import './ActiveProjects.css';

function ActiveProjects() {
  const { activeProjects } = useProjects();

  return (
    <div className="card active-projects-card">
      <div className="card-header">
        <h3 className="card-title">Active Projects</h3>
        <Link to="/projects" className="card-link">View All</Link>
      </div>
      <div className="active-projects-grid">
        {activeProjects.length > 0 ? (
          activeProjects.map((project) => (
            <div key={project.id} className="active-project-item">
              <div className="project-top">
                <h4 className="project-name">{project.name}</h4>
                <span className={`status-badge status-${project.status}`}>
                  {project.status}
                </span>
              </div>
              <div className="project-meta">
                <span>{project.client}</span>
                <span className="meta-dot">•</span>
                <span>{project.technology}</span>
              </div>
              <div className="project-progress">
                <div className="progress-header">
                  <span className="progress-label">Progress</span>
                  <span className="progress-percent">{project.progress}%</span>
                </div>
                <div className="progress-bar">
                  <div
                    className="progress-fill"
                    style={{ width: `${project.progress}%` }}
                  />
                </div>
              </div>
              <div className="project-footer">
                <span className={`priority priority-${project.priority}`}>
                  {project.priority} priority
                </span>
                <span className="start-date">{project.startDate}</span>
              </div>
            </div>
          ))
        ) : (
          <p style={{ color: 'var(--text-muted)', fontSize: '0.875rem' }}>
            No active projects yet.
          </p>
        )}
      </div>
    </div>
  );
}

export default ActiveProjects;
