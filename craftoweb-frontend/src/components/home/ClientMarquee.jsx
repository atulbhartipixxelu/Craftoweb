import { clientLogos } from '../../data/content';
import './ClientMarquee.css';

function ClientMarquee() {
  const items = [...clientLogos, ...clientLogos];

  return (
    <section className="client-marquee">
      <p className="marquee-label">Trusted by Industry Leaders & Fast-Growing Startups</p>
      <div className="marquee-track-wrap">
        <div className="marquee-track">
          {items.map((name, i) => (
            <span key={`${name}-${i}`} className="marquee-item">
              {name}
            </span>
          ))}
        </div>
      </div>
    </section>
  );
}

export default ClientMarquee;
