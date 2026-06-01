import { Link } from 'react-router-dom';
import { HiOutlineArrowRight } from 'react-icons/hi';
import LottiePlayer from '../common/LottiePlayer';
import SectionHeading from '../ui/SectionHeading';
import { lottieSources } from '../../data/content';
import './CompanyIntro.css';

function CompanyIntro() {
  return (
    <section className="section company-intro">
      <div className="container company-intro-grid">
        <div className="gsap-reveal-left company-intro-lottie card-glass">
          <LottiePlayer src={lottieSources.hero} />
        </div>
        <div className="gsap-reveal-right company-intro-text">
          <SectionHeading
            eyebrow="Company Introduction"
            title="CraftoWeb — your partner in digital excellence"
            description="We are a full-service IT agency specializing in UI/UX design, web development, CMS platforms, and digital marketing. Since 2017, we've helped businesses worldwide build websites and applications that users love and businesses trust."
          />
          <p>
            From startups launching their first MVP to enterprises scaling global platforms, our team combines creative design with robust engineering using React, Next.js, WordPress, and Shopify.
          </p>
          <Link to="/about" className="btn btn-primary">
            Learn About Us <HiOutlineArrowRight />
          </Link>
        </div>
      </div>
    </section>
  );
}

export default CompanyIntro;
