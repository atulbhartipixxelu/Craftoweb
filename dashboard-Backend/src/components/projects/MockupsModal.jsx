import { useState } from 'react';
import { useProjects } from '../../context/ProjectsContext';

const emptyMockup = { title: '', imageUrl: '', description: '', status: 'draft' };

function MockupsModal({ project, onClose }) {
  const { getMockupsByProject, addMockup, deleteMockup } = useProjects();
  const projectMockups = getMockupsByProject(project.id);
  const [showForm, setShowForm] = useState(false);
  const [form, setForm] = useState(emptyMockup);

  const handleImageUpload = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => setForm((prev) => ({ ...prev, imageUrl: ev.target.result }));
    reader.readAsDataURL(file);
  };

  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.title.trim()) return;
    setSubmitting(true);
    try {
      await addMockup({ ...form, projectId: project.id });
      setForm(emptyMockup);
      setShowForm(false);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content modal-lg" onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h3>Mockups — {project.name}</h3>
          <button type="button" className="modal-close" onClick={onClose}>
            ×
          </button>
        </div>

        <div className="modal-body">
          <div className="mockup-section-header" style={{ marginBottom: 16 }}>
            <span style={{ fontSize: '0.8125rem', color: 'var(--text-muted)' }}>
              {projectMockups.length} mockup{projectMockups.length !== 1 ? 's' : ''}
            </span>
            <button
              type="button"
              className="btn-primary btn-sm"
              onClick={() => setShowForm(!showForm)}
            >
              + Add Mockup
            </button>
          </div>

          {showForm && (
            <form onSubmit={handleSubmit} className="mockup-item-form">
              <div className="form-group">
                <label>Mockup Title *</label>
                <input
                  value={form.title}
                  onChange={(e) => setForm((prev) => ({ ...prev, title: e.target.value }))}
                  placeholder="e.g. Dashboard UI v2"
                  required
                />
              </div>
              <div className="form-group">
                <label>Upload Image</label>
                <input type="file" accept="image/*" onChange={handleImageUpload} />
                {form.imageUrl && (
                  <img src={form.imageUrl} alt="Preview" className="mockup-preview" />
                )}
              </div>
              <div className="form-group">
                <label>Description</label>
                <textarea
                  value={form.description}
                  onChange={(e) => setForm((prev) => ({ ...prev, description: e.target.value }))}
                  placeholder="Notes about this mockup"
                />
              </div>
              <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" className="btn-primary btn-sm" disabled={submitting}>
                  {submitting ? 'Saving...' : 'Save Mockup'}
                </button>
                <button
                  type="button"
                  className="btn-secondary btn-sm"
                  onClick={() => {
                    setShowForm(false);
                    setForm(emptyMockup);
                  }}
                >
                  Cancel
                </button>
              </div>
            </form>
          )}

          {projectMockups.length > 0 ? (
            <div className="mockup-grid">
              {projectMockups.map((mockup) => (
                <div key={mockup.id} className="mockup-card">
                  {mockup.imageUrl ? (
                    <img src={mockup.imageUrl} alt={mockup.title} />
                  ) : (
                    <div
                      style={{
                        height: 140,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        background: 'var(--bg-hover)',
                        color: 'var(--text-muted)',
                        fontSize: '0.75rem',
                      }}
                    >
                      No image
                    </div>
                  )}
                  <div className="mockup-card-body">
                    <h4>{mockup.title}</h4>
                    {mockup.description && <p>{mockup.description}</p>}
                    <div className="mockup-card-actions">
                      <span style={{ fontSize: '0.6875rem', color: 'var(--text-dim)' }}>
                        {mockup.createdAt}
                      </span>
                      <button
                        type="button"
                        className="btn-danger btn-sm"
                        onClick={() => deleteMockup(mockup.id)}
                      >
                        Delete
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            !showForm && (
              <div className="empty-state">
                <p>No mockups added yet for this project.</p>
                <button type="button" className="btn-primary btn-sm" onClick={() => setShowForm(true)}>
                  Add First Mockup
                </button>
              </div>
            )
          )}
        </div>

        <div className="modal-footer">
          <button type="button" className="btn-secondary" onClick={onClose}>
            Close
          </button>
        </div>
      </div>
    </div>
  );
}

export default MockupsModal;
