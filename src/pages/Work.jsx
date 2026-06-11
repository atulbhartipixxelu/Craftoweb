import { useState } from 'react';
import SectionHeading from '../components/ui/SectionHeading';
import CTABand from '../components/ui/CTABand';
import { industries } from '../data/content';
import { usePortfolio } from '../hooks/usePortfolio';
import './Work.css';

function Work() {
  const { projects, loading, error } = usePortfolio();
  const [filter, setFilter] = useState('All');

  const categories = ['All', ...new Set(projects.map((p) => p.category))];
  const filtered =
    filter === 'All' ? projects : projects.filter((p) => p.category === filter);

  return (
    <div className="page-shell">
      <header className="page-hero" data-aos="fade-down">
        <div className="container">
          <span className="eyebrow">Portfolio</span>
          <h1 className="display-lg">
            Work that <span className="text-gradient">speaks for itself</span>
          </h1>
          <p>Live projects from our dashboard — updated as we deliver new work.</p>
        </div>
      </header>

      <section className="section">
        <div className="container">
          {loading ? (
            <p className="work-status">Loading portfolio...</p>
          ) : (
            <>
              {error && <p className="work-status work-status-error">{error}</p>}

              {projects.length > 0 && (
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
              )}

              <div className="work-grid">
                {filtered.length > 0 ? (
                  filtered.map((p, i) => (
                    <article
                      key={p.id}
                      className="work-item card-glass"
                      data-aos="fade-up"
                      data-aos-delay={i * 60}
                    >
                      <div className="work-item-img">
                        <img src={p.image} alt={p.title} loading="lazy" />
                        <span>{p.category}</span>
                      </div>
                      <div className="work-item-body">
                        <h3>{p.title}</h3>
                        <p>{p.subtitle}</p>
                      </div>
                    </article>
                  ))
                ) : (
                  <p className="work-status">
                    {error ? 'Please try again later.' : 'No projects to show yet.'}
                  </p>
                )}
              </div>
            </>
          )}

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
