import { useState, useEffect } from 'react';

const emptyUser = {
  name: '',
  email: '',
  password: '',
  role: 'user',
};

function UserFormModal({ user, onClose, onSave }) {
  const isEdit = !!user;
  const [form, setForm] = useState(emptyUser);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (user) {
      setForm({
        name: user.name,
        email: user.email,
        password: '',
        role: user.role,
      });
    }
  }, [user]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (!form.name.trim() || !form.email.trim()) return;
    if (!isEdit && !form.password.trim()) {
      setError('Password is required for new users.');
      return;
    }

    setSubmitting(true);
    try {
      const payload = {
        name: form.name.trim(),
        email: form.email.trim(),
        role: form.role,
      };
      if (form.password.trim()) {
        payload.password = form.password;
      }
      await onSave(payload);
      onClose();
    } catch (err) {
      const msg =
        err.response?.data?.message ||
        Object.values(err.response?.data?.errors || {}).flat().join(' ') ||
        'Failed to save user.';
      setError(msg);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content" onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h3>{isEdit ? 'Edit User' : 'Add New User'}</h3>
          <button type="button" className="modal-close" onClick={onClose}>
            ×
          </button>
        </div>

        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            {error && <div className="form-error" style={{ marginBottom: 16 }}>{error}</div>}

            <div className="form-group">
              <label htmlFor="name">Full Name *</label>
              <input
                id="name"
                name="name"
                value={form.name}
                onChange={handleChange}
                placeholder="Enter name"
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="email">Email *</label>
              <input
                id="email"
                name="email"
                type="email"
                value={form.email}
                onChange={handleChange}
                placeholder="user@example.com"
                required
              />
            </div>

            <div className="form-group">
              <label htmlFor="password">
                Password {isEdit ? '(leave blank to keep current)' : '*'}
              </label>
              <input
                id="password"
                name="password"
                type="password"
                value={form.password}
                onChange={handleChange}
                placeholder={isEdit ? '••••••••' : 'Min 8 characters'}
                minLength={isEdit ? 0 : 8}
              />
            </div>

            <div className="form-group">
              <label htmlFor="role">Role *</label>
              <select id="role" name="role" value={form.role} onChange={handleChange}>
                <option value="user">User</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </div>
          </div>

          <div className="modal-footer">
            <button type="button" className="btn-secondary" onClick={onClose}>
              Cancel
            </button>
            <button type="submit" className="btn-primary" disabled={submitting}>
              {submitting ? 'Saving...' : isEdit ? 'Update User' : 'Add User'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default UserFormModal;
