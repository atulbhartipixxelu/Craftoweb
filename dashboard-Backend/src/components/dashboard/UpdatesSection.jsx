import { Link } from 'react-router-dom';
import { upcomingTasks } from '../../data/mockData';
import { useProjects } from '../../context/ProjectsContext';
import './UpdatesSection.css';

function UpdatesSection() {
  const { dailyUpdates, getProjectById } = useProjects();

  const recent = [...dailyUpdates]
    .sort((a, b) => b.date.localeCompare(a.date))
    .slice(0, 5);

  return (
    <div className="updates-section">
      <div className="card updates-card">
        <div className="card-header">
          <h3 className="card-title">Recent Updates</h3>
          <Link to="/updates" className="card-link">View All</Link>
        </div>
        <ul className="updates-list">
          {recent.length > 0 ? (
            recent.map((update) => {
              const project = getProjectById(update.projectId);
              return (
                <li key={update.id} className="update-item">
                  <div className="update-dot" />
                  <div className="update-content">
                    <span className="update-project">{project?.name ?? 'Project'}</span>
                    <p className="update-desc">{update.description}</p>
                    <div className="update-meta">
                      <span>{update.date}</span>
                      {update.hours > 0 && <span>{update.hours}h worked</span>}
                    </div>
                  </div>
                </li>
              );
            })
          ) : (
            <li className="update-item">
              <div className="update-content">
                <p className="update-desc" style={{ color: 'var(--text-muted)' }}>
                  No updates yet. <Link to="/updates">Add your first update</Link>
                </p>
              </div>
            </li>
          )}
        </ul>
      </div>

      <div className="card tasks-card">
        <div className="card-header">
          <h3 className="card-title">Upcoming Tasks</h3>
        </div>
        <ul className="tasks-list">
          {upcomingTasks.map((task) => (
            <li key={task.id} className="task-item">
              <div className="task-checkbox" />
              <div className="task-content">
                <span className="task-title">{task.title}</span>
                <div className="task-meta">
                  <span className="task-due">Due: {task.dueDate}</span>
                  <span className={`priority priority-${task.priority}`}>
                    {task.priority}
                  </span>
                </div>
              </div>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}

export default UpdatesSection;
