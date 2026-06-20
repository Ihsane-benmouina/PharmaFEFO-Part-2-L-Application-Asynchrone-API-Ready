<div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-md border border-gray-100">

    <div class="mb-6 border-b pb-4">
        <h2 class="text-xl font-black text-gray-950 flex items-center gap-2">
            Réception &amp; Entrées intelligentes
        </h2>
        <p class="text-xs text-gray-500 mt-1">Rôle : Préparateur en pharmacie — Saisie des numéros de lot et DLU</p>
    </div>

    <!-- Formulaire — soumission interceptée par dashboard.js via fetch() -->
    <form id="async-add-batch-form" class="space-y-5">

        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-1.5">Médicament Réceptionné :</label>
            <!-- Options injectées par JS via GET /api/v1/products -->
            <select name="produit_id" id="select-produit"
                    class="w-full p-3 border border-gray-200 rounded-lg bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required>
                <option value="">Chargement des produits…</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-1.5">Numéro de Lot (Batch Number) :</label>
            <input type="text" name="numero_lot" id="input-numero-lot"
                   placeholder="Ex: LOT-2026-AUGM"
                   class="w-full p-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   required>
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-1.5">Quantité (Unités boîtes) :</label>
            <input type="number" name="quantite" id="input-quantite"
                   min="1" placeholder="Ex: 100"
                   class="w-full p-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   required>
        </div>

        <div>
            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-1.5">Date de Péremption (DLU) :</label>
            <input type="date" name="date_peremption" id="input-date-peremption"
                   class="w-full p-3 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   required>
            <span class="text-[11px] text-gray-400 mt-1 block">Le système validera uniquement si la date est supérieure ou égale à aujourd'hui.</span>
        </div>

        <button type="submit" id="btn-submit-batch"
                class="w-full bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-sm p-3.5 rounded-lg shadow transition-all uppercase tracking-wider mt-2">
            Enregistrer et Classer dans la file FEFO
        </button>

    </form>
</div>
