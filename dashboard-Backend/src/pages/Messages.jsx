import { useCallback, useEffect, useState } from 'react';
import Header from '../components/layout/Header';
import { contactsApi } from '../services/api';
import './Messages.css';

function Messages() {
  const [contacts, setContacts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [selected, setSelected] = useState(null);

  const loadContacts = useCallback(async () => {
    setLoading(true);
    setError('');

    try {
      const data = await contactsApi.getAll();
      setContacts(data);
    } catch (err) {
      setError(
        err.response?.data?.message || 'Failed to load enquiries.'
      );
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadContacts();
  }, [loadContacts]);

  const handleMarkRead = async (contact) => {
    if (contact.isRead) return;

    try {
      const updated = await contactsApi.markRead(contact.id);
      setContacts((prev) =>
        prev.map((c) => (c.id === updated.id ? updated : c))
      );
      setSelected((prev) => (prev?.id === updated.id ? updated : prev));
    } catch (err) {
      alert(err.response?.data?.message || 'Could not mark as read.');
    }
  };

  const handleDelete = async (contact) => {
    if (!window.confirm(`Delete enquiry from "${contact.name}"?`)) return;

    try {
      await contactsApi.delete(contact.id);
      setContacts((prev) => prev.filter((c) => c.id !== contact.id));
      if (selected?.id === contact.id) setSelected(null);
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to delete.');
    }
  };

  return (
    <>
      <Header title="Contact Enquiries" />
      <div className="page-content">
        <div className="card users-page-card">
          <div className="users-toolbar">
            <h3 className="card-title">
              Website enquiries
              <span className="user-count">({contacts.length})</span>
            </h3>
            <button type="button" className="btn-ghost" onClick={loadContacts}>
              Refresh
            </button>
          </div>

          {error && <div className="users-error">{error}</div>}

          {loading ? (
            <p className="users-loading">Loading enquiries...</p>
          ) : (
            <div className="table-wrapper">
              <table className="users-table full-table">
                <thead>
                  <tr>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {contacts.length > 0 ? (
                    contacts.map((c) => (
                      <tr
                        key={c.id}
                        className={!c.isRead ? 'row-unread' : ''}
                      >
                        <td>{c.isRead ? 'Read' : 'New'}</td>
                        <td className="col-name">{c.name}</td>
                        <td>{c.email}</td>
                        <td>{c.subject || '—'}</td>
                        <td>{c.createdAt ?? '—'}</td>
                        <td>
                          <div className="user-actions">
                            <button
                              type="button"
                              className="btn-ghost btn-sm"
                              onClick={() => {
                                setSelected(c);
                                handleMarkRead(c);
                              }}
                            >
                              View
                            </button>
                            <button
                              type="button"
                              className="btn-danger btn-sm"
                              onClick={() => handleDelete(c)}
                            >
                              Delete
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={6} className="users-empty">
                        No enquiries yet. Contact form submissions will appear here.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          )}

          {selected && (
            <div className="messages-detail">
              <h3>{selected.name}</h3>
              <p><strong>Email:</strong> {selected.email}</p>
              <p><strong>Phone:</strong> {selected.phone || '—'}</p>
              <p><strong>Subject:</strong> {selected.subject || '—'}</p>
              <p><strong>Date:</strong> {selected.createdAt ?? '—'}</p>
              <p><strong>Message:</strong></p>
              <p>{selected.message}</p>
              <div className="user-actions" style={{ marginTop: '1rem' }}>
                <a href={`mailto:${selected.email}`} className="btn-primary btn-sm">
                  Reply by email
                </a>
                <button
                  type="button"
                  className="btn-ghost btn-sm"
                  onClick={() => setSelected(null)}
                >
                  Close
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  );
}

export default Messages;