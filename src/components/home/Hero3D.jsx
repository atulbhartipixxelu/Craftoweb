import { useRef, useMemo, useEffect, useState } from 'react';
import { Canvas, useFrame } from '@react-three/fiber';
import * as THREE from 'three';
import './Hero3D.css';

function CraftScene({ mouse }) {
  const group = useRef();
  const ring = useRef();
  const knot = useRef();
  const orbit = useRef();

  const boxes = useMemo(() => {
    return Array.from({ length: 8 }, (_, i) => {
      const angle = (i / 8) * Math.PI * 2;
      return {
        position: [Math.cos(angle) * 2.4, Math.sin(angle * 0.6) * 0.6, Math.sin(angle) * 2.4],
        scale: 0.18 + (i % 3) * 0.06,
        speed: 0.4 + (i % 4) * 0.15,
      };
    });
  }, []);

  useFrame((state) => {
    const t = state.clock.elapsedTime;

    if (group.current) {
      group.current.rotation.y = t * 0.12 + mouse.current.x * 0.35;
      group.current.rotation.x = Math.sin(t * 0.2) * 0.08 + mouse.current.y * 0.2;
    }

    if (ring.current) {
      ring.current.rotation.z = t * 0.35;
      ring.current.rotation.x = Math.PI / 2.4 + Math.sin(t * 0.4) * 0.1;
    }

    if (knot.current) {
      knot.current.rotation.x = t * 0.25;
      knot.current.rotation.y = t * 0.18;
    }

    if (orbit.current) {
      orbit.current.rotation.y = -t * 0.22;
    }
  });

  return (
    <group ref={group}>
      <mesh ref={knot}>
        <torusKnotGeometry args={[0.85, 0.22, 180, 24]} />
        <meshStandardMaterial
            color="#b8ff3c"
            metalness={0.85}
            roughness={0.28}
            envMapIntensity={1}
        />
      </mesh>

      <mesh ref={ring} scale={1.55}>
        <torusGeometry args={[1.35, 0.015, 16, 100]} />
        <meshBasicMaterial color="#e8ff9a" transparent opacity={0.55} />
      </mesh>

      <mesh scale={1.9} rotation={[Math.PI / 2, 0, 0]}>
        <torusGeometry args={[1.35, 0.008, 12, 80]} />
        <meshBasicMaterial color="#6b6558" transparent opacity={0.35} />
      </mesh>

      <group ref={orbit}>
        {boxes.map((box, i) => (
          <mesh key={i} position={box.position} scale={box.scale}>
            <boxGeometry args={[1, 1, 1]} />
            <meshStandardMaterial
              color={i % 2 === 0 ? '#d4ff8a' : '#6b778a'}
              metalness={0.4}
              roughness={0.45}
              wireframe={i % 3 === 0}
            />
          </mesh>
        ))}
      </group>

      <mesh position={[0, -1.8, 0]} rotation={[-Math.PI / 2, 0, 0]}>
        <circleGeometry args={[3.2, 64]} />
        <meshBasicMaterial color="#000000" transparent opacity={0.35} />
      </mesh>
    </group>
  );
}

function Hero3D() {
  const mouse = useRef({ x: 0, y: 0 });
  const [ready, setReady] = useState(false);
  const dpr = useMemo(() => Math.min(typeof window !== 'undefined' ? window.devicePixelRatio : 1, 1.75), []);

  useEffect(() => {
    setReady(true);
    const onMove = (e) => {
      mouse.current.x = (e.clientX / window.innerWidth - 0.5) * 2;
      mouse.current.y = (e.clientY / window.innerHeight - 0.5) * 2;
    };
    window.addEventListener('pointermove', onMove, { passive: true });
    return () => window.removeEventListener('pointermove', onMove);
  }, []);

  if (!ready) return <div className="hero-3d hero-3d-fallback" aria-hidden="true" />;

  return (
    <div className="hero-3d">
      <Canvas
        camera={{ position: [0, 0.4, 5.2], fov: 42 }}
        dpr={dpr}
        gl={{ antialias: true, alpha: true, powerPreference: 'high-performance' }}
        onCreated={({ gl }) => {
          gl.setClearColor(new THREE.Color('#000000'), 0);
        }}
      >
        <ambientLight intensity={0.35} />
        <directionalLight position={[4, 6, 3]} intensity={1.4} color="#fff4e0" />
        <directionalLight position={[-4, -2, -3]} intensity={0.4} color="#7a9ab0" />
        <pointLight position={[0, 2, 2]} intensity={0.8} color="#b8ff3c" />
        <CraftScene mouse={mouse} />
      </Canvas>
    </div>
  );
}

export default Hero3D;
