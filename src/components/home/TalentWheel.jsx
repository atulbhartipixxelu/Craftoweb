import { useEffect, useRef } from 'react';
import { Link } from 'react-router-dom';
import { HiOutlineArrowRight, HiOutlinePlus } from 'react-icons/hi';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { MotionPathPlugin } from 'gsap/MotionPathPlugin';
import { team, whyChoose } from '../../data/content';
import './TalentWheel.css';

gsap.registerPlugin(ScrollTrigger, MotionPathPlugin);

const slides = [
  {
    title: whyChoose[0].title,
    text: 'You work directly with the people designing and building the product. Every project stays with senior leads, so you get a full studio without the extra layers.',
    cta: 'Meet the makers',
    to: '/about',
    faces: true,
  },
  {
    title: whyChoose[1].title,
    text: whyChoose[1].desc,
    cta: 'See the work',
    to: '/work',
    faces: false,
  },
  {
    title: whyChoose[2].title,
    text: whyChoose[2].desc,
    cta: 'Start a project',
    to: '/contact',
    faces: false,
  },
];

function TalentWheel() {
  const rootRef = useRef(null);

  useEffect(() => {
    const root = rootRef.current;
    if (!root) return undefined;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const narrow = window.matchMedia('(max-width: 1023px)').matches;
    if (reduced || narrow) return undefined;

    const blocks = root.querySelectorAll('.wheel-block');
    const path = root.querySelector('#talent-wheel-path');
    const rings = root.querySelector('.wheel-rings');
    if (!blocks.length || !path) return undefined;

    const count = blocks.length;
    const gap = 0.28;
    const travel = 1 - (count - 1) * gap;
    const fade = 0.16;

    const ctx = gsap.context(() => {
      const raw = MotionPathPlugin.getRawPath(path);
      MotionPathPlugin.cacheRawPathMeasurements(raw);
      const start = MotionPathPlugin.getPositionOnPath(raw, 0);

      gsap.set(blocks, {
        xPercent: -50,
        yPercent: -50,
        x: start.x,
        y: start.y,
        autoAlpha: 0,
        scale: 0.92,
        force3D: true,
      });

      const timeline = gsap.timeline({
        defaults: { force3D: true },
        scrollTrigger: {
          trigger: root,
          start: 'top top',
          end: '+=180%',
          pin: true,
          scrub: 0.45,
          anticipatePin: 1,
          invalidateOnRefresh: true,
        },
      });

      blocks.forEach((block, index) => {
        const startAt = index * gap;
        const endAt = startAt + travel;
        timeline.to(
          block,
          {
            motionPath: {
              path,
              align: path,
              alignOrigin: [0.5, 0.5],
              start: 0,
              end: 1,
            },
            ease: 'none',
            duration: travel,
          },
          startAt
        );
        timeline.fromTo(
          block,
          { autoAlpha: 0, scale: 0.9 },
          { autoAlpha: 1, scale: 1, ease: 'power1.out', duration: fade },
          startAt
        );
        timeline.to(block, { autoAlpha: 0, scale: 0.94, ease: 'power1.in', duration: fade }, endAt - fade);
      });

      if (rings) {
        timeline.fromTo(rings, { rotation: 0 }, { rotation: -120, ease: 'none', duration: 1, transformOrigin: '50% 50%' }, 0);
      }
    }, root);

    return () => ctx.revert();
  }, []);

  return (
    <section className="talent-wheel" ref={rootRef} aria-label="Why teams choose CraftoWeb">
      <div className="wheel-rings-slot" aria-hidden="true">
        <svg className="wheel-rings" viewBox="0 0 800 800">
          <circle cx="400" cy="400" r="150" />
          <circle cx="400" cy="400" r="210" />
          <circle cx="400" cy="400" r="270" />
          <circle cx="400" cy="400" r="330" />
          <circle cx="400" cy="400" r="390" />
        </svg>
      </div>

      <svg className="wheel-path" viewBox="0 0 1400 800" preserveAspectRatio="none" aria-hidden="true">
        <path id="talent-wheel-path" d="M40,220 C350,560 1050,560 1360,220" fill="none" />
      </svg>

      <div className="wheel-stage">
        {slides.map((slide) => (
          <article key={slide.title} className="wheel-block">
            {slide.faces ? <FaceStack /> : <Mark />}
            <h3>{slide.title}</h3>
            <p>{slide.text}</p>
            <Link to={slide.to} className="wheel-btn">
              {slide.cta} <HiOutlineArrowRight />
            </Link>
          </article>
        ))}
      </div>
    </section>
  );
}

function FaceStack() {
  return (
    <div className="face-pill">
      <div className="face-stack">
        {team.map((member) => (
          <img key={member.name} src={member.image} alt={member.name} />
        ))}
      </div>
      <Link to="/about" className="face-add" aria-label="Meet the team">
        <HiOutlinePlus />
      </Link>
    </div>
  );
}

function Mark() {
  return <span className="wheel-mark" aria-hidden="true" />;
}

export default TalentWheel;
