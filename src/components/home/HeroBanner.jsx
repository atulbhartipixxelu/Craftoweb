import { lazy, Suspense } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { HiOutlineArrowRight, HiOutlinePlay } from 'react-icons/hi';
import { clientLogos, achievements } from '../../data/content';
import AnimatedCounter from '../common/AnimatedCounter';
import Hero3DFallback from './Hero3DFallback';
import { useMouseParallax } from '../../hooks/useMouseParallax';
import './HeroBanner.css';

const Hero3D = lazy(() => import('./Hero3D'));

function HeroBanner() {
  const logos = [...clientLogos, ...clientLogos];
  const parallaxRef = useMouseParallax(0.025);

  return (
    <section className="hero">
      <div className="hero-bg" aria-hidden="true">
        <div className="hero-orb hero-orb-1" />
        <div className="hero-orb hero-orb-2" />
        <div className="hero-grid" />
      </div>

      <div className="container hero-layout">
        <motion.div
          className="hero-copy"
          initial={{ opacity: 0, y: 32 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.7, ease: [0.22, 1, 0.36, 1] }}
        >
          <span className="hero-badge">CraftoWeb.com · Premium IT Agency</span>
          <h1 className="display-xl">
            We craft <span className="text-gradient">digital experiences</span> that grow your business.
          </h1>
          <p className="hero-lead">
            Web development, UI/UX design, CMS, and digital marketing — built with React, modern animations, and human-centered strategy.
          </p>
          <div className="hero-actions">
            <Link to="/contact" className="btn btn-primary">
              Start Your Project <HiOutlineArrowRight />
            </Link>
            <Link to="/work" className="btn btn-ghost">
              <HiOutlinePlay /> View Portfolios
            </Link>
          </div>
          <div className="hero-stats">
            {achievements.slice(0, 3).map((a) => (
              <div key={a.label} className="hero-stat">
                <strong>
                  <AnimatedCounter end={a.value} suffix={a.suffix} />
                </strong>
                <span>{a.label}</span>
              </div>
            ))}
          </div>
        </motion.div>

        <motion.div
          className="hero-visual"
          ref={parallaxRef}
          initial={{ opacity: 0, scale: 0.96 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ duration: 0.8, delay: 0.2 }}
        >
          <Suspense fallback={<Hero3DFallback />}>
            <Hero3D />
          </Suspense>
          <div className="hero-card hero-card-float card-glass">
            <span className="hero-float-icon">✦</span>
            <div>
              <strong>98%</strong>
              <small>Client satisfaction</small>
            </div>
          </div>
        </motion.div>
      </div>

      <div className="hero-marquee">
        <div className="hero-marquee-track animate-marquee">
          {logos.map((name, i) => (
            <span key={`${name}-${i}`}>{name}</span>
          ))}
        </div>
      </div>
    </section>
  );
}

export default HeroBanner;
