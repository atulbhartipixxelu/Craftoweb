import { useEffect, useState } from 'react';
import { fetchPortfolio, mapProjectToWorkItem } from '../services/projectsApi';

export function usePortfolio() {
  const [projects, setProjects] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    let cancelled = false;

    (async () => {
      try {
        const data = await fetchPortfolio();
        if (!cancelled) {
          setProjects(data.map(mapProjectToWorkItem));
        }
      } catch {
        if (!cancelled) {
          setError('Unable to load projects right now.');
        }
      } finally {
        if (!cancelled) {
          setLoading(false);
        }
      }
    })();

    return () => {
      cancelled = true;
    };
  }, []);

  return { projects, loading, error };
}
