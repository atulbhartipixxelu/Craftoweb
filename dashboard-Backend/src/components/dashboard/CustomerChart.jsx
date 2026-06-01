import { PieChart, Pie, Cell, ResponsiveContainer } from 'recharts';
import { HiOutlineThumbUp, HiOutlineFlag } from 'react-icons/hi';
import { customerData } from '../../data/mockData';
import './CustomerChart.css';

function CustomerChart() {
  return (
    <div className="card customer-chart-card">
      <h3 className="card-title">Customer</h3>
      <div className="donut-wrapper">
        <ResponsiveContainer width="100%" height={180}>
          <PieChart>
            <Pie
              data={customerData}
              cx="50%"
              cy="50%"
              innerRadius={55}
              outerRadius={80}
              paddingAngle={3}
              dataKey="value"
              stroke="none"
            >
              {customerData.map((entry) => (
                <Cell key={entry.name} fill={entry.color} />
              ))}
            </Pie>
          </PieChart>
        </ResponsiveContainer>
      </div>
      <div className="customer-legend">
        <div className="customer-legend-item">
          <HiOutlineThumbUp className="legend-icon legend-direct" />
          <span>Direct</span>
        </div>
        <div className="customer-legend-item">
          <HiOutlineFlag className="legend-icon legend-social" />
          <span>Social</span>
        </div>
      </div>
    </div>
  );
}

export default CustomerChart;
