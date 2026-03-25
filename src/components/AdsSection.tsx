import React from 'react';
import { motion } from 'motion/react';
import { Megaphone } from 'lucide-react';

interface Ad {
  id: string;
  title: string;
  imageUrl: string;
  linkUrl?: string;
  active: boolean;
  startDate?: string;
  endDate?: string;
}

interface AdsSectionProps {
  ads: Ad[];
}

export const AdsSection: React.FC<AdsSectionProps> = ({ ads }) => {
  const activeAds = ads.filter(ad => {
    if (!ad.active) return false;
    
    // If no dates are set, it's always active
    if (!ad.startDate || !ad.endDate) return true;
    
    const now = new Date();
    const start = new Date(ad.startDate);
    const end = new Date(ad.endDate);
    
    // Set end date to the end of that day
    end.setHours(23, 59, 59, 999);
    
    return now >= start && now <= end;
  });
  
  if (activeAds.length === 0) {
    return (
      <section className="py-24 bg-white/5 border-y border-white/5">
        <div className="container mx-auto px-6 text-center">
          <div className="flex items-center justify-center gap-3">
            <div className="p-2 rounded-lg bg-latin-end/20 text-latin-end">
              <Megaphone size={20} />
            </div>
            <h2 className="text-xl font-black text-white tracking-tight uppercase">No hay publicidad por el momento</h2>
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="py-24 bg-white/5 border-y border-white/5">
      <div className="container mx-auto px-6">
        <div className="flex items-center gap-3 mb-12">
          <div className="p-2 rounded-lg bg-latin-end/20 text-latin-end">
            <Megaphone size={20} />
          </div>
          <h2 className="text-2xl font-black text-white tracking-tight uppercase">Publicidad</h2>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {activeAds.map((ad) => (
            <motion.a
              key={ad.id}
              href={ad.linkUrl || '#'}
              target={ad.linkUrl ? "_blank" : "_self"}
              rel="noopener noreferrer"
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              whileHover={{ scale: 1.02 }}
              className="relative group block aspect-[16/9] rounded-2xl overflow-hidden border border-white/10 bg-white/5"
            >
              <img
                src={ad.imageUrl}
                alt={ad.title}
                className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                referrerPolicy="no-referrer"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                <p className="text-white font-bold text-lg">{ad.title}</p>
              </div>
            </motion.a>
          ))}
        </div>
      </div>
    </section>
  );
};
