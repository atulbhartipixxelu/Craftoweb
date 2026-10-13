import { Link } from 'react-router-dom';
import { HiOutlineArrowRight } from 'react-icons/hi';
import {
  services,
  projects,
  toolLogos,
  achievements,
  processSteps,
  testimonials,
  blogPosts,
  companyInfo,
} from '../data/content';
import TalentWheel from '../components/home/TalentWheel';
import './Home.css';

function Home() {
  const logos = [...toolLogos, ...toolLogos, ...toolLogos];

  return (
    <div className="shine-home">
      <section className="shine-hero">
        <div className="container shine-hero-grid">
          <div>
            <p className="shine-kicker">Digital studio · Est. for ambitious brands</p>
            <h1>
              Designing & developing
              <span> beyond the ordinary</span>
            </h1>
            <p>
              Elevate your business with next-generation websites, product design, and development. We build work that
              captivates, converts, and connects.
            </p>
            <div className="shine-hero-actions">
              <Link to="/contact" className="btn btn-primary">
                Get a quote
              </Link>
              <Link to="/work" className="shine-link">
                See our work <HiOutlineArrowRight />
              </Link>
            </div>
            <div className="shine-clients">
              <strong>400+</strong>
              <span>Clients & projects</span>
            </div>
          </div>
          <div className="shine-hero-visual">
            <span className="shine-hero-rail">{projects[0].category}</span>
            <img src={projects[0].image} alt={projects[0].title} />
            <div className="shine-float-card">
              <img src={projects[2].image} alt="" />
              <div>
                <strong>{projects[0].title}</strong>
                <span>{projects[0].category}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="shine-tech">
        <div className="container shine-tech-head">
          <h2>Our tech expertise</h2>
        </div>
        <div className="shine-marquee" aria-hidden="true">
          <div>
            {logos.map((name, i) => (
              <span key={`${name}-${i}`}>{name}</span>
            ))}
          </div>
        </div>
      </section>

      <section className="shine-who">
        <div className="container shine-who-grid">
          <div>
            <p className="shine-label">Who are we?</p>
            <h2>A studio that turns complex ideas into working products.</h2>
          </div>
          <div>
            <p>
              CraftoWeb has spent years building websites, stores, and platforms for ambitious teams. From UI/UX and
              front-end to Laravel, Node, and Shopify, we stay with the work until it is live and useful.
            </p>
            <Link to="/about" className="shine-link">
              Read more <HiOutlineArrowRight />
            </Link>
          </div>
        </div>
      </section>

      <section className="shine-process">
        <div className="container">
          <div className="shine-section-head">
            <div>
              <p className="shine-label">How we work</p>
              <h2>A clear path from first call to launch.</h2>
            </div>
            <p>Six stages. One team. No handoff surprises.</p>
          </div>
          <ol className="shine-steps">
            {processSteps.map((step) => (
              <li key={step.step}>
                <span>{step.step}</span>
                <h3>{step.title}</h3>
                <p>{step.subtitle}</p>
              </li>
            ))}
          </ol>
        </div>
      </section>

      <section className="shine-offer">
        <div className="container">
          <p className="shine-label">What we offer</p>
          <h2>Turn to our services and make the business part of the build.</h2>
          <div className="shine-pills">
            {services.flatMap((s) => s.items.slice(0, 2)).slice(0, 10).map((item) => (
              <span key={item}>{item}</span>
            ))}
          </div>
          <div className="shine-cards">
            {services.map((service) => (
              <article key={service.id} className="shine-card">
                <img src={service.image} alt="" />
                <h3>{service.title}</h3>
                <p>{service.description}</p>
                <Link to={`/services#${service.id}`} className="shine-estimate">
                  Get estimate <HiOutlineArrowRight />
                </Link>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section className="shine-stats">
        <div className="container shine-stats-row">
          {achievements.map((item) => (
            <div key={item.label}>
              <strong>
                {item.value}
                {item.suffix}
              </strong>
              <span>{item.label}</span>
            </div>
          ))}
        </div>
      </section>

      <TalentWheel />

      <section className="shine-quotes">
        <div className="container">
          <p className="shine-label">Client notes</p>
          <blockquote>
            <p>&ldquo;{testimonials[0].text}&rdquo;</p>
            <footer>
              <img src={testimonials[0].avatar} alt="" />
              <div>
                <strong>{testimonials[0].name}</strong>
                <span>{testimonials[0].role}</span>
              </div>
            </footer>
          </blockquote>
          <div className="shine-quote-row">
            {testimonials.slice(1, 4).map((item) => (
              <article key={item.id}>
                <p>&ldquo;{item.text}&rdquo;</p>
                <strong>{item.name}</strong>
                <span>{item.role}</span>
              </article>
            ))}
          </div>
          <Link to="/reviews" className="shine-link">
            All reviews <HiOutlineArrowRight />
          </Link>
        </div>
      </section>

      <section className="shine-work">
        <div className="container">
          <div className="shine-work-head">
            <div>
              <p className="shine-label">Portfolio</p>
              <h2>Selected work</h2>
            </div>
            <Link to="/work" className="shine-link">
              View all <HiOutlineArrowRight />
            </Link>
          </div>
          <div className="shine-work-grid">
            {projects.slice(0, 3).map((project) => (
              <Link key={project.id} to="/work" className="shine-work-card">
                <img src={project.image} alt={project.title} />
                <div>
                  <span>{project.category}</span>
                  <h3>{project.title}</h3>
                  <p>{project.subtitle}</p>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <section className="shine-insights">
        <div className="container">
          <div className="shine-section-head">
            <div>
              <p className="shine-label">Insights</p>
              <h2>Notes from the studio.</h2>
            </div>
            <Link to="/blog" className="shine-link">
              All articles <HiOutlineArrowRight />
            </Link>
          </div>
          <div className="shine-posts">
            {blogPosts.slice(0, 3).map((post) => (
              <Link key={post.id} to={`/blog/${post.slug}`} className="shine-post">
                <img src={post.image} alt="" />
                <span>{post.category}</span>
                <h3>{post.title}</h3>
                <p>{post.excerpt}</p>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <section className="shine-cta">
        <div className="container shine-cta-grid">
          <div>
            <p className="shine-label shine-label-light">Start a project</p>
            <h2>
              Like what you see?
              <span> Let’s start a conversation.</span>
            </h2>
          </div>
          <div className="shine-cta-side">
            <a href={`mailto:${companyInfo.email}`}>{companyInfo.email}</a>
            <a href={`tel:${companyInfo.phone.replace(/\s/g, '')}`}>{companyInfo.phone}</a>
            <Link to="/contact" className="btn btn-primary">
              Get in touch
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}

export default Home;
