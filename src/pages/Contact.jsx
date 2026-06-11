import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { HiOutlineMail, HiOutlinePhone, HiOutlineLocationMarker } from 'react-icons/hi';
import SectionHeading from '../components/ui/SectionHeading';
import LottiePlayer from '../components/common/LottiePlayer';
import { companyInfo, socialLinks, lottieSources } from '../data/content';
import './Contact.css';

const API_URL = import.meta.env.VITE_API_URL || 'https://api.craftoweb.com/api';

function Contact() {
  const [form, setForm] = useState({ name: '', email: '', phone: '', subject: '', message: '' });
  const [errors, setErrors] = useState({});
  const [submitted, setSubmitted] = useState(false);
  const [loading, setLoading] = useState(false);
  const [submitError, setSubmitError] = useState('');

  const validate = () => {
    const e = {};
    if (!form.name.trim()) e.name = 'Name is required';
    if (!form.email.trim()) e.email = 'Email is required';
    else if (!/\S+@\S+\.\S+/.test(form.email)) e.email = 'Invalid email';
    if (!form.message.trim()) e.message = 'Message is required';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!validate()) return;
    setLoading(true);
    setSubmitError('');
    try {
      const response = await fetch(`${API_URL}/contact`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(form),
      });
      const data = await response.json().catch(() => ({}));
      if (!response.ok) {
        const msg =
          data.message ||
          Object.values(data.errors || {}).flat().join(' ') ||
          'Could not send message. Please try again.';
        setSubmitError(msg);
        return;
      }
      setSubmitted(true);
      setForm({ name: '', email: '', phone: '', subject: '', message: '' });
    } catch {
      setSubmitError('Network error. Please check your connection and try again.');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
    if (errors[name]) setErrors((prev) => ({ ...prev, [name]: '' }));
  };

  return (
    <div className="page-shell">
      <header className="page-hero" data-aos="fade-down">
        <div className="container">
          <span className="eyebrow">Contact</span>
          <h1 className="display-lg">
            Let&apos;s start something <span className="text-gradient">great</span>
          </h1>
          <p>Tell us about your project. We typically respond within one business day.</p>
        </div>
      </header>

      <section className="section">
        <div className="container contact-layout">
          <div className="contact-aside" data-aos="fade-right">
            <SectionHeading
              title="We'd love to hear from you"
              description="Whether you need a new website, app redesign, or growth strategy — our team is ready to help."
            />
            <ul className="contact-info-list">
              <li>
                <HiOutlineMail />
                <div>
                  <strong>Email</strong>
                  <a href={`mailto:${companyInfo.email}`}>{companyInfo.email}</a>
                </div>
              </li>
              <li>
                <HiOutlinePhone />
                <div>
                  <strong>Phone</strong>
                  <a href={`tel:${companyInfo.phone.replace(/\s/g, '')}`}>{companyInfo.phone}</a>
                </div>
              </li>
              <li>
                <HiOutlineLocationMarker />
                <div>
                  <strong>Office</strong>
                  <span>{companyInfo.address}</span>
                </div>
              </li>
            </ul>
            <div className="contact-social-row">
              {socialLinks.map((s) => (
                <a key={s.name} href={s.url} target="_blank" rel="noreferrer">
                  {s.name}
                </a>
              ))}
            </div>
          </div>

          <div className="contact-form-card card-glass" data-aos="fade-left">
            <AnimatePresence mode="wait">
              {submitted ? (
                <motion.div
                  key="ok"
                  className="form-success"
                  initial={{ opacity: 0, scale: 0.95 }}
                  animate={{ opacity: 1, scale: 1 }}
                >
                  <LottiePlayer src={lottieSources.success} className="success-lottie" />
                  <h3>Message sent!</h3>
                  <p>Thanks for reaching out. We&apos;ll get back to you soon.</p>
                  <button type="button" className="btn btn-primary" onClick={() => setSubmitted(false)}>
                    Send another
                  </button>
                </motion.div>
              ) : (
                <motion.form key="form" onSubmit={handleSubmit} initial={{ opacity: 0 }} animate={{ opacity: 1 }}>
                  <h3>Send a message</h3>
                  {submitError && (
                    <div className="form-error" style={{ marginBottom: '1rem', color: '#f87171' }}>
                      {submitError}
                    </div>
                  )}
                  <div className="form-row">
                    <div className="form-field">
                      <label htmlFor="name">Name *</label>
                      <input id="name" name="name" value={form.name} onChange={handleChange} className={errors.name ? 'err' : ''} />
                      {errors.name && <em>{errors.name}</em>}
                    </div>
                    <div className="form-field">
                      <label htmlFor="email">Email *</label>
                      <input id="email" name="email" type="email" value={form.email} onChange={handleChange} className={errors.email ? 'err' : ''} />
                      {errors.email && <em>{errors.email}</em>}
                    </div>
                  </div>
                  <div className="form-row">
                    <div className="form-field">
                      <label htmlFor="phone">Phone</label>
                      <input id="phone" name="phone" value={form.phone} onChange={handleChange} />
                    </div>
                    <div className="form-field">
                      <label htmlFor="subject">Subject</label>
                      <input id="subject" name="subject" value={form.subject} onChange={handleChange} />
                    </div>
                  </div>
                  <div className="form-field">
                    <label htmlFor="message">Message *</label>
                    <textarea id="message" name="message" rows={5} value={form.message} onChange={handleChange} className={errors.message ? 'err' : ''} />
                    {errors.message && <em>{errors.message}</em>}
                  </div>
                  <button type="submit" className="btn btn-primary" disabled={loading} style={{ width: '100%' }}>
                    {loading ? 'Sending…' : 'Send message'}
                  </button>
                </motion.form>
              )}
            </AnimatePresence>
          </div>
        </div>
      </section>

      <section className="section section-tight">
        <div className="container contact-map card-glass" data-aos="fade-up">
          <iframe
            title="CraftoWeb location"
            src={companyInfo.mapEmbed}
            width="100%"
            height="360"
            style={{ border: 0, borderRadius: 'var(--radius-lg)' }}
            allowFullScreen
            loading="lazy"
            referrerPolicy="no-referrer-when-downgrade"
          />
        </div>
      </section>
    </div>
  );
}

export default Contact;
