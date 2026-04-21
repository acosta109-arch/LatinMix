<?php
/**
 * VISTA: CONTENIDO HOME (NOTICIAS + BARRA LATERAL)
 */
require_once __DIR__ . '/../includes/functions.php';

// Cargar Datos
$config = load_data('radio_config');
$all_news = load_data('radio_news');

// Lógica de Filtrado y Paginación
$current_cat = $_GET['cat'] ?? 'Todas';
$current_page = max(1, (int)($_GET['page'] ?? 1));
$show_all = isset($_GET['show_all']) && $_GET['show_all'] == '1';
$items_per_page = 10;

$news = $all_news;
if ($current_cat !== 'Todas') {
    $news = array_filter($all_news, function($n) use ($current_cat) {
        return strtolower($n['category']) === strtolower($current_cat);
    });
}

// Total antes de paginar
$total_filtered_news = count($news);
$total_pages = ceil($total_filtered_news / $items_per_page);

// Aplicar Paginación si no es "Ver Todas"
if (!$show_all) {
    $offset = ($current_page - 1) * $items_per_page;
    $news = array_slice($news, $offset, $items_per_page);
}
?>
<main id="main-content" class="pb-56 overflow-hidden">
    
    <!-- FIXED HERO SECTION -->
    <section id="inicio" class="relative min-h-[90vh] flex flex-col items-center justify-center text-center px-6 overflow-hidden bg-radio-black">
        <!-- Floating Elements Background -->
        <div class="absolute inset-0 pointer-events-none opacity-20">
            <!-- Musical Notes -->
            <div class="absolute top-[10%] left-[15%] text-4xl animate-drift-slow text-latin-start">♪</div>
            <div class="absolute top-[60%] left-[10%] text-6xl animate-drift text-mix-start">♫</div>
            <div class="absolute top-[30%] right-[15%] text-5xl animate-drift-mid text-latin-end">♬</div>
            <div class="absolute bottom-[20%] right-[20%] text-3xl animate-drift-slow text-mix-end">♭</div>
            
            <!-- Global Symbols (Globes) -->
            <div class="absolute top-[20%] right-[30%] animate-spin-slow text-latin-start">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
            </div>
            <div class="absolute bottom-[15%] left-[25%] animate-spin-slow-reverse text-mix-start opacity-70">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
            </div>

            <!-- News Symbols -->
            <div class="absolute top-[45%] left-[5%] animate-drift-mid text-radio-silver opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path><path d="M18 14h-8"></path><path d="M15 18h-5"></path><path d="M10 6h8v4h-8V6Z"></path></svg>
            </div>
            <div class="absolute bottom-[40%] right-[10%] animate-drift-slow text-radio-red opacity-40">
                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5L6 9H2V15H6L11 19V5Z"></path><path d="M19.07 4.93C20.94 6.81 22 9.32 22 12C22 14.68 20.94 17.19 19.07 19.07"></path><path d="M15.54 8.46C16.41 9.34 17 10.6 17 12C17 13.4 16.41 14.66 15.54 15.54"></path></svg>
            </div>
        </div>

        <!-- Blur Orbs -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-latin-start/20 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-latin-end/20 rounded-full blur-[120px] animate-pulse"></div>
        
        <div class="z-10 w-full px-4">
            <h1 class="text-3xl sm:text-4xl md:text-[5rem] font-black tracking-tight leading-[1.1] md:leading-[1.2] italic py-8 uppercase selection:text-latin-start">
                <span class="gradient-text">LATIN MIX</span> <br> <span class="gradient-text">¡LA VOZ QUE NOS UNE!</span>
            </h1>
            <p class="max-w-2xl mx-auto text-radio-gray text-sm md:text-xl mb-12 font-medium leading-relaxed px-4 italic">
                Latin Mix es la emisora favorita de los latinos, con un balance musical e informativo que complace los más exquisitos gustos de los oyentes.
            </p>
            
        </div>
    </section>

    <style>
        @keyframes drift { 0%, 100% { transform: translate(0,0) rotate(0deg); } 25% { transform: translate(10px, 15px) rotate(5deg); } 50% { transform: translate(-5px, 20px) rotate(-5deg); } 75% { transform: translate(-15px, 10px) rotate(3deg); } }
        .animate-drift { animation: drift 12s ease-in-out infinite; }
        .animate-drift-slow { animation: drift 20s ease-in-out infinite; }
        .animate-drift-mid { animation: drift 15s ease-in-out infinite; }
        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .animate-spin-slow { animation: spin-slow 40s linear infinite; }
        .animate-spin-slow-reverse { animation: spin-slow 50s linear infinite reverse; }
    </style>

    <!-- RADIO ÚTIL FM - SECCIÓN EN VIVO (7 PM - 9 PM) -->
    <section id="radio-util" class="relative py-24 px-6 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-transparent to-blue-400/5 pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500/5 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="glass p-8 md:p-16 rounded-[60px] border-white/10 shadow-3xl flex flex-col lg:flex-row items-center gap-12 lg:gap-24">
                
                <!-- Visual del Programa -->
                <div class="relative group w-full lg:w-1/3 aspect-square max-w-[320px]">
                    <div class="absolute -inset-4 bg-blue-500/20 rounded-[50px] blur-2xl group-hover:bg-blue-500/30 transition-all duration-700"></div>
                    <div class="relative h-full w-full bg-[#0d0d0f] rounded-[50px] border border-white/10 overflow-hidden flex items-center justify-center p-8 shadow-2xl">
                        <div class="text-center">
                            <i class="bi bi-broadcast text-7xl text-blue-500 mb-6 block animate-pulse"></i>
                            <h4 class="text-xs font-black uppercase tracking-[0.4em] text-radio-gray">Radio Útil FM</h4>
                        </div>
                        
                        <!-- Pulsing Live Badge -->
                        <div class="absolute top-6 left-6 flex items-center gap-2">
                             <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                             </span>
                             <span class="text-[9px] font-black uppercase tracking-widest text-white/80">EN VIVO</span>
                        </div>
                    </div>
                </div>

                <!-- Info y Player -->
                <div class="flex-1 text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start gap-4 mb-6">
                        <div class="w-10 h-[2px] bg-blue-500"></div>
                        <span class="text-xs font-black uppercase tracking-[0.4em] text-blue-500">Transmisión Especial</span>
                    </div>
                    
                    <h2 class="text-4xl md:text-6xl font-black mb-8 tracking-tighter uppercase italic leading-none">
                        Sintoniza <br> <span class="text-blue-500">Radio Útil FM</span>
                    </h2>
                    
                    <p class="text-radio-gray text-lg mb-10 leading-relaxed font-medium max-w-xl">
                        No te pierdas nuestra programación estelar en vivo de <span class="text-white font-bold">7:00 PM a 9:00 PM</span>. Conectamos contigo a través de la red de Radio Útil FM.
                    </p>

                    <!-- Player Interface -->
                    <div class="flex flex-col sm:flex-row items-center gap-8 p-4 bg-white/5 rounded-[30px] border border-white/5 backdrop-blur-md" 
                         x-data="{ 
                            playingUtil: false, 
                            playerUtil: null,
                            streamUrl: 'https://shoutcast.tmcreativos.com/proxy/radioutil?mp=/stream',
                            init() {
                                window.utilPlayer = this;
                            },
                            toggle() {
                                if(!this.playerUtil) {
                                    this.playerUtil = new Audio(this.streamUrl);
                                }
                                if(this.playingUtil) {
                                    this.playerUtil.pause();
                                    this.playingUtil = false;
                                } else {
                                    // Pausar el player principal si está sonando
                                    if(window.radioPlayer && typeof window.radioPlayer.toggle === 'function' && window.radioPlayer.isPlaying) {
                                        window.radioPlayer.toggle();
                                    }
                                    this.playerUtil.play();
                                    this.playingUtil = true;
                                }
                            }
                         }">
                        
                        <button @click="toggle()" 
                                class="w-16 h-16 rounded-full bg-blue-500 text-black flex items-center justify-center hover:scale-110 active:scale-95 transition-all shadow-lg shadow-blue-500/20 group">
                            <template x-if="!playingUtil">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="ml-1"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </template>
                            <template x-if="playingUtil">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                            </template>
                        </button>

                        <div class="text-left flex-1">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-black uppercase italic text-white tracking-tight" x-text="playingUtil ? 'Escuchando en Vivo' : 'Escuchar Ahora'"></span>
                                <div x-show="playingUtil" class="flex items-end gap-1 h-3">
                                    <div class="w-1 bg-blue-500 animate-eq-1 h-1/2"></div>
                                    <div class="w-1 bg-blue-400 animate-eq-2 h-full"></div>
                                    <div class="w-1 bg-blue-500 animate-eq-3 h-3/4"></div>
                                </div>
                            </div>
                            <p class="text-[10px] font-bold text-radio-gray uppercase tracking-widest mt-1">streaming.radioutilfm.com</p>
                        </div>

                        <div class="hidden sm:flex items-center gap-4 px-8 border-l border-white/10">
                            <div class="text-right">
                                <span class="block text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Frecuencia</span>
                                <span class="text-xs font-black text-white italic">DIGITAL HD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NOTICIAS + SIDEBAR LAYOUT -->
    <section id="noticias" class="py-20 px-2 md:px-6">
        <div class="max-w-7xl mx-auto">
            
            <!-- HEADER & FILTROS -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-20 gap-10 px-4 md:px-0">
                <div class="relative -left-2 md:-left-4">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-[2px] bg-latin-start"></div>
                        <h2 class="text-sm font-black uppercase tracking-[0.3em] text-latin-start">Sala de Prensa</h2>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-black tracking-tighter uppercase leading-tight italic py-2">Explora la <br> <span class="gradient-text">Actualidad</span></h2>
                </div>

                <!-- FILTRO DE CATEGORIAS -->
                <div class="flex flex-wrap gap-2 glass p-2 rounded-2xl">
                    <?php 
                    $cats = ['Todas', 'Nacional/Local', 'Internacional', 'Economía', 'Sociedad', 'Cultura', 'Deportes', 'Opinión'];
                    foreach($cats as $cat): ?>
                        <a 
                            href="index.php?cat=<?php echo $cat; ?>" 
                            onclick="event.preventDefault(); loadPage(this.href)"
                            class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?php echo (strtolower($current_cat) === strtolower($cat)) ? 'bg-latin-start text-white shadow-lg' : 'hover:bg-white/5 text-radio-gray'; ?>"
                        >
                            <?php echo $cat; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- GRID PRINCIPAL -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-16">
                <!-- COLUMNA NOTICIAS (3/4) -->
                <div class="lg:col-span-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 h-fit mb-16">
                        <?php if (empty($news)): ?>
                            <div class="col-span-2 py-40 text-center glass rounded-[40px]">
                                <p class="text-radio-gray text-sm font-black uppercase tracking-widest">No hay noticias en esta categoría</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php foreach ($news as $n): ?>
                            <article 
                                onclick="spaNavigate('<?php echo $n['id']; ?>')"
                                class="group relative bg-[#0d0d0f] border border-white/5 rounded-[50px] overflow-hidden hover:border-latin-start/40 transition-all duration-700 cursor-pointer flex flex-col h-full shadow-2xl"
                            >
                                <div class="aspect-[16/10] overflow-hidden relative">
                                    <div class="absolute inset-0 bg-latin-start/0 group-hover:bg-latin-start/10 transition-colors z-10"></div>
                                    <img src="<?php echo $n['image']; ?>" alt="<?php echo $n['title']; ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                                </div>
                                <div class="p-10 flex flex-col flex-grow relative">
                                    <div class="flex justify-between items-center mb-6">
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] px-4 py-1.5 rounded-full bg-white/5 text-white/60 border border-white/10"><?php echo $n['category']; ?></span>
                                        <span class="text-[10px] font-bold text-radio-gray"><?php echo format_date($n['date']); ?></span>
                                    </div>
                                    <h3 class="text-2xl font-black mb-5 leading-tight group-hover:text-latin-start transition-colors uppercase tracking-tight italic"><?php echo $n['title']; ?></h3>
                                    <p class="text-radio-gray text-sm line-clamp-2 mb-8 font-medium leading-relaxed flex-grow"><?php echo $n['summary']; ?></p>
                                    <div class="mt-auto pt-6 border-t border-white/5 flex items-center justify-between">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-white/40 group-hover:text-latin-start transition-all">Seguir Leyendo</span>
                                        <div class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center group-hover:border-latin-start transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-latin-start group-hover:translate-x-1 transition-transform"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- PAGINACIÓN & VER TODAS -->
                    <?php if ($total_filtered_news > 0): ?>
                        <div class="flex flex-col md:flex-row items-center justify-between gap-8 pt-12 border-t border-white/5">
                            <!-- Botón Ver Todas -->
                            <?php if (!$show_all && $total_filtered_news > $items_per_page): ?>
                                <a href="index.php?cat=<?php echo $current_cat; ?>&show_all=1" 
                                   onclick="event.preventDefault(); loadPage(this.href)"
                                   class="px-8 py-4 rounded-2xl bg-white/5 border border-white/10 hover:border-latin-start/40 hover:bg-white/10 text-[10px] font-black uppercase tracking-widest transition-all text-white group flex items-center gap-3">
                                    <span>Ver todas las noticias</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform"><polyline points="7 13 12 18 17 13"></polyline><polyline points="7 6 12 11 17 6"></polyline></svg>
                                </a>
                            <?php elseif ($show_all): ?>
                                <a href="index.php?cat=<?php echo $current_cat; ?>&page=1" 
                                   onclick="event.preventDefault(); loadPage(this.href)"
                                   class="px-8 py-4 rounded-2xl bg-latin-start/20 border border-latin-start/40 hover:bg-latin-start/30 text-[10px] font-black uppercase tracking-widest transition-all text-latin-start group flex items-center gap-3">
                                    <span>Volver a paginación</span>
                                </a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>

                            <!-- Números de Página -->
                            <?php if (!$show_all && $total_pages > 1): ?>
                                <div class="flex items-center gap-2">
                                    <!-- Prev -->
                                    <?php if ($current_page > 1): ?>
                                        <a href="index.php?cat=<?php echo $current_cat; ?>&page=<?php echo $current_page - 1; ?>" 
                                           onclick="event.preventDefault(); loadPage(this.href)"
                                           class="w-12 h-12 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:border-latin-start text-radio-gray hover:text-latin-start transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                        </a>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <a href="index.php?cat=<?php echo $current_cat; ?>&page=<?php echo $i; ?>" 
                                           onclick="event.preventDefault(); loadPage(this.href)"
                                           class="w-12 h-12 flex items-center justify-center rounded-xl font-black text-sm transition-all <?php echo ($i === $current_page) ? 'bg-latin-start text-white shadow-lg shadow-latin-start/20' : 'bg-white/5 border border-white/10 text-radio-gray hover:border-latin-start hover:text-white'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <!-- Next -->
                                    <?php if ($current_page < $total_pages): ?>
                                        <a href="index.php?cat=<?php echo $current_cat; ?>&page=<?php echo $current_page + 1; ?>" 
                                           onclick="event.preventDefault(); loadPage(this.href)"
                                           class="w-12 h-12 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:border-latin-start text-radio-gray hover:text-latin-start transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- COLUMNA PUBLICIDAD (1/4) -->
                <div>
                    <?php include __DIR__ . '/sidebar.php'; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- LIVE SECTION -->
    <section class="py-40 px-6 bg-[#050505]">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-5xl md:text-8xl font-black mb-16 tracking-tighter uppercase leading-none italic">RADIO <span class="gradient-text">TV LIVE</span></h2>
            <div class="rounded-[60px] overflow-hidden glass aspect-video shadow-[0_0_150px_rgba(255,0,128,0.15)] border border-white/10 relative group">
                <div class="absolute inset-0 border-[20px] border-white/5 rounded-[60px] pointer-events-none z-10 transition-all group-hover:border-latin-start/10"></div>
                <iframe 
                    width="100%" 
                    height="100%" 
                    src="https://www.youtube.com/embed/<?php echo $config['youtubeLiveId']; ?>?autoplay=0&mute=1" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    <!-- SOBRE NOSOTROS (MODERNO) -->
    <section id="nosotros" class="py-40 px-6 relative overflow-hidden bg-radio-black">
        <!-- Glow effects -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-latin-start/5 rounded-full blur-[150px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                <!-- Columna Texto -->
                <div>
                    <div class="flex items-center gap-4 mb-8 text-latin-start">
                        <div class="w-12 h-[2px] bg-current"></div>
                        <span class="text-sm font-black uppercase tracking-[0.4em]">La Esencia de LATIN MIX</span>
                    </div>
                    <h2 class="text-5xl md:text-7xl font-black mb-12 tracking-tighter uppercase leading-none italic">Más que una Radio, <br> <span class="gradient-text">Somos un Movimiento</span></h2>
                    <p class="text-radio-gray text-xl mb-12 leading-relaxed font-medium">Nacimos en el corazón de la cultura latina para ser el puente entre los ritmos que nos definen y el mundo que nos escucha. En LATIN MIX, la música no solo suena, se vive y se siente.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <div class="glass p-8 rounded-3xl border-white/5 hover:border-latin-start/30 transition-all group">
                            <h4 class="text-latin-start font-black mb-4 uppercase text-xs tracking-widest">Nuestra Visión</h4>
                            <p class="text-radio-gray text-sm leading-relaxed">Ser la señal líder que une voces en todo el continente bajo el ritmo de nuestra identidad latina.</p>
                        </div>
                        <div class="glass p-8 rounded-3xl border-white/5 hover:border-latin-start/30 transition-all group">
                            <h4 class="text-latin-start font-black mb-4 uppercase text-xs tracking-widest">Nuestra Voz</h4>
                            <p class="text-radio-gray text-sm leading-relaxed">Informar, entretener y conectar con la pasión que nos caracteriza, 24 horas al día, 7 días a la semana.</p>
                        </div>
                    </div>
                </div>

                <!-- Columna Visual/Imagen -->
                <div class="relative group">
                    <div class="absolute -inset-4 bg-latin-start/10 rounded-[60px] blur-3xl group-hover:bg-latin-start/20 transition-all"></div>
                    <div class="relative aspect-square rounded-[60px] overflow-hidden glass border-white/10 flex items-center justify-center p-20 shadow-2xl">
                        <img src="assets/logo.png" class="w-full h-auto object-contain animate-float" alt="Sobre Nosotros LatinMix">
                        
                        <!-- Mini Stats Overlay -->
                        <div class="absolute bottom-12 left-1/2 -translate-x-1/2 flex gap-4 w-full justify-center px-12">
                            <div class="glass px-8 py-4 rounded-2xl text-center border-white/5 flex-initial min-w-[140px] scale-90 md:scale-100">
                                <span class="block text-2xl font-black gradient-text tracking-tighter">24/7</span>
                                <span class="text-[8px] font-black uppercase tracking-widest text-radio-gray">Digital & HD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>
</main>
