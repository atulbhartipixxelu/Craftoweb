function UserViewModal({ user, onClose, onEdit }) {
  if (!user) return null;

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div className="modal-content" onClick={(e) => e.stopPropagation()}>
        <div className="modal-header">
          <h3>User Details</h3>
          <button type="button" className="modal-close" onClick={onClose}>
            ×
          </button>
        </div>

        <div className="modal-body user-view-body">
          <div className="user-view-avatar">
            {user.name.charAt(0).toUpperCase()}
          </div>
          <dl className="user-view-list">
            <div>
              <dt>Name</dt>
              <dd>{user.name}</dd>
            </div>
            <div>
              <dt>Email</dt>
              <dd>{user.email}</dd>
            </div>
            <div>
              <dt>Role</dt>
              <dd>
                <span className={`role-badge role-${user.role}`}>
                  {user.role === 'super_admin' ? 'Super Admin' : 'User'}
                </span>
              </dd>
            </div>
            <div>
              <dt>Joined</dt>
              <dd>{user.createdAt ?? '—'}</dd>
            </div>
          </dl>
        </div>

        <div className="modal-footer">
          <button type="button" className="btn-secondary" onClick={onClose}>
            Close
          </button>
          <button type="button" className="btn-primary" onClick={() => onEdit(user)}>
            Edit User
          </button>
        </div>
      </div>
    </div>
  );
}

export default UserViewModal;
