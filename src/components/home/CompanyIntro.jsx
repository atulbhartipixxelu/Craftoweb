import { Link } from 'react-router-dom';
import { achievements } from '../../data/content';
import AnimatedCounter from '../common/AnimatedCounter';
import './CompanyIntro.css';

function CompanyIntro() {
  return (
    <section className="section company-intro">
      <div className="container company-intro-grid">
        <div className="company-intro-text gsap-reveal-left" data-aos="fade-right">
          <span className="eyebrow">About the studio</span>
          <h2 className="display-lg">
            A digital studio that still believes in <em>craft</em>.
          </h2>
          <p>
            CraftoWeb builds websites and products with intention — sharp design, solid engineering, and a clear point of view. No template feel. No noisy trends.
          </p>
          <p>
            Since 2017 we have partnered with startups and growing brands across React, Next.js, WordPress, Shopify, and digital growth.
          </p>
          <Link to="/about" className="btn btn-primary">
            Our story
          </Link>
        </div>

        <div className="company-intro-stats gsap-reveal-right" data-aos="fade-left">
          {achievements.map((a) => (
            <div key={a.label} className="company-stat">
              <strong>
                <AnimatedCounter end={a.value} suffix={a.suffix} />
              </strong>
              <span>{a.label}</span>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

export default CompanyIntro;
