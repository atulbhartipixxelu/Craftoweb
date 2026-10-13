const API_URL = import.meta.env.VITE_API_URL || 'https://api.craftoweb.com/api';

const DEFAULT_IMAGE =
  'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80';

const TECH_IMAGES = {
  'React.js': 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=800&q=80',
  'Next.js': 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&q=80',
  Shopify: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&q=80',
  WordPress: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&q=80',
};

export async function fetchPortfolio() {
  const res = await fetch(`${API_URL}/portfolio`);
  if (!res.ok) {
    throw new Error('Failed to load portfolio');
  }
  const json = await res.json();
  return json.data ?? [];
}

export function mapProjectToWorkItem(project) {
  const subtitle = project.description?.trim()
    || (project.client ? `Client: ${project.client}` : project.technology);

  return {
    id: project.id,
    title: project.name,
    subtitle,
    category: project.technology,
    image: project.image || TECH_IMAGES[project.technology] || DEFAULT_IMAGE,
    status: project.status,
  };
}
