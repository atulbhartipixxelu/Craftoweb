import { useRef } from 'react';
import './GlowHeading.css';

function GlowHeading({ as: Tag = 'h2', className = '', children, ...props }) {
  const ref = useRef(null);

  const handleMove = (e) => {
    const el = ref.current;
    if (!el) return;
    const rect = el.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    el.style.setProperty('--glow-x', `${x}%`);
    el.style.setProperty('--glow-y', `${y}%`);
  };

  const handleLeave = () => {
    const el = ref.current;
    if (el) {
      el.style.setProperty('--glow-x', '50%');
      el.style.setProperty('--glow-y', '50%');
    }
  };

  return (
    <Tag
      ref={ref}
      className={`glow-heading ${className}`}
      onMouseMove={handleMove}
      onMouseLeave={handleLeave}
      {...props}
    >
      <span className="glow-heading-text">{children}</span>
    </Tag>
  );
}

export default GlowHeading;
