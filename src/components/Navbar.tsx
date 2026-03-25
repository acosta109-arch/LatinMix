import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Menu, X, Radio, Play, Facebook, Instagram, Twitter, Youtube, Music2 } from 'lucide-react';

interface NavbarProps {
  config?: any;
}

export const Navbar: React.FC<NavbarProps> = ({ config }) => {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const socialLinks = [
    { icon: Facebook, url: config?.facebookUrl || '#', name: 'Facebook' },
    { icon: Instagram, url: config?.instagramUrl || '#', name: 'Instagram' },
    { icon: Music2, url: config?.tiktokUrl || '#', name: 'TikTok' },
    { icon: Twitter, url: config?.xUrl || '#', name: 'X' },
    { icon: Youtube, url: config?.youtubeChannelUrl || '#', name: 'YouTube' },
  ];

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navLinks = [
    { name: 'Inicio', href: '#inicio' },
    { name: 'Noticias', href: '#noticias' },
    { name: 'Galería', href: '#galeria' },
    { name: 'En Vivo', href: '#envivo' },
    { name: 'Contacto', href: '#contacto' },
    { name: 'Iniciar Sesión', href: '/admin' },
  ];

  return (
    <nav
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled ? 'bg-radio-black/90 backdrop-blur-md py-3 shadow-lg' : 'bg-transparent py-6'
      }`}
    >
      <div className="container mx-auto px-6 flex items-center justify-between">
        {/* Brand */}
        <a href="#inicio" className="flex flex-col group">
          <div className="flex leading-none font-black text-xl sm:text-2xl tracking-tighter transition-transform group-hover:scale-105">
            <span className="text-gradient-latin">LATIN</span>
            <span className="text-gradient-mix ml-1">MIX</span>
          </div>
          <span className="text-[10px] font-bold tracking-widest text-radio-gray uppercase mt-0.5">La emisora de todos</span>
        </a>

        {/* Desktop Menu */}
        <div className="hidden md:flex items-center gap-8">
          {navLinks.map((link) => (
            <a
              key={link.name}
              href={link.href}
              className="text-sm font-semibold text-white/80 hover:text-white transition-colors relative group"
            >
              {link.name}
              <span className="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-latin-start to-mix-end transition-all group-hover:w-full" />
            </a>
          ))}
        </div>

        {/* Action Button & Socials */}
        <div className="flex items-center gap-6">
          <div className="hidden lg:flex items-center gap-3 mr-2">
            {socialLinks.map((link, i) => (
              <a
                key={i}
                href={link.url}
                target="_blank"
                rel="noopener noreferrer"
                className="text-white/60 hover:text-latin-start transition-colors"
              >
                <link.icon size={16} />
              </a>
            ))}
          </div>

          <button className="hidden sm:flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-latin-start to-latin-end text-radio-black font-bold text-sm shadow-xl hover:scale-105 transition-transform active:scale-95">
            <Radio size={18} className="animate-pulse" />
            ESCUCHAR EN VIVO
          </button>

          {/* Mobile Toggle */}
          <button
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            className="md:hidden p-2 text-white hover:bg-white/10 rounded-lg transition-colors"
          >
            {isMobileMenuOpen ? <X size={28} /> : <Menu size={28} />}
          </button>
        </div>
      </div>

      {/* Mobile Menu */}
      <AnimatePresence>
        {isMobileMenuOpen && (
          <motion.div
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: 'auto' }}
            exit={{ opacity: 0, height: 0 }}
            className="md:hidden bg-radio-black border-t border-white/10 overflow-hidden"
          >
            <div className="flex flex-col p-6 gap-4">
              {navLinks.map((link) => (
                <a
                  key={link.name}
                  href={link.href}
                  onClick={() => setIsMobileMenuOpen(false)}
                  className="text-lg font-bold text-white/90 hover:text-latin-start transition-colors"
                >
                  {link.name}
                </a>
              ))}
              
              <div className="flex items-center gap-6 py-4 border-t border-white/5 mt-2">
                {socialLinks.map((link, i) => (
                  <a
                    key={i}
                    href={link.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-white/60 hover:text-latin-start transition-colors"
                  >
                    <link.icon size={20} />
                  </a>
                ))}
              </div>

              <button className="flex items-center justify-center gap-2 w-full mt-2 px-6 py-4 rounded-xl bg-gradient-to-r from-latin-start to-latin-end text-radio-black font-bold shadow-xl">
                <Play size={20} fill="currentColor" />
                ESCUCHAR EN VIVO
              </button>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </nav>
  );
};
