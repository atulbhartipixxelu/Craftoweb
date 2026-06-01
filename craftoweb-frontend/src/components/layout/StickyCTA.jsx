import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import './StickyCTA.css';

function StickyCTA() {
  return (
    <motion.div
      className="sticky-cta"
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: 1.2 }}
    >
      <Link to="/contact" className="sticky-cta-btn">
        <span className="sticky-label">Let&apos;s Talk</span>
        <span className="sticky-emoji">🤙</span>
      </Link>
    </motion.div>
  );
}

export default StickyCTA;
