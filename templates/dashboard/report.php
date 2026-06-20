<div class="max-w-xl mx-auto bg-slate-950 p-1 rounded-3xl shadow-2xl border border-slate-800">
    <div class="bg-gradient-to-b from-slate-900 to-slate-950 p-6 sm:p-8 rounded-[22px]">

        <div class="flex justify-between items-start border-b border-slate-800/80 pb-4 mb-6">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider">Comptabilité Analytique</h2>
                <p class="text-[10px] text-slate-500 mt-0.5 font-medium">Extraction : <span class="text-slate-400">Pertes de Stock Périodiques</span></p>
            </div>
            <span class="text-[9px] font-mono font-semibold bg-slate-800 text-slate-300 px-2 py-0.5 rounded border border-slate-700/60 uppercase">Scope Admin</span>
        </div>

        <p class="text-[11px] text-slate-400 font-medium leading-relaxed mb-6 text-left">
            Ce tableau d'analyse liste la valorisation financière nette des unités médicales dont le cycle de vie a expiré et dont l'état en base de données a été marqué avec le tag applicatif
            <span class="font-mono bg-slate-900 border border-slate-800 px-1.5 py-0.5 rounded text-rose-400 text-[10px]">Status::EXPIRED</span>.
        </p>

        <!-- Valeur financière — injectée par JS via GET /api/v1/report/loss -->
        <div class="relative overflow-hidden p-6 bg-gradient-to-r from-slate-900 to-slate-950 border border-slate-800 rounded-2xl text-left my-4 group">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-16 h-16 text-rose-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest">Valeur Financière du Gâchis</span>
            <div class="mt-2 flex items-baseline gap-2 font-mono">
                <!-- Spinner affiché pendant le chargement -->
                <div id="report-loading" class="flex items-center gap-2">
                    <div class="w-5 h-5 border-2 border-rose-400 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-slate-400 text-sm">Calcul en cours…</span>
                </div>
                <!-- Valeur injectée par JS -->
                <span id="report-total-loss" class="hidden text-4xl font-extrabold text-white tracking-tight"></span>
                <span id="report-currency" class="hidden text-sm font-sans font-bold text-rose-500 uppercase">DH</span>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-900 text-left flex items-center justify-between text-[10px] text-slate-500 font-mono">
            <div class="flex items-center gap-1.5">
                <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                <span>Source: rapports_perte</span>
            </div>
            <span>Statut: Live Sync</span>
        </div>

    </div>
</div>
