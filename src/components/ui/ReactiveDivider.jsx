import { useEffect, useRef, useState } from 'react';
import './ReactiveDivider.css';

function ReactiveDivider({ className = '' }) {
  const ref = useRef(null);
  const [width, setWidth] = useState(0);

  useEffect(() => {
    const el = ref.current;
    if (!el) return undefined;
    const measure = () => setWidth(Math.round(el.getBoundingClientRect().width));
    measure();
    const observer = new ResizeObserver(measure);
    observer.observe(el);
    window.addEventListener('resize', measure);
    return () => {
      observer.disconnect();
      window.removeEventListener('resize', measure);
    };
  }, []);

  return (
    <div className={`rx-divider ${className}`} ref={ref} aria-hidden="true">
      <span className="rx-dot" />
      <span className="rx-line" />
      <span className="rx-width">{width}px</span>
      <span className="rx-line" />
      <span className="rx-dot" />
    </div>
  );
}

export default ReactiveDivider;
