import React, { useState, useEffect, useCallback, Component, ErrorInfo, ReactNode } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { AnimatePresence, motion } from 'motion/react';
import { SplashScreen } from './components/SplashScreen';
import { Navbar } from './components/Navbar';
import { Hero } from './components/Hero';
import { RadioPlayer } from './components/RadioPlayer';
import { NewsCarousel } from './components/NewsCarousel';
import { NewsSection } from './components/NewsSection';
import { PhotoGallery } from './components/PhotoGallery';
import { AboutUs } from './components/AboutUs';
import { Footer } from './components/Footer';
import { Admin } from './components/Admin';
import { YouTubeLive } from './components/YouTubeLive';
import { AdsSection } from './components/AdsSection';

function MainSite() {
  const [config, setConfig] = useState<any>(null);
  const [ads, setAds] = useState<any[]>([]);
  const [news, setNews] = useState<any[]>([]);

  const loadData = useCallback(() => {
    // Load Config
    const savedConfig = localStorage.getItem('radio_config');
    if (savedConfig) {
      setConfig(JSON.parse(savedConfig));
    } else {
      // Default config if none exists
      setConfig({
        azuraCastUrl: '',
        youtubeLiveId: '',
        facebookUrl: 'https://facebook.com',
        instagramUrl: 'https://instagram.com',
        tiktokUrl: 'https://tiktok.com',
        xUrl: 'https://x.com',
        youtubeChannelUrl: 'https://youtube.com'
      });
    }

    // Load Ads
    const savedAds = localStorage.getItem('radio_ads');
    if (savedAds) {
      setAds(JSON.parse(savedAds));
    }

    // Load News
    const savedNews = localStorage.getItem('radio_news');
    if (savedNews) {
      setNews(JSON.parse(savedNews));
    }
  }, []);

  useEffect(() => {
    loadData();

    // Listen for storage changes from other tabs/components
    window.addEventListener('storage', loadData);
    return () => window.removeEventListener('storage', loadData);
  }, [loadData]);

  return (
    <motion.div 
      key="main-content" 
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      className="flex flex-col"
    >
      <Navbar config={config} />
      <main>
        <Hero streamUrl={config?.azuraCastUrl} />
        <YouTubeLive videoId={config?.youtubeLiveId} />
        <AdsSection ads={ads} />
        <NewsCarousel news={news} />
        <NewsSection news={news} />
        <PhotoGallery />
        <AboutUs />
      </main>
      <Footer config={config} />
      <RadioPlayer streamUrl={config?.azuraCastUrl} />
    </motion.div>
  );
}

export default function App() {
  const [showSplash, setShowSplash] = useState(true);

  // Stabilize the complete callback to prevent timer resets
  const handleSplashComplete = useCallback(() => {
    setShowSplash(false);
  }, []);

  return (
    <div className="min-h-screen bg-radio-black selection:bg-latin-start selection:text-radio-black">
      <AnimatePresence mode="wait">
        {showSplash ? (
          <SplashScreen key="splash" onComplete={handleSplashComplete} />
        ) : (
          <Router>
            <Routes>
              <Route path="/admin" element={<Admin />} />
              <Route path="/" element={<MainSite />} />
            </Routes>
          </Router>
        )}
      </AnimatePresence>
    </div>
  );
}
