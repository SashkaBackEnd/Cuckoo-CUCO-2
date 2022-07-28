
import { useEffect, useState } from 'react';

export const useWindowSize = () => {
  const [windowSize, setWindowSize] = useState({
    width: 1600,
    height: 1000,
  });

  // const isMobile = windowSize.width <= 711; // this 660px
  const isMobile = windowSize.width <= 1280;
  const isMobileSE = windowSize.width <= 400;

  useEffect(() => {
    function handleResize() {
      setWindowSize({
        width: window.innerWidth,
        height: window.innerHeight,
      });
    }

    window.addEventListener('resize', handleResize);

    handleResize();

    return () => window.removeEventListener('resize', handleResize);
  }, []);

  return {
    size: windowSize,
    isMobile,
    isMobileSE,
  };
};
