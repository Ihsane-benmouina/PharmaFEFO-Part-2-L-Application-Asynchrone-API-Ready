<div class="max-w-4xl mx-auto bg-slate-900 text-slate-100 p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-800">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800 mb-6">
        <div>
            <span class="text-[10px] font-bold tracking-widest text-emerald-400 uppercase bg-emerald-500/10 px-2.5 py-1 rounded-md">Moteur FEFO Intuitif</span>
            <h2 class="text-xl font-bold tracking-tight text-white mt-2">DÉSTOCKAGE AUTOMATIQUE</h2>
        </div>
        <div class="text-left sm:text-right">
            <p id="sortie-user-info" class="text-[11px] text-slate-400 font-medium">Session active : <span class="text-slate-200">—</span></p>
            <p class="text-[9px] text-slate-500 font-mono mt-0.5">Rule: Safe Extraction closest expiration</p>
        </div>
    </div>

    <!-- Feedback JS (succès / erreur) -->
    <div id="sortie-feedback" class="hidden mb-6"></div>

    <!-- Étape 1 : sélection produit -->
    <div class="mb-8 p-1 bg-slate-950 border border-slate-800 rounded-2xl flex flex-col sm:flex-row gap-1 items-stretch">
        <div class="flex-grow flex items-center px-4 py-2 sm:py-0">
            <span class="text-[10px] font-black uppercase text-emerald-500 tracking-wider mr-3 shrink-0">01 / Produit</span>
            <!-- Options injectées par JS via GET /api/v1/products -->
            <select id="sortie-select-produit"
                    class="w-full bg-transparent text-xs font-medium text-slate-300 focus:outline-none cursor-pointer appearance-none">
                <option value="" class="bg-slate-950">Chargement des produits…</option>
            </select>
        </div>
        <button id="btn-analyser-lot"
                class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-xs px-6 py-3 rounded-xl transition-all uppercase tracking-wide shrink-0">
            Analyser Lot
        </button>
    </div>

    <!-- Étape 2 & 3 : résultat FEFO + formulaire déstockage — remplis par JS -->
    <div id="sortie-result" class="hidden">
        <!-- Lot suggéré par l'algorithme FEFO -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

            <div class="md:col-span-3 p-5 bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 rounded-2xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-4">
                        <span class="inline-block w-2 h-2 rounded-sm bg-emerald-500"></span>
                        02 / Cible d'extraction optimal
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Le moteur FEFO préconise la sortie prioritaire du lot suivant en raison de sa date de péremption imminente :
                    </p>
                </div>
                <div class="mt-6 space-y-2.5 font-mono text-xs">
                    <div class="flex justify-between p-2 bg-slate-900/60 rounded-xl border border-slate-800/40">
                        <span class="text-slate-500">Batch ID</span>
                        <span id="fefo-lot-numero" class="text-slate-200 font-bold">—</span>
                    </div>
                    <div class="flex justify-between p-2 bg-slate-900/60 rounded-xl border border-slate-800/40">
                        <span class="text-slate-500">Expiration</span>
                        <span id="fefo-lot-expiry" class="text-rose-400 font-bold">—</span>
                    </div>
                    <div class="flex justify-between p-2 bg-slate-900/60 rounded-xl border border-slate-800/40">
                        <span class="text-slate-500">Vol. Disponible</span>
                        <span id="fefo-lot-qty" class="text-emerald-400 font-bold">— unités</span>
                    </div>
                </div>
            </div>

            <!-- Étape 3 : saisie quantité + bouton déstockage -->
            <div class="md:col-span-2 p-5 bg-slate-950 border border-slate-800 rounded-2xl flex flex-col justify-between space-y-4">
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-3">03 / Quantité</span>
                    <input type="number" id="sortie-qty-input"
                           min="1" placeholder="Qté ex: 5"
                           class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl text-xs font-mono text-white focus:outline-none focus:border-emerald-500 transition-all placeholder:text-slate-600"
                           required>
                    <span class="text-[10px] text-slate-500 mt-2 block leading-snug">
                        Seuil limite bridé à <span id="fefo-lot-max" class="text-slate-300 font-mono font-semibold">—</span>u.
                    </span>
                </div>
                <!-- data-lot-id injecté par JS lors du chargement du lot FEFO -->
                <button id="btn-valider-sortie" data-lot-id=""
                        class="w-full py-3.5 bg-slate-800 hover:bg-emerald-600 hover:text-slate-950 border border-slate-700 hover:border-emerald-500 text-slate-200 font-bold text-xs rounded-xl transition-all uppercase tracking-wider shadow-md">
                    Valider Sortie
                </button>
            </div>
        </div>
    </div>

    <!-- Message rupture de stock — affiché par JS si aucun lot disponible -->
    <div id="sortie-no-stock" class="hidden p-6 bg-rose-500/5 border border-rose-500/10 text-rose-400 text-xs font-semibold rounded-2xl text-center">
        ⚠️ Alerte rupture critique : Aucun lot actif disponible sur le réseau de stockage pour ce produit.
    </div>

</div>
