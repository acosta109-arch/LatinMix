import React, { useState, useEffect } from 'react';
import { LogOut, Save, Plus, Trash2, Settings, Megaphone, Share2, LogIn, Lock, Mail } from 'lucide-react';

// Hardcoded credentials
const ADMIN_EMAIL = 'Admin@gmail.com';
const ADMIN_PASSWORD = 'Admin123@@';

export const Admin: React.FC = () => {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  
  const [config, setConfig] = useState({
    azuraCastUrl: '',
    youtubeLiveId: '',
    facebookUrl: '',
    instagramUrl: '',
    tiktokUrl: '',
    xUrl: '',
    youtubeChannelUrl: ''
  });
  
  const [ads, setAds] = useState<any[]>([]);
  const [newAd, setNewAd] = useState({ title: '', imageUrl: '', linkUrl: '', active: true, startDate: '', endDate: '' });
  
  const [news, setNews] = useState<any[]>([]);
  const [newNews, setNewNews] = useState({ title: '', image: '', summary: '', date: '', category: 'Nacional' });

  useEffect(() => {
    // Check session
    const session = localStorage.getItem('radio_admin_session');
    if (session === 'true') {
      setIsLoggedIn(true);
    }

    // Load Config
    const savedConfig = localStorage.getItem('radio_config');
    if (savedConfig) {
      setConfig(JSON.parse(savedConfig));
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

    setLoading(false);
  }, []);

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    
    if (email === ADMIN_EMAIL && password === ADMIN_PASSWORD) {
      setIsLoggedIn(true);
      localStorage.setItem('radio_admin_session', 'true');
    } else {
      setError('Credenciales incorrectas. Por favor, verifique su correo y contraseña.');
    }
  };

  const handleLogout = () => {
    setIsLoggedIn(false);
    localStorage.removeItem('radio_admin_session');
  };

  const saveConfig = () => {
    localStorage.setItem('radio_config', JSON.stringify(config));
    // Trigger a storage event for other tabs
    window.dispatchEvent(new Event('storage'));
    alert('Configuración guardada correctamente');
  };

  const addAd = () => {
    if (!newAd.title || !newAd.imageUrl) return;
    const adWithId = { ...newAd, id: Date.now().toString(), createdAt: new Date().toISOString() };
    const updatedAds = [...ads, adWithId];
    setAds(updatedAds);
    localStorage.setItem('radio_ads', JSON.stringify(updatedAds));
    window.dispatchEvent(new Event('storage'));
    setNewAd({ title: '', imageUrl: '', linkUrl: '', active: true, startDate: '', endDate: '' });
  };

  const toggleAd = (id: string, active: boolean) => {
    const updatedAds = ads.map(ad => ad.id === id ? { ...ad, active: !active } : ad);
    setAds(updatedAds);
    localStorage.setItem('radio_ads', JSON.stringify(updatedAds));
    window.dispatchEvent(new Event('storage'));
  };

  const deleteAd = (id: string) => {
    if (!confirm('¿Estás seguro de eliminar esta publicidad?')) return;
    const updatedAds = ads.filter(ad => ad.id !== id);
    setAds(updatedAds);
    localStorage.setItem('radio_ads', JSON.stringify(updatedAds));
    window.dispatchEvent(new Event('storage'));
  };

  const addNews = () => {
    if (!newNews.title || !newNews.image || !newNews.summary) return;
    const newsWithId = { ...newNews, id: Date.now().toString() };
    const updatedNews = [...news, newsWithId];
    setNews(updatedNews);
    localStorage.setItem('radio_news', JSON.stringify(updatedNews));
    window.dispatchEvent(new Event('storage'));
    setNewNews({ title: '', image: '', summary: '', date: '', category: 'Nacional' });
  };

  const deleteNews = (id: string) => {
    if (!confirm('¿Estás seguro de eliminar esta noticia?')) return;
    const updatedNews = news.filter(n => n.id !== id);
    setNews(updatedNews);
    localStorage.setItem('radio_news', JSON.stringify(updatedNews));
    window.dispatchEvent(new Event('storage'));
  };

  if (loading) return <div className="min-h-screen bg-radio-black flex items-center justify-center text-white">Cargando...</div>;

  if (!isLoggedIn) {
    return (
      <div className="min-h-screen bg-radio-black flex items-center justify-center p-6">
        <div className="w-full max-w-md bg-white/5 border border-white/10 rounded-3xl p-10 backdrop-blur-2xl shadow-2xl">
          <div className="text-center mb-10">
            <div className="inline-flex p-4 rounded-2xl bg-gradient-to-br from-latin-start to-mix-end mb-6 shadow-lg">
              <Lock className="text-white" size={32} />
            </div>
            <h2 className="text-3xl font-black text-white mb-2 tracking-tighter uppercase">Iniciar Sesión</h2>
            <p className="text-radio-gray text-sm">Acceso exclusivo para personal autorizado</p>
          </div>
          
          <form onSubmit={handleLogin} className="space-y-6">
            <div className="space-y-2">
              <label className="block text-radio-gray text-xs font-bold uppercase tracking-widest ml-1">Correo Electrónico</label>
              <div className="relative">
                <Mail className="absolute left-4 top-1/2 -translate-y-1/2 text-radio-gray" size={18} />
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white focus:outline-none focus:ring-2 focus:ring-latin-start/50 transition-all"
                  placeholder="ejemplo@correo.com"
                  required
                />
              </div>
            </div>
            
            <div className="space-y-2">
              <label className="block text-radio-gray text-xs font-bold uppercase tracking-widest ml-1">Contraseña</label>
              <div className="relative">
                <Lock className="absolute left-4 top-1/2 -translate-y-1/2 text-radio-gray" size={18} />
                <input
                  type="password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white focus:outline-none focus:ring-2 focus:ring-latin-start/50 transition-all"
                  placeholder="••••••••"
                  required
                />
              </div>
            </div>
            
            {error && (
              <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold text-center">
                {error}
              </div>
            )}
            
            <button
              type="submit"
              className="w-full py-4 rounded-2xl bg-gradient-to-r from-latin-start to-latin-end text-radio-black font-black shadow-xl hover:scale-[1.02] transition-all active:scale-95 flex items-center justify-center gap-3"
            >
              <LogIn size={20} />
              ENTRAR AL PANEL
            </button>
          </form>
          
          <div className="mt-10 pt-8 border-t border-white/10 text-center">
            <a href="/" className="text-latin-start text-xs font-bold hover:text-white transition-colors tracking-widest uppercase">
              Volver al Inicio
            </a>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-radio-black text-white p-6 sm:p-12">
      <div className="max-w-6xl mx-auto">
        <header className="flex flex-col md:flex-row justify-between items-center mb-16 gap-6">
          <div className="text-center md:text-left">
            <h1 className="text-5xl font-black tracking-tighter mb-2 bg-gradient-to-r from-white to-radio-gray bg-clip-text text-transparent">PANEL DE CONTROL</h1>
            <p className="text-radio-gray text-sm font-medium tracking-widest uppercase">Gestión de Contenido y Configuración</p>
          </div>
          <button
            onClick={handleLogout}
            className="flex items-center gap-2 px-8 py-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all text-sm font-bold"
          >
            <LogOut size={18} />
            Cerrar Sesión
          </button>
        </header>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10">
          {/* Streaming & YouTube */}
          <section className="bg-white/5 border border-white/10 rounded-3xl p-10 backdrop-blur-sm">
            <div className="flex items-center gap-4 mb-8">
              <div className="p-3 rounded-2xl bg-latin-start/20 text-latin-start">
                <Settings size={24} />
              </div>
              <h2 className="text-2xl font-black tracking-tight uppercase">Streaming & YouTube</h2>
            </div>
            <div className="space-y-6">
              <div className="space-y-2">
                <label className="block text-radio-gray text-xs font-bold uppercase tracking-widest ml-1">AzuraCast URL</label>
                <input
                  type="text"
                  value={config.azuraCastUrl}
                  onChange={(e) => setConfig({ ...config, azuraCastUrl: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-5 text-white focus:outline-none focus:ring-2 focus:ring-latin-start/50 transition-all"
                  placeholder="https://radio.example.com/listen/radio/radio.mp3"
                />
              </div>
              <div className="space-y-2">
                <label className="block text-radio-gray text-xs font-bold uppercase tracking-widest ml-1">YouTube Live Video ID</label>
                <input
                  type="text"
                  value={config.youtubeLiveId}
                  onChange={(e) => setConfig({ ...config, youtubeLiveId: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-5 text-white focus:outline-none focus:ring-2 focus:ring-latin-start/50 transition-all"
                  placeholder="dQw4w9WgXcQ"
                />
              </div>
              <button
                onClick={saveConfig}
                className="flex items-center justify-center gap-3 w-full py-5 rounded-2xl bg-latin-start text-radio-black font-black shadow-lg shadow-latin-start/20 hover:scale-[1.02] transition-all active:scale-95"
              >
                <Save size={20} />
                GUARDAR CAMBIOS
              </button>
            </div>
          </section>

          {/* Redes Sociales */}
          <section className="bg-white/5 border border-white/10 rounded-3xl p-10 backdrop-blur-sm">
            <div className="flex items-center gap-4 mb-8">
              <div className="p-3 rounded-2xl bg-mix-start/20 text-mix-start">
                <Share2 size={24} />
              </div>
              <h2 className="text-2xl font-black tracking-tight uppercase">Redes Sociales</h2>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {['facebookUrl', 'instagramUrl', 'tiktokUrl', 'xUrl', 'youtubeChannelUrl'].map((key) => (
                <div key={key} className="space-y-2">
                  <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">
                    {key.replace('Url', '').replace('youtubeChannel', 'YouTube')}
                  </label>
                  <input
                    type="text"
                    value={(config as any)[key]}
                    onChange={(e) => setConfig({ ...config, [key]: e.target.value })}
                    className="w-full bg-white/5 border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-mix-start/50 transition-all"
                    placeholder="https://..."
                  />
                </div>
              ))}
            </div>
          </section>

          {/* Publicidad */}
          <section className="lg:col-span-2 bg-white/5 border border-white/10 rounded-3xl p-10 backdrop-blur-sm">
            <div className="flex items-center gap-4 mb-8">
              <div className="p-3 rounded-2xl bg-latin-end/20 text-latin-end">
                <Megaphone size={24} />
              </div>
              <h2 className="text-2xl font-black tracking-tight uppercase">Gestión de Publicidad</h2>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12 p-8 bg-white/5 rounded-2xl border border-white/5">
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Título</label>
                <input
                  type="text"
                  placeholder="Título del anuncio"
                  value={newAd.title}
                  onChange={(e) => setNewAd({ ...newAd, title: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm"
                />
              </div>
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Imagen URL</label>
                <input
                  type="text"
                  placeholder="URL de la imagen"
                  value={newAd.imageUrl}
                  onChange={(e) => setNewAd({ ...newAd, imageUrl: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm"
                />
              </div>
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Enlace</label>
                <input
                  type="text"
                  placeholder="Link (opcional)"
                  value={newAd.linkUrl}
                  onChange={(e) => setNewAd({ ...newAd, linkUrl: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm"
                />
              </div>
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Fecha Inicio</label>
                <input
                  type="date"
                  value={newAd.startDate}
                  onChange={(e) => setNewAd({ ...newAd, startDate: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white"
                />
              </div>
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Fecha Fin</label>
                <input
                  type="date"
                  value={newAd.endDate}
                  onChange={(e) => setNewAd({ ...newAd, endDate: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white"
                />
              </div>
              <div className="flex items-end">
                <button
                  onClick={addAd}
                  className="flex items-center justify-center gap-2 w-full bg-latin-end text-radio-black font-black rounded-xl py-3 shadow-lg shadow-latin-end/20 hover:scale-[1.02] transition-all active:scale-95"
                >
                  <Plus size={20} />
                  AÑADIR PUBLICIDAD
                </button>
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
              {ads.map((ad) => (
                <div key={ad.id} className="relative group bg-white/5 border border-white/10 rounded-2xl overflow-hidden transition-all hover:border-white/30">
                  <div className="aspect-video overflow-hidden">
                    <img src={ad.imageUrl} alt={ad.title} className="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-all duration-500 group-hover:scale-110" />
                  </div>
                  <div className="p-6">
                    <h3 className="font-bold text-sm mb-2 line-clamp-1">{ad.title}</h3>
                    <div className="text-[10px] text-radio-gray mb-4">
                      {ad.startDate && ad.endDate ? `${ad.startDate} al ${ad.endDate}` : 'Sin fecha límite'}
                    </div>
                    <div className="flex justify-between items-center">
                      <button
                        onClick={() => toggleAd(ad.id, ad.active)}
                        className={`text-[10px] font-black px-3 py-1.5 rounded-lg transition-all ${ad.active ? 'bg-green-500/20 text-green-500 border border-green-500/30' : 'bg-red-500/20 text-red-500 border border-red-500/30'}`}
                      >
                        {ad.active ? 'ACTIVO' : 'INACTIVO'}
                      </button>
                      <button onClick={() => deleteAd(ad.id)} className="p-2 rounded-lg text-radio-gray hover:bg-red-500/10 hover:text-red-500 transition-all">
                        <Trash2 size={18} />
                      </button>
                    </div>
                  </div>
                </div>
              ))}
              {ads.length === 0 && (
                <div className="col-span-full py-20 text-center border-2 border-dashed border-white/10 rounded-3xl">
                  <p className="text-radio-gray font-medium">No hay anuncios configurados</p>
                </div>
              )}
            </div>
          </section>

          {/* Noticias */}
          <section className="lg:col-span-2 bg-white/5 border border-white/10 rounded-3xl p-10 backdrop-blur-sm">
            <div className="flex items-center gap-4 mb-8">
              <div className="p-3 rounded-2xl bg-latin-start/20 text-latin-start">
                <Plus size={24} />
              </div>
              <h2 className="text-2xl font-black tracking-tight uppercase">Gestión de Noticias</h2>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12 p-8 bg-white/5 rounded-2xl border border-white/5">
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Título</label>
                <input
                  type="text"
                  placeholder="Título de la noticia"
                  value={newNews.title}
                  onChange={(e) => setNewNews({ ...newNews, title: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm"
                />
              </div>
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Imagen URL</label>
                <input
                  type="text"
                  placeholder="URL de la foto"
                  value={newNews.image}
                  onChange={(e) => setNewNews({ ...newNews, image: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm"
                />
              </div>
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Fecha</label>
                <input
                  type="date"
                  value={newNews.date}
                  onChange={(e) => setNewNews({ ...newNews, date: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white"
                />
              </div>
              <div className="space-y-2 md:col-span-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Descripción / Resumen</label>
                <textarea
                  placeholder="Breve descripción de la noticia..."
                  value={newNews.summary}
                  onChange={(e) => setNewNews({ ...newNews, summary: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm min-h-[100px]"
                />
              </div>
              <div className="space-y-2">
                <label className="block text-radio-gray text-[10px] font-black uppercase tracking-widest ml-1">Categoría</label>
                <select
                  value={newNews.category}
                  onChange={(e) => setNewNews({ ...newNews, category: e.target.value })}
                  className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white"
                >
                  <option value="Nacional">Nacional</option>
                  <option value="Internacional">Internacional</option>
                </select>
              </div>
              <div className="flex items-end md:col-span-3">
                <button
                  onClick={addNews}
                  className="flex items-center justify-center gap-2 w-full bg-latin-start text-radio-black font-black rounded-xl py-4 shadow-lg shadow-latin-start/20 hover:scale-[1.02] transition-all active:scale-95"
                >
                  <Plus size={20} />
                  PUBLICAR NOTICIA
                </button>
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
              {news.map((n) => (
                <div key={n.id} className="bg-white/5 border border-white/10 rounded-2xl overflow-hidden group">
                  <div className="aspect-video overflow-hidden">
                    <img src={n.image} alt={n.title} className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                  </div>
                  <div className="p-6">
                    <div className="flex justify-between items-start mb-2">
                      <span className="text-[10px] font-bold text-latin-start uppercase tracking-widest">{n.category}</span>
                      <span className="text-[10px] text-radio-gray">{n.date}</span>
                    </div>
                    <h3 className="font-bold text-sm mb-3 line-clamp-2">{n.title}</h3>
                    <p className="text-radio-gray text-xs line-clamp-3 mb-4">{n.summary}</p>
                    <button
                      onClick={() => deleteNews(n.id)}
                      className="flex items-center gap-2 text-red-500 text-xs font-bold hover:text-red-400 transition-colors"
                    >
                      <Trash2 size={14} />
                      ELIMINAR
                    </button>
                  </div>
                </div>
              ))}
              {news.length === 0 && (
                <div className="col-span-full py-20 text-center border-2 border-dashed border-white/10 rounded-3xl">
                  <p className="text-radio-gray font-medium">No hay noticias publicadas</p>
                </div>
              )}
            </div>
          </section>
        </div>
      </div>
    </div>
  );
};
