import { useEffect } from 'react';
import AOS from 'aos';

export function useAos() {
  useEffect(() => {
    AOS.init({
      duration: 800,
      easing: 'ease-out-cubic',
      once: true,
      offset: 80,
    });
  }, []);
}
