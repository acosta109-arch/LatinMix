import React, { useState, useRef, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { Play, Pause, Volume2, VolumeX, Radio, Music2 } from 'lucide-react';

interface RadioPlayerProps {
  streamUrl?: string;
}

export const RadioPlayer: React.FC<RadioPlayerProps> = ({ streamUrl }) => {
  const [isPlaying, setIsPlaying] = useState(false);
  const [isMuted, setIsMuted] = useState(false);
  const [volume, setVolume] = useState(80);
  const audioRef = useRef<HTMLAudioElement | null>(null);

  useEffect(() => {
    if (streamUrl) {
      if (audioRef.current) {
        audioRef.current.src = streamUrl;
      } else {
        audioRef.current = new Audio(streamUrl);
      }
    }
  }, [streamUrl]);

  useEffect(() => {
    if (audioRef.current) {
      audioRef.current.volume = isMuted ? 0 : volume / 100;
    }
  }, [volume, isMuted]);

  const togglePlay = () => {
    if (!audioRef.current) return;

    if (isPlaying) {
      audioRef.current.pause();
    } else {
      audioRef.current.play().catch(err => console.error("Error playing audio:", err));
    }
    setIsPlaying(!isPlaying);
  };

  return (
    <div className="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 w-[95%] max-w-4xl">
      <motion.div
        initial={{ y: 100, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ delay: 4, duration: 0.8 }}
        className="glass rounded-2xl p-4 md:p-6 shadow-2xl flex flex-col md:flex-row items-center gap-4 md:gap-8"
      >
        {/* Station Info */}
        <div className="flex items-center gap-4 w-full md:w-auto">
          <div className="relative group cursor-pointer">
            <div className="w-14 h-14 md:w-16 md:h-16 rounded-xl bg-gradient-to-br from-latin-start to-mix-end flex items-center justify-center shadow-lg overflow-hidden">
              <Music2 size={32} className="text-white" />
              <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <Play size={24} className="text-white" fill="currentColor" />
              </div>
            </div>
            {isPlaying && (
              <div className="absolute -top-1 -right-1 w-4 h-4 bg-radio-red rounded-full border-2 border-radio-black animate-pulse" />
            )}
          </div>
          <div className="flex-1 min-w-0">
            <div className="flex items-center gap-2 mb-1">
              <span className="flex items-center gap-1 px-2 py-0.5 rounded bg-radio-red text-[10px] font-black text-white uppercase tracking-tighter">
                <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                EN VIVO
              </span>
              <p className="text-xs font-bold text-radio-gray uppercase tracking-widest truncate">Latin Mix Radio</p>
            </div>
            <h3 className="text-white font-bold text-lg truncate">La Mezcla Perfecta</h3>
          </div>
        </div>

        {/* Controls */}
        <div className="flex items-center justify-center gap-6 flex-1">
          <button
            onClick={togglePlay}
            className="w-14 h-14 rounded-full bg-white text-radio-black flex items-center justify-center shadow-xl hover:scale-110 transition-transform active:scale-95"
          >
            {isPlaying ? <Pause size={28} fill="currentColor" /> : <Play size={28} className="ml-1" fill="currentColor" />}
          </button>

          {/* Visualizer (Simplified) */}
          <div className="hidden lg:flex items-end gap-1 h-10 w-48">
            {[...Array(20)].map((_, i) => (
              <motion.div
                key={i}
                animate={isPlaying ? { height: [4, 30, 10, 40, 6] } : { height: 4 }}
                transition={{ duration: 0.8, repeat: Infinity, delay: i * 0.05 }}
                className="flex-1 bg-gradient-to-t from-mix-start to-latin-start rounded-full opacity-40"
              />
            ))}
          </div>
        </div>

        {/* Volume & Extras */}
        <div className="hidden md:flex items-center gap-4 w-48 justify-end">
          <button
            onClick={() => setIsMuted(!isMuted)}
            className="text-radio-gray hover:text-white transition-colors"
          >
            {isMuted || volume === 0 ? <VolumeX size={20} /> : <Volume2 size={20} />}
          </button>
          <div className="w-24 h-1.5 bg-white/10 rounded-full relative overflow-hidden group cursor-pointer">
            <div
              className="absolute inset-y-0 left-0 bg-gradient-to-r from-latin-start to-mix-end transition-all"
              style={{ width: `${isMuted ? 0 : volume}%` }}
            />
          </div>
          <button className="p-2 rounded-lg hover:bg-white/5 text-radio-gray hover:text-white transition-all">
            <Radio size={20} />
          </button>
        </div>
      </motion.div>
    </div>
  );
};
