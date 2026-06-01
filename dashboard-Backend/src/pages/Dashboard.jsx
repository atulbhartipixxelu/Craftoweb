import Header from '../components/layout/Header';
import DashboardStats from '../components/dashboard/DashboardStats';
import OverviewChart from '../components/dashboard/OverviewChart';
import TopProjectsTable from '../components/dashboard/TopProjectsTable';
import QuickStats from '../components/dashboard/QuickStats';
import CustomerChart from '../components/dashboard/CustomerChart';
import ChatPanel from '../components/dashboard/ChatPanel';
import ActiveProjects from '../components/dashboard/ActiveProjects';
import UpdatesSection from '../components/dashboard/UpdatesSection';

function Dashboard() {
  return (
    <>
      <Header title="Dashboard Overview" />
      <div className="main-content">
        <div className="content-center">
          <DashboardStats />
          <OverviewChart />
          <ActiveProjects />
          <TopProjectsTable />
          <QuickStats />
          <UpdatesSection />
        </div>
        <div className="content-right">
          <CustomerChart />
          <ChatPanel />
        </div>
      </div>
    </>
  );
}

export default Dashboard;
