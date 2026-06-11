import { useCallback, useEffect, useState } from 'react';
import Header from '../components/layout/Header';
import ClientFormModal from '../components/clients/ClientFormModal';
import { clientsApi } from '../services/api';
import './Clients.css';

function Clients() {
  const [clients, setClients] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [formClient, setFormClient] = useState(null);
  const [showAddModal, setShowAddModal] = useState(false);

  const loadClients = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const data = await clientsApi.getAll();
      setClients(data);
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load clients.');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadClients();
  }, [loadClients]);

  const handleCreate = async (payload) => {
    const created = await clientsApi.create(payload);
    setClients((prev) => [created, ...prev]);
  };

  const handleUpdate = async (payload) => {
    const updated = await clientsApi.update(formClient.id, payload);
    setClients((prev) => prev.map((c) => (c.id === updated.id ? updated : c)));
  };

  const handleDelete = async (client) => {
    if (!window.confirm(`Delete client "${client.name}"? This cannot be undone.`)) return;
    try {
      await clientsApi.delete(client.id);
      setClients((prev) => prev.filter((c) => c.id !== client.id));
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to delete client.');
    }
  };

  return (
    <>
      <Header title="Client Management" />
      <div className="page-content">
        <div className="card clients-page-card">
          <div className="clients-toolbar">
            <h3 className="card-title">
              All Clients
              <span className="client-count">({clients.length})</span>
            </h3>
            <button className="btn-primary" onClick={() => setShowAddModal(true)}>
              + Add Client
            </button>
          </div>

          {error && <div className="clients-error">{error}</div>}

          {loading ? (
            <p className="clients-loading">Loading clients...</p>
          ) : (
            <div className="table-wrapper">
              <table className="clients-table full-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Added</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {clients.length > 0 ? (
                    clients.map((client) => (
                      <tr key={client.id}>
                        <td className="col-name">{client.name}</td>
                        <td>{client.company || '—'}</td>
                        <td>{client.contactPerson || '—'}</td>
                        <td>
                          {client.email ? (
                            <a href={`mailto:${client.email}`} className="client-email-link">
                              {client.email}
                            </a>
                          ) : (
                            '—'
                          )}
                        </td>
                        <td>{client.phone || '—'}</td>
                        <td>
                          <span className={`status-badge status-${client.status}`}>
                            {client.status === 'active' ? 'Active' : 'Inactive'}
                          </span>
                        </td>
                        <td>{client.createdAt ?? '—'}</td>
                        <td>
                          <div className="client-actions">
                            <button
                              type="button"
                              className="btn-secondary btn-sm"
                              onClick={() => setFormClient(client)}
                            >
                              Edit
                            </button>
                            <button
                              type="button"
                              className="btn-danger btn-sm"
                              onClick={() => handleDelete(client)}
                            >
                              Delete
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={8} className="clients-empty">
                        No clients yet. Add your first client.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>

      {showAddModal && (
        <ClientFormModal
          onClose={() => setShowAddModal(false)}
          onSave={handleCreate}
        />
      )}

      {formClient && (
        <ClientFormModal
          client={formClient}
          onClose={() => setFormClient(null)}
          onSave={handleUpdate}
        />
      )}
    </>
  );
}

export default Clients;
