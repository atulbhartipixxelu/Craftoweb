import { useState } from 'react';
import Header from '../components/layout/Header';
import ProjectFormModal from '../components/projects/ProjectFormModal';
import MockupsModal from '../components/projects/MockupsModal';
import { technologies } from '../data/mockData';
import { useProjects } from '../context/ProjectsContext';
import './Projects.css';

function Projects() {
  const { projects, getMockupsByProject, deleteProject } = useProjects();
  const [activeTab, setActiveTab] = useState('All');
  const [showAddModal, setShowAddModal] = useState(false);
  const [editProject, setEditProject] = useState(null);
  const [mockupProject, setMockupProject] = useState(null);

  const filtered =
    activeTab === 'All'
      ? projects
      : projects.filter((p) => p.technology === activeTab);

  const handleDelete = async (project) => {
    if (!window.confirm(`Delete project "${project.name}"? This cannot be undone.`)) return;
    try {
      await deleteProject(project.id);
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to delete project.');
    }
  };

  return (
    <>
      <Header title="Project Management" />
      <div className="page-content">
        <div className="tech-tabs">
          <button
            className={`tech-tab ${activeTab === 'All' ? 'active' : ''}`}
            onClick={() => setActiveTab('All')}
          >
            All
          </button>
          {technologies.map((tech) => (
            <button
              key={tech}
              className={`tech-tab ${activeTab === tech ? 'active' : ''}`}
              onClick={() => setActiveTab(tech)}
            >
              {tech}
            </button>
          ))}
        </div>

        <div className="card projects-page-card">
          <div className="projects-toolbar">
            <h3 className="card-title">
              {activeTab === 'All' ? 'All Projects' : `${activeTab} Projects`}
              <span className="project-count">({filtered.length})</span>
            </h3>
            <button className="btn-primary" onClick={() => setShowAddModal(true)}>
              + Add Project
            </button>
          </div>

          <div className="table-wrapper">
            <table className="projects-table full-table">
              <thead>
                <tr>
                  <th>Project Name</th>
                  <th>Client</th>
                  <th>Technology</th>
                  <th>Start Date</th>
                  <th>Status</th>
                  <th>Priority</th>
                  <th>Progress</th>
                  <th>Value</th>
                  <th>Mockups</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                {filtered.map((project) => {
                  const mockupCount = getMockupsByProject(project.id).length;
                  return (
                    <tr key={project.id}>
                      <td className="col-name">{project.name}</td>
                      <td>{project.client}</td>
                      <td>
                        <span className="tech-badge">{project.technology}</span>
                      </td>
                      <td>{project.startDate}</td>
                      <td>
                        <span className={`status-badge status-${project.status}`}>
                          {project.status}
                        </span>
                      </td>
                      <td>
                        <span className={`priority priority-${project.priority}`}>
                          {project.priority}
                        </span>
                      </td>
                      <td>
                        <div className="table-progress">
                          <div className="progress-bar">
                            <div
                              className="progress-fill"
                              style={{ width: `${project.progress}%` }}
                            />
                          </div>
                          <span>{project.progress}%</span>
                        </div>
                      </td>
                      <td className="col-value">{project.value}</td>
                      <td>
                        <button
                          type="button"
                          className="btn-mockups"
                          onClick={() => setMockupProject(project)}
                        >
                          {mockupCount > 0 ? `${mockupCount} Mockup${mockupCount > 1 ? 's' : ''}` : '+ Add'}
                        </button>
                      </td>
                      <td>
                        <div className="project-actions">
                          <button
                            type="button"
                            className="btn-secondary btn-sm"
                            onClick={() => setEditProject(project)}
                          >
                            Edit
                          </button>
                          <button
                            type="button"
                            className="btn-danger btn-sm"
                            onClick={() => handleDelete(project)}
                          >
                            Delete
                          </button>
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {showAddModal && <ProjectFormModal onClose={() => setShowAddModal(false)} />}
      {editProject && (
        <ProjectFormModal project={editProject} onClose={() => setEditProject(null)} />
      )}
      {mockupProject && (
        <MockupsModal project={mockupProject} onClose={() => setMockupProject(null)} />
      )}
    </>
  );
}

export default Projects;
