import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { HiOutlineMail, HiOutlinePhone } from 'react-icons/hi';
import './FloatingContact.css';

function FloatingContact() {
  return (
    <div className="floating-contact">
      <motion.a
        href="tel:+919876543210"
        className="float-btn float-phone"
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.95 }}
        aria-label="Call us"
      >
        <HiOutlinePhone />
      </motion.a>
      <motion.div whileHover={{ scale: 1.1 }} whileTap={{ scale: 0.95 }}>
        <Link to="/contact" className="float-btn float-mail" aria-label="Contact us">
          <HiOutlineMail />
        </Link>
      </motion.div>
    </div>
  );
}

export default FloatingContact;
