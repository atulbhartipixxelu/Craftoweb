import { Link, useParams } from 'react-router-dom';
import { HiOutlineArrowLeft } from 'react-icons/hi';
import CTABand from '../components/ui/CTABand';
import { blogPosts } from '../data/content';
import './BlogDetail.css';

function BlogDetail() {
  const { slug } = useParams();
  const post = blogPosts.find((p) => p.slug === slug);

  if (!post) {
    return (
      <div className="page-shell">
        <div className="page-hero container">
          <h1 className="display-lg">Article not found</h1>
          <Link to="/blog" className="btn btn-primary">
            Back to blog
          </Link>
        </div>
      </div>
    );
  }

  const related = blogPosts.filter((p) => p.slug !== slug);

  return (
    <article className="page-shell blog-detail">
      <header className="blog-detail-header" data-aos="fade-down">
        <div className="container blog-detail-header-inner">
          <Link to="/blog" className="back-link">
            <HiOutlineArrowLeft /> All articles
          </Link>
          <span className="eyebrow">{post.category}</span>
          <h1 className="display-lg">{post.title}</h1>
          <p className="blog-detail-meta">
            {post.date} · {post.readTime} · {post.author}
          </p>
        </div>
        <div className="blog-detail-cover">
          <img src={post.image} alt={post.title} />
        </div>
      </header>

      <div className="container blog-detail-body" data-aos="fade-up">
        <p className="lead">{post.excerpt}</p>
        <p>
          At CraftoWeb, we share practical insights to help you build better digital products. Whether you&apos;re launching a startup or scaling an enterprise platform, the right strategy makes all the difference.
        </p>
        <p>
          From choosing the right tech stack to optimizing conversion rates, our team has seen what works across dozens of industries. Here&apos;s what we recommend based on real project experience.
        </p>
        <h2>Key takeaways</h2>
        <ul>
          <li>Prioritize user experience and page performance from day one</li>
          <li>Choose technologies that scale with your business goals</li>
          <li>Invest in SEO and quality content early</li>
          <li>Partner with a team that communicates clearly and delivers on time</li>
        </ul>
      </div>

      {related.length > 0 && (
        <section className="section section-tight">
          <div className="container">
            <h2 className="display-lg" style={{ marginBottom: 32, fontSize: '1.75rem' }}>
              More to read
            </h2>
            <div className="blog-detail-related">
              {related.map((p) => (
                <Link key={p.id} to={`/blog/${p.slug}`} className="related-card card-glass">
                  <img src={p.image} alt={p.title} loading="lazy" />
                  <div>
                    <span>{p.category}</span>
                    <h3>{p.title}</h3>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}

      <CTABand />
    </article>
  );
}

export default BlogDetail;
