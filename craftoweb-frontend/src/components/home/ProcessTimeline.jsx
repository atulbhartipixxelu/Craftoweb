import { useEffect, useRef } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import SectionHeading from '../ui/SectionHeading';
import { processSteps } from '../../data/content';
import './ProcessTimeline.css';

gsap.registerPlugin(ScrollTrigger);

function ProcessTimeline() {
  const lineRef = useRef(null);
  const sectionRef = useRef(null);

  useEffect(() => {
    const line = lineRef.current;
    const section = sectionRef.current;
    if (!line || !section) return undefined;

    const tween = gsap.fromTo(
      line,
      { scaleY: 0 },
      {
        scaleY: 1,
        ease: 'none',
        scrollTrigger: {
          trigger: section,
          start: 'top 70%',
          end: 'bottom 30%',
          scrub: 1,
        },
      }
    );

    return () => tween.scrollTrigger?.kill();
  }, []);

  return (
    <section className="section process-timeline-section" ref={sectionRef}>
      <div className="container">
        <SectionHeading
          eyebrow="Process Timeline"
          title="From idea to launch in six steps"
          description="Our proven workflow keeps every project on track — transparent, collaborative, and results-driven."
          align="center"
        />
        <div className="process-timeline-wrap">
          <div className="process-line" ref={lineRef} aria-hidden="true" />
          <div className="process-timeline-list">
            {processSteps.map((step, i) => (
              <div
                key={step.step}
                className={`process-timeline-item card-glass gsap-reveal ${i % 2 === 1 ? 'alt' : ''}`}
                data-aos="fade-up"
                data-aos-delay={i * 80}
              >
                <span className="process-dot">{step.step}</span>
                <div>
                  <h4>{step.title}</h4>
                  <small>{step.subtitle}</small>
                  <p>{step.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}

export default ProcessTimeline;
