import { useCallback, useEffect, useState } from 'react';
import Header from '../components/layout/Header';
import UserFormModal from '../components/users/UserFormModal';
import UserViewModal from '../components/users/UserViewModal';
import { useAuth } from '../context/AuthContext';
import { usersApi } from '../services/api';
import './Users.css';

function Users() {
  const { user: currentUser } = useAuth();
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [formUser, setFormUser] = useState(null);
  const [viewUser, setViewUser] = useState(null);
  const [showAddModal, setShowAddModal] = useState(false);

  const loadUsers = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const data = await usersApi.getAll();
      setUsers(data);
    } catch (err) {
      setError(
        err.response?.data?.message ||
          'Failed to load users. Super admin access required.'
      );
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadUsers();
  }, [loadUsers]);

  const handleCreate = async (payload) => {
    const created = await usersApi.create(payload);
    setUsers((prev) => [created, ...prev]);
  };

  const handleUpdate = async (payload) => {
    const updated = await usersApi.update(formUser.id, payload);
    setUsers((prev) => prev.map((u) => (u.id === updated.id ? updated : u)));
    setViewUser((prev) => (prev?.id === updated.id ? updated : prev));
  };

  const handleDelete = async (user) => {
    if (!window.confirm(`Delete user "${user.name}"? This cannot be undone.`)) return;
    try {
      await usersApi.delete(user.id);
      setUsers((prev) => prev.filter((u) => u.id !== user.id));
      if (viewUser?.id === user.id) setViewUser(null);
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to delete user.');
    }
  };

  const openEdit = (user) => {
    setViewUser(null);
    setFormUser(user);
  };

  const isSuperAdmin = currentUser?.role === 'super_admin';

  if (!isSuperAdmin) {
    return (
      <>
        <Header title="User Management" />
        <div className="page-content">
          <div className="card users-forbidden">
            <p>Only super admin can manage users.</p>
          </div>
        </div>
      </>
    );
  }

  return (
    <>
      <Header title="User Management" />
      <div className="page-content">
        <div className="card users-page-card">
          <div className="users-toolbar">
            <h3 className="card-title">
              All Users
              <span className="user-count">({users.length})</span>
            </h3>
            <button className="btn-primary" onClick={() => setShowAddModal(true)}>
              + Add User
            </button>
          </div>

          {error && <div className="users-error">{error}</div>}

          {loading ? (
            <p className="users-loading">Loading users...</p>
          ) : (
            <div className="table-wrapper">
              <table className="users-table full-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {users.length > 0 ? (
                    users.map((user) => (
                      <tr key={user.id}>
                        <td className="col-name">{user.name}</td>
                        <td>{user.email}</td>
                        <td>
                          <span className={`role-badge role-${user.role}`}>
                            {user.role === 'super_admin' ? 'Super Admin' : 'User'}
                          </span>
                        </td>
                        <td>{user.createdAt ?? '—'}</td>
                        <td>
                          <div className="user-actions">
                            <button
                              type="button"
                              className="btn-ghost btn-sm"
                              onClick={() => setViewUser(user)}
                            >
                              View
                            </button>
                            <button
                              type="button"
                              className="btn-secondary btn-sm"
                              onClick={() => openEdit(user)}
                            >
                              Edit
                            </button>
                            <button
                              type="button"
                              className="btn-danger btn-sm"
                              onClick={() => handleDelete(user)}
                              disabled={user.id === currentUser?.id}
                              title={
                                user.id === currentUser?.id
                                  ? 'Cannot delete yourself'
                                  : 'Delete user'
                              }
                            >
                              Delete
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))
                  ) : (
                    <tr>
                      <td colSpan={5} className="users-empty">
                        No users found. Add your first user.
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
        <UserFormModal
          onClose={() => setShowAddModal(false)}
          onSave={handleCreate}
        />
      )}

      {formUser && (
        <UserFormModal
          user={formUser}
          onClose={() => setFormUser(null)}
          onSave={handleUpdate}
        />
      )}

      {viewUser && (
        <UserViewModal
          user={viewUser}
          onClose={() => setViewUser(null)}
          onEdit={openEdit}
        />
      )}
    </>
  );
}

export default Users;
