import { useMemo, useState } from 'react';
import { Link } from 'react-router-dom';
import { HiOutlineArrowRight, HiOutlineSearch } from 'react-icons/hi';
import { blogPosts, blogCategories } from '../data/content';
import './Blog.css';

const PER_PAGE = 6;

function Blog() {
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('All');
  const [page, setPage] = useState(1);

  const featured = blogPosts.find((p) => p.featured) || blogPosts[0];

  const filtered = useMemo(() => {
    return blogPosts.filter((post) => {
      const matchCat = category === 'All' || post.category === category;
      const q = search.toLowerCase().trim();
      const matchSearch =
        !q ||
        post.title.toLowerCase().includes(q) ||
        post.excerpt.toLowerCase().includes(q) ||
        post.category.toLowerCase().includes(q);
      return matchCat && matchSearch;
    });
  }, [search, category]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
  const currentPage = Math.min(page, totalPages);
  const paginated = filtered.slice((currentPage - 1) * PER_PAGE, currentPage * PER_PAGE);

  const handleCategory = (cat) => {
    setCategory(cat);
    setPage(1);
  };

  return (
    <div className="page-shell">
      <header className="page-hero" data-aos="fade-down">
        <div className="container">
          <span className="eyebrow">Blog</span>
          <h1 className="display-lg">
            Latest <span className="text-gradient">articles</span> & insights
          </h1>
          <p>Tips on design, development, SEO, and growing your business online.</p>
        </div>
      </header>

      <section className="section">
        <div className="container">
          <div className="blog-toolbar card-glass" data-aos="fade-up">
            <div className="blog-search">
              <HiOutlineSearch />
              <input
                type="search"
                placeholder="Search articles..."
                value={search}
                onChange={(e) => {
                  setSearch(e.target.value);
                  setPage(1);
                }}
              />
            </div>
            <div className="blog-categories">
              {blogCategories.map((cat) => (
                <button
                  key={cat}
                  type="button"
                  className={category === cat ? 'active' : ''}
                  onClick={() => handleCategory(cat)}
                >
                  {cat}
                </button>
              ))}
            </div>
          </div>

          {featured && category === 'All' && !search && currentPage === 1 && (
            <Link to={`/blog/${featured.slug}`} className="blog-featured card-glass gsap-reveal" data-aos="fade-up">
              <div className="blog-featured-img">
                <img src={featured.image} alt={featured.title} loading="lazy" />
                <span className="featured-badge">Featured</span>
              </div>
              <div className="blog-featured-body">
                <span>{featured.category} · {featured.date}</span>
                <h2>{featured.title}</h2>
                <p>{featured.excerpt}</p>
                <span className="read-more">
                  Read article <HiOutlineArrowRight />
                </span>
              </div>
            </Link>
          )}

          <div className="blog-page-grid">
            {paginated
              .filter((p) => !(category === 'All' && !search && currentPage === 1 && p.id === featured?.id))
              .map((post, i) => (
                <Link
                  key={post.id}
                  to={`/blog/${post.slug}`}
                  className="blog-page-card card-glass gsap-reveal"
                  data-aos="fade-up"
                  data-aos-delay={i * 60}
                >
                  <img src={post.image} alt={post.title} loading="lazy" />
                  <div>
                    <span>{post.category}</span>
                    <h3>{post.title}</h3>
                    <p>{post.excerpt}</p>
                  </div>
                </Link>
              ))}
          </div>

          {filtered.length === 0 && (
            <p className="blog-empty">No articles found. Try a different search or category.</p>
          )}

          {totalPages > 1 && (
            <div className="blog-pagination" data-aos="fade-up">
              <button type="button" disabled={currentPage <= 1} onClick={() => setPage((p) => p - 1)}>
                Previous
              </button>
              {Array.from({ length: totalPages }, (_, i) => i + 1).map((n) => (
                <button
                  key={n}
                  type="button"
                  className={currentPage === n ? 'active' : ''}
                  onClick={() => setPage(n)}
                >
                  {n}
                </button>
              ))}
              <button type="button" disabled={currentPage >= totalPages} onClick={() => setPage((p) => p + 1)}>
                Next
              </button>
            </div>
          )}
        </div>
      </section>
    </div>
  );
}

export default Blog;
