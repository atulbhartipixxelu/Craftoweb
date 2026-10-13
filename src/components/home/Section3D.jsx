import { useMemo, useRef } from 'react';
import { Canvas, useFrame } from '@react-three/fiber';
import './Section3D.css';

function OrbitingShapes({ color = '#b8ff3c' }) {
  const group = useRef();

  useFrame((state) => {
    const t = state.clock.elapsedTime;
    if (!group.current) return;
    group.current.rotation.y = t * 0.28;
    group.current.rotation.x = Math.sin(t * 0.35) * 0.25;
  });

  return (
    <group ref={group}>
      <mesh>
        <icosahedronGeometry args={[1.1, 0]} />
        <meshStandardMaterial color={color} metalness={0.7} roughness={0.25} wireframe />
      </mesh>
      <mesh position={[1.8, 0.4, -0.4]} scale={0.35}>
        <boxGeometry />
        <meshStandardMaterial color="#ffffff" metalness={0.5} roughness={0.4} />
      </mesh>
      <mesh position={[-1.6, -0.5, 0.6]} scale={0.45}>
        <octahedronGeometry args={[1]} />
        <meshStandardMaterial color={color} metalness={0.8} roughness={0.2} />
      </mesh>
      <mesh rotation={[Math.PI / 2, 0, 0]} scale={1.6}>
        <torusGeometry args={[1, 0.02, 12, 80]} />
        <meshBasicMaterial color={color} transparent opacity={0.5} />
      </mesh>
    </group>
  );
}

function Section3D({ className = '', color }) {
  const dpr = useMemo(() => Math.min(typeof window !== 'undefined' ? window.devicePixelRatio : 1, 1.5), []);

  return (
    <div className={`section-3d ${className}`} aria-hidden="true">
      <Canvas camera={{ position: [0, 0, 4.2], fov: 42 }} dpr={dpr} gl={{ antialias: true, alpha: true }}>
        <ambientLight intensity={0.4} />
        <directionalLight position={[3, 4, 2]} intensity={1.2} />
        <OrbitingShapes color={color} />
      </Canvas>
    </div>
  );
}

export default Section3D;
