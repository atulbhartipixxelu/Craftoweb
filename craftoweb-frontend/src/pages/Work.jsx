import { useState } from 'react';
import SectionHeading from '../components/ui/SectionHeading';
import CTABand from '../components/ui/CTABand';
import { projects, industries } from '../data/content';
import './Work.css';

function Work() {
  const [filter, setFilter] = useState('All');
  const categories = ['All', ...new Set(projects.map((p) => p.category))];
  const filtered = filter === 'All' ? projects : projects.filter((p) => p.category === filter);

  return (
    <div className="page-shell">
      <header className="page-hero" data-aos="fade-down">
        <div className="container">
          <span className="eyebrow">Portfolio</span>
          <h1 className="display-lg">
            Work that <span className="text-gradient">speaks for itself</span>
          </h1>
          <p>Case studies and launches across SaaS, e-commerce, fintech, and more.</p>
        </div>
      </header>

      <section className="section">
        <div className="container">
          <div className="work-filters" data-aos="fade-up">
            {categories.map((cat) => (
              <button
                key={cat}
                type="button"
                className={`filter-btn ${filter === cat ? 'active' : ''}`}
                onClick={() => setFilter(cat)}
              >
                {cat}
              </button>
            ))}
          </div>

          <div className="work-grid">
            {filtered.map((p, i) => (
              <article key={p.id} className="work-item card-glass" data-aos="fade-up" data-aos-delay={i * 60}>
                <div className="work-item-img">
                  <img src={p.image} alt={p.title} loading="lazy" />
                  <span>{p.category}</span>
                </div>
                <div className="work-item-body">
                  <h3>{p.title}</h3>
                  <p>{p.subtitle}</p>
                </div>
              </article>
            ))}
          </div>

          <div className="work-industries" data-aos="fade-up">
            <p>Industries we serve:</p>
            <div className="industry-tags">
              {industries.map((ind) => (
                <span key={ind}>{ind}</span>
              ))}
            </div>
          </div>
        </div>
      </section>

      <CTABand />
    </div>
  );
}

export default Work;
