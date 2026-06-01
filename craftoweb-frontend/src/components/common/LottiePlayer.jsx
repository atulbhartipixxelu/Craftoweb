import { useEffect, useState } from 'react';
import Lottie from 'lottie-react';
import './LottiePlayer.css';

function LottiePlayer({ src, className = '', loop = true, autoplay = true }) {
  const [data, setData] = useState(null);

  useEffect(() => {
    let cancelled = false;
    if (!src) return undefined;

    fetch(src)
      .then((res) => res.json())
      .then((json) => {
        if (!cancelled) setData(json);
      })
      .catch(() => {
        if (!cancelled) setData(null);
      });

    return () => {
      cancelled = true;
    };
  }, [src]);

  if (!data) {
    return <div className={`lottie-placeholder ${className}`} aria-hidden="true" />;
  }

  return (
    <Lottie
      animationData={data}
      loop={loop}
      autoplay={autoplay}
      className={`lottie-player ${className}`}
    />
  );
}

export default LottiePlayer;
