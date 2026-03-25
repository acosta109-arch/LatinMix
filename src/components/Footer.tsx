import React from 'react';
import { Facebook, Instagram, Twitter, Youtube, Mail, Phone, MapPin, Radio, Music2 } from 'lucide-react';

interface FooterProps {
  config?: any;
}

export const Footer: React.FC<FooterProps> = ({ config }) => {
  const socialLinks = [
    { icon: Facebook, url: config?.facebookUrl || '#', name: 'Facebook' },
    { icon: Instagram, url: config?.instagramUrl || '#', name: 'Instagram' },
    { icon: Music2, url: config?.tiktokUrl || '#', name: 'TikTok' }, // Using Music2 for TikTok
    { icon: Twitter, url: config?.xUrl || '#', name: 'X' },
    { icon: Youtube, url: config?.youtubeChannelUrl || '#', name: 'YouTube' },
  ];

  return (
    <footer id="contacto" className="bg-radio-black pt-24 pb-32 border-t border-white/5">
      <div className="container mx-auto px-6">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
          {/* Brand */}
          <div className="lg:col-span-1">
            <div className="flex flex-col leading-none font-black text-3xl tracking-tighter mb-6">
              <span className="text-gradient-latin">LATIN</span>
              <span className="text-gradient-mix">MIX</span>
              <span className="text-[10px] font-bold tracking-widest text-radio-gray uppercase mt-2">La emisora de todos</span>
            </div>
            <p className="text-radio-gray text-sm leading-relaxed mb-8">
              La emisora líder en música latina y noticias. Conectando corazones a través de la mejor mezcla de ritmos y actualidad.
            </p>
            <div className="flex gap-4 flex-wrap">
              {socialLinks.map((link, i) => (
                <a
                  key={i}
                  href={link.url}
                  target="_blank"
                  rel="noopener noreferrer"
                  title={link.name}
                  className="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-radio-gray hover:text-white hover:bg-latin-start/20 hover:border-latin-start/50 transition-all"
                >
                  <link.icon size={18} />
                </a>
              ))}
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-white font-bold text-lg mb-8">Navegación</h4>
            <ul className="space-y-4">
              {['Inicio', 'Noticias', 'Galería', 'En Vivo', 'Sobre Nosotros'].map((item) => (
                <li key={item}>
                  <a href={`#${item.toLowerCase().replace(' ', '-')}`} className="text-radio-gray hover:text-latin-start transition-colors text-sm font-medium">
                    {item}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact Info */}
          <div>
            <h4 className="text-white font-bold text-lg mb-8">Contacto</h4>
            <ul className="space-y-6">
              <li className="flex items-start gap-4">
                <div className="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                  <MapPin size={16} className="text-latin-start" />
                </div>
                <p className="text-radio-gray text-sm">Av. Principal de la Música, Edificio Latin Mix, Piso 10. Ciudad Capital.</p>
              </li>
              <li className="flex items-center gap-4">
                <div className="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                  <Phone size={16} className="text-mix-start" />
                </div>
                <p className="text-radio-gray text-sm">+1 (555) 123-4567</p>
              </li>
              <li className="flex items-center gap-4">
                <div className="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0">
                  <Mail size={16} className="text-latin-end" />
                </div>
                <p className="text-radio-gray text-sm">contacto@latinmixradio.com</p>
              </li>
            </ul>
          </div>

          {/* Newsletter */}
          <div>
            <h4 className="text-white font-bold text-lg mb-8">Newsletter</h4>
            <p className="text-radio-gray text-sm mb-6">Suscríbete para recibir noticias exclusivas y promociones.</p>
            <form className="relative">
              <input
                type="email"
                placeholder="Tu correo electrónico"
                className="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-white text-sm focus:outline-none focus:border-latin-start transition-colors"
              />
              <button className="absolute right-2 top-1.5 bottom-1.5 px-4 rounded-lg bg-gradient-to-r from-latin-start to-latin-end text-radio-black font-bold text-xs">
                UNIRSE
              </button>
            </form>
          </div>
        </div>

        <div className="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4">
          <p className="text-radio-gray text-xs">
            © 2026 Latin Mix Radio. Todos los derechos reservados.
          </p>
          <div className="flex gap-8">
            <a href="/admin" className="text-latin-start hover:text-white text-xs transition-colors font-bold uppercase tracking-widest">Iniciar Sesión</a>
            <a href="#" className="text-radio-gray hover:text-white text-xs transition-colors">Términos y Condiciones</a>
            <a href="#" className="text-radio-gray hover:text-white text-xs transition-colors">Política de Privacidad</a>
          </div>
        </div>
      </div>
    </footer>
  );
};
