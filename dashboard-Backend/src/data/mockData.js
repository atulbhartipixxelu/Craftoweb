export const overviewChartData = [
  { month: 'Jan', deals: 280, revenue: 320 },
  { month: 'Feb', deals: 350, revenue: 380 },
  { month: 'Mar', deals: 400, revenue: 420 },
  { month: 'Apr', deals: 320, revenue: 360 },
  { month: 'May', deals: 450, revenue: 480 },
  { month: 'Jun', deals: 380, revenue: 400 },
  { month: 'Jul', deals: 520, revenue: 540 },
  { month: 'Aug', deals: 480, revenue: 500 },
  { month: 'Spt', deals: 560, revenue: 580 },
];

export const customerData = [
  { name: 'Direct', value: 62, color: '#a855f7' },
  { name: 'Social', value: 38, color: '#f97316' },
];

export const topProjects = [
  {
    id: 1,
    name: 'Finance',
    icon: '💰',
    startDate: '2024-01-15',
    lead: 'Alice',
    company: 'FinCorp',
    value: '$12,500',
  },
  {
    id: 2,
    name: 'Ecommerce',
    icon: '🛒',
    startDate: '2024-02-20',
    lead: 'Sarah',
    company: 'ShopMax',
    value: '$8,200',
  },
  {
    id: 3,
    name: 'Health',
    icon: '🏥',
    startDate: '2024-03-10',
    lead: 'Bob',
    company: 'MediCare',
    value: '$15,000',
  },
  {
    id: 4,
    name: 'Education',
    icon: '📚',
    startDate: '2024-04-05',
    lead: 'Alice',
    company: 'EduTech',
    value: '$9,800',
  },
];

export const activeProjects = [
  {
    id: 1,
    name: 'School Portal Redesign',
    client: 'Green Valley School',
    technology: 'React.js',
    startDate: '2025-03-01',
    status: 'active',
    priority: 'high',
    progress: 75,
  },
  {
    id: 2,
    name: 'E-commerce Store',
    client: 'ShopMax Ltd',
    technology: 'Shopify',
    startDate: '2025-04-10',
    status: 'active',
    priority: 'medium',
    progress: 45,
  },
  {
    id: 3,
    name: 'Corporate Website',
    client: 'FinCorp Inc',
    technology: 'WordPress',
    startDate: '2025-04-20',
    status: 'active',
    priority: 'high',
    progress: 60,
  },
  {
    id: 4,
    name: 'Portfolio Website',
    client: 'John Designer',
    technology: 'Next.js',
    startDate: '2025-05-01',
    status: 'pending',
    priority: 'low',
    progress: 20,
  },
];

export const chatContacts = [
  {
    id: 1,
    name: 'Sarah Johnson',
    avatar: 'https://i.pravatar.cc/150?img=1',
    message: 'Hi there!',
    time: '5:10am',
    unread: 2,
  },
  {
    id: 2,
    name: 'Alice',
    avatar: 'https://i.pravatar.cc/150?img=5',
    message: "How's it going?",
    time: '5:10am',
    unread: 5,
  },
  {
    id: 3,
    name: 'Bob',
    avatar: 'https://i.pravatar.cc/150?img=3',
    message: "How's it going?",
    time: '5:10am',
    unread: 0,
  },
];

export const chatMessages = [
  { id: 1, sender: 'other', text: "How's it going?", time: '5:08am' },
  { id: 2, sender: 'me', text: 'Great! Working on the dashboard.', time: '5:09am' },
  { id: 3, sender: 'other', text: 'Looks amazing so far!', time: '5:10am' },
];

export const quickStats = [
  { id: 1, label: 'Completed', value: '25+', icon: 'trophy' },
  { id: 2, label: 'Reviews', value: '105+', icon: 'star' },
  { id: 3, label: 'Working', value: '25+', icon: 'hourglass' },
];

export const dashboardStats = {
  totalProjects: 48,
  activeProjects: 12,
  completedProjects: 25,
  pendingProjects: 11,
};

export const recentUpdates = [
  {
    id: 1,
    project: 'School Portal',
    description: 'Updated homepage slider and navigation menu',
    date: '2025-05-29',
    hours: 3,
  },
  {
    id: 2,
    project: 'E-commerce Store',
    description: 'Added payment gateway integration',
    date: '2025-05-28',
    hours: 5,
  },
  {
    id: 3,
    project: 'Corporate Website',
    description: 'Fixed responsive layout issues on mobile',
    date: '2025-05-27',
    hours: 2,
  },
];

export const upcomingTasks = [
  { id: 1, title: 'Deploy School Portal to production', dueDate: '2025-06-02', priority: 'high' },
  { id: 2, title: 'Client review meeting - ShopMax', dueDate: '2025-06-03', priority: 'medium' },
  { id: 3, title: 'SSL certificate renewal - FinCorp', dueDate: '2025-06-05', priority: 'high' },
];

export const technologies = [
  'WordPress', 'Shopify', 'HTML', 'CSS', 'JavaScript', 'Bootstrap',
  'React.js', 'Next.js', 'Wix', 'Squarespace', 'PHP', 'Laravel',
  'Webflow', 'Framer', 'Email Templates',
];

export const navGroups = [
  {
    title: 'General',
    items: [
      { path: '/', label: 'Dashboard', icon: 'dashboard' },
      { path: '/messages', label: 'Message', icon: 'message', badge: 2 },
      { path: '/schedule', label: 'Schedule', icon: 'schedule', badge: 3, hasAdd: true },
      { path: '/analytics', label: 'Analytics', icon: 'analytics' },
      { path: '/news', label: 'News', icon: 'news' },
      { path: '/recruitment', label: 'Recruitment', icon: 'recruitment' },
      { path: '/projects', label: 'Project', icon: 'project', hasAdd: true },
    ],
  },
  {
    title: 'Myspace',
    items: [
      { path: '/activity', label: 'Activity', icon: 'activity' },
      { path: '/shared', label: 'Shared', icon: 'shared' },
      { path: '/privacy', label: 'Privacy', icon: 'privacy' },
    ],
  },
  {
    title: 'Support',
    items: [
      { path: '/settings', label: 'Setting', icon: 'settings' },
      { path: '/help', label: 'Help!', icon: 'help' },
      { path: '/chat', label: 'Chat', icon: 'chat', badge: 5 },
    ],
  },
];

export const managementNav = [
  { path: '/projects', label: 'Projects', icon: 'project' },
  { path: '/users', label: 'Users', icon: 'users', superAdminOnly: true },
  { path: '/clients', label: 'Clients', icon: 'clients' },
  { path: '/domains', label: 'Domains & Hosting', icon: 'domain' },
  { path: '/updates', label: 'Daily Updates', icon: 'updates' },
  { path: '/schools', label: 'School Websites', icon: 'school' },
  { path: '/reports', label: 'Reports', icon: 'reports' },
];
