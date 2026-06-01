import { HiOutlineStar, HiOutlineClock } from 'react-icons/hi';
import { FaTrophy } from 'react-icons/fa';
import { quickStats } from '../../data/mockData';
import './QuickStats.css';

const iconComponents = {
  trophy: FaTrophy,
  star: HiOutlineStar,
  hourglass: HiOutlineClock,
};

function QuickStats() {
  return (
    <div className="quick-stats">
      {quickStats.map((stat) => {
        const Icon = iconComponents[stat.icon];
        return (
          <div key={stat.id} className="card quick-stat-card">
            <div className="stat-icon-wrap">
              <Icon className="stat-icon" />
            </div>
            <span className="stat-value">{stat.value}</span>
            <span className="stat-label">{stat.label}</span>
          </div>
        );
      })}
    </div>
  );
}

export default QuickStats;
