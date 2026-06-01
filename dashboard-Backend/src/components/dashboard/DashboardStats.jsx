import { useProjects } from '../../context/ProjectsContext';
import {
  HiOutlineFolder,
  HiOutlineLightningBolt,
  HiOutlineCheckCircle,
  HiOutlineClock,
} from 'react-icons/hi';
import './DashboardStats.css';

const statsConfig = [
  {
    key: 'totalProjects',
    label: 'Total Projects',
    icon: HiOutlineFolder,
    color: 'purple',
  },
  {
    key: 'activeProjects',
    label: 'Active Projects',
    icon: HiOutlineLightningBolt,
    color: 'orange',
  },
  {
    key: 'completedProjects',
    label: 'Completed',
    icon: HiOutlineCheckCircle,
    color: 'green',
  },
  {
    key: 'pendingProjects',
    label: 'Pending',
    icon: HiOutlineClock,
    color: 'pink',
  },
];

function DashboardStats() {
  const { dashboardStats } = useProjects();

  return (
    <div className="dashboard-stats">
      {statsConfig.map(({ key, label, icon: Icon, color }) => (
        <div key={key} className={`stat-card stat-${color}`}>
          <div className="stat-card-icon">
            <Icon />
          </div>
          <div className="stat-card-info">
            <span className="stat-card-value">{dashboardStats[key] ?? 0}</span>
            <span className="stat-card-label">{label}</span>
          </div>
        </div>
      ))}
    </div>
  );
}

export default DashboardStats;
