import { motion } from 'framer-motion';
import './HeroBubbles.css';

function HeroBubbles() {
  return (
    <div className="hero-bubbles" aria-hidden="true">
      <motion.span
        className="bubble bubble-1"
        animate={{ y: [0, -20, 0], x: [0, 10, 0] }}
        transition={{ duration: 6, repeat: Infinity, ease: 'easeInOut' }}
      />
      <motion.span
        className="bubble bubble-2"
        animate={{ y: [0, 15, 0], x: [0, -12, 0] }}
        transition={{ duration: 7, repeat: Infinity, ease: 'easeInOut' }}
      />
      <motion.span
        className="bubble bubble-3"
        animate={{ y: [0, -12, 0] }}
        transition={{ duration: 5, repeat: Infinity, ease: 'easeInOut' }}
      />
      <motion.span
        className="shield-icon"
        animate={{ rotate: [0, 5, -5, 0] }}
        transition={{ duration: 8, repeat: Infinity }}
      >
        🛡️
      </motion.span>
    </div>
  );
}

export default HeroBubbles;
