import { useEffect, useRef } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { processSteps } from '../../data/content';
import './AnimatedTimeline.css';

gsap.registerPlugin(ScrollTrigger);

function AnimatedTimeline() {
  const trackRef = useRef(null);

  useEffect(() => {
    const track = trackRef.current;
    if (!track) return undefined;

    const items = track.querySelectorAll('.timeline-item');
    const ctx = gsap.context(() => {
      items.forEach((item, i) => {
        gsap.from(item, {
          scrollTrigger: {
            trigger: item,
            start: 'top 85%',
          },
          opacity: 0,
          x: i % 2 === 0 ? -40 : 40,
          duration: 0.7,
          ease: 'power2.out',
        });
      });
    }, track);

    return () => ctx.revert();
  }, []);

  return (
    <div className="animated-timeline" ref={trackRef}>
      {processSteps.map((step) => (
        <div key={step.step} className="timeline-item card-glass">
          <span className="timeline-step">{step.step}</span>
          <div>
            <h4>{step.title}</h4>
            <p>{step.subtitle}</p>
          </div>
        </div>
      ))}
    </div>
  );
}

export default AnimatedTimeline;
