<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaFEFO — Moteur de Stock Virtuel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        medical: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #059669; }

        .sidebar-link { position: relative; }
        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 4px; height: 0;
            background: #ffffff;
            border-radius: 0 4px 4px 0;
            transition: height 0.25s cubic-bezier(0.4,0,0.2,1);
        }
        .sidebar-link:hover::before { height: 50%; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .main-content { animation: fadeIn 0.35s cubic-bezier(0.4,0,0.2,1); }
    </style>
</head>

<body class="bg-slate-50/50 text-slate-900 antialiased font-sans min-h-screen flex">

    <div id="sidebar-overlay"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-md z-40 hidden lg:hidden"
         onclick="toggleSidebar()"></div>

    <aside id="sidebar"
           class="w-72 bg-gradient-to-b from-emerald-700 via-emerald-600 to-medical-600 text-emerald-100 fixed h-full top-0 left-0 z-50 flex flex-col justify-between border-r border-emerald-500 shadow-xl -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

        <div class="flex flex-col flex-1 overflow-y-auto">

            <div class="p-6 border-b border-emerald-500/40 flex items-center gap-3 bg-white/10">
                <div class="w-10 h-10 rounded-xl bg-white border border-emerald-200 text-emerald-600 flex items-center justify-center shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-white tracking-tight leading-none">Pharma<span class="text-emerald-200 font-mono">FEFO</span></h1>
                    <span class="text-[9px] text-emerald-100/80 font-semibold tracking-widest uppercase block mt-1">Moteur de Stock</span>
                </div>
            </div>

            <?php if (isset($_SESSION['user_nom'])): ?>
                <div class="m-4 p-4 rounded-xl bg-white/10 border border-white/20 flex items-center gap-3 backdrop-blur-md">
                    <div class="w-9 h-9 rounded-lg bg-white text-emerald-700 font-bold text-xs flex items-center justify-center uppercase shadow-sm">
                        <?= mb_substr($_SESSION['user_nom'], 0, 2) ?>
                    </div>
                    <div class="overflow-hidden flex-1 min-w-0">
                        <p class="font-semibold text-xs text-white truncate"><?= htmlspecialchars($_SESSION['user_nom']) ?></p>
                        <span class="text-[9px] px-2 py-0.5 rounded font-mono bg-white/20 text-white border border-white/30 uppercase tracking-wider inline-block mt-1 font-bold">
                            <?= htmlspecialchars($_SESSION['user_role']) ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>

            <nav class="px-3 py-2 space-y-1 flex-1">
                <span class="px-4 text-[10px] font-bold uppercase tracking-wider text-emerald-200 block mb-3 mt-2">Navigation Principale</span>

                <a href="index.php?action=dashboard" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 hover:bg-white/15 text-emerald-100 hover:text-white group">
                    <span class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                    </span>
                    Tableau de Bord
                </a>

                <?php if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['preparateur', 'pharmacien', 'admin'])): ?>
                    <a href="index.php?action=add-batch" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 hover:bg-white/15 text-emerald-100 hover:text-white group">
                        <span class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </span>
                        Entrée de Lot
                    </a>
                    <a href="index.php?action=dispense" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 hover:bg-white/15 text-emerald-100 hover:text-white group">
                        <span class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-orange-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                            </svg>
                        </span>
                        Sortie Intelligente
                    </a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <span class="px-4 pt-5 text-[10px] font-bold uppercase tracking-wider text-emerald-100 block mb-3">Privilèges Admin</span>
                    <a href="index.php?action=users" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 hover:bg-white/15 text-emerald-50 hover:text-white group">
                        <span class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-purple-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </span>
                        Gestion Équipe
                    </a>
                    <a href="index.php?action=report" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all duration-200 hover:bg-white/15 text-emerald-50 hover:text-white group">
                        <span class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 group-hover:bg-white/20 flex items-center justify-center transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-purple-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </span>
                        Rapport Financier
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <div class="p-4 border-t border-white/10 bg-white/5 backdrop-blur-sm">
            <a href="index.php?action=logout"
               class="flex items-center justify-center gap-2 w-full px-4 py-2 rounded-xl bg-white/10 hover:bg-rose-600 text-white text-xs font-semibold uppercase tracking-wider transition-all duration-200 border border-white/10 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                Déconnexion
            </a>
        </div>
    </aside>

    <div class="flex-grow lg:pl-72 min-h-screen flex flex-col w-full">

        <header class="h-16 bg-white/75 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-4">
                <button type="button" onclick="toggleSidebar()"
                        class="lg:hidden w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600 hover:bg-emerald-100 transition-colors"
                        aria-label="Ouvrir le menu">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="flex items-center gap-2.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs text-slate-400 font-medium hidden sm:inline">Base de données connectée via <span class="text-slate-600 font-mono">PDO</span></span>
                    <span class="text-xs text-slate-400 font-medium sm:hidden">PDO Actif</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-100/60">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-emerald-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <span class="text-xs text-emerald-800 font-semibold font-mono"><?= date('d M Y') ?></span>
                </div>
                <div class="sm:hidden text-xs text-slate-500 font-bold font-mono bg-slate-100 px-2.5 py-1 rounded">
                    <?= date('d/m/Y') ?>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 flex-grow max-w-[1400px] w-full mx-auto main-content">
            <?= $content ?? '<div class="flex flex-col items-center justify-center py-24 text-slate-400 bg-white rounded-2xl border border-slate-100 shadow-xs"><p class="text-sm font-medium text-slate-400">Aucun contenu disponible.</p></div>' ?>
        </main>

        <footer class="px-4 sm:px-8 py-5 bg-white border-t border-slate-200/60 mt-auto">
            <div class="max-w-[1400px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-400 font-medium">
                <p>&copy; 2026 <span class="text-emerald-700 font-semibold">PharmaFEFO</span> — Algorithme FEFO Strict homologué pour l'évaluation Web &amp; Mobile.</p>
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/50 text-[10px] font-bold uppercase tracking-wider">
                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span> FEFO
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-50 text-slate-500 border border-slate-200 text-[10px] font-mono font-bold">
                        v2026
                    </span>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>

    <!-- Scripts JS Partie 2 — chargés après le DOM -->
    <script src="js/app.js"></script>
    <script src="js/dashboard.js"></script>

</body>
</html>
