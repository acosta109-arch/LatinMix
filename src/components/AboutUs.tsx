import React from 'react';
import { motion } from 'motion/react';
import { Radio, Users, Globe, Award } from 'lucide-react';

export const AboutUs: React.FC = () => {
  const stats = [
    { icon: <Radio className="text-latin-start" />, label: 'Años al Aire', value: '15+' },
    { icon: <Users className="text-mix-start" />, label: 'Oyentes Diarios', value: '2M+' },
    { icon: <Globe className="text-latin-end" />, label: 'Países', value: '45' },
    { icon: <Award className="text-radio-red" />, label: 'Premios', value: '12' },
  ];

  return (
    <section id="sobre-nosotros" className="py-24 relative overflow-hidden">
      {/* Background Decoration */}
      <div className="absolute top-0 right-0 w-96 h-96 bg-latin-start/10 blur-[120px] rounded-full -mr-48 -mt-48" />
      <div className="absolute bottom-0 left-0 w-96 h-96 bg-mix-start/10 blur-[120px] rounded-full -ml-48 -mb-48" />

      <div className="container mx-auto px-6">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <motion.div
            initial={{ opacity: 0, x: -50 }}
            whileInView={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
          >
            <span className="text-latin-start font-bold tracking-widest uppercase text-sm mb-4 block">Nuestra Historia</span>
            <h2 className="text-4xl md:text-5xl font-black text-white tracking-tighter mb-8">
              MÁS QUE UNA EMISORA, <br />
              <span className="text-gradient-mix text-6xl md:text-7xl">UNA PASIÓN</span>
            </h2>
            <div className="space-y-6 text-radio-gray text-lg leading-relaxed">
              <p>
                Latin Mix Radio nació con el sueño de unir a la comunidad latina a través de la música que nos mueve y la información que nos importa. Desde nuestros inicios, hemos sido el puente entre los grandes éxitos y las nuevas promesas.
              </p>
              <p>
                Nuestra misión es entretener, informar y acompañar a nuestra audiencia las 24 horas del día, con una programación dinámica, tecnológica y siempre a la vanguardia de las tendencias globales.
              </p>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-4 gap-8 mt-12">
              {stats.map((stat, i) => (
                <div key={i} className="text-center">
                  <div className="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center mx-auto mb-4 border border-white/10">
                    {stat.icon}
                  </div>
                  <p className="text-2xl font-black text-white mb-1">{stat.value}</p>
                  <p className="text-[10px] font-bold text-radio-gray uppercase tracking-widest">{stat.label}</p>
                </div>
              ))}
            </div>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, scale: 0.8 }}
            whileInView={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            className="relative"
          >
            <div className="relative z-10 rounded-[2rem] overflow-hidden shadow-2xl border border-white/10">
              <img
                src="https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&q=80&w=1000"
                alt="Latin Mix Team"
                className="w-full h-full object-cover"
                referrerPolicy="no-referrer"
              />
            </div>
            {/* Decorative Frame */}
            <div className="absolute -inset-4 border-2 border-latin-start/30 rounded-[2.5rem] -z-0" />
            <div className="absolute -bottom-8 -right-8 w-48 h-48 bg-gradient-to-br from-latin-start to-mix-end rounded-full blur-3xl opacity-20" />
          </motion.div>
        </div>
      </div>
    </section>
  );
};
