import { Link } from 'react-router-dom';
import { HiOutlineArrowRight } from 'react-icons/hi';
import './CTABand.css';

function CTABand({
  title = 'Ready to empower your business with better IT?',
  subtitle = "Let's map the roadmap and ship something your users will remember.",
  buttonText = "Let's talk",
  buttonTo = '/contact',
}) {
  return (
    <section className="cta-band">
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
