import { useEffect, useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import LottiePlayer from './LottiePlayer';
import { lottieSources } from '../../data/content';
import './PageLoader.css';

function PageLoader() {
  const [done, setDone] = useState(false);

  useEffect(() => {
    const t = setTimeout(() => setDone(true), 1600);
    return () => clearTimeout(t);
  }, []);

  return (
    <AnimatePresence>
      {!done && (
        <motion.div
          className="page-loader"
          initial={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.5 }}
        >
          <div className="page-loader-inner">
            <LottiePlayer src={lottieSources.loading} className="loader-lottie" />
            <p className="logo">Crafto<span>Web</span></p>
          </div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}

export default PageLoader;
