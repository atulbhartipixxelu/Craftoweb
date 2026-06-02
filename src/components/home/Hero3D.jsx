import { useRef, useMemo } from 'react';
import { Canvas, useFrame } from '@react-three/fiber';
import { Float, MeshDistortMaterial, Sphere } from '@react-three/drei';
import './Hero3D.css';

function FloatingShapes() {
  const group = useRef();

  useFrame((state) => {
    if (group.current) {
      group.current.rotation.y = state.clock.elapsedTime * 0.15;
      group.current.rotation.x = Math.sin(state.clock.elapsedTime * 0.2) * 0.1;
    }
  });

  return (
    <group ref={group}>
      <Float speed={2} rotationIntensity={0.5} floatIntensity={1}>
        <Sphere args={[1.2, 64, 64]} position={[0, 0, 0]}>
          <MeshDistortMaterial
            color="#22d3ee"
            attach="material"
            distort={0.4}
            speed={2}
            roughness={0.2}
            metalness={0.8}
          />
        </Sphere>
      </Float>
      <Float speed={3} rotationIntensity={1} floatIntensity={1.5}>
        <mesh position={[2.2, 0.8, -1]}>
          <torusGeometry args={[0.5, 0.15, 16, 48]} />
          <meshStandardMaterial color="#a78bfa" metalness={0.6} roughness={0.3} />
        </mesh>
      </Float>
      <Float speed={2.5} rotationIntensity={0.8} floatIntensity={1.2}>
        <mesh position={[-2, -0.5, 0.5]}>
          <octahedronGeometry args={[0.6]} />
          <meshStandardMaterial color="#818cf8" metalness={0.7} roughness={0.2} />
        </mesh>
      </Float>
    </group>
  );
}

function Hero3D() {
  const dpr = useMemo(() => Math.min(window.devicePixelRatio, 2), []);

  return (
    <div className="hero-3d">
      <Canvas
        camera={{ position: [0, 0, 5], fov: 45 }}
        dpr={dpr}
        gl={{ antialias: true, alpha: true }}
      >
        <ambientLight intensity={0.5} />
        <directionalLight position={[10, 10, 5]} intensity={1.2} />
        <pointLight position={[-10, -10, -5]} intensity={0.5} color="#a78bfa" />
        <FloatingShapes />
      </Canvas>
    </div>
  );
}

export default Hero3D;
