import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { ThemeProvider } from './context/ThemeContext';
import { AuthProvider } from './context/AuthContext';
import { ProjectsProvider } from './context/ProjectsContext';
import ProtectedRoute from './components/auth/ProtectedRoute';
import Layout from './components/layout/Layout';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import Projects from './pages/Projects';
import DailyUpdates from './pages/DailyUpdates';
import Users from './pages/Users';
import Messages from './pages/Messages';
import Clients from './pages/Clients';
import PlaceholderPage from './pages/PlaceholderPage';

function App() {
  return (
    <ThemeProvider>
      <AuthProvider>
        <BrowserRouter>
          <Routes>
            <Route path="/login" element={<Login />} />
            <Route element={<ProtectedRoute />}>
              <Route
                element={
                  <ProjectsProvider>
                    <Layout />
                  </ProjectsProvider>
                }
              >
                <Route index element={<Dashboard />} />
                <Route path="projects" element={<Projects />} />
                <Route path="users" element={<Users />} />
                <Route path="messages" element={<Messages />} />
                <Route
                  path="schedule"
                  element={
                    <PlaceholderPage
                      title="Schedule"
                      description="View and manage your project schedules and deadlines."
                    />
                  }
                />
                <Route
                  path="analytics"
                  element={
                    <PlaceholderPage
                      title="Analytics & Reports"
                      description="Monthly work reports, technology-wise projects, and performance charts."
                    />
                  }
                />
                <Route
                  path="news"
                  element={
                    <PlaceholderPage
                      title="News"
                      description="Latest industry news and updates."
                    />
                  }
                />
                <Route
                  path="recruitment"
                  element={
                    <PlaceholderPage
                      title="Recruitment"
                      description="Manage recruitment and hiring processes."
                    />
                  }
                />
                <Route
                  path="activity"
                  element={
                    <PlaceholderPage
                      title="Activity"
                      description="Track your recent activity and work history."
                    />
                  }
                />
                <Route
                  path="shared"
                  element={
                    <PlaceholderPage
                      title="Shared"
                      description="View shared projects and collaborations."
                    />
                  }
                />
                <Route
                  path="privacy"
                  element={
                    <PlaceholderPage
                      title="Privacy"
                      description="Manage privacy settings and data protection."
                    />
                  }
                />
                <Route
                  path="settings"
                  element={
                    <PlaceholderPage
                      title="Settings"
                      description="Configure your dashboard preferences and account settings."
                    />
                  }
                />
                <Route
                  path="help"
                  element={
                    <PlaceholderPage
                      title="Help & Support"
                      description="Get help and support for using the dashboard."
                    />
                  }
                />
                <Route
                  path="chat"
                  element={
                    <PlaceholderPage
                      title="Chat"
                      description="Real-time chat with clients and team members."
                    />
                  }
                />
                <Route path="clients" element={<Clients />} />
                <Route
                  path="domains"
                  element={
                    <PlaceholderPage
                      title="Domain & Hosting Manager"
                      description="Track domain names, hosting providers, SSL status, and credentials."
                    />
                  }
                />
                <Route path="updates" element={<DailyUpdates />} />
                <Route
                  path="schools"
                  element={
                    <PlaceholderPage
                      title="School Website Management"
                      description="Dedicated section for managing school websites, CMS details, and maintenance."
                    />
                  }
                />
                <Route
                  path="reports"
                  element={
                    <PlaceholderPage
                      title="Reports & Analytics"
                      description="View monthly work reports, client statistics, and performance charts."
                    />
                  }
                />
              </Route>
            </Route>
          </Routes>
        </BrowserRouter>
      </AuthProvider>
    </ThemeProvider>
  );
}

export default App;
