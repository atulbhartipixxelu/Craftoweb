import { Link } from 'react-router-dom';
import { useProjects } from '../../context/ProjectsContext';
import './TopProjectsTable.css';

const projectIcons = {
  Finance: '💰',
  Ecommerce: '🛒',
  Health: '🏥',
  Education: '📚',
};

function TopProjectsTable() {
  const { topProjects } = useProjects();

  return (
    <div className="card top-projects-card">
      <div className="card-header">
        <h3 className="card-title">Top Project&apos;s</h3>
        <Link to="/projects" className="card-link">All</Link>
      </div>
      <div className="table-wrapper">
        <table className="projects-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Project</th>
              <th>Start</th>
              <th>Client</th>
              <th>Technology</th>
              <th>Value</th>
            </tr>
          </thead>
          <tbody>
            {topProjects.map((project, index) => (
              <tr key={project.id}>
                <td className="col-no">{index + 1}</td>
                <td className="col-project">
                  <span className="project-icon">{projectIcons[project.name] || '📁'}</span>
                  <span>{project.name}</span>
                </td>
                <td>{project.startDate}</td>
                <td>{project.client}</td>
                <td>{project.technology}</td>
                <td className="col-value">{project.value}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default TopProjectsTable;
