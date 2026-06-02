import { useEffect } from 'react';
import Lenis from 'lenis';

function SmoothScroll({ children }) {
  useEffect(() => {
    const lenis = new Lenis({
      duration: 1.15,
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      smoothWheel: true,
      touchMultiplier: 1.5,
    });

    document.documentElement.classList.add('lenis');

    let rafId;
    const raf = (time) => {
      lenis.raf(time);
      rafId = requestAnimationFrame(raf);
    };
    rafId = requestAnimationFrame(raf);

    return () => {
      document.documentElement.classList.remove('lenis');
      cancelAnimationFrame(rafId);
      lenis.destroy();
    };
  }, []);

  return children;
}

export default SmoothScroll;
