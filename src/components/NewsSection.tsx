import React, { useState } from 'react';
import { motion } from 'motion/react';
import { NEWS_DATA } from '../constants';
import { Calendar, ArrowRight, Tag } from 'lucide-react';

interface NewsItem {
  id: string;
  title: string;
  image: string;
  summary: string;
  date: string;
  category: string;
}

interface NewsSectionProps {
  news?: NewsItem[];
}

export const NewsSection: React.FC<NewsSectionProps> = ({ news = [] }) => {
  const [activeCategory, setActiveCategory] = useState<'Todos' | 'Nacional' | 'Internacional'>('Todos');

  const displayNews = news.length > 0 ? news : NEWS_DATA;

  const filteredNews = activeCategory === 'Todos'
    ? displayNews
    : displayNews.filter(n => n.category === activeCategory);

  return (
    <section id="noticias" className="py-24 bg-radio-black">
      <div className="container mx-auto px-6">
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
          <div>
            <span className="text-latin-start font-bold tracking-widest uppercase text-sm mb-2 block">Actualidad</span>
            <h2 className="text-4xl md:text-5xl font-black text-white tracking-tighter">ÚLTIMAS NOTICIAS</h2>
          </div>

          <div className="flex gap-2 p-1 bg-white/5 rounded-xl border border-white/10">
            {['Todos', 'Nacional', 'Internacional'].map((cat) => (
              <button
                key={cat}
                onClick={() => setActiveCategory(cat as any)}
                className={`px-6 py-2 rounded-lg text-sm font-bold transition-all ${
                  activeCategory === cat
                    ? 'bg-gradient-to-r from-latin-start to-latin-end text-radio-black shadow-lg'
                    : 'text-radio-gray hover:text-white'
                }`}
              >
                {cat}
              </button>
            ))}
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {filteredNews.map((news, index) => (
            <motion.article
              key={news.id}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.1 }}
              viewport={{ once: true }}
              className="group bg-white/5 rounded-3xl overflow-hidden border border-white/10 hover:border-white/20 transition-all hover:shadow-2xl hover:shadow-latin-start/5"
            >
              <div className="relative h-64 overflow-hidden">
                <img
                  src={news.image}
                  alt={news.title}
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                  referrerPolicy="no-referrer"
                />
                <div className="absolute top-4 left-4 flex gap-2">
                  <span className="px-3 py-1 rounded-full bg-radio-black/60 backdrop-blur-md text-[10px] font-bold text-white uppercase tracking-wider border border-white/10">
                    {news.category}
                  </span>
                </div>
              </div>

              <div className="p-8">
                <div className="flex items-center gap-4 text-radio-gray text-xs font-bold mb-4">
                  <div className="flex items-center gap-1">
                    <Calendar size={14} className="text-latin-start" />
                    {news.date.includes('-') ? new Date(news.date).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }) : news.date}
                  </div>
                  <div className="flex items-center gap-1">
                    <Tag size={14} className="text-mix-start" />
                    Música
                  </div>
                </div>

                <h3 className="text-xl font-bold text-white mb-4 group-hover:text-latin-start transition-colors line-clamp-2 leading-tight">
                  {news.title}
                </h3>
                <p className="text-radio-gray text-sm mb-6 line-clamp-3 leading-relaxed">
                  {news.summary}
                </p>

                <button className="flex items-center gap-2 text-sm font-black text-white group-hover:gap-4 transition-all">
                  LEER MÁS
                  <ArrowRight size={18} className="text-latin-start" />
                </button>
              </div>
            </motion.article>
          ))}
        </div>
      </div>
    </section>
  );
};
