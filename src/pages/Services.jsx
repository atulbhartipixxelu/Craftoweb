import { Link } from 'react-router-dom';
import { HiOutlineCheck, HiOutlineArrowRight } from 'react-icons/hi';
import SectionHeading from '../components/ui/SectionHeading';
import CTABand from '../components/ui/CTABand';
import LottiePlayer from '../components/common/LottiePlayer';
import { services, serviceCategories, lottieSources } from '../data/content';
import './Services.css';

const serviceLotties = [
  lottieSources.design,
  lottieSources.code,
  lottieSources.design,
  lottieSources.code,
  lottieSources.growth,
];

function Services() {
  return (
    <div className="page-shell">
      <header className="page-hero" data-aos="fade-down">
        <div className="container">
          <span className="eyebrow">Services</span>
          <h1 className="display-lg">
            Full-service <span className="text-gradient">digital solutions</span>
          </h1>
          <p>UI/UX Design · Web Development · CMS · Front-End · Digital Marketing</p>
        </div>
      </header>

      <section className="section">
        <div className="container services-list">
          {services.map((s, i) => (
            <article
              key={s.id}
              id={s.id}
              className="service-block card-glass gsap-reveal"
              data-aos="fade-up"
            >
              <div className="service-block-grid">
                <div className="service-lottie-wrap card-glass">
                  <LottiePlayer src={serviceLotties[i]} />
                </div>
                <div className="service-block-content">
                  <span className="service-index">0{i + 1}</span>
                  <h2>{s.title}</h2>
                  <p>{s.description}</p>
                  <ul>
                    {s.items.map((item) => (
                      <li key={item}>
                        <HiOutlineCheck /> {item}
                      </li>
                    ))}
                  </ul>
                  <Link to="/contact" className="btn btn-primary">
                    Get a quote <HiOutlineArrowRight />
                  </Link>
                </div>
              </div>
            </article>
          ))}
        </div>
      </section>

      <section className="section section-tight">
        <div className="container">
          <SectionHeading eyebrow="Interactive Showcase" title="Built for your stage" align="center" />
          <div className="service-cats">
            {serviceCategories.map((cat, i) => (
              <Link
                key={cat.title}
                to={cat.path}
                className="service-cat card-glass gsap-reveal"
                data-aos="fade-up"
                data-aos-delay={i * 50}
              >
                <small>{cat.tag}</small>
                <h3>{cat.title}</h3>
                <p>{cat.desc}</p>
              </Link>
            ))}
          </div>
        </div>
      </section>

      <CTABand title="Need a custom package?" subtitle="We'll tailor services to your goals and budget." />
    </div>
  );
}

export default Services;
