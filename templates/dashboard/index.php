<div class="space-y-6 text-slate-700 font-sans">

    <!-- Bannière d'alerte asynchrone — remplie par JS -->
    <div id="async-alert-banner"
         class="hidden mb-2 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r shadow-sm text-amber-900 text-xs font-bold">
        <span id="async-alert-text"></span>
    </div>

    <!-- Header avec filtres -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <div class="w-2 h-4 bg-slate-400 rounded-xs"></div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Surveillance Globale des Lots</h2>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">
                Visibilité analytique et traçabilité réglementaire ordonnées selon la méthode stricte
                <span class="font-semibold text-slate-600">Premier Périmé, Premier Sorti (FEFO)</span>.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto text-xs">
            <!-- Bouton admin — affiché par JS si rôle=admin -->
            <a id="btn-add-collaborateur"
               href="index.php?action=users"
               class="hidden px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold transition-all flex items-center gap-1.5 shadow-xs uppercase tracking-wider">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter Collaborateur
            </a>

            <button data-criteria="all"
                    class="filter-btn active px-3.5 py-2 rounded-lg font-medium border transition-all bg-indigo-600 text-white border-indigo-600">
                Tous les lots
            </button>
            <button data-criteria="critical"
                    class="filter-btn px-3.5 py-2 rounded-lg font-medium border transition-all text-rose-600 border-rose-100 hover:bg-rose-50/50">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 inline-block animate-pulse"></span>
                Alerte Critique (&lt;30j)
            </button>
        </div>
    </div>

    <!-- Couloir d'alerte FEFO — conteneur rempli par JS -->
    <div id="alert-corridor" class="hidden bg-amber-50/40 border border-amber-200 rounded-xl p-5 relative overflow-hidden">
        <div class="flex items-center gap-1.5 font-bold text-amber-800 text-[11px] tracking-wide uppercase mb-3.5">
            <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            COULOIR D'ALERTE FEFO (PÉREMPTION PRÉVUE LE MOIS PROCHAIN)
        </div>
        <!-- Cartes d'alerte générées dynamiquement par JS -->
        <div id="alert-corridor-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3"></div>
    </div>

    <!-- Tableau principal des lots -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">

        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-3 bg-slate-400 rounded-xs"></span> Etat Virtuel du Stock Actif
            </div>
            <span class="text-xs text-slate-500 bg-white px-2.5 py-1 rounded-md border border-slate-200 font-medium">
                Total : <span id="batch-count" class="font-bold text-slate-800">—</span> lot(s) filtré(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap text-xs">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-400 font-semibold tracking-wide uppercase">
                        <th class="py-3 px-5">Désignation Produit</th>
                        <th class="py-3 px-4">Référence</th>
                        <th class="py-3 px-4">N° de Lot</th>
                        <th class="py-3 px-4">Quantité Physique</th>
                        <th class="py-3 px-4">DLU (Péremption)</th>
                        <th class="py-3 px-4">Indicateur Diagnostic</th>
                        <th class="py-3 px-5 text-right">Actions Asynchrones</th>
                    </tr>
                </thead>
                <!-- Corps du tableau — entièrement généré par dashboard.js -->
                <tbody id="batch-table-body" class="divide-y divide-slate-100 text-slate-600">
                    <!-- Skeleton de chargement initial -->
                    <tr id="loading-row">
                        <td colspan="7" class="py-12 px-5 text-center text-slate-400 font-medium">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-5 h-5 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-xs">Chargement des lots…</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
