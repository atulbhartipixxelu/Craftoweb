import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation, Pagination, Autoplay, EffectCoverflow } from 'swiper/modules';
import { motion } from 'framer-motion';
import { HiOutlineStar } from 'react-icons/hi';
import SectionHeading from '../components/ui/SectionHeading';
import CTABand from '../components/ui/CTABand';
import AnimatedCounter from '../components/common/AnimatedCounter';
import { testimonials } from '../data/content';
import './Reviews.css';

function Reviews() {
  return (
    <div className="page-shell">
      <header className="page-hero" data-aos="fade-down">
        <div className="container">
          <span className="eyebrow">Reviews</span>
          <h1 className="display-lg">
            Client <span className="text-gradient">testimonials</span>
          </h1>
          <p>Real feedback from businesses we&apos;ve helped succeed.</p>
        </div>
      </header>

      <section className="section section-tight">
        <div className="container reviews-summary card-glass" data-aos="zoom-in">
          <div className="reviews-score">
            <span className="score-num">5.0</span>
            <div className="score-stars">
              {[1, 2, 3, 4, 5].map((i) => (
                <HiOutlineStar key={i} />
              ))}
            </div>
            <p>
              <AnimatedCounter end={testimonials.length} suffix="+" /> verified reviews · Rating Showcase
            </p>
          </div>
          <div className="rating-bars">
            {[5, 4, 3, 2, 1].map((star) => (
              <div key={star} className="rating-bar-row">
                <span>{star} ★</span>
                <div className="bar-track">
                  <motion.div
                    className="bar-fill"
                    initial={{ width: 0 }}
                    whileInView={{ width: star === 5 ? '96%' : '4%' }}
                    viewport={{ once: true }}
                    transition={{ duration: 1 }}
                  />
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <SectionHeading eyebrow="Testimonial Slider" title="What clients say" align="center" />
          <Swiper
            modules={[EffectCoverflow, Navigation, Pagination, Autoplay]}
            effect="coverflow"
            grabCursor
            centeredSlides
            slidesPerView="auto"
            coverflowEffect={{ rotate: 0, stretch: 0, depth: 140, modifier: 2.2, slideShadows: false }}
            navigation
            pagination={{ clickable: true }}
            autoplay={{ delay: 4500, disableOnInteraction: false }}
            className="reviews-swiper"
          >
            {testimonials.map((t) => (
              <SwiperSlide key={t.id}>
                <article className="review-slide card-glass">
                  <div className="review-stars">
                    {[...Array(t.rating)].map((_, j) => (
                      <HiOutlineStar key={j} />
                    ))}
                  </div>
                  <p>&ldquo;{t.text}&rdquo;</p>
                  <footer>
                    <img src={t.avatar} alt={t.name} loading="lazy" />
                    <div>
                      <strong>{t.name}</strong>
                      <span>{t.role}</span>
                    </div>
                  </footer>
                </article>
              </SwiperSlide>
            ))}
          </Swiper>
        </div>
      </section>

      <section className="section section-tight">
        <div className="container">
          <SectionHeading eyebrow="Success Stories" title="Client achievements" align="center" />
          <div className="success-grid">
            {testimonials.map((t, i) => (
              <article key={t.id} className="success-card card-glass gsap-reveal" data-aos="fade-up" data-aos-delay={i * 80}>
                <header>
                  <img src={t.avatar} alt={t.name} loading="lazy" />
                  <div>
                    <strong>{t.name}</strong>
                    <span>{t.role}</span>
                  </div>
                </header>
                <p>{t.text}</p>
                <span className="verified">★ Verified Client</span>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container video-reviews card-glass gsap-reveal" data-aos="fade-up">
          <div className="video-play">▶</div>
          <div>
            <h3>Video Reviews</h3>
            <p>Watch client success stories — coming soon on our YouTube channel.</p>
          </div>
        </div>
      </section>

      <CTABand title="Ready to join them?" subtitle="Let's build something your customers will rave about." />
    </div>
  );
}

export default Reviews;
