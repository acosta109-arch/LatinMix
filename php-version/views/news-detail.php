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
                    
                    <!-- Cuerpo de la noticia simulado (ya que en el JSON solo hay resumen por ahora) -->
                    <div class="space-y-8 text-radio-gray">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                        
                        <blockquote class="bg-white/5 p-10 rounded-3xl border border-white/10 italic text-white text-2xl font-black tracking-tight leading-snug">
                            "La música latina está viviendo su mejor momento histórico, rompiendo fronteras y uniendo corazones en todo el globo."
                        </blockquote>

                        <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                        
                        <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
                    </div>

                    <!-- Footer Noticia -->
                    <div class="mt-20 pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-10">
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-black uppercase tracking-widest text-white">Compartir:</span>
                            <div class="flex gap-2">
                                <button class="w-10 h-10 rounded-xl glass hover:bg-latin-start/20 hover:text-latin-start transition-all flex items-center justify-center">FB</button>
                                <button class="w-10 h-10 rounded-xl glass hover:bg-latin-start/20 hover:text-latin-start transition-all flex items-center justify-center">TW</button>
                                <button class="w-10 h-10 rounded-xl glass hover:bg-latin-start/20 hover:text-latin-start transition-all flex items-center justify-center">WA</button>
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
