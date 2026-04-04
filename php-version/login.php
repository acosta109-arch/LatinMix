<?php
/**
 * PÁGINA DE INICIO DE SESIÓN - REDISEÑO PROFESIONAL
 */
require_once __DIR__ . '/includes/functions.php';

// Si ya está logueado, redirigir al admin
if (is_logged_in()) {
    redirect('admin.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validate_csrf($csrf_token)) {
        $error = 'Error de seguridad (CSRF). Inténtalo de nuevo.';
    } else {
        // Cargar usuarios dinámicos
        $users = load_data('radio_users');
        $user_found = null;
        
        foreach($users as $user) {
            if (strtolower($user['email']) === strtolower($email)) {
                $user_found = $user;
                break;
            }
        }

        if ($user_found && $user_found['password'] === $password) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $user_found['email'];
            $_SESSION['admin_name'] = $user_found['name'];
            $_SESSION['admin_id'] = $user_found['id'];
            redirect('admin.php');
        } else {
            $error = 'Credenciales incorrectas o usuario no activo.';
        }
    }
}

// Imagen de fondo generada
$bg_image = 'latin_musical_background_1774878126551.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo - LATIN MIX Radio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: #050505; 
            background-image: url('<?php echo $bg_image; ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(13, 13, 13, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-glass:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: #f5961e;
            box-shadow: 0 0 20px rgba(245, 150, 30, 0.2);
        }
        .btn-premium {
            background: linear-gradient(135deg, #f5961e 0%, #d22882 100%);
            box-shadow: 0 10px 25px -5px rgba(245, 150, 30, 0.4);
            transition: all 0.3s ease;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(245, 150, 30, 0.6);
            filter: brightness(1.1);
        }
        .music-note {
            position: absolute;
            opacity: 0.1;
            pointer-events: none;
            z-index: -1;
            animation: float 10s infinite linear;
        }
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100vh) rotate(360deg); }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6 overflow-hidden">
    <!-- Overlay sutil -->
    <div class="fixed inset-0 bg-black/40 z-0"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo/Cabecera -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-white tracking-widest uppercase italic mb-2">
                LATIN<span class="text-[#f5961e]"> MIX</span>
            </h1>
            <div class="h-1 w-20 bg-gradient-to-r from-[#f5961e] to-[#d22882] mx-auto rounded-full"></div>
        </div>

        <div class="glass-card rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden">
            <!-- Brillo decorativo superior -->
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-[#f5961e]/20 rounded-full blur-3xl"></div>
            
            <div class="text-center mb-10 relative">
                <h2 class="text-2xl font-bold text-white mb-2">Panel Administrativo</h2>
                <p class="text-gray-400 text-xs font-medium uppercase tracking-widest">Ingrese sus credenciales</p>
            </div>
            
            <form action="login.php" method="POST" class="space-y-6 relative">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="space-y-2">
                    <label class="block text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em] ml-1">Correo Electrónico</label>
                    <div class="relative group">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-indigo-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </span>
                        <input type="email" name="email" class="w-full input-glass rounded-2xl py-4 pl-14 pr-5 text-white focus:outline-none placeholder-gray-600" placeholder="admin@latinmix.com" required>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="block text-gray-400 text-[10px] font-bold uppercase tracking-[0.2em] ml-1">Contraseña</label>
                    <div class="relative group">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-indigo-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </span>
                        <input type="password" name="password" class="w-full input-glass rounded-2xl py-4 pl-14 pr-5 text-white focus:outline-none placeholder-gray-600" placeholder="••••••••" required>
                    </div>
                </div>
                
                <?php if ($error): ?>
                    <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold text-center flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <button type="submit" class="w-full py-5 rounded-2xl btn-premium text-white font-black uppercase tracking-[0.2em] text-xs transition-all active:scale-95 flex items-center justify-center gap-3">
                    AUTENTICAR ACCESO
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </form>
            
            <div class="mt-10 pt-8 border-t border-white/5 text-center">
                <a href="index.php" class="text-gray-500 text-[10px] font-bold hover:text-[#f5961e] transition-colors tracking-widest uppercase flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    VOLVER A LA EMISORA
                </a>
            </div>
        </div>
        
        <!-- Footer Info -->
        <p class="text-center mt-8 text-gray-600 text-[9px] uppercase tracking-[0.4em] font-bold">
            LATIN MIX Radio &copy; <?php echo date('Y'); ?> • Panel de Gestión v2.0
        </p>
    </div>

    <!-- Notas musicales animadas -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;
            const notes = ['♪', '♫', '♬', '♩'];
            const colors = ['#f5961e', '#ffe632', '#d22882', '#781496'];
            
            for (let i = 0; i < 20; i++) {
                const note = document.createElement('div');
                note.className = 'music-note';
                note.style.color = colors[Math.floor(Math.random() * colors.length)];
                note.innerText = notes[Math.floor(Math.random() * notes.length)];
                note.style.left = Math.random() * 100 + 'vw';
                note.style.top = (Math.random() * 100 + 100) + 'vh'; // Empiezan abajo
                note.style.fontSize = (Math.random() * 20 + 20) + 'px';
                note.style.opacity = (Math.random() * 0.4 + 0.1).toString();
                note.style.animationDelay = (Math.random() * 10) + 's';
                note.style.animationDuration = (Math.random() * 10 + 15) + 's';
                body.appendChild(note);
            }
        });
    </script>
</body>
</html>
