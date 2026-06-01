import Header from '../components/layout/Header';

function PlaceholderPage({ title, description }) {
  return (
    <>
      <Header title={title} />
      <div className="page-placeholder">
        <h2>{title}</h2>
        <p>{description}</p>
        <button className="btn-primary" style={{ marginTop: 8 }}>
          Coming Soon
        </button>
      </div>
    </>
  );
}

export default PlaceholderPage;
