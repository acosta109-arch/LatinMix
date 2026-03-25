import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { GALLERY_DATA } from '../constants';
import { Maximize2, X, ChevronLeft, ChevronRight } from 'lucide-react';

export const PhotoGallery: React.FC = () => {
  const [selectedImage, setSelectedImage] = useState<number | null>(null);

  const openLightbox = (index: number) => setSelectedImage(index);
  const closeLightbox = () => setSelectedImage(null);
  const nextImage = () => setSelectedImage((prev) => (prev !== null && prev < GALLERY_DATA.length - 1 ? prev + 1 : 0));
  const prevImage = () => setSelectedImage((prev) => (prev !== null && prev > 0 ? prev - 1 : GALLERY_DATA.length - 1));

  return (
    <section id="galeria" className="py-24 bg-radio-black/50">
      <div className="container mx-auto px-6">
        <div className="text-center mb-16">
          <span className="text-mix-start font-bold tracking-widest uppercase text-sm mb-2 block">Visuales</span>
          <h2 className="text-4xl md:text-5xl font-black text-white tracking-tighter">GALERÍA LATIN MIX</h2>
          <div className="w-24 h-1.5 bg-gradient-to-r from-latin-start to-mix-end mx-auto mt-6 rounded-full" />
        </div>

        <div className="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6">
          {GALLERY_DATA.map((img, index) => (
            <motion.div
              key={img.id}
              initial={{ opacity: 0, scale: 0.9 }}
              whileInView={{ opacity: 1, scale: 1 }}
              transition={{ delay: index * 0.1 }}
              viewport={{ once: true }}
              className="relative group cursor-pointer rounded-3xl overflow-hidden break-inside-avoid"
              onClick={() => openLightbox(index)}
            >
              <img
                src={img.url}
                alt={img.title}
                className="w-full h-auto object-cover transition-transform duration-700 group-hover:scale-110"
                referrerPolicy="no-referrer"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-radio-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-8">
                <div className="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                  <h4 className="text-white font-bold text-xl mb-1">{img.title}</h4>
                  <p className="text-radio-gray text-sm mb-4">{img.description}</p>
                  <div className="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                    <Maximize2 size={18} />
                  </div>
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Lightbox */}
      <AnimatePresence>
        {selectedImage !== null && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 z-[60] bg-radio-black/95 backdrop-blur-xl flex items-center justify-center p-4 md:p-12"
          >
            <button
              onClick={closeLightbox}
              className="absolute top-8 right-8 text-white/60 hover:text-white transition-colors"
            >
              <X size={40} />
            </button>

            <button
              onClick={prevImage}
              className="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-white transition-all"
            >
              <ChevronLeft size={32} />
            </button>

            <button
              onClick={nextImage}
              className="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center text-white transition-all"
            >
              <ChevronRight size={32} />
            </button>

            <motion.div
              key={selectedImage}
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              className="max-w-5xl w-full h-full flex flex-col items-center justify-center"
            >
              <img
                src={GALLERY_DATA[selectedImage].url}
                alt={GALLERY_DATA[selectedImage].title}
                className="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl"
                referrerPolicy="no-referrer"
              />
              <div className="mt-8 text-center">
                <h3 className="text-2xl font-bold text-white mb-2">{GALLERY_DATA[selectedImage].title}</h3>
                <p className="text-radio-gray">{GALLERY_DATA[selectedImage].description}</p>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </section>
  );
};
