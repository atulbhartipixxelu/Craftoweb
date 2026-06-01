import { useState, useEffect } from 'react';
import { technologies } from '../../data/mockData';
import { useProjects } from '../../context/ProjectsContext';

const emptyProject = {
  name: '',
  client: '',
  technology: 'React.js',
  startDate: new Date().toISOString().split('T')[0],
  status: 'active',
  priority: 'medium',
  progress: 0,
  value: '',
};

const emptyMockup = { title: '', imageUrl: '', description: '', status: 'draft' };

function ProjectFormModal({ project, onClose }) {
  const isEdit = !!project;
  const { addProject, updateProject } = useProjects();
  const [form, setForm] = useState(emptyProject);
  const [mockupList, setMockupList] = useState([]);
  const [showMockupForm, setShowMockupForm] = useState(false);
  const [currentMockup, setCurrentMockup] = useState(emptyMockup);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (project) {
      setForm({
        name: project.name,
        client: project.client,
        technology: project.technology,
        startDate: project.startDate,
        status: project.status,
        priority: project.priority,
        progress: project.progress,
        value: project.value,
      });
    }
  }, [project]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleImageUpload = (e, setter) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => setter((prev) => ({ ...prev, imageUrl: ev.target.result }));
    reader.readAsDataURL(file);
  };

  const addMockupToList = () => {
    if (!currentMockup.title.trim()) return;
    setMockupList((prev) => [...prev, { ...currentMockup, id: Date.now() }]);
    setCurrentMockup(emptyMockup);
    setShowMockupForm(false);
  };

  const removeMockupFromList = (id) => {
    setMockupList((prev) => prev.filter((m) => m.id !== id));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.name.trim() || !form.client.trim()) return;
    setSubmitting(true);
    setError('');
    try {
      const payload = {
        ...form,
        progress: Number(form.progress),
        value: form.value || '$0',
      };
      if (isEdit) {
        await updateProject(project.id, payload);
      } else {
        await addProject(payload, mockupList);
      }
      onClose();
    } catch (err) {
      const msg =
        err.response?.data?.message ||
        Object.values(err.response?.data?.errors || {}).flat().join(' ') ||
        'Failed to save project.';
      setError(msg);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content modal-lg" onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h3>{isEdit ? 'Edit Project' : 'Add New Project'}</h3>
          <button type="button" className="modal-close" onClick={onClose}>
            ×
          </button>
        </div>

        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            {error && <div className="form-error" style={{ marginBottom: 16 }}>{error}</div>}

            <div className="form-row">
              <div className="form-group">
                <label htmlFor="name">Project Name *</label>
                <input
                  id="name"
                  name="name"
                  value={form.name}
                  onChange={handleChange}
                  placeholder="e.g. School Portal"
                  required
                />
              </div>
              <div className="form-group">
                <label htmlFor="client">Client *</label>
                <input
                  id="client"
                  name="client"
                  value={form.client}
                  onChange={handleChange}
                  placeholder="Client name"
                  required
                />
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label htmlFor="technology">Technology</label>
                <select
                  id="technology"
                  name="technology"
                  value={form.technology}
                  onChange={handleChange}
                >
                  {technologies.map((tech) => (
                    <option key={tech} value={tech}>
                      {tech}
                    </option>
                  ))}
                </select>
              </div>
              <div className="form-group">
                <label htmlFor="startDate">Start Date</label>
                <input
                  id="startDate"
                  name="startDate"
                  type="date"
                  value={form.startDate}
                  onChange={handleChange}
                />
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label htmlFor="status">Status</label>
                <select id="status" name="status" value={form.status} onChange={handleChange}>
                  <option value="active">Active</option>
                  <option value="pending">Pending</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
              <div className="form-group">
                <label htmlFor="priority">Priority</label>
                <select id="priority" name="priority" value={form.priority} onChange={handleChange}>
                  <option value="high">High</option>
                  <option value="medium">Medium</option>
                  <option value="low">Low</option>
                </select>
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label htmlFor="progress">Progress (%)</label>
                <input
                  id="progress"
                  name="progress"
                  type="number"
                  min="0"
                  max="100"
                  value={form.progress}
                  onChange={handleChange}
                />
              </div>
              <div className="form-group">
                <label htmlFor="value">Project Value</label>
                <input
                  id="value"
                  name="value"
                  value={form.value}
                  onChange={handleChange}
                  placeholder="e.g. $5,000"
                />
              </div>
            </div>

            {!isEdit && (
              <div className="mockup-section">
                <div className="mockup-section-header">
                  <h3>Project Mockups</h3>
                  <button
                    type="button"
                    className="btn-secondary btn-sm"
                    onClick={() => setShowMockupForm(true)}
                  >
                    + Add Mockup
                  </button>
                </div>

                {mockupList.length > 0 && (
                  <div className="mockup-grid" style={{ marginBottom: 16 }}>
                    {mockupList.map((mockup) => (
                      <div key={mockup.id} className="mockup-card">
                        {mockup.imageUrl && <img src={mockup.imageUrl} alt={mockup.title} />}
                        <div className="mockup-card-body">
                          <h4>{mockup.title}</h4>
                          <button
                            type="button"
                            className="btn-danger btn-sm"
                            onClick={() => removeMockupFromList(mockup.id)}
                          >
                            Remove
                          </button>
                        </div>
                      </div>
                    ))}
                  </div>
                )}

                {showMockupForm && (
                  <div className="mockup-item-form">
                    <div className="form-group">
                      <label>Mockup Title *</label>
                      <input
                        value={currentMockup.title}
                        onChange={(e) =>
                          setCurrentMockup((prev) => ({ ...prev, title: e.target.value }))
                        }
                        placeholder="e.g. Homepage Design v1"
                      />
                    </div>
                    <div className="form-group">
                      <label>Upload Image</label>
                      <input type="file" accept="image/*" onChange={(e) => handleImageUpload(e, setCurrentMockup)} />
                      {currentMockup.imageUrl && (
                        <img src={currentMockup.imageUrl} alt="Preview" className="mockup-preview" />
                      )}
                    </div>
                    <div className="form-group">
                      <label>Description</label>
                      <textarea
                        value={currentMockup.description}
                        onChange={(e) =>
                          setCurrentMockup((prev) => ({ ...prev, description: e.target.value }))
                        }
                        placeholder="Optional notes about this mockup"
                      />
                    </div>
                    <div style={{ display: 'flex', gap: 8 }}>
                      <button type="button" className="btn-primary btn-sm" onClick={addMockupToList}>
                        Save Mockup
                      </button>
                      <button
                        type="button"
                        className="btn-secondary btn-sm"
                        onClick={() => {
                          setShowMockupForm(false);
                          setCurrentMockup(emptyMockup);
                        }}
                      >
                        Cancel
                      </button>
                    </div>
                  </div>
                )}

                {mockupList.length === 0 && !showMockupForm && (
                  <p style={{ fontSize: '0.8125rem', color: 'var(--text-muted)' }}>
                    Add design mockups for this project (optional)
                  </p>
                )}
              </div>
            )}
          </div>

          <div className="modal-footer">
            <button type="button" className="btn-secondary" onClick={onClose}>
              Cancel
            </button>
            <button type="submit" className="btn-primary" disabled={submitting}>
              {submitting ? 'Saving...' : isEdit ? 'Update Project' : 'Add Project'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default ProjectFormModal;
