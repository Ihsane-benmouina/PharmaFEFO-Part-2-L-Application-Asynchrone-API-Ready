<div class="max-w-xl mx-auto bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl text-slate-100">

    <div class="mb-6 pb-4 border-b border-slate-800">
        <h2 class="text-base font-bold text-white tracking-tight uppercase flex items-center gap-2">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
            </span>
            Flux de Veille d'Anticipation
        </h2>
        <p class="text-xs text-slate-400 mt-1 font-medium">Lots dont l'expiration (DLU) intervient au cours du cycle mensuel suivant :</p>
    </div>

    <!-- Conteneur rempli par JS via GET /api/v1/dashboard/notifications -->
    <div id="notifications-loading" class="flex flex-col items-center gap-2 py-8 text-slate-400">
        <div class="w-5 h-5 border-2 border-orange-400 border-t-transparent rounded-full animate-spin"></div>
        <span class="text-xs">Chargement des alertes…</span>
    </div>

    <!-- Message vide — affiché par JS si aucune alerte -->
    <div id="notifications-empty" class="hidden p-6 bg-slate-950 border border-slate-800 rounded-2xl text-center">
        <p class="text-xs font-medium text-emerald-400 font-mono">✓ STATUS_OK: Aucun élément critique détecté pour le mois prochain.</p>
    </div>

    <!-- Timeline des alertes — générée par JS -->
    <div id="notifications-list" class="hidden relative pl-4 border-l border-slate-800 space-y-5"></div>

    <div class="mt-8 pt-4 border-t border-slate-800 flex justify-start">
        <a href="index.php?action=dashboard" class="text-[11px] font-mono text-slate-400 hover:text-emerald-400 transition-colors flex items-center gap-1">
            <span>&lt;!--</span> Retour Dashboard <span>--&gt;</span>
        </a>
    </div>
</div>
