import { Link } from 'react-router-dom';
import {
  HiOutlineArrowRight,
  HiOutlineCode,
  HiOutlineColorSwatch,
  HiOutlineDatabase,
  HiOutlineLightningBolt,
  HiOutlineTrendingUp,
  HiOutlineStar,
} from 'react-icons/hi';
import HeroBanner from '../components/home/HeroBanner';
import CompanyIntro from '../components/home/CompanyIntro';
import WhyChoose from '../components/home/WhyChoose';
import ProcessTimeline from '../components/home/ProcessTimeline';
import SectionHeading from '../components/ui/SectionHeading';
import CTABand from '../components/ui/CTABand';
import {
  services,
  testimonials,
  blogPosts,
} from '../data/content';
import { usePortfolio } from '../hooks/usePortfolio';
import './Home.css';

const serviceIcons = [
  HiOutlineColorSwatch,
  HiOutlineCode,
  HiOutlineDatabase,
  HiOutlineLightningBolt,
  HiOutlineTrendingUp,
];

function Home() {
  const { projects, loading: projectsLoading } = usePortfolio();

  return (
    <div className="home">
      {/* Hero Banner with 3D Animation */}
      <HeroBanner />

      {/* Company Introduction */}
      <CompanyIntro />

      {/* Services Overview */}
      <section className="section home-services">
        <div className="container">
          <SectionHeading
            eyebrow="Services Overview"
            title="Everything your brand needs online"
            description="UI/UX Design · Web Development · CMS · Front-End · Digital Marketing"
            align="center"
          />
          <div className="services-bento">
            {services.map((s, i) => {
              const Icon = serviceIcons[i] || HiOutlineCode;
              return (
                <Link
                  key={s.id}
                  to={`/services#${s.id}`}
                  className={`service-tile card-glass gsap-reveal ${i === 0 ? 'span-2' : ''}`}
                  data-aos="fade-up"
                  data-aos-delay={i * 60}
                >
                  <Icon className="service-tile-icon" />
                  <h3>{s.title}</h3>
                  <p>{s.description}</p>
                  <span className="service-tile-link">
                    Explore service <HiOutlineArrowRight />
                  </span>
                </Link>
              );
            })}
          </div>
        </div>
      </section>

      {/* Why Choose CraftoWeb */}
      <WhyChoose />

      {/* Featured Projects */}
      <section className="section home-work">
        <div className="container">
          <div className="work-header">
            <SectionHeading
              eyebrow="Featured Projects"
              title="Work we're proud of"
              description="Case studies across SaaS, e-commerce, fintech, and education."
            />
            <Link to="/work" className="btn btn-ghost">
              View portfolio <HiOutlineArrowRight />
            </Link>
          </div>
          <div className="work-showcase">
            {projectsLoading ? (
              <p className="work-loading">Loading featured projects...</p>
            ) : projects.length > 0 ? (
              projects.slice(0, 4).map((p, i) => (
                <Link
                  key={p.id}
                  to="/work"
                  className={`work-card card-glass gsap-reveal ${i === 0 ? 'work-featured' : ''}`}
                  data-aos="fade-up"
                  data-aos-delay={i * 80}
                >
                  <div className="work-card-img">
                    <img src={p.image} alt={p.title} loading="lazy" />
                    <span className="work-cat">{p.category}</span>
                  </div>
                  <div className="work-card-body">
                    <h3>{p.title}</h3>
                    <p>{p.subtitle}</p>
                  </div>
                </Link>
              ))
            ) : (
              <p className="work-loading">Projects will appear here once added in the dashboard.</p>
            )}
          </div>
        </div>
      </section>

      {/* Client Testimonials */}
      <section className="section home-testimonials">
        <div className="container">
          <SectionHeading
            eyebrow="Client Testimonials"
            title="Trusted by leaders worldwide"
            align="center"
          />
          <div className="testimonial-grid">
            {testimonials.map((t, i) => (
              <article key={t.id} className="testimonial-card card-glass gsap-reveal" data-aos="fade-up" data-aos-delay={i * 70}>
                <div className="testimonial-stars">
                  {[...Array(t.rating)].map((_, j) => (
                    <HiOutlineStar key={j} />
                  ))}
                </div>
                <p>&ldquo;{t.text}&rdquo;</p>
                <footer>
                  <img src={t.avatar} alt={t.name} loading="lazy" />
                  <div>
                    <strong>{t.name}</strong>
                    <span>{t.role}</span>
                  </div>
                </footer>
              </article>
            ))}
          </div>
          <div className="testimonial-cta" data-aos="fade-up">
            <Link to="/reviews" className="btn btn-outline">
              All reviews <HiOutlineArrowRight />
            </Link>
          </div>
        </div>
      </section>

      {/* Process Timeline — GSAP */}
      <ProcessTimeline />

      {/* Blog Highlights */}
      <section className="section home-blog">
        <div className="container">
          <div className="work-header">
            <SectionHeading eyebrow="Blog Highlights" title="Latest insights" />
            <Link to="/blog" className="btn btn-ghost">
              View all articles <HiOutlineArrowRight />
            </Link>
          </div>
          <div className="blog-grid">
            {blogPosts.slice(0, 3).map((post, i) => (
              <Link
                key={post.id}
                to={`/blog/${post.slug}`}
                className="blog-card card-glass gsap-reveal"
                data-aos="fade-up"
                data-aos-delay={i * 80}
              >
                <div className="blog-card-img">
                  <img src={post.image} alt={post.title} loading="lazy" />
                </div>
                <div className="blog-card-body">
                  <span>{post.category} · {post.readTime}</span>
                  <h3>{post.title}</h3>
                  <p>{post.excerpt}</p>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Contact CTA Section */}
      <CTABand
        title="Have a project in mind?"
        subtitle="Let's build something extraordinary together. Get in touch for a free consultation."
        buttonText="Contact Us"
        buttonTo="/contact"
      />
    </div>
  );
}

export default Home;
