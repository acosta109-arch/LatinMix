import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { NEWS_DATA } from '../constants';
import { ChevronLeft, ChevronRight } from 'lucide-react';

interface NewsItem {
  id: string;
  title: string;
  image: string;
  summary: string;
  date: string;
  category: string;
  featured?: boolean;
}

interface NewsCarouselProps {
  news?: NewsItem[];
}

export const NewsCarousel: React.FC<NewsCarouselProps> = ({ news = [] }) => {
  const displayNews = news.length > 0 ? news : NEWS_DATA;
  const featuredNews = displayNews.filter(n => n.featured).length > 0 
    ? displayNews.filter(n => n.featured)
    : displayNews.slice(0, 3); // Fallback to first 3 if none marked as featured

  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    if (featuredNews.length === 0) return;
    const timer = setInterval(() => {
      setCurrentIndex((prev) => (prev >= featuredNews.length - 1 ? 0 : prev + 1));
    }, 5000);
    return () => clearInterval(timer);
  }, [featuredNews.length]);

  const next = () => setCurrentIndex((prev) => (prev >= featuredNews.length - 1 ? 0 : prev + 1));
  const prev = () => setCurrentIndex((prev) => (prev <= 0 ? featuredNews.length - 1 : prev - 1));

  if (featuredNews.length === 0) return null;

  return (
    <section className="py-12 bg-radio-black">
      <div className="container mx-auto px-6">
        <div className="relative h-[400px] md:h-[500px] rounded-[2.5rem] overflow-hidden group">
          <AnimatePresence mode="wait">
            <motion.div
              key={currentIndex}
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              transition={{ duration: 0.8 }}
              className="absolute inset-0"
            >
              <img
                src={featuredNews[currentIndex].image}
                alt={featuredNews[currentIndex].title}
                className="w-full h-full object-cover"
                referrerPolicy="no-referrer"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-radio-black via-radio-black/40 to-transparent" />
              
              <div className="absolute bottom-0 left-0 right-0 p-8 md:p-16">
                <motion.div
                  initial={{ y: 30, opacity: 0 }}
                  animate={{ y: 0, opacity: 1 }}
                  transition={{ delay: 0.3 }}
                  className="max-w-2xl"
                >
                  <span className="px-4 py-1 rounded-full bg-latin-start text-radio-black font-bold text-xs uppercase tracking-widest mb-4 inline-block">
                    Destacado
                  </span>
                  <h2 className="text-3xl md:text-5xl font-black text-white mb-4 tracking-tighter">
                    {featuredNews[currentIndex].title}
                  </h2>
                  <p className="text-radio-gray text-lg mb-6 line-clamp-2">
                    {featuredNews[currentIndex].summary}
                  </p>
                  <button className="px-8 py-3 rounded-full bg-white text-radio-black font-bold hover:bg-latin-start transition-colors">
                    Leer Historia
                  </button>
                </motion.div>
              </div>
            </motion.div>
          </AnimatePresence>

          {/* Controls */}
          <div className="absolute top-1/2 -translate-y-1/2 left-6 right-6 flex justify-between opacity-0 group-hover:opacity-100 transition-opacity">
            <button onClick={prev} className="w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-all">
              <ChevronLeft size={24} />
            </button>
            <button onClick={next} className="w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white hover:bg-white/20 transition-all">
              <ChevronRight size={24} />
            </button>
          </div>

          {/* Indicators */}
          <div className="absolute bottom-8 right-8 flex gap-2">
            {featuredNews.map((_, i) => (
              <button
                key={i}
                onClick={() => setCurrentIndex(i)}
                className={`h-1.5 rounded-full transition-all ${
                  currentIndex === i ? 'w-8 bg-latin-start' : 'w-2 bg-white/30'
                }`}
              />
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};
