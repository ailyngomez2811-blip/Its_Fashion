<?php
session_start();
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: login.php");
    exit;
}
$token = htmlspecialchars($_GET['token']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Its Fashion | Nueva Contraseña</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../../img/icono head .png" type="image/png">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; } .gradient-soft { background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 100%); } .gradient-accent { background: linear-gradient(135deg, #3b82f6 0%, #3b82f6 100%); } .custom-shadow { box-shadow: 0 20px 50px rgba(59, 130, 246, 0.15); } .fade-in { animation: fadeIn 0.5s ease-out; } @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }</style>
</head>
<body class="gradient-soft min-h-screen flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden custom-shadow p-8 md:p-12 fade-in">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 gradient-accent rounded-2xl mb-4 shadow-lg shadow-blue-200">
                <i class="fas fa-lock text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight mb-2">Nueva Contraseña</h1>
            <p class="text-slate-500 text-sm">Crea una contraseña segura para tu cuenta</p>
        </div>

        <?php if (isset($_SESSION['alert'])): ?>
            <?php 
                $alert = $_SESSION['alert']; 
                $bg = $alert['icon'] === 'success' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-red-50 border-red-500 text-red-700';
                $fa = $alert['icon'] === 'success' ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500';
            ?>
            <div class="mb-6 p-4 <?= $bg ?> border-l-4 rounded-lg text-sm flex items-start gap-3">
                <i class="fas <?= $fa ?> mt-0.5 flex-shrink-0"></i>
                <span class="flex-1"><?= htmlspecialchars($alert['text']) ?></span>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <form method="POST" action="../../controllers/auth/RecuperacionController.php?action=restablecer" class="space-y-5">
            <input type="hidden" name="token" value="<?= $token ?>">
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nueva Contraseña <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-blue-500 transition-colors pointer-events-none">
                        <i class="fas fa-key text-sm"></i>
                    </span>
                    <input type="password" name="password" placeholder="••••••••" required
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white transition-all text-slate-700 placeholder:text-slate-400">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Confirmar Contraseña <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-blue-500 transition-colors pointer-events-none">
                        <i class="fas fa-check text-sm"></i>
                    </span>
                    <input type="password" name="confirmar_password" placeholder="••••••••" required
                        class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white transition-all text-slate-700 placeholder:text-slate-400">
                </div>
            </div>

            <button type="submit" class="w-full gradient-accent text-white font-bold py-4 rounded-xl hover:shadow-xl hover:shadow-blue-200 transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg shadow-blue-200">
                Guardar Contraseña
            </button>
        </form>
    </div>
</body>
</html>
