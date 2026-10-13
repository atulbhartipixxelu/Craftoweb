import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { AnimatePresence, motion } from 'framer-motion';
import { HiOutlineArrowRight, HiOutlineX } from 'react-icons/hi';
import './HireBidderPopup.css';

const STORAGE_KEY = 'craftoweb-hire-bidder-seen';

const roles = [
  { label: 'Laravel', bid: '₹45k+' },
  { label: 'React', bid: '₹40k+' },
  { label: 'Node.js', bid: '₹50k+' },
  { label: 'Shopify', bid: '₹35k+' },
  { label: 'UI/UX', bid: '₹30k+' },
  { label: 'Full Stack', bid: '₹55k+' },
];

function HireBidderPopup() {
  const [open, setOpen] = useState(false);

  useEffect(() => {
    if (typeof window === 'undefined') return undefined;
    if (localStorage.getItem(STORAGE_KEY)) return undefined;

    const t = setTimeout(() => setOpen(true), 2200);
    return () => clearTimeout(t);
  }, []);

  useEffect(() => {
    if (!open) return undefined;
    const prev = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    return () => {
      document.body.style.overflow = prev;
    };
  }, [open]);

  const dismiss = () => {
    localStorage.setItem(STORAGE_KEY, '1');
    setOpen(false);
  };

  return (
    <AnimatePresence>
      {open && (
        <motion.div
          className="hire-bidder-overlay"
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.35 }}
          onClick={dismiss}
          role="presentation"
        >
          <motion.div
            className="hire-bidder-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="hire-bidder-title"
            initial={{ opacity: 0, y: 48, scale: 0.92, rotateX: 8 }}
            animate={{ opacity: 1, y: 0, scale: 1, rotateX: 0 }}
            exit={{ opacity: 0, y: 24, scale: 0.96 }}
            transition={{ type: 'spring', stiffness: 280, damping: 24 }}
            onClick={(e) => e.stopPropagation()}
          >
            <button type="button" className="hire-bidder-close" onClick={dismiss} aria-label="Close">
              <HiOutlineX />
            </button>

            <div className="hire-bidder-visual" aria-hidden="true">
              <div className="hire-bidder-orb hire-bidder-orb--a" />
              <div className="hire-bidder-orb hire-bidder-orb--b" />
              <div className="hire-bidder-radar">
                <span />
                <span />
                <span />
              </div>
              <div className="hire-bidder-pulse">
                <strong>LIVE</strong>
                <em>Talent bids open</em>
              </div>
            </div>

            <div className="hire-bidder-body">
              <span className="hire-bidder-badge">First visit perk</span>
              <h2 id="hire-bidder-title">
                Hire a <em>bidder</em> who actually ships
              </h2>
              <p>
                Skip endless proposals. Lock a vetted CraftoWeb expert — frontend, backend, Shopify, or full-stack —
                with clear scope and a fixed bid.
              </p>

              <ul className="hire-bidder-roles">
                {roles.map((r, i) => (
                  <motion.li
                    key={r.label}
                    initial={{ opacity: 0, y: 12 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: 0.2 + i * 0.05 }}
                  >
                    <span>{r.label}</span>
                    <strong>{r.bid}</strong>
                  </motion.li>
                ))}
              </ul>

              <div className="hire-bidder-actions">
                <Link to="/contact" className="btn btn-primary" onClick={dismiss}>
                  Hire a bidder <HiOutlineArrowRight />
                </Link>
                <button type="button" className="btn btn-ghost" onClick={dismiss}>
                  Maybe later
                </button>
              </div>
            </div>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}

export default HireBidderPopup;
