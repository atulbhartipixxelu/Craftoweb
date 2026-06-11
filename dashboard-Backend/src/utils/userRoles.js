export const USER_ROLE_LABELS = {
  super_admin: 'Super Admin',
  user: 'User',
  api_client: 'API Client',
};

export function formatUserRole(role) {
  return USER_ROLE_LABELS[role] ?? role;
}
