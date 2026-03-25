import React, { useState, useRef } from 'react';
import { motion } from 'motion/react';
import { Play, Pause, ChevronRight } from 'lucide-react';

interface HeroProps {
  streamUrl?: string;
}

export const Hero: React.FC<HeroProps> = ({ streamUrl }) => {
  const [isPlaying, setIsPlaying] = useState(false);
  const audioRef = useRef<HTMLAudioElement | null>(null);

  const togglePlay = () => {
    if (!streamUrl) return;
    
    if (!audioRef.current) {
      audioRef.current = new Audio(streamUrl);
    }

    if (isPlaying) {
      audioRef.current.pause();
    } else {
      audioRef.current.play().catch(err => console.error("Error playing audio:", err));
    }
    setIsPlaying(!isPlaying);
  };

  return (
    <section id="inicio" className="relative h-screen w-full overflow-hidden flex items-center">
      {/* Background with Overlay */}
      <div className="absolute inset-0 z-0">
        <img
          src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&q=80&w=1920"
          alt="Radio Studio"
          className="w-full h-full object-cover scale-105"
          referrerPolicy="no-referrer"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-radio-black via-radio-black/60 to-transparent" />
        <div className="absolute inset-0 bg-gradient-to-t from-radio-black via-transparent to-transparent" />
      </div>

      <div className="container mx-auto px-6 relative z-10">
        <div className="max-w-3xl">
          <motion.div
            initial={{ opacity: 0, x: -50 }}
            whileInView={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
          >
            <span className="inline-block px-4 py-1.5 rounded-full bg-mix-start/20 border border-mix-start/30 text-mix-start font-bold text-xs tracking-widest uppercase mb-6">
              Sintoniza la Pasión
            </span>
            <h1 className="text-5xl md:text-7xl lg:text-8xl font-black text-white leading-[0.9] mb-8 tracking-tighter">
              LA MEZCLA <br />
              <span className="text-gradient-latin">PERFECTA</span> DE <br />
              <span className="text-gradient-mix">MÚSICA</span> Y NOTICIAS
            </h1>
            <p className="text-lg md:text-xl text-radio-gray max-w-xl mb-10 leading-relaxed">
              Desde el corazón de la ciudad para todo el mundo. Latin Mix Radio te trae los éxitos del momento y la información que necesitas.
            </p>

            <div className="flex flex-wrap gap-4">
              <button 
                onClick={togglePlay}
                className="group flex items-center gap-3 px-8 py-4 rounded-full bg-gradient-to-r from-latin-start to-mix-end text-white font-bold text-lg shadow-2xl hover:scale-105 transition-all active:scale-95"
              >
                <div className="bg-white/20 p-2 rounded-full group-hover:bg-white/30 transition-colors">
                  {isPlaying ? <Pause size={20} fill="currentColor" /> : <Play size={20} fill="currentColor" />}
                </div>
                {isPlaying ? 'PAUSAR RADIO' : 'ESCUCHAR AHORA'}
              </button>
              <button className="flex items-center gap-2 px-8 py-4 rounded-full border border-white/20 hover:bg-white/10 text-white font-bold transition-all">
                VER NOTICIAS
                <ChevronRight size={20} />
              </button>
            </div>
          </motion.div>
        </div>
      </div>

      {/* Floating Elements */}
      <motion.div
        animate={{ y: [0, -20, 0] }}
        transition={{ duration: 4, repeat: Infinity, ease: "easeInOut" }}
        className="absolute right-[10%] top-[20%] hidden lg:block"
      >
        <div className="glass p-6 rounded-3xl w-64">
          <div className="flex items-center gap-4 mb-4">
            <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-latin-start to-latin-end flex items-center justify-center">
              {isPlaying ? <Pause size={24} className="text-radio-black" fill="currentColor" /> : <Play size={24} className="text-radio-black" fill="currentColor" />}
            </div>
            <div>
              <p className="text-xs text-radio-gray font-bold uppercase tracking-wider">Sonando</p>
              <p className="font-bold text-white truncate">{isPlaying ? 'En Vivo' : 'Radio Pausada'}</p>
            </div>
          </div>
          <div className="flex gap-1 items-end h-8">
            {[...Array(8)].map((_, i) => (
              <motion.div
                key={i}
                animate={isPlaying ? { height: [4, 20, 8, 24, 6] } : { height: 4 }}
                transition={{ duration: 1, repeat: Infinity, delay: i * 0.1 }}
                className="flex-1 bg-latin-start rounded-full opacity-60"
              />
            ))}
          </div>
        </div>
      </motion.div>
    </section>
  );
};
