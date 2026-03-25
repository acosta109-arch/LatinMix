import React from 'react';
import { motion } from 'motion/react';
import { Youtube, ExternalLink, Video } from 'lucide-react';

interface YouTubeLiveProps {
  videoId: string;
}

export const YouTubeLive: React.FC<YouTubeLiveProps> = ({ videoId }) => {
  if (!videoId) {
    return (
      <section id="envivo" className="py-24 bg-radio-black">
        <div className="container mx-auto px-6 text-center">
          <div className="p-12 rounded-3xl border border-dashed border-white/20 bg-white/5">
            <div className="flex justify-center mb-6">
              <div className="p-4 rounded-full bg-latin-start/20 text-latin-start">
                <Video size={48} />
              </div>
            </div>
            <h2 className="text-2xl font-black text-white uppercase tracking-tighter">Youtube Offline</h2>
          </div>
        </div>
      </section>
    );
  }

  return (
    <section id="envivo" className="py-24 bg-radio-black">
      <div className="container mx-auto px-6">
        <div className="flex flex-col md:flex-row items-end justify-between gap-6 mb-12">
          <div>
            <div className="flex items-center gap-2 text-latin-start font-black text-xs tracking-[0.3em] uppercase mb-4">
              <div className="w-2 h-2 rounded-full bg-latin-start animate-ping" />
              TRANSMISIÓN EN VIVO
            </div>
            <h2 className="text-4xl sm:text-6xl font-black text-white tracking-tighter">
              MÍRANOS EN <span className="text-gradient-mix">YOUTUBE</span>
            </h2>
          </div>
          <a
            href={`https://www.youtube.com/watch?v=${videoId}`}
            target="_blank"
            rel="noopener noreferrer"
            className="flex items-center gap-2 text-radio-gray hover:text-white transition-colors font-bold text-sm"
          >
            Abrir en YouTube <ExternalLink size={16} />
          </a>
        </div>

        <div className="relative aspect-video rounded-3xl overflow-hidden border border-white/10 shadow-2xl bg-white/5">
          <iframe
            src={`https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1`}
            title="YouTube Live Stream"
            frameBorder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowFullScreen
            className="absolute inset-0 w-full h-full"
          ></iframe>
        </div>
      </div>
    </section>
  );
};
