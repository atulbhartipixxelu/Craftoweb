import './FigmaFrame.css';

function FigmaFrame({ label, children }) {
  return (
    <div className="figma-frame">
      {label ? <span className="figma-frame__label">{label}</span> : null}
      <span className="figma-frame__handle tl" />
      <span className="figma-frame__handle tc" />
      <span className="figma-frame__handle tr" />
      <span className="figma-frame__handle ml" />
      <span className="figma-frame__handle mr" />
      <span className="figma-frame__handle bl" />
      <span className="figma-frame__handle bc" />
      <span className="figma-frame__handle br" />
      <div className="figma-frame__body">{children}</div>
    </div>
  );
}

export default FigmaFrame;
