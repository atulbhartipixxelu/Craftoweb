import { useEffect } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function useHomeScroll(enabled = true) {
  useEffect(() => {
    if (!enabled) return undefined;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) return undefined;

    const ctx = gsap.context(() => {
      gsap.utils.toArray('.fw-reveal').forEach((el) => {
        gsap.from(el, {
          scrollTrigger: { trigger: el, start: 'top 88%', toggleActions: 'play none none reverse' },
          y: 70,
          opacity: 0,
          duration: 1,
          ease: 'power3.out',
        });
      });

      gsap.utils.toArray('.fw-reveal-left').forEach((el) => {
        gsap.from(el, {
          scrollTrigger: { trigger: el, start: 'top 85%' },
          x: -80,
          rotateY: 12,
          opacity: 0,
          duration: 1,
          transformPerspective: 900,
          ease: 'power3.out',
        });
      });

      gsap.utils.toArray('.fw-reveal-right').forEach((el) => {
        gsap.from(el, {
          scrollTrigger: { trigger: el, start: 'top 85%' },
          x: 80,
          rotateY: -12,
          opacity: 0,
          duration: 1,
          transformPerspective: 900,
          ease: 'power3.out',
        });
      });

      gsap.utils.toArray('.fw-scale').forEach((el) => {
        gsap.from(el, {
          scrollTrigger: { trigger: el, start: 'top 90%' },
          scale: 0.86,
          opacity: 0,
          duration: 1.05,
          ease: 'power3.out',
        });
      });

      gsap.utils.toArray('.fw-stagger').forEach((group) => {
        const kids = group.querySelectorAll('.fw-stagger-item');
        gsap.from(kids, {
          scrollTrigger: { trigger: group, start: 'top 85%' },
          y: 50,
          opacity: 0,
          rotateX: 18,
          transformPerspective: 800,
          duration: 0.75,
          stagger: 0.1,
          ease: 'power3.out',
        });
      });

      gsap.utils.toArray('.fw-parallax').forEach((el) => {
        gsap.to(el, {
          yPercent: -18,
          ease: 'none',
          scrollTrigger: {
            trigger: el.parentElement || el,
            start: 'top bottom',
            end: 'bottom top',
            scrub: true,
          },
        });
      });

      gsap.utils.toArray('.fw-zoom-img').forEach((el) => {
        gsap.fromTo(
          el,
          { scale: 1.18 },
          {
            scale: 1,
            ease: 'none',
            scrollTrigger: {
              trigger: el.parentElement || el,
              start: 'top bottom',
              end: 'bottom top',
              scrub: true,
            },
          }
        );
      });

      const line = document.querySelector('.fw-progress-line');
      if (line) {
        gsap.fromTo(
          line,
          { scaleX: 0 },
          {
            scaleX: 1,
            ease: 'none',
            scrollTrigger: {
              trigger: document.documentElement,
              start: 'top top',
              end: 'bottom bottom',
              scrub: 0.3,
            },
          }
        );
      }

      gsap.utils.toArray('.fw-marquee-track').forEach((track) => {
        gsap.to(track, {
          xPercent: -50,
          ease: 'none',
          duration: 28,
          repeat: -1,
        });
      });
    });

    return () => ctx.revert();
  }, [enabled]);
}

export default useHomeScroll;
