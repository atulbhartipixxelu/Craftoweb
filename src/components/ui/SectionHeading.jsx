import './SectionHeading.css';

function SectionHeading({ eyebrow, title, description, align = 'left', className = '' }) {
  return (
    <header className={`section-heading ${align} ${className}`}>
      {eyebrow && <span className="eyebrow">{eyebrow}</span>}
      {title && <h2 className="display-lg section-heading-title">{title}</h2>}
      {description && <p className="section-heading-desc">{description}</p>}
    </header>
  );
}

export default SectionHeading;
