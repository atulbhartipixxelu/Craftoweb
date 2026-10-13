import SectionHeading from '../ui/SectionHeading';
import { whyChoose } from '../../data/content';
import './WhyChoose.css';

function WhyChoose() {
  return (
    <section className="section why-choose">
      <div className="container">
        <SectionHeading
          eyebrow="Why CraftoWeb"
          title="Quiet confidence. Loud results."
          description="We ship work that feels considered — from first sketch to final deploy."
          align="center"
        />
        <div className="why-grid">
          {whyChoose.map((item, i) => (
            <article
              key={item.title}
              className="why-card gsap-reveal"
              data-aos="fade-up"
              data-aos-delay={i * 50}
            >
              <span className="why-index">0{i + 1}</span>
              <h3>{item.title}</h3>
              <p>{item.desc}</p>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}

export default WhyChoose;
