<?php
/**
 * PANEL DE CONTROL ADMINISTRATIVO
 */
require_once __DIR__ . '/includes/functions.php';

// Verificar sesión
if (!is_logged_in()) {
    redirect('login.php');
}

$message = '';
$error = '';

// Cargar Datos
$config = load_data('radio_config');
$ads = load_data('radio_ads');
$news = load_data('radio_news');
$users_list = load_data('radio_users');

// Manejar GET para data reactiva
if (isset($_GET['get_users'])) {
    header('Content-Type: application/json');
    echo json_encode(load_data('radio_users'));
    exit;
}

// Manejar POST (Actualizaciones)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf_token'] ?? '')) {
        $error = "Error de validación (CSRF). Inténtalo de nuevo.";
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'save_config':
                $config['azuraCastUrl'] = clean_input($_POST['azuraCastUrl']);
                $config['streamUrl'] = clean_input($_POST['streamUrl']);
                $config['youtubeLiveId'] = clean_input($_POST['youtubeLiveId']);
                $config['facebookUrl'] = clean_input($_POST['facebookUrl']);
                $config['instagramUrl'] = clean_input($_POST['instagramUrl']);
                $config['tiktokUrl'] = clean_input($_POST['tiktokUrl']);
                $config['xUrl'] = clean_input($_POST['xUrl']);
                $config['youtubeChannelUrl'] = clean_input($_POST['youtubeChannelUrl']);
                save_data('radio_config', $config);
                $message = "Configuración guardada correctamente.";
                break;
                
            case 'add_ad':
                $uploaded_path = upload_image($_FILES['adImage'], 'ads');
                if ($uploaded_path) {
                    $newAd = [
                        'id' => uniqid(),
                        'title' => clean_input($_POST['title']),
                        'imageUrl' => $uploaded_path,
                        'active' => isset($_POST['active']),
                        'startDate' => clean_input($_POST['startDate']),
                        'endDate' => clean_input($_POST['endDate']),
                        'createdAt' => date('c')
                    ];
                    $ads[] = $newAd;
                    save_data('radio_ads', $ads);
                    $message = "Publicidad añadida y archivo subido.";
                } else {
                    $error = "Error al subir la imagen publicitaria. Asegúrese de que sea JPG/PNG/WEBP.";
                }
                break;

            case 'edit_ad':
                $id = $_POST['id'];
                foreach($ads as &$ad) {
                    if ($ad['id'] === $id) {
                        $ad['title'] = clean_input($_POST['title']);
                        $ad['active'] = isset($_POST['active']);
                        $ad['startDate'] = clean_input($_POST['startDate']);
                        $ad['endDate'] = clean_input($_POST['endDate']);
                        
                        if (!empty($_FILES['adImage']['name'])) {
                            $uploaded_path = upload_image($_FILES['adImage'], 'ads');
                            if ($uploaded_path) {
                                $ad['imageUrl'] = $uploaded_path;
                            }
                        }
                    }
                }
                save_data('radio_ads', $ads);
                $message = "Publicidad actualizada correctamente.";
                break;
                
            case 'delete_ad':
                $id = $_POST['id'];
                // Opcional: Eliminar archivo físico
                $ads = array_filter($ads, function($ad) use ($id) { return $ad['id'] !== $id; });
                save_data('radio_ads', array_values($ads));
                $message = "Publicidad eliminada.";
                break;

            case 'toggle_ad':
                $id = $_POST['id'];
                foreach($ads as &$ad) {
                    if ($ad['id'] === $id) {
                        $ad['active'] = !$ad['active'];
                    }
                }
                save_data('radio_ads', $ads);
                $message = "Estado cambiado.";
                break;
                
            case 'add_news':
                $uploaded_path = upload_image($_FILES['newsImage'], 'news');
                if ($uploaded_path) {
                    $newNewsItem = [
                        'id' => uniqid(),
                        'title' => clean_input($_POST['title']),
                        'image' => $uploaded_path,
                        'summary' => clean_input($_POST['summary']),
                        'date' => clean_input($_POST['date']),
                        'category' => clean_input($_POST['category']),
                    ];
                    $news[] = $newNewsItem;
                    save_data('radio_news', $news);
                    $message = "Noticia publicada con imagen local.";
                } else {
                    $error = "Error al subir la imagen de la noticia.";
                }
                break;
                
            case 'delete_news':
                $id = $_POST['id'];
                $news = array_filter($news, function($n) use ($id) { return $n['id'] !== $id; });
                save_data('radio_news', array_values($news));
                $message = "Noticia eliminada.";
                break;

            case 'add_user':
                if ($_SESSION['admin_email'] !== 'Admin@latinmix.com') {
                    $error = "Acceso denegado: Solo el administrador principal puede gestionar usuarios.";
                    break;
                }
                $email = strtolower(clean_input($_POST['email']));
                if (!str_ends_with($email, '@latinmix.com')) {
                    $error = "El correo debe terminar exclusivamente en @latinmix.com";
                    break;
                }
                $new_users = load_data('radio_users');
                foreach($new_users as $u) {
                    if (strtolower($u['email']) === $email) {
                        $error = "El correo ya está registrado.";
                        break 2;
                    }
                }
                
                $newUser = [
                    'id' => uniqid('usr_'),
                    'name' => clean_input($_POST['name']),
                    'email' => $email,
                    'password' => $_POST['password'], // Almacenado plano por solicitud: "que se pueda ver"
                    'role' => clean_input($_POST['role']),
                    'status' => 'Active',
                    'createdAt' => date('c')
                ];
                $new_users[] = $newUser;
                save_data('radio_users', $new_users);
                $message = "Usuario gestor añadido correctamente.";
                break;

            case 'edit_user':
                if ($_SESSION['admin_email'] !== 'Admin@latinmix.com') {
                    $error = "Acceso denegado.";
                    break;
                }
                $id = $_POST['id'];
                $edit_users = load_data('radio_users');
                foreach($edit_users as &$u) {
                    if ($u['id'] === $id) {
                        $u['name'] = clean_input($_POST['name']);
                        // No permitimos cambiar el email después de creado para evitar bypass de @latinmix.com si no se valida
                        $u['role'] = clean_input($_POST['role']);
                        if (!empty($_POST['password'])) {
                            $u['password'] = $_POST['password'];
                        }
                    }
                }
                save_data('radio_users', $edit_users);
                $message = "Usuario gestor actualizado.";
                break;

            case 'delete_user':
                if ($_SESSION['admin_email'] !== 'Admin@latinmix.com') {
                    $error = "Solo el Admin principal puede eliminar otros usuarios.";
                    break;
                }
                $id = $_POST['id'];
                if ($id === $_SESSION['admin_id']) {
                    $error = "No puedes eliminar tu propia cuenta.";
                    break;
                }
                $del_users = load_data('radio_users');
                $del_users = array_filter($del_users, function($u) use ($id) { return $u['id'] !== $id; });
                save_data('radio_users', array_values($del_users));
                $message = "Usuario gestor eliminado.";
                break;

            case 'logout':
                session_destroy();
                redirect('index.php');
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LATIN MIX Radio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --accent-orange: #f5961e;
            --accent-yellow: #ffe632;
            --bg-body: #080808;
            --bg-sidebar: #0d0d0d;
            --bg-card: rgba(20, 20, 20, 0.6);
            --border-ui: rgba(255, 255, 255, 0.04);
            --text-muted: #6b7280;
        }

        [x-cloak] { display: none !important; }
        
        html, body {
            height: 100%;
            height: 100dvh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg-body); 
            color: #f3f4f6;
        }

        /* SaaS Layout Container */
        .app-container {
            display: flex;
            height: 100%;
            height: 100dvh;
            width: 100vw;
            overflow: hidden;
            position: relative;
        }

        /* Sidebar Styles (Responsive) */
        .sidebar {
            width: var(--sidebar-width);
            height: 100%;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-ui);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 1000;
        }

        /* Desktop specific sidebar logic */
        @media (min-width: 1024px) {
            .sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        }

        /* Mobile specific sidebar logic */
        @media (max-width: 1023px) {
            .sidebar {
                position: absolute;
                left: -100%;
                width: 280px;
                box-shadow: 20px 0 50px rgba(0,0,0,0.5);
            }
            .sidebar.open {
                left: 0;
            }
            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.6);
                backdrop-filter: blur(4px);
                z-index: 999;
            }
        }

        /* Main Workspace Area */
        .workspace {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0; 
            background: var(--bg-body);
            position: relative;
            height: 100%;
        }

        /* Content Area with custom scroll */
        .content-scroller {
            flex: 1;
            overflow-y: auto;
            padding: 2.5rem;
            scroll-behavior: smooth;
        }

        @media (max-width: 768px) {
            .content-scroller {
                padding: 1rem;
                padding-bottom: 12rem; /* Espacio extra para asegurar scroll total */
            }
        }

        .content-scroller::-webkit-scrollbar { width: 6px; }
        .content-scroller::-webkit-scrollbar-track { background: transparent; }
        .content-scroller::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }

        /* Navigation Items Improved */
        .nav-btn {
            @apply flex items-center gap-4 px-6 py-4 rounded-2xl text-[11px] font-black transition-all duration-500 mb-2 w-full text-left relative group uppercase tracking-widest;
            color: #555;
        }

        .nav-btn:hover { 
            color: #fff; 
            background: rgba(255, 255, 255, 0.02);
            padding-left: 1.75rem;
        }

        .nav-btn.active {
            background: linear-gradient(to right, rgba(245, 150, 30, 0.08), transparent);
            color: var(--accent-orange);
            border-left: 3px solid var(--accent-orange);
            border-radius: 0 20px 20px 0;
            padding-left: 1.75rem;
        }

        .nav-btn i {
            font-size: 1.25rem;
            transition: all 0.4s ease;
        }

        .nav-btn:hover i {
            color: var(--accent-orange);
            transform: scale(1.2);
        }

        .nav-btn.active i {
            filter: drop-shadow(0 0 8px rgba(245, 150, 30, 0.4));
        }

        .sidebar-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(255,255,255,0.05), transparent);
            margin: 2rem 1.5rem;
        }

        /* Premium Cards */
        .stat-widget {
            background: var(--bg-card);
            border: 1px solid var(--border-ui);
            border-radius: 1.5rem;
            padding: 1.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .glass-card {
            background: rgba(20, 20, 20, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-ui);
            border-radius: 2rem;
            padding: 2.5rem;
        }

        @media (max-width: 768px) {
            .stat-widget {
                padding: 1.25rem;
            }
            .glass-card {
                padding: 1.5rem;
            }
        }

        .stat-widget:hover {
            border-color: rgba(245, 150, 30, 0.2);
            transform: translateY(-2px);
            background: rgba(25, 25, 25, 0.8);
        }

        /* Visual Accents */
        .glow-orange { box-shadow: 0 0 40px -10px rgba(245, 150, 30, 0.15); }
        .gradient-text {
            background: linear-gradient(135deg, var(--accent-orange), var(--accent-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Topbar Styles */
        .header-bar {
            height: 75px;
            padding: 0 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(8, 8, 8, 0.5);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-ui);
            flex-shrink: 0;
        }

        /* Animation Keyframes */
        @keyframes reveal {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .reveal-anim { animation: reveal 0.6s cubic-bezier(0.2, 1, 0.3, 1) forwards; }

        /* Signal Wave Animation */
        .signal-bar {
            width: 3px;
            background: var(--accent-orange);
            border-radius: 50px;
            animation: bounce 1s ease-in-out infinite alternate;
        }
        @keyframes bounce { 
            0% { height: 4px; opacity: 0.3; } 
            100% { height: 16px; opacity: 1; } 
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            pointer-events: none;
        }

        .toast-card {
            pointer-events: auto;
            min-width: 320px;
            max-width: 420px;
            background: rgba(15, 15, 15, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 1.5rem;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            items-center gap: 1rem;
            animation: slideInToast 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideInToast {
            from { opacity: 0; transform: translateX(100%) scale(0.9); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        .toast-success { border-bottom: 3px solid var(--accent-orange); }
        .toast-error { border-bottom: 3px solid #ef4444; }

        /* Vinyl Record Aesthetic */
        @keyframes vinyl-rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-vinyl { animation: vinyl-rotate 8s linear infinite; }
        .vinyl-record {
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, #222 0%, #111 40%, #000 100%);
            border: 4px solid #1a1a1a;
            box-shadow: 0 0 30px rgba(0,0,0,0.8), inset 0 0 20px rgba(255,255,255,0.05);
            position: relative;
        }
        .vinyl-disc {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: radial-gradient(circle, #222 0%, #111 40%, #000 100%);
            border: 3px solid #1a1a1a;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            position: relative;
        }
        .vinyl-disc.playing {
            animation: vinyl-rotate 5s linear infinite;
        }
        .vinyl-grooves {
            position: absolute;
            inset: 5px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.03);
            background: repeating-radial-gradient(circle, #000 0px, #111 2px, #000 4px);
            opacity: 0.4;
        }
    </style>
    <script>
        window.usersListData = <?php echo json_encode(array_values($users_list)); ?>;
        window.newsListData = <?php echo json_encode(array_values($news)); ?>;
        window.adsListData = <?php echo json_encode(array_values($ads)); ?>;
    </script>
</head>
<body 
    x-data="{ 
        currentView: 'dashboard', 
        userView: 'list',
        userData: { id: '', name: '', email: '', password: '', role: 'Admin' },
        sidebarOpen: window.innerWidth > 1024, 
        isMobile: window.innerWidth < 1024,
        toasts: [],
        showPass: false,
        searchTerm: '',
        deleteConfirmOpen: false,
        userToDelete: null,
        usersList: window.usersListData,
        async submitUser(action) {
            const formData = new FormData(this.$refs.userForm);
            formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            formData.append('action', action);
            
            try {
                const response = await fetch('admin.php', { method: 'POST', body: formData });
                if (response.ok) {
                    // Actualizar lista local (simulación de respuesta o recarga de datos)
                    // Para rapidez, recargamos solo la data vía fetch o simplemente actualizamos el array
                    const resText = await response.text();
                    // En este sistema, PHP procesa y da un mensaje. 
                    // Para que sea 100% reactivo, buscamos los datos frescos
                    const freshDataResponse = await fetch('admin.php?get_users=1');
                    if (freshDataResponse.ok) {
                        this.usersList = await freshDataResponse.json();
                        this.addToast(action === 'add_user' ? 'Usuario creado' : 'Cambios guardados');
                        this.userView = 'list';
                    }
                }
            } catch (e) {
                this.addToast('Error de conexión', 'error');
            }
        },
        itemToDelete: null,
        openDeleteModal(type, item) {
            this.itemToDelete = { type, ...item };
            this.deleteConfirmOpen = true;
        },
        async confirmDelete() {
            if (!this.itemToDelete) return;
            
            const formData = new FormData();
            formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            formData.append('id', this.itemToDelete.id);

            let action = '';
            if (this.itemToDelete.type === 'user') action = 'delete_user';
            if (this.itemToDelete.type === 'news') action = 'delete_news';
            if (this.itemToDelete.type === 'ad') action = 'delete_ad';
            
            formData.append('action', action);

            try {
                const response = await fetch('admin.php', { method: 'POST', body: formData });
                if (response.ok) {
                    if (this.itemToDelete.type === 'user') this.usersList = this.usersList.filter(u => u.id !== this.itemToDelete.id);
                    if (this.itemToDelete.type === 'news') this.newsList = this.newsList.filter(n => n.id !== this.itemToDelete.id);
                    if (this.itemToDelete.type === 'ad') this.adsList = this.adsList.filter(a => a.id !== this.itemToDelete.id);
                    
                    this.addToast('Elemento eliminado correctamente');
                    this.deleteConfirmOpen = false;
                    this.itemToDelete = null;
                }
            } catch (e) {
                this.addToast('Error al procesar la eliminación', 'error');
            }
        },
        addToast(msg, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, msg, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 6000);
        },
        audioPlaying: false,
        audioStream: null,
        newsList: window.newsListData,
        adsList: window.adsListData,
        toggleAudio() {
            try {
                if (!this.audioStream) {
                    this.audioStream = new Audio('<?php echo $config['streamUrl']; ?>');
                    this.audioStream.preload = 'auto';
                }
                if (this.audioPlaying) {
                    this.audioStream.pause();
                    this.audioPlaying = false;
                } else {
                    const playPromise = this.audioStream.play();
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            this.audioPlaying = true;
                        }).catch(e => {
                            console.error('Audio Fail:', e);
                            this.addToast('Señal no disponible o bloqueada', 'error');
                        });
                    }
                }
            } catch (e) {
                this.addToast('Error al iniciar el monitor', 'error');
            }
        },
        init() {
            // Manejar redimensionamiento
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024;
                if (!this.isMobile) this.sidebarOpen = true;
            });

            // Iniciar monitor automáticamente al entrar al panel (algunos navegadores lo permiten tras login)
            setTimeout(() => {
                this.toggleAudio();
            }, 100);

            // Fallback por si el navegador bloquea el inicio automático
            window.addEventListener('click', () => {
                if(!this.audioPlaying) {
                    this.toggleAudio();
                }
            }, { once: true });

            <?php if ($message): ?>
                this.addToast('<?php echo addslashes($message); ?>', 'success');
            <?php endif; ?>
            <?php if ($error): ?>
                this.addToast('<?php echo addslashes($error); ?>', 'error');
            <?php endif; ?>
        }
    }">

    <!-- Toast Notifications Layer -->
    <div class="toast-container">
        <template x-for="toast in toasts" :key="toast.id">
            <div 
                :class="toast.type === 'success' ? 'toast-success' : 'toast-error'"
                class="toast-card group"
                @click="toasts = toasts.filter(t => t.id !== toast.id)">
                
                <div :class="toast.type === 'success' ? 'bg-orange-500/10 text-orange-500' : 'bg-red-500/10 text-red-500'" class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0">
                    <i :class="toast.type === 'success' ? 'bi-patch-check-fill' : 'bi-slash-circle-fill'" class="text-xl"></i>
                </div>
                
                <div class="flex-1">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-gray-500 mb-1" x-text="toast.type === 'success' ? 'Sistema / Confirmación' : 'Sistema / Error'"></p>
                    <p class="text-xs font-bold text-white leading-relaxed" x-text="toast.msg"></p>
                </div>
                
                <button class="text-gray-600 hover:text-white transition-colors">
                    <i class="bi bi-x text-xl"></i>
                </button>
            </div>
        </template>
    </div>

    <div class="app-container">
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen && isMobile" @click="sidebarOpen = false" class="sidebar-overlay" x-cloak></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                sidebarOpen ? (isMobile ? 'open' : '') : (isMobile ? '' : 'collapsed')
            ]"
            class="sidebar">
            
            <!-- Brand Identity -->
            <div class="h-[90px] flex items-center px-8 border-b border-white/[0.03] mb-8 bg-black/20">
                <div class="flex items-center gap-4 group cursor-pointer">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-yellow-400 flex items-center justify-center shadow-[0_0_25px_rgba(245,150,30,0.25)] transition-transform group-hover:rotate-12 duration-500 p-2 overflow-hidden">
                        <img src="assets/logo.png" class="w-full h-full object-contain" alt="Logo">
                    </div>
                    <div x-show="sidebarOpen" class="flex flex-col">
                        <span class="font-black text-xl tracking-tighter italic text-white leading-none">LATIN<span class="text-orange-500"> MIX</span></span>
                        <span class="text-[9px] font-black text-gray-700 uppercase tracking-[0.4em] mt-1">PRO CORE</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 px-2 space-y-2 overflow-y-auto custom-scrollbar">
                <div class="px-4">
                    <p x-show="sidebarOpen" class="px-4 text-[9px] font-black text-gray-700 uppercase tracking-[0.35em] mb-4">Métricas Globales</p>
                    <nav class="flex flex-col space-y-1">
                        <button @click="currentView = 'dashboard'" :class="currentView === 'dashboard' ? 'active' : ''" class="nav-btn w-full block">
                            <div class="flex items-center gap-4">
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span x-show="sidebarOpen">Panel Maestro</span>
                            </div>
                        </button>
                    </nav>
                </div>

                <div class="sidebar-divider"></div>

                <div class="px-4">
                    <p x-show="sidebarOpen" class="px-4 text-[9px] font-black text-gray-700 uppercase tracking-[0.35em] mb-4">Gestión de Medios</p>
                    <nav class="flex flex-col space-y-2">
                        <button @click="currentView = 'news'" :class="currentView === 'news' ? 'active' : ''" class="nav-btn w-full block">
                            <div class="flex items-center gap-4">
                                <i class="bi bi-newspaper"></i>
                                <span x-show="sidebarOpen">Editorial</span>
                            </div>
                        </button>
                        <button @click="currentView = 'ads'" :class="currentView === 'ads' ? 'active' : ''" class="nav-btn w-full block">
                            <div class="flex items-center gap-4">
                                <i class="bi bi-megaphone-fill"></i>
                                <span x-show="sidebarOpen">Publicidad</span>
                            </div>
                        </button>
                    </nav>
                </div>

                <div class="sidebar-divider"></div>

                <?php if ($_SESSION['admin_email'] === 'Admin@latinmix.com'): ?>
                <div class="px-4 animate-view">
                    <p x-show="sidebarOpen" class="px-4 text-[9px] font-black text-gray-700 uppercase tracking-[0.35em] mb-4">Sistema Pro</p>
                    <nav class="flex flex-col space-y-1">
                        <button @click="currentView = 'users'; userView = 'list'" :class="currentView === 'users' ? 'active' : ''" class="nav-btn w-full block">
                            <div class="flex items-center gap-4">
                                <i class="bi bi-people-fill"></i>
                                <span x-show="sidebarOpen">Gestión de Usuarios</span>
                            </div>
                        </button>
                        <button @click="currentView = 'config'" :class="currentView === 'config' ? 'active' : ''" class="nav-btn w-full block">
                            <div class="flex items-center gap-4">
                                <i class="bi bi-sliders2-vertical"></i>
                                <span x-show="sidebarOpen">Configuración</span>
                            </div>
                        </button>
                    </nav>
                </div>
                <?php endif; ?>
            </div>

            <!-- User Session Footer -->
            <div class="p-4 border-t border-white/[0.04] bg-black/20">
                <div x-show="sidebarOpen" class="flex items-center gap-3 p-3 rounded-2xl bg-white/[0.02] border border-white/[0.04] mb-4 animate-view">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=f5961e&color=000&bold=true" class="w-9 h-9 rounded-xl border border-white/10" alt="Avatar">
                    <div class="flex flex-col min-w-0">
                        <span class="text-[11px] font-black text-white truncate"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span>
                            <span class="text-[9px] text-gray-500 font-bold uppercase tracking-tighter">Sintonía Directa</span>
                        </div>
                    </div>
                </div>
                
                <form action="admin.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" @click="if(audioStream) audioStream.pause()" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-red-500/5 hover:bg-red-500 text-red-500 hover:text-white transition-all text-[10px] font-black uppercase tracking-[0.2em] border border-red-500/10 active:scale-95">
                        <i class="bi bi-power text-base"></i>
                        <span x-show="sidebarOpen" class="animate-view">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="workspace">
            <!-- Header Bar -->
            <header class="header-bar !px-4 md:!px-10">
                <div class="flex items-center gap-4 md:gap-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="flex w-10 h-10 items-center justify-center rounded-xl bg-white/5 text-gray-400 hover:text-white transition-all">
                        <i :class="sidebarOpen ? 'bi-list-nested' : 'bi-list'" class="text-xl"></i>
                    </button>
                    <div class="h-8 w-px bg-white/10"></div>
<?php 
                        $days = ['Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado'];
                        $months = ['January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo', 'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio', 'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre', 'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'];
                        $current_day = $days[date('l')];
                        $current_month = $months[date('F')];
                        $full_date = "$current_day, " . date('d') . " de $current_month";
                    ?>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-600 uppercase tracking-[0.3em] leading-none mb-1">Reporte de Estado</span>
                        <span class="text-xs font-bold text-white"><?php echo $full_date; ?></span>
                    </div>
                </div>

                <div class="flex-1"></div>

                <div class="flex items-center gap-4">
                    <!-- Elementos removidos por solicitud -->
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="content-scroller">
                <div class="max-w-6xl mx-auto space-y-12 reveal-anim">
                    


                    <!-- Dashboard View -->
                    <section x-show="currentView === 'dashboard'" class="space-y-10" x-cloak>
                        
                        <!-- Page Title -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6 border-b border-white/[0.04] pb-8">
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-orange-500 uppercase tracking-[0.4em]">Analytics / Global</p>
                                <h1 class="text-5xl font-black tracking-tighter text-white italic leading-none">PANEL <span class="text-gray-700">MAESTRO</span></h1>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="px-4 py-2 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center gap-3">
                                    <div class="flex items-end gap-1 h-4">
                                        <div class="signal-bar" style="animation-delay: 0.1s"></div>
                                        <div class="signal-bar" style="animation-delay: 0.3s"></div>
                                        <div class="signal-bar" style="animation-delay: 0.2s"></div>
                                        <div class="signal-bar" style="animation-delay: 0.5s"></div>
                                    </div>
                                    <span class="text-[10px] font-black text-orange-500 uppercase tracking-widest">Señal En Vivo</span>
                                </div>
                            </div>
                        </div>

                        <!-- Main Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                            
                            <!-- Key Metrics (Left 8 cols) -->
                            <div class="lg:col-span-8 space-y-8">
                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Stat Ads -->
                                    <div class="stat-widget group hover:glow-orange">
                                        <div class="flex justify-between items-start mb-6">
                                            <div class="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center text-orange-500 border border-orange-500/20 group-hover:rotate-6 transition-transform">
                                                <i class="bi bi-lightning-charge text-xl"></i>
                                            </div>
                                            <span class="text-[9px] px-2 py-1 bg-green-500/10 text-green-500 font-black rounded-lg">+12.5%</span>
                                        </div>
                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Impactos en Ads</p>
                                        <div class="flex items-end gap-3">
                                            <h4 class="text-4xl font-black text-white leading-none"><?php echo count($ads); ?></h4>
                                            <span class="text-xs font-bold text-gray-600 mb-1 italic">Campañas Activas</span>
                                        </div>
                                    </div>
                                    <!-- Stat News -->
                                    <div class="stat-widget group">
                                        <div class="flex justify-between items-start mb-6">
                                            <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-white border border-white/10">
                                                <i class="bi bi-journal-text text-xl"></i>
                                            </div>
                                            <span class="text-[9px] px-2 py-1 bg-white/5 text-gray-500 font-black rounded-lg">Estable</span>
                                        </div>
                                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Alcance Editorial</p>
                                        <div class="flex items-end gap-3">
                                            <h4 class="text-4xl font-black text-white leading-none"><?php echo count($news); ?></h4>
                                            <span class="text-xs font-bold text-gray-600 mb-1 italic">Noticias Publicadas</span>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <!-- Right Column (4 cols) -->
                            <div class="lg:col-span-4 space-y-8">
                                <!-- Master Radio Monitor (Square Version) -->
                                <div class="stat-widget p-10 bg-neutral-900 border border-white/5 hover:border-orange-500/30 transition-all flex flex-col items-center justify-center text-center group/mini rounded-[3rem]">
                                    <!-- Mini Vinyl -->
                                    <div class="relative mb-8">
                                        <div :class="audioPlaying ? 'animate-vinyl' : ''" class="w-32 h-32 rounded-full bg-gradient-to-br from-[#1a1a1a] to-[#000] border-4 border-white/5 flex items-center justify-center relative shadow-2xl">
                                            <div class="w-12 h-12 rounded-full bg-black relative z-10 border-2 border-white/10 overflow-hidden flex items-center justify-center p-1 group-hover/mini:scale-110 transition-transform duration-700">
                                                <img src="assets/logo.png" class="w-full h-full object-contain" alt="Logo">
                                            </div>
                                            <!-- Vinyl Groove Pattern -->
                                            <div class="absolute inset-2 border border-white/[0.03] rounded-full"></div>
                                            <div class="absolute inset-4 border border-white/[0.02] rounded-full"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="w-full flex-1 flex flex-col items-center justify-center">
                                        <h5 class="text-[10px] font-black text-orange-500 uppercase tracking-[0.4em] italic mb-6">Master Monitor</h5>

                                        <!-- Ultra-Visible Button with Logo Colors -->
                                        <button @click="toggleAudio()" 
                                                class="w-20 h-20 rounded-full flex items-center justify-center transition-all shadow-2xl group/play border-4 border-white/10 active:scale-95" 
                                                :class="audioPlaying ? 'bg-red-600 text-white animate-pulse shadow-red-500/50' : 'bg-gradient-to-br from-orange-500 to-yellow-400 text-black shadow-orange-500/50 hover:scale-105'">
                                            <i class="bi" :class="audioPlaying ? 'bi-pause-fill' : 'bi-play-fill'" :class="audioPlaying ? 'text-white' : 'text-black text-3xl'" style="line-height: 1; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Status Card -->
                                <div class="stat-widget bg-gradient-to-br from-[#111] to-[#080808] border-orange-500/10 relative overflow-hidden group">
                                    <i class="bi bi-music-note-beamed absolute -bottom-4 -right-4 text-9xl text-white/[0.02] -rotate-12"></i>
                                    <div class="relative z-10 flex flex-col items-center py-6">
                                        <div class="w-20 h-20 rounded-full bg-orange-500/10 flex items-center justify-center mb-6 relative">
                                            <div class="absolute inset-0 rounded-full border-2 border-orange-500/20 animate-ping"></div>
                                            <i class="bi bi-vinyl-fill text-4xl text-orange-500"></i>
                                        </div>
                                        <h3 class="text-2xl font-black italic text-center tracking-tighter mb-2 italic uppercase">SISTEMA <span class="text-orange-500">OPERATIVO</span></h3>
                                        <p class="text-[11px] text-gray-500 font-medium text-center px-6 leading-relaxed">Núcleo central activo. Todo el contenido se está sincronizando con los servidores de streaming.</p>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="space-y-4">
                                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-700 ml-2">Accesos Maestros</h3>
                                    <div class="grid grid-cols-1 gap-4">
                                        <button @click="currentView = 'news'" class="p-6 rounded-2xl bg-white/[0.02] border border-white/[0.05] hover:border-orange-500/40 hover:bg-orange-500/[0.03] transition-all flex flex-col items-center text-center group">
                                            <i class="bi bi-pencil-fill text-xl text-orange-500 mb-3 group-hover:scale-110 transition-transform"></i>
                                            <span class="text-[10px] font-black text-white uppercase tracking-widest">Publicar Noticia</span>
                                        </button>
                                        <button @click="currentView = 'ads'" class="p-6 rounded-2xl bg-white/[0.02] border border-white/[0.05] hover:border-orange-500/40 hover:bg-orange-500/[0.03] transition-all flex flex-col items-center text-center group">
                                            <i class="bi bi-plus-circle-fill text-xl text-orange-500 mb-3 group-hover:scale-110 transition-transform"></i>
                                            <span class="text-[10px] font-black text-white uppercase tracking-widest">Nuevo Anuncio</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

        <!-- Config View -->
        <section x-show="currentView === 'config'" class="animate-view" x-cloak>
            <div class="glass-card p-12 max-w-5xl">
                <div class="flex items-center gap-4 mb-12">
                    <div class="w-2.5 h-10 bg-orange-500 rounded-full"></div>
                    <h2 class="text-3xl font-black italic tracking-tighter">CONFIGURACIÓN <span class="text-gray-600">DEL SISTEMA</span></h2>
                </div>

                <form action="admin.php" method="POST" class="space-y-12">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="save_config">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                        <!-- Transmisión -->
                        <div class="space-y-8">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-broadcast text-orange-500"></i>
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Señales en Vivo</h3>
                            </div>
                            <div class="space-y-6">
                                 <div class="space-y-2">
                                    <label class="block text-gray-600 text-[10px] font-black uppercase tracking-widest ml-1">AzuraCast Embed URL</label>
                                    <input type="text" name="azuraCastUrl" value="<?php echo $config['azuraCastUrl']; ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl py-5 px-6 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/30 transition-all font-medium" placeholder="https://radio.latinmixfm.com/public/latinmixfm/embed...">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-gray-600 text-[10px] font-black uppercase tracking-widest ml-1">Direct Stream URL (Audio Only)</label>
                                    <input type="text" name="streamUrl" value="<?php echo $config['streamUrl'] ?? ''; ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl py-5 px-6 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/30 transition-all font-medium" placeholder="https://radio.latinmixfm.com/listen/latinmixfm/radio.mp3">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-gray-600 text-[10px] font-black uppercase tracking-widest ml-1">YouTube Video ID (En vivo)</label>
                                    <input type="text" name="youtubeLiveId" value="<?php echo $config['youtubeLiveId']; ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl py-5 px-6 text-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-500/30 transition-all font-medium" placeholder="Ej: dQw4w9WgXcQ">
                                </div>
                            </div>
                        </div>

                        <!-- Social -->
                        <div class="space-y-8">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-share-fill text-orange-500"></i>
                                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400">Ecosistema Social</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-600 ml-1">Facebook</label>
                                    <input type="text" name="facebookUrl" value="<?php echo $config['facebookUrl']; ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-xs focus:ring-2 focus:ring-orange-500/20 outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-600 ml-1">Instagram</label>
                                    <input type="text" name="instagramUrl" value="<?php echo $config['instagramUrl']; ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-xs focus:ring-2 focus:ring-orange-500/20 outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-600 ml-1">TikTok</label>
                                    <input type="text" name="tiktokUrl" value="<?php echo $config['tiktokUrl']; ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-xs focus:ring-2 focus:ring-orange-500/20 outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-600 ml-1">YouTube Channel</label>
                                    <input type="text" name="youtubeChannelUrl" value="<?php echo $config['youtubeChannelUrl'] ?? ''; ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-xs focus:ring-2 focus:ring-orange-500/20 outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-white/5">
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-yellow-500 py-4 px-10 rounded-2xl text-black font-black uppercase tracking-widest text-xs hover:scale-[1.02] transition-all w-full sm:w-auto min-w-[240px]">
                            Guardar Cambios Maestros
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Ads View -->
        <section x-show="currentView === 'ads'" x-data="{ 
            filter: 'all', 
            editing: false,
            editData: { id: '', title: '', startDate: '', endDate: '', active: true, imageUrl: '' },
            openEdit(ad) {
                this.editing = true;
                this.editData = { ...ad };
                // Scroll to top of section
                document.getElementById('ad_form_container').scrollIntoView({ behavior: 'smooth' });
            },
            cancelEdit() {
                this.editing = false;
                this.editData = { id: '', title: '', startDate: '', endDate: '', active: true, imageUrl: '' };
            }
        }" class="space-y-10 animate-view" x-cloak>
            <div id="ad_form_container" class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Form -->
                <div class="glass-card !p-6 md:!p-10 h-fit lg:sticky lg:top-24">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-2.5 h-7 bg-orange-500 rounded-full"></div>
                        <h3 class="text-xl font-black italic tracking-tighter" x-text="editing ? 'EDITAR PUBLICIDAD' : 'NUEVO ANUNCIO'"></h3>
                    </div>

                    <form action="admin.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" :value="editing ? 'edit_ad' : 'add_ad'">
                        <input type="hidden" name="id" x-model="editData.id" x-show="editing">
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Título de la Campaña</label>
                            <input type="text" name="title" x-model="editData.title" placeholder="Ej: Promo Verano 2026" class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-orange-500/20 outline-none transition-all" required>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-orange-400 italic ml-1" x-text="editing ? 'Cambiar Foto (Opcional)' : 'Subir Foto'"></label>
                            <div class="relative group cursor-pointer">
                                <input type="file" name="adImage" :required="!editing" class="w-full bg-orange-500/5 border border-orange-500/10 rounded-2xl px-4 py-4 text-xs text-white file:bg-orange-500 file:border-none file:rounded-xl file:text-[9px] file:text-black file:font-black file:uppercase file:mr-4 file:cursor-pointer transition-all hover:bg-orange-500/10">
                            </div>
                            <template x-if="editing && editData.imageUrl">
                                <div class="mt-4 p-2 bg-white/5 rounded-xl border border-white/10">
                                    <p class="text-[8px] text-gray-500 uppercase font-black mb-2">Imagen Actual:</p>
                                    <img :src="editData.imageUrl" class="w-full h-24 object-cover rounded-lg">
                                </div>
                            </template>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase text-gray-500 ml-1">Fecha Inicio</label>
                                <input type="date" name="startDate" x-model="editData.startDate" class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-[10px] focus:ring-2 focus:ring-orange-500/20 outline-none text-white transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase text-gray-500 ml-1">Fecha Fin</label>
                                <input type="date" name="endDate" x-model="editData.endDate" class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 text-[10px] focus:ring-2 focus:ring-orange-500/20 outline-none text-white transition-all">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-white/5 p-4 rounded-2xl border border-white/5">
                           <input type="checkbox" name="active" x-model="editData.active" id="ad_active_chk" class="w-5 h-5 rounded-lg accent-orange-500 border-white/10 bg-transparent">
                           <label for="ad_active_chk" class="text-[10px] text-gray-400 font-bold uppercase tracking-widest cursor-pointer select-none">Estado Activo</label>
                        </div>

                        <div class="space-y-4">
                            <button type="submit" class="w-full py-5 rounded-2xl bg-gradient-to-r from-orange-500 to-yellow-400 text-black font-black uppercase tracking-widest text-xs shadow-xl shadow-orange-500/20 hover:scale-[1.02] active:scale-95 transition-all" x-text="editing ? 'Guardar Cambios' : 'Confirmar Publicidad'">
                            </button>
                            
                            <button type="button" x-show="editing" @click="cancelEdit()" class="w-full py-5 rounded-2xl bg-white/5 border border-white/10 text-white font-black uppercase tracking-widest text-xs hover:bg-white/10 transition-all">
                                Cancelar Edición
                            </button>
                        </div>
                    </form>
                </div>

                <!-- List & Filters -->
                <div class="lg:col-span-2 glass-card !p-6 md:!p-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-12 border-b border-white/[0.03] pb-8">
                        <div class="flex items-center gap-4 shrink-0">
                            <div class="w-12 h-12 rounded-2xl bg-orange-500/10 flex items-center justify-center text-orange-500 shadow-inner">
                                <i class="bi bi-collection-play-fill text-2xl"></i>
                            </div>
                            <h3 class="text-xl md:text-2xl font-black italic uppercase tracking-tighter">Inventario <span class="text-gray-500">Publicitario</span></h3>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                            <!-- Local Ad Filter -->
                            <div class="relative group w-full sm:w-56">
                                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-[10px]"></i>
                                <input type="text" x-model="searchTerm" placeholder="Buscar..." class="bg-white/5 border border-white/10 rounded-xl pl-9 pr-4 py-2.5 text-[10px] font-bold text-white focus:outline-none focus:ring-1 focus:ring-orange-500/30 w-full transition-all">
                            </div>
                            
                            <div class="flex p-1 bg-white/5 rounded-2xl border border-white/5 max-w-full overflow-x-auto">
                                <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-orange-500 text-black shadow-lg shadow-orange-500/20' : 'text-gray-500 hover:text-white'" class="px-4 py-2.5 rounded-xl text-[9px] font-black transition-all uppercase tracking-widest whitespace-nowrap">TODOS</button>
                                <button @click="filter = 'active'" :class="filter === 'active' ? 'bg-green-600 text-white shadow-lg shadow-green-500/20' : 'text-gray-500 hover:text-white'" class="px-4 py-2.5 rounded-xl text-[9px] font-black transition-all uppercase tracking-widest whitespace-nowrap">ACTIVOS</button>
                                <button @click="filter = 'inactive'" :class="filter === 'inactive' ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'text-gray-500 hover:text-white'" class="px-4 py-2.5 rounded-xl text-[9px] font-black transition-all uppercase tracking-widest whitespace-nowrap">PAUSADOS</button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 pr-2 custom-scrollbar">
                        <template x-for="ad in adsList.filter(a => (filter === 'all' || (filter === 'active' ? a.active : !a.active)) && (a.title.toLowerCase().includes(searchTerm.toLowerCase())))" :key="ad.id">
                            <div x-transition class="flex flex-col md:flex-row items-center justify-between p-6 bg-white/[0.02] border border-white/5 rounded-[2.5rem] group hover:bg-white/[0.04] transition-all gap-6">
                                <div class="flex flex-col md:flex-row items-center gap-6 w-full md:w-auto text-center md:text-left">
                                    <div class="relative shrink-0">
                                        <img :src="ad.imageUrl" class="w-24 h-24 md:w-20 md:h-20 rounded-[1.5rem] object-cover border border-white/10 group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-[1.5rem] flex items-center justify-center">
                                            <a :href="ad.imageUrl" target="_blank" class="text-white">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-lg font-black uppercase tracking-tighter mb-2 italic text-white" x-text="ad.title"></h4>
                                        <div class="flex flex-wrap justify-center md:justify-start gap-2">
                                            <span class="text-[8px] px-3 py-1 bg-white/5 border border-white/5 rounded-lg text-gray-500 uppercase font-black tracking-widest" x-text="ad.startDate || 'S. Inicio'"></span>
                                            <span class="text-[8px] px-3 py-1 bg-white/5 border border-white/5 rounded-lg text-gray-500 uppercase font-black tracking-widest" x-text="ad.endDate || 'S. Fin'"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3 w-full md:w-auto justify-center md:justify-end border-t md:border-t-0 border-white/5 pt-4 md:pt-0">
                                    <button @click="openEdit(ad)" class="w-12 h-12 rounded-2xl bg-white/5 text-gray-500 hover:text-orange-500 hover:bg-orange-500/10 transition-all flex items-center justify-center" title="Editar">
                                        <i class="bi bi-pencil-square text-xl"></i>
                                    </button>

                                    <form action="admin.php" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="action" value="toggle_ad">
                                        <input type="hidden" name="id" :value="ad.id">
                                        <button type="submit" class="px-6 h-12 rounded-2xl text-[9px] font-black tracking-widest border transition-all flex items-center gap-2 whitespace-nowrap"
                                            :class="ad.active ? 'bg-green-500/10 text-green-500 border-green-500/20 hover:bg-green-500 hover:text-white' : 'bg-red-500/10 text-red-500 border-red-500/20 hover:bg-red-500 hover:text-white'">
                                            <i class="bi" :class="ad.active ? 'bi-play-fill' : 'bi-pause-fill'"></i>
                                            <span x-text="ad.active ? 'ACTIVO' : 'PAUSADO'"></span>
                                        </button>
                                    </form>

                                    <button type="button" @click="openDeleteModal('ad', ad)" class="w-12 h-12 rounded-2xl bg-white/5 text-gray-500 hover:text-red-500 hover:bg-red-500/10 transition-all flex items-center justify-center">
                                        <i class="bi bi-trash3-fill text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State Ads -->
                        <div x-show="adsList.filter(a => (filter === 'all' || (filter === 'active' ? a.active : !a.active)) && (a.title.toLowerCase().includes(searchTerm.toLowerCase()))).length === 0"
                             class="py-32 flex flex-col items-center justify-center space-y-6 animate-view">
                            <div class="w-24 h-24 bg-white/[0.03] border border-white/5 rounded-full flex items-center justify-center relative overflow-hidden">
                                <i class="bi bi-search text-4xl text-gray-800"></i>
                                <div class="absolute inset-0 bg-gradient-to-t from-orange-500/10 to-transparent"></div>
                            </div>
                            <div class="text-center">
                                <h4 class="text-xl font-black text-white italic uppercase tracking-tighter">Sin rastro de campañas</h4>
                                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2">No hay anuncios que coincidan con tu búsqueda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- News View -->
        <section x-show="currentView === 'news'" x-data="{ newsFilter: 'all' }" class="space-y-12 animate-view" x-cloak>
            <div class="glass-card p-12">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-2.5 h-8 bg-orange-500 rounded-full"></div>
                    <h3 class="text-3xl font-black uppercase italic tracking-tighter text-white">REDACCIÓN <span class="text-gray-600">EDITORIAL</span></h3>
                </div>
                
                <form action="admin.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="add_news">
                    
                    <div class="md:col-span-3 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Encabezado de la Noticia</label>
                        <input type="text" name="title" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 text-sm focus:ring-2 focus:ring-yellow-500/20 outline-none transition-all placeholder:text-gray-700 font-bold" placeholder="Escribe un título que capte la atención..." required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Fecha de Emisión</label>
                        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 text-sm text-white focus:ring-2 focus:ring-yellow-500/20 outline-none transition-all cursor-pointer" required>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-yellow-400 italic ml-1">Fotografía Principal de la Noticia</label>
                        <div class="relative group">
                            <input type="file" name="newsImage" class="w-full bg-yellow-500/5 border border-yellow-500/10 rounded-2xl px-5 py-4 text-xs text-white file:bg-yellow-500 file:border-none file:rounded-xl file:text-[9px] file:text-black file:font-black file:uppercase file:mr-4 file:cursor-pointer transition-all hover:bg-yellow-500/10" required>
                        </div>
                    </div>
                    <div class="space-y-2" x-data="{ 
                        open: false, 
                        selected: 'Nacional/Local',
                        options: [
                            { id: 'Nacional/Local', label: 'NACIONAL/LOCAL', icon: 'bi-geo-alt-fill' },
                            { id: 'Internacional', label: 'INTERNACIONAL', icon: 'bi-globe-americas' },
                            { id: 'Economía', label: 'ECONOMÍA', icon: 'bi-bank' },
                            { id: 'Sociedad', label: 'SOCIEDAD', icon: 'bi-people-fill' },
                            { id: 'Cultura', label: 'CULTURA', icon: 'bi-palette-fill' },
                            { id: 'Deportes', label: 'DEPORTES', icon: 'bi-trophy-fill' },
                            { id: 'Opinión', label: 'OPINIÓN', icon: 'bi-chat-quote-fill' }
                        ]
                    }">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500 ml-1">Sección / Categoría</label>
                        <input type="hidden" name="category" :value="selected">
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-5 text-sm text-white focus:ring-2 focus:ring-yellow-500/50 outline-none flex items-center justify-between transition-all group">
                                <div class="flex items-center gap-3">
                                    <i :class="options.find(o => o.id === selected).icon" class="text-yellow-500 text-lg"></i>
                                    <span class="font-bold" x-text="options.find(o => o.id === selected).label"></span>
                                </div>
                                <i class="bi bi-chevron-down text-gray-600 group-hover:text-yellow-500 transition-colors"></i>
                            </button>
                            
                            <div x-show="open" x-transition.origin.top class="absolute z-50 w-full mt-2 bg-neutral-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-xl">
                                <template x-for="option in options" :key="option.id">
                                    <div @click="selected = option.id; open = false" 
                                         class="px-6 py-4 flex items-center gap-4 hover:bg-yellow-500/10 cursor-pointer transition-colors group">
                                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-gray-500 group-hover:text-yellow-500 transition-colors">
                                            <i :class="option.icon"></i>
                                        </div>
                                        <span class="text-xs font-black uppercase tracking-widest text-gray-400 group-hover:text-white" x-text="option.label"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-4 space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-500">Cuerpo de la Noticia / Resumen</label>
                        <textarea name="summary" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm min-h-[150px] focus:ring-2 focus:ring-yellow-500/20 outline-none text-gray-300 transition-all" placeholder="Escribe el cuerpo del artículo aquí..." required></textarea>
                    </div>
                    <button type="submit" class="md:col-span-4 py-5 rounded-2xl bg-gradient-to-r from-yellow-500 to-orange-500 text-black font-black uppercase tracking-[0.2em] text-xs hover:scale-[1.01] active:scale-95 transition-all shadow-xl shadow-yellow-600/20">Lanzar Noticia al Sitio</button>
                </form>
            </div>

            <!-- News Grid & Filters -->
            <div class="space-y-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <div class="flex items-center gap-8">
                        <h3 class="text-2xl font-black italic tracking-tighter">ARTÍCULOS PUBLICADOS</h3>
                        <!-- News Search -->
                        <div class="relative group hidden xl:block">
                            <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600"></i>
                            <input type="text" x-model="searchTerm" placeholder="Buscar noticia por título..." class="bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-2 text-[10px] font-bold text-white focus:outline-none focus:ring-1 focus:ring-orange-500/30 w-72 transition-all">
                        </div>
                    </div>
                    <div class="flex gap-4 p-1 bg-white/5 rounded-2xl border border-white/5" x-data="{ open: false }">
                        <div class="relative">
                            <button @click="open = !open" @click.away="open = false" class="bg-transparent border-none rounded-xl px-6 py-2 text-xs font-black uppercase tracking-widest outline-none flex items-center gap-3 text-gray-400 hover:text-white transition-all">
                                <i class="bi bi-filter-left text-lg"></i>
                                <span x-text="newsFilter === 'all' ? 'Todas las Categorías' : newsFilter"></span>
                                <i class="bi bi-chevron-down text-[10px]"></i>
                            </button>
                            
                            <div x-show="open" x-transition.origin.top class="absolute right-0 mt-3 z-50 w-64 bg-neutral-900 border border-white/10 rounded-2xl shadow-3xl overflow-hidden backdrop-blur-2xl">
                                <div @click="newsFilter = 'all'; open = false" class="px-5 py-4 hover:bg-white/5 cursor-pointer flex items-center gap-3 transition-all">
                                    <i class="bi bi-globe2 text-gray-500"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Todas las Categorías</span>
                                </div>
                                <div class="h-px bg-white/5 mx-4"></div>
                                <div @click="newsFilter = 'Nacional/Local'; open = false" class="px-5 py-4 hover:bg-white/5 cursor-pointer flex items-center gap-3 transition-all">
                                    <i class="bi bi-geo-alt-fill text-yellow-500"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nacional/Local</span>
                                </div>
                                <div @click="newsFilter = 'Internacional'; open = false" class="px-5 py-4 hover:bg-white/5 cursor-pointer flex items-center gap-3 transition-all">
                                    <i class="bi bi-globe-americas text-blue-400"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Internacional</span>
                                </div>
                                <div @click="newsFilter = 'Economía'; open = false" class="px-5 py-4 hover:bg-white/5 cursor-pointer flex items-center gap-3 transition-all">
                                    <i class="bi bi-bank text-emerald-400"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Economía</span>
                                </div>
                                <div @click="newsFilter = 'Sociedad'; open = false" class="px-5 py-4 hover:bg-white/5 cursor-pointer flex items-center gap-3 transition-all">
                                    <i class="bi bi-people-fill text-cyan-400"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Sociedad</span>
                                </div>
                                <div @click="newsFilter = 'Cultura'; open = false" class="px-5 py-4 hover:bg-white/5 cursor-pointer flex items-center gap-3 transition-all">
                                    <i class="bi bi-palette-fill text-purple-400"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Cultura</span>
                                </div>
                                <div @click="newsFilter = 'Deportes'; open = false" class="px-5 py-4 hover:bg-white/5 cursor-pointer flex items-center gap-3 transition-all">
                                    <i class="bi bi-trophy-fill text-orange-400"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Deportes</span>
                                </div>
                                <div @click="newsFilter = 'Opinión'; open = false" class="px-5 py-4 hover:bg-white/5 cursor-pointer flex items-center gap-3 transition-all">
                                    <i class="bi bi-chat-quote-fill text-pink-400"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Opinión</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="n in newsList.filter(item => (newsFilter === 'all' || item.category === newsFilter) && (item.title.toLowerCase().includes(searchTerm.toLowerCase())))" :key="n.id">
                        <div x-transition class="content-card overflow-hidden group flex flex-col h-full border border-white/5 hover:border-yellow-500/30">
                            <div class="relative h-60 overflow-hidden">
                                <img :src="n.image" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-1000">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60"></div>
                                <div class="absolute top-4 left-4">
                                    <span class="px-5 py-2 bg-yellow-500 rounded-full text-[9px] text-black font-black uppercase tracking-widest shadow-lg" x-text="n.category">
                                    </span>
                                </div>
                            </div>
                            <div class="p-8 flex flex-col flex-1">
                                <h4 class="text-xl font-black mt-2 leading-tight uppercase italic line-clamp-2 text-white group-hover:text-yellow-400 transition-colors" x-text="n.title"></h4>
                                <p class="text-gray-500 text-xs mt-4 line-clamp-3 leading-relaxed" x-text="n.summary"></p>
                                
                                <div class="mt-auto pt-8 flex justify-between items-center border-t border-white/5 mt-8">
                                     <div class="flex items-center gap-2 text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <span class="text-[10px] font-bold" x-text="new Date(n.date).toLocaleDateString()"></span>
                                     </div>
                                     <button type="button" @click="openDeleteModal('news', n)" class="text-[10px] font-black text-red-500/60 hover:text-red-500 transition-colors tracking-widest uppercase">ELIMINAR</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Users Management View -->
        <section x-show="currentView === 'users'" class="space-y-12 animate-view" x-cloak>
            
            <!-- Users List Screen -->
            <div x-show="userView === 'list'" class="space-y-10">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8">
                    <div class="flex items-center gap-5">
                        <div class="w-1.5 h-12 bg-gradient-to-b from-orange-500 to-yellow-500 rounded-full"></div>
                        <div class="flex items-center gap-8">
                            <div>
                                <h2 class="text-4xl font-black italic tracking-tighter uppercase text-white leading-none">USUARIOS <span class="text-gray-700 italic">GESTORES</span></h2>
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mt-2">Control de accesos administrativos</p>
                            </div>
                            <!-- User Filter -->
                            <div class="relative group hidden xl:block translate-y-1">
                                <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-600"></i>
                                <input type="text" x-model="searchTerm" placeholder="Filtrar por nombre o email..." class="bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-2 text-[10px] font-bold text-white focus:outline-none focus:ring-1 focus:ring-orange-500/30 w-64 transition-all">
                            </div>
                        </div>
                    </div>
                    <button @click="userView = 'create'; userData = { id: '', name: '', email: '', password: '', role: 'Admin' }" class="px-10 py-5 rounded-[2rem] bg-orange-500 text-black font-black uppercase tracking-widest text-[10px] hover:scale-105 active:scale-95 transition-all shadow-2xl shadow-orange-500/20 flex items-center gap-3">
                        <i class="bi bi-person-plus-fill text-lg"></i> RECLUTAR NUEVO GESTOR
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-10">
                    <template x-for="user in usersList.filter(u => u.name.toLowerCase().includes(searchTerm.toLowerCase()) || u.email.toLowerCase().includes(searchTerm.toLowerCase()))" :key="user.id">
                        <div class="glass-card flex flex-col group relative overflow-hidden p-0 border border-white/5 hover:border-orange-500/40 transition-all duration-700 hover:shadow-[0_0_50px_rgba(245,150,30,0.1)] !rounded-[3.5rem]">
                            <!-- Header Decorative -->
                            <div class="h-24 bg-gradient-to-r from-orange-500/20 to-yellow-500/5 relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                            </div>
                            
                            <div class="px-10 pb-10 -mt-12 relative flex-1 flex flex-col">
                                <div class="flex justify-between items-end mb-6">
                                    <div class="w-24 h-24 rounded-[2rem] bg-black p-1.5 border border-white/10 relative overflow-hidden group-hover:scale-105 transition-transform duration-500">
                                         <img :src="'https://ui-avatars.com/api/?name=' + user.name + '&background=f5961e&color=000&bold=true&size=128'" class="w-full h-full object-cover rounded-[1.6rem]">
                                         <div x-show="user.id === '<?php echo $_SESSION['admin_id']; ?>'" class="absolute inset-0 bg-orange-500/20 backdrop-blur-sm flex items-center justify-center">
                                             <span class="px-3 py-1 bg-black text-orange-500 text-[7px] font-black uppercase rounded-lg">YOU</span>
                                         </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="px-4 py-1.5 rounded-full bg-orange-500 text-black text-[9px] font-black uppercase tracking-[0.2em]" x-text="user.role"></span>
                                        <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest" x-text="'ID: ' + user.id.substring(0,8)"></span>
                                    </div>
                                </div>
                                
                                <h3 class="text-3xl font-black text-white mb-2 uppercase italic tracking-tighter leading-none" x-text="user.name"></h3>
                                <div class="flex items-center gap-2 text-gray-500 mb-8 p-3 bg-white/5 rounded-xl border border-white/5">
                                    <i class="bi bi-envelope-fill text-orange-500"></i>
                                    <p class="text-[10px] font-bold lowercase tracking-tight truncate" x-text="user.email"></p>
                                </div>
                                
                                <div class="mt-auto space-y-3">
                                    <div class="flex gap-3">
                                        <button @click="userView = 'edit'; userData = { ...user }; showPass = false" class="flex-1 py-4 rounded-2xl bg-white/5 border border-white/10 text-[9px] font-black text-gray-300 hover:bg-white/10 hover:text-white uppercase tracking-widest transition-all">
                                            <i class="bi bi-person-gear mr-2"></i> PERFIL GESTOR
                                        </button>
                                        <button @click="userView = 'edit'; userData = { ...user }; showPass = true" class="px-5 py-4 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-[9px] font-black text-orange-500 hover:bg-orange-500 hover:text-black transition-all">
                                            <i class="bi bi-key-fill text-lg"></i>
                                        </button>
                                    </div>

                                    <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                                        <p class="text-[8px] font-bold text-gray-600 uppercase tracking-widest">AUTORIZACIÓN: <span class="text-gray-400">CORPORATIVA</span></p>
                                        
                                        <button @click="openDeleteModal('user', user)" x-show="'<?php echo $_SESSION['admin_email']; ?>' === 'Admin@latinmix.com' && user.id !== '<?php echo $_SESSION['admin_id']; ?>'" class="w-10 h-10 rounded-xl bg-red-500/5 text-red-500/40 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty State Users -->
                <div x-show="usersList.filter(u => u.name.toLowerCase().includes(searchTerm.toLowerCase()) || u.email.toLowerCase().includes(searchTerm.toLowerCase())).length === 0"
                     class="py-32 flex flex-col items-center justify-center space-y-6 animate-view">
                    <div class="w-24 h-24 bg-white/[0.03] border border-white/5 rounded-full flex items-center justify-center relative overflow-hidden">
                        <i class="bi bi-person-x text-4xl text-gray-800"></i>
                        <div class="absolute inset-0 bg-gradient-to-t from-orange-500/10 to-transparent"></div>
                    </div>
                    <div class="text-center">
                        <h4 class="text-xl font-black text-white italic uppercase tracking-tighter">Gestor no ubicado</h4>
                        <p class="text-[10px] text-gray-600 font-bold uppercase tracking-[0.2em] mt-2">No hay usuarios que coincidan con tu filtro</p>
                    </div>
                </div>
            </div>

            <!-- Create / Edit User Screen -->
            <div x-show="userView === 'create' || userView === 'edit'" x-transition class="max-w-4xl mx-auto py-10">
                <div class="glass-card p-16 relative overflow-hidden">
                    <!-- Brillo decorativo -->
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl"></div>

                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-16 relative">
                        <div class="flex items-center gap-5">
                            <div class="w-2 h-10 bg-orange-500 rounded-full shadow-[0_0_15px_rgba(245,150,30,0.5)] shrink-0"></div>
                            <div>
                                <h3 class="text-3xl md:text-4xl font-black italic tracking-tighter text-white uppercase leading-none" x-text="userView === 'create' ? 'RECLUTAR GESTOR' : 'EXPEDIENTE GESTOR'"></h3>
                                <p class="text-[9px] md:text-[10px] font-black uppercase tracking-[0.2em] md:tracking-[0.3em] text-gray-500 mt-2" x-text="userView === 'create' ? 'Configura un nuevo acceso administrativo' : 'Actualización de permisos y credenciales'"></p>
                            </div>
                        </div>
                        <button @click="userView = 'list'" class="w-full md:w-auto px-6 py-4 md:py-3 rounded-[2rem] md:rounded-xl bg-white/5 border border-white/10 text-[10px] font-black text-gray-400 hover:text-white uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-arrow-left text-lg md:text-sm"></i>
                            <span x-text="window.innerWidth < 768 ? 'VOLVER' : 'ABORTAR Y VOLVER'"></span>
                        </button>
                    </div>

                    <form x-ref="userForm" @submit.prevent="submitUser(userView === 'create' ? 'add_user' : 'edit_user')" class="space-y-10 relative">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" :value="userView === 'create' ? 'add_user' : 'edit_user'">
                        <input type="hidden" name="id" x-model="userData.id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-500 ml-1">Firma / Nombre Completo</label>
                                <div class="relative">
                                    <i class="bi bi-person absolute left-6 top-1/2 -translate-y-1/2 text-gray-600"></i>
                                    <input type="text" name="name" x-model="userData.name" class="w-full bg-white/[0.03] border border-white/10 rounded-[1.5rem] pl-14 pr-8 py-5 text-sm focus:ring-2 focus:ring-orange-500/30 outline-none text-white transition-all font-bold placeholder:text-gray-800" placeholder="Ej: Juan Pérez" required>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-500 ml-1">Canal de Contacto / Email</label>
                                <div class="relative">
                                    <i class="bi bi-envelope absolute left-6 top-1/2 -translate-y-1/2 text-gray-600"></i>
                                    <input type="email" name="email" x-model="userData.email" class="w-full bg-white/[0.03] border border-white/10 rounded-[1.5rem] pl-14 pr-8 py-5 text-sm focus:ring-2 focus:ring-orange-500/30 outline-none text-white transition-all font-bold placeholder:text-gray-800" placeholder="admin@latinmix.com" required>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-500 ml-1">Nivel de Privilegios</label>
                                <div class="relative">
                                    <i class="bi bi-shield-lock absolute left-6 top-1/2 -translate-y-1/2 text-gray-600"></i>
                                    <select name="role" x-model="userData.role" class="w-full bg-white/[0.03] border border-white/10 rounded-[1.5rem] pl-14 pr-12 py-5 text-sm focus:ring-2 focus:ring-orange-500/50 outline-none text-white transition-all font-bold appearance-none cursor-pointer">
                                        <option value="Admin" class="bg-black">Admin Colaborador</option>
                                        <option value="SuperAdmin" class="bg-black">Super Administrador</option>
                                        <option value="Editor" class="bg-black">Editor de Contenido</option>
                                    </select>
                                    <i class="bi bi-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-gray-600 pointer-events-none"></i>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[11px] font-black uppercase tracking-[0.2em] text-orange-400 italic ml-1" x-text="userView === 'create' ? 'Contraseña de Acceso' : 'Contraseña Actual'"></label>
                                <div class="relative">
                                    <i class="bi bi-key absolute left-6 top-1/2 -translate-y-1/2 text-gray-600"></i>
                                    <input :type="showPass ? 'text' : 'password'" name="password" x-model="userData.password" :required="userView === 'create'" class="w-full bg-white/[0.03] border border-white/10 rounded-[1.5rem] pl-14 pr-14 py-5 text-sm focus:ring-2 focus:ring-orange-500/30 outline-none text-white transition-all font-bold placeholder:text-gray-800" placeholder="••••••••">
                                    <button type="button" @click="showPass = !showPass" class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                                        <i class="bi" :class="showPass ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pt-12 border-t border-white/5 flex flex-col items-center">
                            <button type="submit" class="w-full py-6 rounded-[2rem] bg-gradient-to-r from-orange-500 to-yellow-500 text-black font-black uppercase tracking-[0.3em] text-xs hover:scale-[1.02] active:scale-95 transition-all shadow-[0_20px_50px_rgba(245,150,30,0.3)] flex items-center justify-center gap-4">
                                <i :class="userView === 'create' ? 'bi-person-check-fill' : 'bi-save-fill'" class="text-lg"></i>
                                <span x-text="userView === 'create' ? 'DECRETAR ACCESO ADMINISTRATIVO' : 'GUARDAR MODIFICACIONES DE PERFIL'"></span>
                            </button>
                            <p class="mt-6 text-[9px] text-gray-600 font-bold uppercase tracking-widest italic">Asegúrese de verificar la identidad del nuevo gestor antes de autorizar.</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Custom Delete Confirmation Modal -->
        <div x-show="deleteConfirmOpen" x-transition.opacity class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-xl flex items-center justify-center p-6" x-cloak>
            <div x-show="deleteConfirmOpen" x-transition.scale.90 class="glass-card max-w-sm w-full p-10 text-center space-y-6 !rounded-[3rem] border border-red-500/20 shadow-2xl shadow-red-500/5">
                <div class="w-20 h-20 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="bi bi-exclamation-triangle-fill text-4xl"></i>
                </div>
                <h4 class="text-xl font-black text-white italic uppercase tracking-tighter" x-text="itemToDelete?.type === 'user' ? '¿ELIMINAR GESTOR?' : '¿ELIMINAR REGISTRO?'"></h4>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest leading-relaxed">
                    <span x-show="itemToDelete?.type === 'user'">Esta acción revocará todos los accesos de <span class="text-white font-black" x-text="itemToDelete?.name"></span>.</span>
                    <span x-show="itemToDelete?.type === 'news' || itemToDelete?.type === 'ad'">¿Estás seguro de eliminar <span class="text-white font-black" x-text="itemToDelete?.title || itemToDelete?.advertiser"></span> permanentemente?</span>
                    Es un cambio irreversible.
                </p>
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button @click="deleteConfirmOpen = false; itemToDelete = null" class="py-4 rounded-2xl bg-white/5 text-[9px] font-black text-gray-400 hover:bg-white/10 hover:text-white uppercase tracking-widest transition-all">
                        CANCELAR
                    </button>
                    <button @click="confirmDelete()" class="py-4 rounded-2xl bg-red-600 text-white shadow-xl shadow-red-600/30 text-[9px] font-black uppercase tracking-widest hover:scale-105 active:scale-95 transition-all">
                        CONFIRMAR
                    </button>
                </div>
            </div>
        </div>

            </div> <!-- max-w-6xl -->
        </div> <!-- content-scroller -->
    </div> <!-- workspace -->
</div> <!-- app-container -->

    <script>
        // Any specific dashboard logic can go here
        console.log("LatinMix Admin Core Initialized");
    </script>
</body>
</html>
