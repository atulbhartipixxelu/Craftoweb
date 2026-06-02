import { Component } from 'react';

class ErrorBoundary extends Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError() {
    return { hasError: true };
  }

  componentDidCatch(error, info) {
    console.error('CraftoWeb render error:', error, info);
  }

  render() {
    if (this.state.hasError) {
      return (
        <div
          style={{
            minHeight: '100vh',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'center',
            justifyContent: 'center',
            padding: 24,
            background: '#030712',
            color: '#f8fafc',
            textAlign: 'center',
            fontFamily: 'system-ui, sans-serif',
          }}
        >
          <h1 style={{ marginBottom: 12 }}>Something went wrong</h1>
          <p style={{ color: '#94a3b8', maxWidth: 420, marginBottom: 24 }}>
            Please refresh the page. If the problem continues, clear your browser cache.
          </p>
          <button
            type="button"
            onClick={() => window.location.reload()}
            style={{
              padding: '12px 24px',
              borderRadius: 9999,
              border: 'none',
              background: 'linear-gradient(135deg, #22d3ee, #818cf8)',
              color: '#030712',
              fontWeight: 700,
              cursor: 'pointer',
            }}
          >
            Reload
          </button>
        </div>
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary;
