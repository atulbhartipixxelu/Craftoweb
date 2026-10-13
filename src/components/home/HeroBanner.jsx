import { lazy, Suspense } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { HiOutlineArrowRight } from 'react-icons/hi';
import { projects } from '../../data/content';
import Hero3DFallback from './Hero3DFallback';
import './HeroBanner.css';

const Hero3D = lazy(() => import('./Hero3D'));

function HeroBanner() {
  const featured = projects[0];

  return (
    <section className="px-hero">
      <div className="px-hero-stage" aria-hidden="true">
        <Suspense fallback={<Hero3DFallback />}>
          <Hero3D />
        </Suspense>
        <div className="px-hero-shade" />
      </div>

      <div className="container px-hero-grid">
        <motion.div
          className="px-hero-copy"
          initial={{ opacity: 0, y: 28 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, ease: [0.22, 1, 0.36, 1] }}
        >
          <h1 className="display-xl">
            Designing & developing <br />
            beyond <em>the ordinary</em>
          </h1>
          <p>
            At CraftoWeb, we build websites that do more than look good. They captivate, convert, and connect —
            from strategy to pixel-perfect execution.
          </p>
          <div className="px-hero-actions">
            <Link to="/contact" className="btn btn-primary">
              Book a short call <HiOutlineArrowRight />
            </Link>
            <Link to="/work" className="btn btn-ghost">
              Our Work
            </Link>
          </div>
        </motion.div>

        <motion.div
          className="px-hero-float"
          initial={{ opacity: 0, y: 40, rotate: 2 }}
          animate={{ opacity: 1, y: 0, rotate: 0 }}
          transition={{ duration: 0.9, delay: 0.15 }}
        >
          <Link to="/work" className="px-case-card">
            <img src={featured.image} alt={featured.title} />
            <div>
              <strong>{featured.title}</strong>
              <span>Increase conversion 34%</span>
              <em>
                View Case <HiOutlineArrowRight />
              </em>
            </div>
          </Link>
        </motion.div>
      </div>
    </section>
  );
}

export default HeroBanner;
