import {
  AreaChart,
  Area,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from 'recharts';
import { overviewChartData } from '../../data/mockData';
import './OverviewChart.css';

function CustomTooltip({ active, payload, label }) {
  if (!active || !payload?.length) return null;

  return (
    <div className="chart-tooltip">
      <p className="tooltip-label">{label}</p>
      {payload.map((entry) => (
        <p key={entry.name} style={{ color: entry.color }}>
          {entry.name}: ${entry.value}
        </p>
      ))}
    </div>
  );
}

function OverviewChart() {
  return (
    <div className="card overview-chart-card">
      <div className="overview-legend">
        <span className="legend-item">
          <span className="legend-dot legend-deals" />
          Deals
        </span>
        <span className="legend-item">
          <span className="legend-dot legend-revenue" />
          Revenue
        </span>
      </div>
      <div className="chart-container">
        <ResponsiveContainer width="100%" height={280}>
          <AreaChart data={overviewChartData} margin={{ top: 10, right: 10, left: 0, bottom: 0 }}>
            <defs>
              <linearGradient id="dealsGradient" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stopColor="#f97316" stopOpacity={0.4} />
                <stop offset="100%" stopColor="#f97316" stopOpacity={0} />
              </linearGradient>
              <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stopColor="#a855f7" stopOpacity={0.3} />
                <stop offset="100%" stopColor="#a855f7" stopOpacity={0} />
              </linearGradient>
            </defs>
            <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.04)" vertical={false} />
            <XAxis
              dataKey="month"
              axisLine={false}
              tickLine={false}
              tick={{ fill: '#8b8b9a', fontSize: 12 }}
            />
            <YAxis
              axisLine={false}
              tickLine={false}
              tick={{ fill: '#8b8b9a', fontSize: 12 }}
              tickFormatter={(v) => `$${v}`}
              domain={[100, 600]}
            />
            <Tooltip content={<CustomTooltip />} />
            <Area
              type="monotone"
              dataKey="revenue"
              name="Revenue"
              stroke="#a855f7"
              strokeWidth={2}
              fill="url(#revenueGradient)"
              dot={false}
              activeDot={{ r: 5, fill: '#a855f7', stroke: '#fff', strokeWidth: 2 }}
            />
            <Area
              type="monotone"
              dataKey="deals"
              name="Deals"
              stroke="#f97316"
              strokeWidth={2.5}
              fill="url(#dealsGradient)"
              dot={false}
              activeDot={{ r: 6, fill: '#f97316', stroke: '#fff', strokeWidth: 2 }}
            />
          </AreaChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}

export default OverviewChart;
