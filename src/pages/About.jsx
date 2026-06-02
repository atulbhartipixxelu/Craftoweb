import { Link } from 'react-router-dom';
import { HiOutlineArrowRight } from 'react-icons/hi';
import SectionHeading from '../components/ui/SectionHeading';
import CTABand from '../components/ui/CTABand';
import AnimatedCounter from '../components/common/AnimatedCounter';
import AnimatedTimeline from '../components/about/AnimatedTimeline';
import LottiePlayer from '../components/common/LottiePlayer';
import { team, values, achievements, lottieSources } from '../data/content';
import './About.css';

function About() {
  return (
    <div className="page-shell">
      <header className="page-hero" data-aos="fade-down">
        <div className="container">
          <span className="eyebrow">About Us</span>
          <h1 className="display-lg">
            Our story, mission & <span className="text-gradient">vision</span>
          </h1>
          <p>Building digital excellence with passion, innovation, and integrity since 2017.</p>
        </div>
      </header>

      <section className="section">
        <div className="container about-story-grid">
          <div className="gsap-reveal-left about-story-text">
            <SectionHeading
              eyebrow="Company Story"
              title="From a small team to a trusted agency"
              description="CraftoWeb began with a vision to help businesses thrive online. Today we deliver full-stack digital solutions for clients worldwide."
            />
            <p>
              We specialize in React, Next.js, WordPress, Shopify, and comprehensive digital marketing — combining stunning design with robust technology on every project.
            </p>
          </div>
          <div className="gsap-reveal-right about-mv card-glass">
            <div className="about-lottie">
              <LottiePlayer src={lottieSources.design} />
            </div>
            <div>
              <h3>Our Mission</h3>
              <p>To empower businesses with innovative digital solutions that drive growth and lasting impact.</p>
            </div>
            <div>
              <h3>Our Vision</h3>
              <p>To be the most trusted IT agency for brands seeking premium web experiences worldwide.</p>
            </div>
          </div>
        </div>
      </section>

      <section className="section section-tight">
        <div className="container">
          <SectionHeading eyebrow="Achievements" title="Numbers that matter" align="center" />
          <div className="about-achievements">
            {achievements.map((a) => (
              <div key={a.label} className="achievement-card card-glass gsap-reveal" data-aos="zoom-in">
                <strong>
                  <AnimatedCounter end={a.value} suffix={a.suffix} />
                </strong>
                <span>{a.label}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="section section-tight">
        <div className="container">
          <SectionHeading eyebrow="Company Values" title="What we stand for" align="center" />
          <div className="values-grid">
            {values.map((v, i) => (
              <div key={v.title} className="value-card card-glass gsap-reveal" data-aos="fade-up" data-aos-delay={i * 60}>
                <span className="value-num">0{i + 1}</span>
                <h3>{v.title}</h3>
                <p>{v.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <SectionHeading eyebrow="Working Process" title="Animated timeline" align="center" />
          <AnimatedTimeline />
        </div>
      </section>

      <section className="section section-tight">
        <div className="container">
          <SectionHeading eyebrow="Team Introduction" title="Meet our experts" align="center" />
          <div className="team-grid">
            {team.map((member, i) => (
              <article key={member.name} className="team-card card-glass gsap-reveal" data-aos="fade-up" data-aos-delay={i * 70}>
                <img src={member.image} alt={member.name} loading="lazy" />
                <h3>{member.name}</h3>
                <p>{member.role}</p>
              </article>
            ))}
          </div>
          <div className="about-cta-center">
            <Link to="/contact" className="btn btn-primary">
              Join our journey <HiOutlineArrowRight />
            </Link>
          </div>
        </div>
      </section>

      <CTABand />
    </div>
  );
}

export default About;
