import { useMemo, useState } from 'react';
import Header from '../components/layout/Header';
import ProjectFormModal from '../components/projects/ProjectFormModal';
import { useProjects } from '../context/ProjectsContext';
import './DailyUpdates.css';

function DailyUpdates() {
  const { projects, dailyUpdates, addDailyUpdate, deleteDailyUpdate, getProjectById } = useProjects();
  const [filterProject, setFilterProject] = useState('all');
  const [filterDate, setFilterDate] = useState('');
  const [showProjectModal, setShowProjectModal] = useState(false);
  const [form, setForm] = useState({
    projectId: '',
    date: new Date().toISOString().split('T')[0],
    description: '',
    hours: '',
  });

  const filteredUpdates = useMemo(() => {
    let list = [...dailyUpdates];
    if (filterProject !== 'all') {
      list = list.filter((u) => u.projectId === Number(filterProject));
    }
    if (filterDate) {
      list = list.filter((u) => u.date === filterDate);
    }
    return list.sort((a, b) => b.date.localeCompare(a.date));
  }, [dailyUpdates, filterProject, filterDate]);

  const groupedByDate = useMemo(() => {
    const groups = {};
    filteredUpdates.forEach((update) => {
      if (!groups[update.date]) groups[update.date] = [];
      groups[update.date].push(update);
    });
    return Object.entries(groups).sort(([a], [b]) => b.localeCompare(a));
  }, [filteredUpdates]);

  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.projectId || !form.description.trim()) return;
    setSubmitting(true);
    try {
      await addDailyUpdate({
        projectId: Number(form.projectId),
        date: form.date,
        description: form.description.trim(),
        hours: Number(form.hours) || 0,
      });
      setForm((prev) => ({ ...prev, description: '', hours: '' }));
    } finally {
      setSubmitting(false);
    }
  };

  const formatDate = (dateStr) => {
    const date = new Date(dateStr + 'T00:00:00');
    return date.toLocaleDateString('en-IN', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  };

  return (
    <>
      <Header title="Daily Work Updates" />
      <div className="page-content daily-updates-page">
        <div className="updates-layout">
          <div className="card updates-form-card">
            <div className="updates-form-header">
              <h3 className="card-title">Add Daily Update</h3>
              <button
                type="button"
                className="btn-secondary btn-sm"
                onClick={() => setShowProjectModal(true)}
              >
                + New Project
              </button>
            </div>

            <form onSubmit={handleSubmit} className="daily-update-form">
              <div className="form-group">
                <label htmlFor="projectId">Project *</label>
                <select
                  id="projectId"
                  value={form.projectId}
                  onChange={(e) => setForm((prev) => ({ ...prev, projectId: e.target.value }))}
                  required
                >
                  <option value="">Select project</option>
                  {projects.map((p) => (
                    <option key={p.id} value={p.id}>
                      {p.name} — {p.client}
                    </option>
                  ))}
                </select>
              </div>

              <div className="form-row">
                <div className="form-group">
                  <label htmlFor="date">Date *</label>
                  <input
                    id="date"
                    type="date"
                    value={form.date}
                    onChange={(e) => setForm((prev) => ({ ...prev, date: e.target.value }))}
                    required
                  />
                </div>
                <div className="form-group">
                  <label htmlFor="hours">Hours Worked</label>
                  <input
                    id="hours"
                    type="number"
                    min="0"
                    step="0.5"
                    value={form.hours}
                    onChange={(e) => setForm((prev) => ({ ...prev, hours: e.target.value }))}
                    placeholder="e.g. 3"
                  />
                </div>
              </div>

              <div className="form-group">
                <label htmlFor="description">Work Description *</label>
                <textarea
                  id="description"
                  value={form.description}
                  onChange={(e) => setForm((prev) => ({ ...prev, description: e.target.value }))}
                  placeholder="What did you work on today?"
                  required
                />
              </div>

              <button type="submit" className="btn-primary" style={{ width: '100%' }} disabled={submitting}>
                {submitting ? 'Saving...' : 'Save Update'}
              </button>
            </form>
          </div>

          <div className="updates-timeline-section">
            <div className="card">
              <div className="updates-filters">
                <h3 className="card-title">Update History</h3>
                <div className="filters-row">
                  <select
                    value={filterProject}
                    onChange={(e) => setFilterProject(e.target.value)}
                    className="filter-select"
                  >
                    <option value="all">All Projects</option>
                    {projects.map((p) => (
                      <option key={p.id} value={p.id}>
                        {p.name}
                      </option>
                    ))}
                  </select>
                  <input
                    type="date"
                    value={filterDate}
                    onChange={(e) => setFilterDate(e.target.value)}
                    className="filter-select"
                  />
                  {(filterProject !== 'all' || filterDate) && (
                    <button
                      type="button"
                      className="btn-ghost btn-sm"
                      onClick={() => {
                        setFilterProject('all');
                        setFilterDate('');
                      }}
                    >
                      Clear
                    </button>
                  )}
                </div>
              </div>

              {groupedByDate.length > 0 ? (
                <div className="updates-timeline">
                  {groupedByDate.map(([date, updates]) => (
                    <div key={date} className="timeline-day">
                      <div className="timeline-date-header">
                        <span className="timeline-date-dot" />
                        <h4>{formatDate(date)}</h4>
                        <span className="timeline-date-raw">{date}</span>
                      </div>
                      <ul className="timeline-updates">
                        {updates.map((update) => {
                          const project = getProjectById(update.projectId);
                          return (
                            <li key={update.id} className="timeline-update-item">
                              <div className="timeline-update-content">
                                <div className="timeline-update-top">
                                  <span className="timeline-project-name">
                                    {project?.name ?? 'Unknown Project'}
                                  </span>
                                  {update.hours > 0 && (
                                    <span className="timeline-hours">{update.hours}h</span>
                                  )}
                                </div>
                                <p className="timeline-desc">{update.description}</p>
                                {project && (
                                  <span className="timeline-client">{project.client}</span>
                                )}
                              </div>
                              <button
                                type="button"
                                className="btn-danger btn-sm"
                                onClick={() => deleteDailyUpdate(update.id)}
                              >
                                Delete
                              </button>
                            </li>
                          );
                        })}
                      </ul>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="empty-state">
                  <p>No updates found. Add your first daily update!</p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      {showProjectModal && <ProjectFormModal onClose={() => setShowProjectModal(false)} />}
    </>
  );
}

export default DailyUpdates;
