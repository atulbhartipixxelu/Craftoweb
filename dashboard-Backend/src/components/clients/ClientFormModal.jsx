import { useState, useEffect } from 'react';

const emptyClient = {
  name: '',
  contactPerson: '',
  email: '',
  phone: '',
  company: '',
  address: '',
  notes: '',
  status: 'active',
};

function ClientFormModal({ client, onClose, onSave }) {
  const isEdit = !!client;
  const [form, setForm] = useState(emptyClient);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (client) {
      setForm({
        name: client.name || '',
        contactPerson: client.contactPerson || '',
        email: client.email || '',
        phone: client.phone || '',
        company: client.company || '',
        address: client.address || '',
        notes: client.notes || '',
        status: client.status || 'active',
      });
    } else {
      setForm(emptyClient);
    }
  }, [client]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (!form.name.trim()) return;

    setSubmitting(true);
    try {
      const payload = {
        name: form.name.trim(),
        contactPerson: form.contactPerson.trim() || null,
        email: form.email.trim() || null,
        phone: form.phone.trim() || null,
        company: form.company.trim() || null,
        address: form.address.trim() || null,
        notes: form.notes.trim() || null,
        status: form.status,
      };
      await onSave(payload);
      onClose();
    } catch (err) {
      const msg =
        err.response?.data?.message ||
        Object.values(err.response?.data?.errors || {}).flat().join(' ') ||
        'Failed to save client.';
      setError(msg);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content modal-content-wide" onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h3>{isEdit ? 'Edit Client' : 'Add New Client'}</h3>
          <button type="button" className="modal-close" onClick={onClose}>
            ×
          </button>
        </div>

        <form onSubmit={handleSubmit}>
          <div className="modal-body">
            {error && <div className="form-error" style={{ marginBottom: 16 }}>{error}</div>}

            <div className="form-row">
              <div className="form-group">
                <label htmlFor="name">Client / Company Name *</label>
                <input
                  id="name"
                  name="name"
                  value={form.name}
                  onChange={handleChange}
                  placeholder="Enter client name"
                  required
                />
              </div>

              <div className="form-group">
                <label htmlFor="company">Company</label>
                <input
                  id="company"
                  name="company"
                  value={form.company}
                  onChange={handleChange}
                  placeholder="Company name"
                />
              </div>
            </div>

            <div className="form-row">
              <div className="form-group">
                <label htmlFor="contactPerson">Contact Person</label>
                <input
                  id="contactPerson"
                  name="contactPerson"
                  value={form.contactPerson}
                  onChange={handleChange}
                  placeholder="Contact person name"
                />
              </div>

              <div className="form-group">
                <label htmlFor="phone">Phone</label>
                <input
                  id="phone"
                  name="phone"
                  value={form.phone}
                  onChange={handleChange}
                  placeholder="+91 98765 43210"
                />
              </div>
            </div>

            <div className="form-group">
              <label htmlFor="email">Email</label>
              <input
                id="email"
                name="email"
                type="email"
                value={form.email}
                onChange={handleChange}
                placeholder="client@example.com"
              />
            </div>

            <div className="form-group">
              <label htmlFor="address">Address</label>
              <textarea
                id="address"
                name="address"
                value={form.address}
                onChange={handleChange}
                placeholder="Full address"
                rows={2}
              />
            </div>

            <div className="form-group">
              <label htmlFor="notes">Notes</label>
              <textarea
                id="notes"
                name="notes"
                value={form.notes}
                onChange={handleChange}
                placeholder="Payment terms, project notes, etc."
                rows={3}
              />
            </div>

            <div className="form-group">
              <label htmlFor="status">Status</label>
              <select id="status" name="status" value={form.status} onChange={handleChange}>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>

          <div className="modal-footer">
            <button type="button" className="btn-secondary" onClick={onClose}>
              Cancel
            </button>
            <button type="submit" className="btn-primary" disabled={submitting}>
              {submitting ? 'Saving...' : isEdit ? 'Update Client' : 'Add Client'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default ClientFormModal;
