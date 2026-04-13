<?php
/**
 * PÁGINA PRINCIPAL - LATINMIX RADIO
 * Versión PHP Profesional 8+
 */
require_once 'includes/functions.php';

// Cargar Configuración y Datos
$config = load_data('radio_config');
$id_noticia = $_GET['news_id'] ?? null;
$category = $_GET['cat'] ?? null;

// DETECCIÓN AJAX PARA SPA-LITE
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    if (!empty($id_noticia)) {
        include 'views/news-detail.php';
    } else {
        include 'views/home-content.php';
    }
    exit;
}

// SEO Meta Dinámico
$site_title = "LATIN MIX Radio | La Emisora #1 de Música Latina";
$site_description = "Sintoniza los mejores éxitos latinos, noticias de entretenimiento y streaming en vivo las 24 horas.";

if (!empty($id_noticia)) {
    $news_list = load_data('radio_news');
    foreach ($news_list as $n) {
        if ($n['id'] === $id_noticia) {
            $site_title = $n['title'] . " | LATIN MIX Radio";
            $site_description = $n['summary'];
            break;
        }
    }
}

// AJAX SPA-lite: Si se pide solo el contenido, devolver la vista correspondiente
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    if (!empty($id_noticia)) {
        include 'views/news-detail.php';
    } else {
        include 'views/home-content.php';
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <title><?php echo $site_title; ?></title>
    <meta name="description" content="<?php echo $site_description; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo BASE_URL; ?>">
    <link rel="icon" type="image/png" href="assets/logo.png">

    <!-- Estilos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        'latin-start': '#f5961e',
                        'latin-end': '#ffe632',
                        'mix-start': '#d22882',
                        'mix-end': '#781496',
                        'radio-red': '#e61e1e',
                        'radio-silver': '#bebebe',
                        'radio-black': '#0a0a0b',
                        'radio-gray': '#bebebe'
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .glass {
            background: rgba(5, 5, 5, 0.82);
            backdrop-filter: blur(25px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .gradient-text {
            background: linear-gradient(to right, #f5961e, #ffe632, #d22882);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-latin {
            background: linear-gradient(to right, #f5961e, #ffe632);
        }

        .gradient-mix {
            background: linear-gradient(to right, #d22882, #781496);
        }

        .btn-premium {
            background: linear-gradient(to right, #f5961e, #ffe632);
            color: #000;
            font-weight: 900;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-premium:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
            box-shadow: 0 0 40px rgba(245, 150, 30, 0.3);
        }

        body {
            background-color: #0a0a0b;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #0a0a0b;
        }

        ::-webkit-scrollbar-thumb {
            background: #222;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #333;
        }

        .nav-item {
            position: relative;
        }

        .nav-item::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            h-0.5;
            background: #ff0080;
            transition: width 0.3s;
        }

        .nav-item:hover::after {
            width: 100%;
        }

        @keyframes progress {
            0% {
                width: 0;
            }

            100% {
                width: 100%;
            }
        }

        .animate-progress {
            animation: progress 3s linear forwards;
        }

        @keyframes scale-pulse {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }

            50% {
                transform: scale(1.05);
                opacity: 1;
            }

            100% {
                transform: scale(1);
            }
        }

        .animate-splash {
            animation: scale-pulse 1.5s ease-out forwards;
        }
    </style>
</head>

<body class="text-white selection:bg-latin-start/30"
    x-data="{ mobileMenu: false, newsDropdown: false, showSplash: true }"
    x-init="setTimeout(() => showSplash = false, 3500)">

    <!-- SPLASH SCREEN (HIGH QUALITY) -->
    <div x-show="showSplash" x-transition:leave="transition ease-in duration-1000"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-110 blur-xl"
        class="fixed inset-0 z-[10000] bg-[#020203] flex flex-col items-center justify-center p-6 overflow-hidden"
        x-data="{ progress: 0 }"
        x-init="let interval = setInterval(() => { if(progress < 100) progress += 1; else clearInterval(interval); }, 30); setTimeout(() => showSplash = false, 4000)">
        <!-- Intense Background Aura -->
        <div class="absolute inset-0 bg-gradient-to-tr from-mix-end/10 via-transparent to-latin-start/10 animate-pulse">
        </div>
        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-latin-start/5 rounded-full blur-[150px]">
        </div>

        <!-- Floating Particles Decoration -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-[20%] left-[10%] text-2xl animate-drift">♪</div>
            <div class="absolute bottom-[30%] right-[15%] text-xl animate-drift-mid">♫</div>
        </div>

        <div class="relative flex flex-col items-center">
            <!-- Logo with Shine effect -->
            <div class="relative group">
                <div
                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent skew-x-[-30deg] -translate-x-[200%] animate-shine pointer-events-none z-20">
                </div>
                <img src="assets/logo.png"
                    class="w-72 md:w-80 h-auto relative z-10 drop-shadow-[0_0_50px_rgba(245,150,30,0.2)]"
                    alt="LatinMix Pro Logo">
            </div>

            <!-- Loading Interface -->
            <div class="mt-20 w-64 text-center">
                <!-- Bar Container -->
                <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-latin-start via-latin-end to-mix-start transition-all duration-300 ease-out"
                        :style="`width: ${progress}%` "></div>
                </div>

                <!-- Status & Percentage -->
                <div class="flex justify-between items-center mt-6">
                    <span
                        class="text-[9px] font-black uppercase tracking-[0.4em] text-radio-gray flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-latin-start animate-pulse"></span>
                        Calibrando Señal
                    </span>
                    <span class="text-xs font-black tabular-nums gradient-text" x-text="`${progress}%` "></span>
                </div>
            </div>

            <!-- Equallizer Animation -->
            <div class="flex items-end gap-1.5 h-16 mt-16 opacity-30">
                <div class="w-1.5 bg-latin-start animate-eq-1 rounded-full"></div>
                <div class="w-1.5 bg-mix-start animate-eq-2 rounded-full"></div>
                <div class="w-1.5 bg-latin-end animate-eq-3 rounded-full"></div>
                <div class="w-1.5 bg-mix-end animate-eq-1 rounded-full"></div>
                <div class="w-1.5 bg-latin-start animate-eq-2 rounded-full"></div>
                <div class="w-1.5 bg-latin-end animate-eq-3 rounded-full"></div>
            </div>
        </div>

        <!-- Tagline -->
        <div class="absolute bottom-12 text-center">
            <p class="text-[8px] font-black uppercase tracking-[0.6em] text-radio-gray opacity-40">LATIN MIX Radio -
                Elevando tu sintonía</p>
        </div>
    </div>

    <style>
        @keyframes shine {
            from {
                transform: translateX(-200%) skewX(-30deg);
            }

            to {
                transform: translateX(200%) skewX(-30deg);
            }
        }

        .animate-shine {
            animation: shine 3s infinite;
        }

        @keyframes eq {

            0%,
            100% {
                height: 20%;
            }

            50% {
                height: 100%;
            }
        }

        .animate-eq-1 {
            animation: eq 0.8s ease-in-out infinite;
        }

        .animate-eq-2 {
            animation: eq 1.2s ease-in-out infinite 0.2s;
        }

        .animate-eq-3 {
            animation: eq 1s ease-in-out infinite 0.4s;
        }
    </style>

    <!-- NAVBAR -->
    <nav class="fixed top-0 w-full z-[9999] glass">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center h-20">
            <div class="flex items-center gap-2 cursor-pointer" onclick="window.location.href='index.php'">
                <img src="assets/logo.png" class="h-12 md:h-16 w-auto" alt="Logo">
            </div>

            <div class="hidden md:flex items-center gap-10 text-xs font-black uppercase tracking-[0.2em]">
                <a href="index.php" class="nav-item hover:text-latin-start transition-all">Inicio</a>

                <!-- DROPDOWN NOTICIAS -->
                <div class="relative nav-item" @mouseenter="newsDropdown = true" @mouseleave="newsDropdown = false">
                    <button class="flex items-center gap-1 hover:text-latin-start transition-all uppercase">
                        Noticias
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                            :class="newsDropdown ? 'rotate-180' : ''" class="transition-transform">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div x-show="newsDropdown" @click.away="newsDropdown = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute top-full left-1/2 -translate-x-1/2 mt-4 w-48 glass rounded-2xl p-4 shadow-2xl space-y-2"
                        x-cloak>
                        <a href="index.php?cat=Local"
                            class="block py-2 px-4 rounded-xl hover:bg-latin-start/20 hover:text-latin-start transition-all">Locales</a>
                        <a href="index.php?cat=Nacional"
                            class="block py-2 px-4 rounded-xl hover:bg-latin-start/20 hover:text-latin-start transition-all">Nacionales</a>
                        <a href="index.php?cat=Internacional"
                            class="block py-2 px-4 rounded-xl hover:bg-latin-start/20 hover:text-latin-start transition-all">Internacionales</a>
                        <a href="index.php?cat=Deportes"
                            class="block py-2 px-4 rounded-xl hover:bg-latin-start/20 hover:text-latin-start transition-all">Deportes</a>
                        <a href="index.php?cat=Arte y Cultura"
                            class="block py-2 px-4 rounded-xl hover:bg-latin-start/20 hover:text-latin-start transition-all">Arte y Cultura</a>
                    </div>
                </div>

                <a href="index.php#nosotros" class="nav-item hover:text-latin-start transition-all">Sobre Nosotros</a>
                <a href="login.php"
                    class="px-6 py-3 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-latin-start/40 transition-all text-white">Login
                </a>
            </div>

            <button @click="mobileMenu = !mobileMenu" class="md:hidden text-white p-2">
                <svg x-show="!mobileMenu" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" x2="20" y1="12" y2="12"></line>
                    <line x1="4" x2="20" y1="6" y2="6"></line>
                    <line x1="4" x2="20" y1="18" y2="18"></line>
                </svg>
                <svg x-show="mobileMenu" x-cloak xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" x2="6" y1="6" y2="18"></line>
                    <line x1="6" x2="18" y1="6" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-transition x-cloak
            class="md:hidden glass absolute top-full left-0 w-full p-8 space-y-6 font-black uppercase tracking-widest flex flex-col items-center"
            x-data="{ mobNews: false }">
            <a href="index.php" @click="mobileMenu = false">Inicio</a>

            <!-- ACORDEON MOBILE NOTICIAS -->
            <div class="w-full flex flex-col items-center">
                <button @click="mobNews = !mobNews"
                    class="flex items-center gap-2 hover:text-latin-start transition-all uppercase">
                    Noticias
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                        :class="mobNews ? 'rotate-180' : ''" class="transition-transform">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div x-show="mobNews" x-collapse
                    class="w-full flex flex-col items-center mt-4 space-y-4 text-radio-gray text-xs">
                    <a href="index.php?cat=Local" @click="mobileMenu = false">Locales</a>
                    <a href="index.php?cat=Nacional" @click="mobileMenu = false">Nacionales</a>
                    <a href="index.php?cat=Internacional" @click="mobileMenu = false">Internacionales</a>
                    <a href="index.php?cat=Deportes" @click="mobileMenu = false">Deportes</a>
                    <a href="index.php?cat=Arte y Cultura" @click="mobileMenu = false">Arte y Cultura</a>
                </div>
            </div>

            <a href="index.php#nosotros" @click="mobileMenu = false">Nosotros</a>
            <a href="login.php" class="text-latin-start">Login </a>
        </div>
    </nav>

    <!-- CONTENT WRAPPER -->
    <div id="content-area" class="pt-20">
        <?php
        if (!empty($id_noticia)) {
            include 'views/news-detail.php';
        } else {
            include 'views/home-content.php';
        }
        ?>
    </div>

    <!-- FOOTER -->
    <footer id="nosotros" class="bg-[#050505] border-t border-white/5 pt-32 pb-60 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-20">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-8">
                    <img src="assets/logo.png" class="h-20 w-auto" alt="Logo">
                </div>
                <p class="text-radio-gray text-base max-w-sm mb-12 leading-loose">Somos el latido de la música latina.
                    Conectando culturas a través de los sonidos que nos hacen vibrar en toda Latinoamérica y el mundo.
                </p>
                <div class="flex gap-4">
                    <a href="<?php echo $config['facebookUrl']; ?>" target="_blank"
                        class="w-12 h-12 rounded-2xl glass flex items-center justify-center hover:bg-latin-start hover:text-white transition-all group"
                        title="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="<?php echo $config['instagramUrl']; ?>" target="_blank"
                        class="w-12 h-12 rounded-2xl glass flex items-center justify-center hover:bg-latin-start hover:text-white transition-all group"
                        title="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="<?php echo $config['tiktokUrl']; ?>" target="_blank"
                        class="w-12 h-12 rounded-2xl glass flex items-center justify-center hover:bg-latin-start hover:text-white transition-all group"
                        title="TikTok">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path>
                        </svg>
                    </a>
                    <a href="https://youtube.com" target="_blank"
                        class="w-12 h-12 rounded-2xl glass flex items-center justify-center hover:bg-latin-start hover:text-white transition-all group"
                        title="YouTube">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22.54 6.42a2.78 2.78 0 0 0-1.94-1.94C18.88 4 12 4 12 4s-6.88 0-8.6.48a2.78 2.78 0 0 0-1.94 1.94C1 8.11 1 12 1 12s0 3.89.46 5.58a2.78 2.78 0 0 0 1.94 1.94c1.72.48 8.6.48 8.6.48s6.88 0 8.6-.48a2.78 2.78 0 0 0 1.94-1.94C23 15.89 23 12 23 12s0-3.89-.46-5.58z">
                            </path>
                            <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon>
                        </svg>
                    </a>
                </div>
            </div>

            <div>
                <h4
                    class="font-black uppercase tracking-[0.3em] text-[10px] mb-10 text-white border-l-2 border-latin-start pl-4">
                    Navegación</h4>
                <ul class="space-y-6 text-[11px] font-black uppercase tracking-widest text-radio-gray">
                    <li><a href="index.php" class="hover:text-white transition-colors">Inicio</a></li>
                    <li><a href="index.php#noticias" class="hover:text-white transition-colors">Noticias</a></li>
                    <li><a href="#nosotros" class="hover:text-white transition-colors">Sobre Nosotros</a></li>
                    <li><a href="login.php" class="hover:text-white transition-colors">Login</a></li>
                </ul>
            </div>

            <div>
                <h4
                    class="font-black uppercase tracking-[0.3em] text-[10px] mb-10 text-white border-l-2 border-latin-start pl-4">
                    Legal & Contacto</h4>
                <ul class="space-y-6 text-[11px] font-black uppercase tracking-widest text-radio-gray">
                    <li><a href="#" class="hover:text-white transition-colors">Privacidad</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Contacto</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Publicidad</a></li>
                </ul>
            </div>
        </div>
        <div
            class="max-w-7xl mx-auto mt-32 pt-12 border-t border-white/5 text-center text-[10px] font-black uppercase tracking-[0.5em] text-radio-gray">
            © 2026 <span class="gradient-text">LATIN MIX RADIO</span>. LA EMISORA MÁS POTENTE DEL CONTINENTE.
        </div>
    </footer>

    <!-- PREMIUM PERSISTENT PLAYER -->
    <div id="radio-player-bar"
        class="fixed bottom-0 left-0 w-full z-[200] glass px-6 py-4 md:px-12 md:py-8 h-24 md:h-32 flex items-center justify-between gap-6 overflow-hidden border-t border-white/10"
        x-data="{ 
            isPlaying: false, 
            volume: 80,
            audio: null,
            showDetails: false,
            init() {
                this.audio = new Audio('<?php echo $config['streamUrl']; ?>');
                this.audio.volume = this.volume / 100;
                
                // Forzar play inmediatamente al cargar (algunos navegadores lo permitirán si hay sesión previa)
                setTimeout(() => {
                    this.toggle();
                }, 100);

                // Fallback por si el navegador bloquea el autoplay inicial
                window.addEventListener('click', () => {
                   if(!this.isPlaying) {
                       this.toggle();
                   }
                }, { once: true });
            },
            toggle() {
                if(this.isPlaying) {
                    this.audio.pause();
                    this.isPlaying = false;
                    localStorage.setItem('radio_paused', 'true');
                } else {
                    this.audio.play().then(() => {
                        this.isPlaying = true;
                        localStorage.removeItem('radio_paused');
                    }).catch(e => {
                        console.log('Interacción requerida para audio');
                        this.isPlaying = false;
                    });
                }
            }
        }" x-init="init()" x-cloak>
        <!-- Background Glow -->
        <div class="absolute -left-20 top-0 w-64 h-full bg-latin-start/5 blur-3xl pointer-events-none"></div>

        <div class="flex items-center gap-4 md:gap-8 flex-1 min-w-0 relative z-10">
            <!-- Pulsating Album Art / Icon -->
            <div class="relative group">
                <div class="absolute -inset-2 bg-gradient-to-r from-latin-start to-latin-end rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"
                    :class="isPlaying ? 'animate-pulse' : ''"></div>
                <div
                    class="relative w-12 md:w-16 h-12 md:h-16 rounded-2xl bg-[#0d0d0f] border border-white/10 flex items-center justify-center overflow-hidden">
                    <img src="assets/logo.png" class="w-8 md:w-10 h-auto opacity-80" alt="LatinMix Icon">
                    <!-- Audio Wave Animation -->
                    <div x-show="isPlaying"
                        class="absolute inset-0 flex items-center justify-center gap-0.5 bg-black/40">
                        <div class="w-1 h-3 bg-latin-start animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-1 h-5 bg-latin-end animate-bounce" style="animation-delay: 0.3s"></div>
                        <div class="w-1 h-4 bg-latin-start animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>

            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <span class="flex h-2 w-2 relative" x-show="isPlaying">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-latin-start opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-latin-start"></span>
                    </span>
                    <h4
                        class="text-xs md:text-lg font-black tracking-tight leading-none uppercase italic truncate text-white">
                        Sintetizando <span class="gradient-text">LATIN MIX FM</span>
                    </h4>
                </div>
                <p
                    class="text-[9px] md:text-xs text-radio-gray font-black uppercase tracking-[0.2em] mt-2 flex items-center gap-2">
                    <span class="text-latin-start">● LIVE</span>
                    <span class="opacity-40">Digital HD 320kbps</span>
                </p>
            </div>
        </div>

        <!-- Center Controls -->
        <div class="flex flex-col items-center gap-3 relative z-10">
            <button @click="toggle()"
                class="w-14 md:w-20 h-14 md:h-20 rounded-full bg-white text-black flex items-center justify-center hover:scale-110 active:scale-95 transition-all shadow-[0_0_50px_rgba(255,255,255,0.15)] group relative">
                <div class="absolute inset-0 rounded-full bg-white animate-ping opacity-10 group-hover:opacity-20"
                    x-show="isPlaying"></div>
                <svg x-show="!isPlaying" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="currentColor" class="ml-1 md:w-8 md:h-8">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
                <svg x-show="isPlaying" x-cloak xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="currentColor" class="md:w-8 md:h-8">
                    <rect x="6" y="4" width="4" height="16"></rect>
                    <rect x="14" y="4" width="4" height="16"></rect>
                </svg>
            </button>
        </div>

        <!-- Right Side: Volume & Extra -->
        <div class="hidden md:flex items-center justify-end gap-8 flex-1 relative z-10">
            <div class="flex items-center gap-4 min-w-[150px]">
                <i class="bi bi-volume-up text-radio-gray text-lg"></i>
                <input type="range" min="0" max="100" x-model="volume" @input="audio.volume = $el.value/100"
                    class="w-full h-1.5 bg-white/10 rounded-full appearance-none cursor-pointer accent-latin-start hover:accent-latin-end transition-all">
            </div>


        </div>

        <!-- MODAL AZURA CAST (OVERLAY) -->
        <div x-show="showDetails" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-10"
            class="fixed inset-0 z-[300] flex items-center justify-center p-6 bg-black/80 backdrop-blur-sm" x-cloak>
            <div
                class="bg-[#151515] w-full max-w-2xl rounded-[40px] overflow-hidden border border-white/10 shadow-2xl relative">
                <button @click="showDetails = false"
                    class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-red-500 transition-all z-20">
                    <i class="bi bi-x-lg text-white"></i>
                </button>

                <div class="p-4 bg-[#212121]">
                    <template x-if="showDetails">
                        <iframe src="<?php echo $config['azuraCastUrl']; ?>" frameborder="0" allowtransparency="true"
                            style="width: 100%; min-height: 450px; border: 0;" allow="autoplay; encrypted-media">
                        </iframe>
                    </template>
                </div>

                <div class="p-8 text-center bg-black/40">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-radio-gray">Estadísticas en Tiempo
                        Real via AzuraCast</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SPA-LITE NAVIGATION SCRIPT -->
    <script>
        document.addEventListener('click', e => {
            const link = e.target.closest('a');

            if (link && link.href && link.href.startsWith(window.location.origin) && !link.target && !link.href.includes('admin.php') && !link.href.includes('login.php')) {
                // Si es un ancla a la misma sección de la página actual, dejar comportamiento nativo
                if (link.hash && link.pathname === window.location.pathname) {
                    return;
                }

                e.preventDefault();
                loadPage(link.href);
            }
        });

        window.onpopstate = () => loadPage(window.location.href, false);

        function loadPage(url, push = true) {
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => {
                    const contentArea = document.getElementById('content-area');
                    if (contentArea) {
                        contentArea.innerHTML = html;
                        if (push) window.history.pushState(null, '', url);

                        if (url.includes('cat=')) {
                            setTimeout(() => {
                                const target = document.getElementById('noticias');
                                if (target) target.scrollIntoView({ behavior: 'smooth' });
                            }, 50);
                        } else if (url.includes('#nosotros')) {
                            setTimeout(() => {
                                const target = document.getElementById('nosotros');
                                if (target) target.scrollIntoView({ behavior: 'smooth' });
                            }, 50);
                        } else {
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    } else {
                        window.location.href = url;
                    }
                });
        }

        window.spaNavigate = (id) => {
            loadPage('index.php?news_id=' + id);
        };
    </script>
</body>

</html>