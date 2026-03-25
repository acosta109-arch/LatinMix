import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';

export const SplashScreen: React.FC<{ onComplete: () => void }> = ({ onComplete }) => {
  useEffect(() => {
    const timer = setTimeout(() => {
      onComplete();
    }, 2500);
    return () => clearTimeout(timer);
  }, [onComplete]);

  return (
    <motion.div
      initial={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      transition={{ duration: 0.8, ease: "easeInOut" }}
      className="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-radio-black"
    >
      <div className="relative flex flex-col items-center">
        {/* Brand Animation */}
        <motion.div
          initial={{ opacity: 0, y: 10 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 1 }}
          className="text-center mb-12"
        >
          <div className="text-5xl sm:text-8xl font-black tracking-tighter mb-4">
            <span className="text-gradient-latin">LATIN</span>
            <span className="text-gradient-mix ml-3">MIX</span>
          </div>
          <p className="text-white/60 font-bold tracking-[0.5em] uppercase text-sm sm:text-base">La emisora de todos</p>
        </motion.div>

        {/* Audio Waves Animation */}
        <div className="flex items-end justify-center gap-1 h-12">
          {[...Array(12)].map((_, i) => (
            <motion.div
              key={i}
              animate={{
                height: [10, 40, 15, 48, 20, 35, 10],
              }}
              transition={{
                duration: 1.2,
                repeat: Infinity,
                delay: i * 0.1,
                ease: "easeInOut",
              }}
              className="w-1.5 rounded-full bg-gradient-to-t from-mix-start to-latin-end"
            />
          ))}
        </div>

        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 1.5, duration: 0.8 }}
          className="mt-8 text-radio-gray font-medium tracking-widest uppercase text-sm"
        >
          Sintonizando la mejor mezcla...
        </motion.p>
      </div>
    </motion.div>
  );
};
