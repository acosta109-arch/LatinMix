<?php
/**
 * VISTA: BARRA LATERAL (PUBLICIDAD)
 */
require_once __DIR__ . '/../includes/functions.php';
$all_ads = load_data('radio_ads');
$active_ads = get_active_ads($all_ads);
?>
<aside class="lg:col-span-1 space-y-8 h-fit lg:sticky lg:top-28">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-8 h-[2px] bg-latin-start"></div>
        <h2 class="text-xs font-black uppercase tracking-[0.3em] text-latin-start">Publicidad</h2>
    </div>

    <?php if (empty($active_ads)): ?>
        <div class="p-8 border border-white/5 bg-white/[0.02] rounded-3xl text-center">
            <p class="text-xs text-radio-gray font-medium italic">Espacio publicitario disponible</p>
        </div>
    <?php endif; ?>

    <?php foreach ($active_ads as $ad): ?>
        <div class="group block overflow-hidden rounded-3xl bg-white/5 border border-white/5 hover:border-latin-start/30 transition-all duration-500">
            <img src="<?php echo $ad['imageUrl']; ?>" alt="Publicidad" class="w-full h-auto block transition-transform duration-1000 group-hover:scale-105">
        </div>
    <?php endforeach; ?>

    <!-- Social Media Call to Action -->
    <div class="p-8 bg-gradient-to-br from-latin-start/10 to-latin-end/10 border border-white/5 rounded-3xl">
        <h4 class="text-sm font-bold mb-4 uppercase tracking-tighter italic">Únete a la Comunidad</h4>
        <p class="text-[11px] text-radio-gray mb-6 leading-relaxed">No te pierdas ninguna novedad. Sintoniza y vibra con nosotros en todas partes.</p>
        <div class="flex gap-3">
             <a href="<?php echo $config['facebookUrl']; ?>" target="_blank" class="w-8 h-8 rounded-lg glass flex items-center justify-center hover:bg-latin-start hover:text-white transition-all group" title="Facebook">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
             </a>
             <a href="<?php echo $config['instagramUrl']; ?>" target="_blank" class="w-8 h-8 rounded-lg glass flex items-center justify-center hover:bg-latin-start hover:text-white transition-all group" title="Instagram">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
             </a>
             <a href="<?php echo $config['tiktokUrl']; ?>" target="_blank" class="w-8 h-8 rounded-lg glass flex items-center justify-center hover:bg-latin-start hover:text-white transition-all group" title="TikTok">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>
             </a>
             <a href="https://youtube.com" target="_blank" class="w-8 h-8 rounded-lg glass flex items-center justify-center hover:bg-latin-start hover:text-white transition-all group" title="YouTube">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-1.94C18.88 4 12 4 12 4s-6.88 0-8.6.48a2.78 2.78 0 0 0-1.94 1.94C1 8.11 1 12 1 12s0 3.89.46 5.58a2.78 2.78 0 0 0 1.94 1.94c1.72.48 8.6.48 8.6.48s6.88 0 8.6-.48a2.78 2.78 0 0 0 1.94-1.94C23 15.89 23 12 23 12s0-3.89-.46-5.58z"></path><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon></svg>
             </a>
        </div>
    </div>
</aside>
