import { Link } from 'react-router-dom';
import { HiOutlineArrowRight } from 'react-icons/hi';
import './CTABand.css';

function CTABand({
  title = 'Ready to build something extraordinary?',
  subtitle = "Let's discuss your project and create a digital experience your users will love.",
  buttonText = 'Start a Project',
  buttonTo = '/contact',
}) {
  return (
    <section className="cta-band">
      <div className="cta-band-glow" aria-hidden="true" />
      <div className="container cta-band-inner">
        <div>
          <h2 className="display-lg">{title}</h2>
          <p>{subtitle}</p>
        </div>
        <Link to={buttonTo} className="btn btn-primary">
          {buttonText} <HiOutlineArrowRight />
        </Link>
      </div>
    </section>
  );
}

export default CTABand;
