import {
  HiOutlineColorSwatch,
  HiOutlineCode,
  HiOutlineClipboardList,
  HiOutlineChartBar,
  HiOutlineSupport,
  HiOutlineGlobe,
} from 'react-icons/hi';
import SectionHeading from '../ui/SectionHeading';
import { whyChoose } from '../../data/content';
import './WhyChoose.css';

const iconMap = {
  design: HiOutlineColorSwatch,
  code: HiOutlineCode,
  process: HiOutlineClipboardList,
  growth: HiOutlineChartBar,
  support: HiOutlineSupport,
  global: HiOutlineGlobe,
};

function WhyChoose() {
  return (
    <section className="section why-choose">
      <div className="container">
        <SectionHeading
          eyebrow="Why Choose CraftoWeb"
          title="What sets us apart"
          description="We don't just build websites — we build growth engines with design, technology, and strategy aligned to your goals."
          align="center"
        />
        <div className="why-grid">
          {whyChoose.map((item, i) => {
            const Icon = iconMap[item.icon] || HiOutlineCode;
            return (
              <article key={item.title} className="why-card card-glass gsap-reveal" data-aos="fade-up" data-aos-delay={i * 60}>
                <div className="why-icon">
                  <Icon />
                </div>
                <h3>{item.title}</h3>
                <p>{item.desc}</p>
              </article>
            );
          })}
        </div>
      </div>
    </section>
  );
}

export default WhyChoose;
