import { useEffect } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function useGsapScroll(refreshKey) {
  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.utils.toArray('.gsap-reveal').forEach((el) => {
        gsap.from(el, {
          scrollTrigger: {
            trigger: el,
            start: 'top 88%',
            toggleActions: 'play none none reverse',
          },
          y: 48,
          opacity: 0,
          duration: 0.9,
          ease: 'power3.out',
        });
      });

      gsap.utils.toArray('.gsap-reveal-left').forEach((el) => {
        gsap.from(el, {
          scrollTrigger: { trigger: el, start: 'top 85%' },
          x: -60,
          opacity: 0,
          duration: 0.85,
          ease: 'power3.out',
        });
      });

      gsap.utils.toArray('.gsap-reveal-right').forEach((el) => {
        gsap.from(el, {
          scrollTrigger: { trigger: el, start: 'top 85%' },
          x: 60,
          opacity: 0,
          duration: 0.85,
          ease: 'power3.out',
        });
      });
    });

    return () => {
      ctx.revert();
    };
  }, [refreshKey]);
}

export default useGsapScroll;
