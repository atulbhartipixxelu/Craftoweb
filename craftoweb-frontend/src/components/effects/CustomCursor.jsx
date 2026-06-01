import { useEffect, useState } from 'react';
import './CustomCursor.css';

function CustomCursor() {
  const [pos, setPos] = useState({ x: 0, y: 0 });
  const [ring, setRing] = useState({ x: 0, y: 0 });
  const [mode, setMode] = useState('default');
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const isTouch = window.matchMedia('(pointer: coarse)').matches;
    if (isTouch) return;

    document.documentElement.classList.add('custom-cursor-active');

    let ringX = 0;
    let ringY = 0;
    let targetX = 0;
    let targetY = 0;
    let rafId;

    const onMove = (e) => {
      targetX = e.clientX;
      targetY = e.clientY;
      setPos({ x: e.clientX, y: e.clientY });
      if (!visible) setVisible(true);

      const target = e.target.closest(
        'a, button, .btn, .magnetic-btn, input, textarea, select, .glow-heading, .section-title, h1, h2, .card, .showcase-card'
      );

      if (target?.closest('.glow-heading, .section-title, h1, h2')) {
        setMode('text');
      } else if (target?.closest('a, button, .btn, .magnetic-btn')) {
        setMode('button');
      } else if (target?.closest('.card, .showcase-card, .service-cat-card')) {
        setMode('card');
      } else {
        setMode('default');
      }
    };

    const animateRing = () => {
      ringX += (targetX - ringX) * 0.15;
      ringY += (targetY - ringY) * 0.15;
      setRing({ x: ringX, y: ringY });
      rafId = requestAnimationFrame(animateRing);
    };
    rafId = requestAnimationFrame(animateRing);

    const onLeave = () => setVisible(false);
    const onEnter = () => setVisible(true);

    window.addEventListener('mousemove', onMove);
    document.addEventListener('mouseleave', onLeave);
    document.addEventListener('mouseenter', onEnter);

    return () => {
      document.documentElement.classList.remove('custom-cursor-active');
      cancelAnimationFrame(rafId);
      window.removeEventListener('mousemove', onMove);
      document.removeEventListener('mouseleave', onLeave);
      document.removeEventListener('mouseenter', onEnter);
    };
  }, [visible]);

  if (typeof window !== 'undefined' && window.matchMedia('(pointer: coarse)').matches) {
    return null;
  }

  return (
    <>
      <div
        className={`cursor-dot ${mode} ${visible ? 'visible' : ''}`}
        style={{ transform: `translate(${pos.x}px, ${pos.y}px)` }}
      />
      <div
        className={`cursor-ring ${mode} ${visible ? 'visible' : ''}`}
        style={{ transform: `translate(${ring.x}px, ${ring.y}px)` }}
      />
    </>
  );
}

export default CustomCursor;
