/**
 * Vite source scaffold — full app migration is in progress.
 * Production still ships from frontend/legacy/assets/ (legacy build mode).
 */
export default function App() {
  return (
    <main className="himcab-scaffold">
      <h1>HimCab frontend (Vite source)</h1>
      <p>
        Abhi production <code>legacy</code> mode se build hoti hai. Landing page
        edit karne ke liye pehle <code>frontend/legacy/assets/</code> use karo.
      </p>
      <p>
        Jab poora app yahan migrate ho jaye, root se chalao:{' '}
        <code>MYCAB_BUILD=vite npm run build:hostinger-mycab</code>
      </p>
    </main>
  );
}
