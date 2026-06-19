<?php
/**
 * templates/dashboard/sortie.php
 * Design identique à la Partie 1 — aucune modification visuelle.
 *
 * @var array       $products
 * @var array|null  $suggestedBatch
 * @var int|null    $selectedProductId
 * @var string|null $error
 * @var string|null $success
 */
?>
<div class="max-w-4xl mx-auto bg-slate-900 text-slate-100 p-6 sm:p-8 rounded-3xl shadow-xl border border-slate-800">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800 mb-6">
        <div>
            <span class="text-[10px] font-bold tracking-widest text-emerald-400 uppercase bg-emerald-500/10 px-2.5 py-1 rounded-md">Moteur FEFO Intuitif</span>
            <h2 class="text-xl font-bold tracking-tight text-white mt-2">DÉSTOCKAGE AUTOMATIQUE</h2>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-[11px] text-slate-400 font-medium">Session active : <span class="text-slate-200">Préparateur</span></p>
            <p class="text-[9px] text-slate-500 font-mono mt-0.5">Rule: Safe Extraction closest expiration</p>
        </div>
    </div>

    <?php if (!empty($error) || !empty($success)): ?>
        <div class="mb-6">
            <?php if (!empty($error)): ?>
                <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs font-medium rounded-2xl flex items-center gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-medium rounded-2xl flex items-center gap-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="index.php" class="mb-8 p-1 bg-slate-950 border border-slate-800 rounded-2xl flex flex-col sm:flex-row gap-1 items-stretch">
        <input type="hidden" name="action" value="dispense">
        <div class="flex-grow flex items-center px-4 py-2 sm:py-0">
            <span class="text-[10px] font-black uppercase text-emerald-500 tracking-wider mr-3 shrink-0">01 / Produit</span>
            <select name="prod_id" class="w-full bg-transparent text-xs font-medium text-slate-300 focus:outline-none cursor-pointer appearance-none" required>
                <option value="" class="bg-slate-950">-- Sélectionner le médicament --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($selectedProductId == $p['id']) ? 'selected' : '' ?> class="bg-slate-950 text-slate-300">
                        <?= htmlspecialchars($p['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-xs px-6 py-3 rounded-xl transition-all uppercase tracking-wide shrink-0">
            Analyser Lot
        </button>
    </form>

    <?php if ($selectedProductId): ?>
        <?php if ($suggestedBatch): ?>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <div class="md:col-span-3 p-5 bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 rounded-2xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-4">
                            <span class="inline-block w-2 h-2 rounded-sm bg-emerald-500"></span> 02 / Cible d'extraction optimal
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">Le moteur FEFO préconise la sortie prioritaire du lot suivant en raison de sa date de péremption imminente :</p>
                    </div>
                    <div class="mt-6 space-y-2.5 font-mono text-xs">
                        <div class="flex justify-between p-2 bg-slate-900/60 rounded-xl border border-slate-800/40">
                            <span class="text-slate-500">Batch ID</span>
                            <span class="text-slate-200 font-bold"><?= htmlspecialchars($suggestedBatch['numero_lot']) ?></span>
                        </div>
                        <div class="flex justify-between p-2 bg-slate-900/60 rounded-xl border border-slate-800/40">
                            <span class="text-slate-500">Expiration</span>
                            <span class="text-rose-400 font-bold"><?= $suggestedBatch['date_peremption'] ?></span>
                        </div>
                        <div class="flex justify-between p-2 bg-slate-900/60 rounded-xl border border-slate-800/40">
                            <span class="text-slate-500">Vol. Disponible</span>
                            <span class="text-emerald-400 font-bold"><?= $suggestedBatch['quantite'] ?> unités</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="index.php?action=dispense"
                      class="md:col-span-2 p-5 bg-slate-950 border border-slate-800 rounded-2xl flex flex-col justify-between space-y-4">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-3">03 / Quantité</span>
                        <input type="number" name="qty" min="1" max="<?= $suggestedBatch['quantite'] ?>"
                               placeholder="Qté ex: 5"
                               class="w-full p-3 bg-slate-900 border border-slate-800 rounded-xl text-xs font-mono text-white focus:outline-none focus:border-emerald-500 transition-all placeholder:text-slate-600"
                               required>
                        <span class="text-[10px] text-slate-500 mt-2 block leading-snug">
                            Seuil limite bridé à <span class="text-slate-300 font-mono font-semibold"><?= $suggestedBatch['quantite'] ?>u</span>.
                        </span>
                    </div>
                    <input type="hidden" name="lot_id" value="<?= $suggestedBatch['id'] ?>">
                    <button type="submit"
                            class="w-full py-3.5 bg-slate-800 hover:bg-emerald-600 hover:text-slate-950 border border-slate-700 hover:border-emerald-500 text-slate-200 font-bold text-xs rounded-xl transition-all uppercase tracking-wider shadow-md">
                        Valider Sortie
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="p-6 bg-rose-500/5 border border-rose-500/10 text-rose-400 text-xs font-semibold rounded-2xl text-center">
                ⚠️ Alerte rupture critique : Aucun lot actif disponible sur le réseau de stockage pour ce produit.
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>
