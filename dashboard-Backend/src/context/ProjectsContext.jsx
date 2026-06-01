import { createContext, useContext, useEffect, useState, useCallback } from 'react';
import { projectsApi, dailyUpdatesApi, mockupsApi } from '../services/api';

const ProjectsContext = createContext(null);

export function ProjectsProvider({ children }) {
  const [initialized, setInitialized] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [projects, setProjects] = useState([]);
  const [dailyUpdates, setDailyUpdates] = useState([]);
  const [mockups, setMockups] = useState([]);
  const [dashboardStats, setDashboardStats] = useState({
    totalProjects: 0,
    activeProjects: 0,
    completedProjects: 0,
    pendingProjects: 0,
  });

  const refreshStats = useCallback(async () => {
    try {
      const stats = await projectsApi.getStats();
      setDashboardStats(stats);
    } catch {
      /* stats are optional */
    }
  }, []);

  const loadData = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const [projectsData, updatesData, mockupsData] = await Promise.all([
        projectsApi.getAll(),
        dailyUpdatesApi.getAll(),
        mockupsApi.getAll(),
      ]);
      setProjects(projectsData);
      setDailyUpdates(updatesData);
      setMockups(mockupsData);
      await refreshStats();
    } catch (err) {
      const msg = err.response?.data?.message || err.message || 'Failed to load data from API';
      setError(
        msg === 'Network Error'
          ? 'Cannot reach API. Start Laravel: cd api && php artisan serve'
          : msg
      );
    } finally {
      setLoading(false);
      setInitialized(true);
    }
  }, [refreshStats]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const updateProject = async (id, projectData) => {
    const updated = await projectsApi.update(id, {
      ...projectData,
      progress: Number(projectData.progress),
      value: projectData.value || '$0',
    });
    setProjects((prev) => prev.map((p) => (p.id === id ? updated : p)));
    await refreshStats();
    return updated;
  };

  const deleteProject = async (id) => {
    await projectsApi.delete(id);
    setProjects((prev) => prev.filter((p) => p.id !== id));
    setMockups((prev) => prev.filter((m) => m.projectId !== id));
    setDailyUpdates((prev) => prev.filter((u) => u.projectId !== id));
    await refreshStats();
  };

  const addProject = async (projectData, projectMockups = []) => {
    const newProject = await projectsApi.create({
      ...projectData,
      mockups: projectMockups.map(({ title, imageUrl, description, status }) => ({
        title,
        imageUrl,
        description,
        status,
      })),
    });
    setProjects((prev) => [...prev, newProject]);

    if (projectMockups.length > 0) {
      const newMockups = await mockupsApi.getByProject(newProject.id);
      setMockups((prev) => [...prev, ...newMockups]);
    }

    await refreshStats();
    return newProject;
  };

  const addDailyUpdate = async (updateData) => {
    const newUpdate = await dailyUpdatesApi.create(updateData);
    setDailyUpdates((prev) => [newUpdate, ...prev]);
    return newUpdate;
  };

  const deleteDailyUpdate = async (id) => {
    await dailyUpdatesApi.delete(id);
    setDailyUpdates((prev) => prev.filter((u) => u.id !== id));
  };

  const addMockup = async (mockupData) => {
    const newMockup = await mockupsApi.create(mockupData);
    setMockups((prev) => [...prev, newMockup]);
    return newMockup;
  };

  const deleteMockup = async (id) => {
    await mockupsApi.delete(id);
    setMockups((prev) => prev.filter((m) => m.id !== id));
  };

  const getProjectById = (id) => projects.find((p) => p.id === id);

  const getMockupsByProject = (projectId) =>
    mockups.filter((m) => m.projectId === projectId);

  const getUpdatesByProject = (projectId) =>
    dailyUpdates.filter((u) => u.projectId === projectId);

  const activeProjects = projects.filter((p) => p.status === 'active');

  const topProjects = [...projects]
    .sort((a, b) => {
      const aVal = parseFloat(String(a.value).replace(/[^0-9.]/g, '')) || 0;
      const bVal = parseFloat(String(b.value).replace(/[^0-9.]/g, '')) || 0;
      return bVal - aVal;
    })
    .slice(0, 5);

  return (
    <ProjectsContext.Provider
      value={{
        projects,
        dailyUpdates,
        mockups,
        dashboardStats,
        activeProjects,
        topProjects,
        loading,
        error,
        refreshData: loadData,
        addProject,
        updateProject,
        deleteProject,
        addDailyUpdate,
        deleteDailyUpdate,
        addMockup,
        deleteMockup,
        getProjectById,
        getMockupsByProject,
        getUpdatesByProject,
      }}
    >
      {children}
    </ProjectsContext.Provider>
  );
}

export function useProjects() {
  const context = useContext(ProjectsContext);
  if (!context) {
    throw new Error('useProjects must be used within ProjectsProvider');
  }
  return context;
}
