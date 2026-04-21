<?php
/**
 * VISTA: DETALLE DE NOTICIA (CON BARRA LATERAL)
 */
require_once __DIR__ . '/../includes/functions.php';

$id = $_GET['news_id'] ?? '';
$news_list = load_data('radio_news');
$current_news = null;

foreach ($news_list as $n) {
    if ($n['id'] === $id) {
        $current_news = $n;
        break;
    }
}

if (!$current_news) {
    echo "<div class='p-20 text-center text-radio-gray italic'>Noticia no encontrada. <a href='index.php' class='text-latin-start font-bold'>Volver al inicio</a></div>";
    return;
}
?>
<main id="main-content" class="pt-32 pb-56 px-6 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- HEADER NOTICIA -->
        <div class="mb-12">
            <a href="index.php" class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-latin-start hover:text-white transition-all mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m11 17-5-5 5-5"></path><path d="M18 17l-5-5 5-5"></path></svg>
                Volver a Noticias
            </a>
            
            <div class="flex items-center gap-4 mb-6">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] px-4 py-1.5 rounded-full bg-latin-start/10 text-latin-start border border-latin-start/20"><?php echo $current_news['category']; ?></span>
                <span class="text-[10px] font-bold text-radio-gray"><?php echo format_date($current_news['date']); ?></span>
            </div>
            
            <h1 class="text-4xl md:text-7xl font-black tracking-tighter leading-none mb-10 max-w-4xl uppercase italic"><?php echo $current_news['title']; ?></h1>
        </div>

        <!-- GRID LAYOUT -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-16">
            <!-- CONTENIDO PRINCIPAL (3/4) -->
            <div class="lg:col-span-3">
                <div class="rounded-[50px] overflow-hidden shadow-2xl mb-12 border border-white/5 aspect-video">
                    <img src="<?php echo $current_news['image']; ?>" alt="<?php echo $current_news['title']; ?>" class="w-full h-full object-cover">
                </div>

                <div class="prose prose-invert prose-lg max-w-none prose-p:text-radio-gray prose-p:leading-[1.8] prose-p:text-lg">
                    <p class="font-bold text-white text-xl mb-10 border-l-4 border-latin-start pl-8 italic">
                        <?php echo $current_news['summary']; ?>
                    </p>
                    
                    <!-- Cuerpo de la noticia -->
                    <div class="space-y-8 text-radio-gray">
                        <p>
                            <?php echo $current_news['summary']; ?>
                        </p>
                        
                        <blockquote class="bg-white/5 p-10 rounded-3xl border border-white/10 italic text-white text-2xl font-black tracking-tight leading-snug">
                            "En Latin Mix, conectamos tus sentidos con la mejor información y el ritmo que define nuestra cultura."
                        </blockquote>

                        <p>Mantente en sintonía para más actualizaciones sobre <?php echo $current_news['category']; ?> y todo lo que sucede en el mundo de la música latina. En Latin Mix, somos ¡LA VOZ QUE NOS UNE!</p>
                    </div>

                    <!-- Footer Noticia -->
                    <div class="mt-20 pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-10">
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-black uppercase tracking-widest text-white">Compartir:</span>
                            <div class="flex gap-2">
                                <button title="Compartir en Facebook" class="w-10 h-10 rounded-xl glass hover:bg-latin-start/20 hover:text-latin-start transition-all flex items-center justify-center">
                                    <i class="bi bi-facebook text-lg"></i>
                                </button>
                                <button title="Compartir en X" class="w-10 h-10 rounded-xl glass hover:bg-latin-start/20 hover:text-latin-start transition-all flex items-center justify-center">
                                    <i class="bi bi-twitter-x text-lg"></i>
                                </button>
                                <button title="Compartir en WhatsApp" class="w-10 h-10 rounded-xl glass hover:bg-latin-start/20 hover:text-latin-start transition-all flex items-center justify-center">
                                    <i class="bi bi-whatsapp text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-radio-gray">Etiquetas: #LATINMIX #RADIO #MUSICA #ACTUALIDAD</p>
                    </div>
                </div>
            </div>

            <!-- PUBLICIDAD (1/4) -->
            <?php include __DIR__ . '/sidebar.php'; ?>
        </div>
    </div>
</main>
