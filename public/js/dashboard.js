// public/js/dashboard.js
// Tableau de bord principal — toutes les données chargées via fetch()

document.addEventListener('DOMContentLoaded', () => {

    // Détection de la page active par présence d'un élément clé
    const isBatchTable  = !!document.getElementById('batch-table-body');
    const isAddBatch    = !!document.getElementById('async-add-batch-form');
    const isSortie      = !!document.getElementById('sortie-select-produit');
    const isUsers       = !!document.getElementById('users-table-body');
    const isReport      = !!document.getElementById('report-total-loss');
    const isNotif       = !!document.getElementById('notifications-list');

    if (isBatchTable) {
        initDashboard();
    }
    if (isAddBatch) {
        initAddBatchPage();
    }
    if (isSortie) {
        initSortiePage();
    }
    if (isUsers) {
        initUsersPage();
    }
    if (isReport) {
        initReportPage();
    }
    if (isNotif) {
        initNotificationsPage();
    }


    // ═══════════════════════════════════════════════════════════
    // PAGE : TABLEAU DE BORD (index.php?action=dashboard)
    // ═══════════════════════════════════════════════════════════
    function initDashboard() {
        loadAlertBanner();
        loadAlertCorridor();
        loadBatches('all');
        initFilterButtons();
    }

    // ── Bannière alerte asynchrone ─────────────────────────────
    async function loadAlertBanner() {
        try {
            const res = await PharmaUI.apiFetch('index.php?action=api/v1/dashboard/alerts');
            if (res.success && res.data.expiringNextMonth > 0) {
                const banner = document.getElementById('async-alert-banner');
                const text   = document.getElementById('async-alert-text');
                if (banner && text) {
                    text.textContent = `⚠️ Prévoyance FEFO : ${res.data.expiringNextMonth} produit(s) expirent dans les 30 prochains jours.`;
                    banner.classList.remove('hidden');
                }
            }
        } catch (e) { /* silencieux */ }
    }

    // ── Couloir d'alerte FEFO (mois prochain) ─────────────────
    async function loadAlertCorridor() {
        try {
            const res = await PharmaUI.apiFetch('index.php?action=api/v1/dashboard/notifications');
            if (!res.success || !res.data.length) return;

            const corridor = document.getElementById('alert-corridor');
            const cards    = document.getElementById('alert-corridor-cards');
            if (!corridor || !cards) return;

            cards.innerHTML = '';
            res.data.forEach(n => {
                const card = document.createElement('div');
                card.className = 'bg-white border border-amber-100 p-3 rounded-lg flex justify-between items-center shadow-xs';
                card.innerHTML = `
                    <div class="space-y-0.5 max-w-[65%]">
                        <span class="font-semibold text-xs text-slate-700 block truncate">${PharmaUI.esc(n.produitNom)}</span>
                        <span class="inline-block text-[10px] text-slate-400 font-mono">Lot: ${PharmaUI.esc(n.numeroLot)}</span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="font-mono font-bold text-amber-700 text-xs block">${PharmaUI.esc(n.datePeremption)}</span>
                        <span class="text-[9px] text-amber-800 bg-amber-100/60 px-1.5 py-0.5 rounded-full font-semibold uppercase mt-1 inline-block">Alerte Proche</span>
                    </div>`;
                cards.appendChild(card);
            });

            corridor.classList.remove('hidden');
        } catch (e) { /* silencieux */ }
    }

    // ── Chargement & rendu du tableau de lots ─────────────────
    async function loadBatches(criteria = 'all') {
        const tbody = document.getElementById('batch-table-body');
        if (!tbody) return;

        // Skeleton spinner
        tbody.innerHTML = `
            <tr><td colspan="7" class="py-12 px-5 text-center text-slate-400">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-5 h-5 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-xs">Chargement des lots…</span>
                </div>
            </td></tr>`;

        try {
            const url = `index.php?action=api/v1/batches${criteria !== 'all' ? '&criteria=' + criteria : ''}`;
            const res = await PharmaUI.apiFetch(url);

            if (res.success) {
                renderBatchTable(res.data);
                // Mise à jour du compteur
                const counter = document.getElementById('batch-count');
                if (counter) counter.textContent = res.data.length;
            }
        } catch (e) {
            tbody.innerHTML = `
                <tr><td colspan="7" class="py-10 px-5 text-center text-rose-400 font-medium text-xs">
                    Erreur lors du chargement des lots.
                </td></tr>`;
        }
    }

    function renderBatchTable(batches) {
        const tbody = document.getElementById('batch-table-body');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (!batches || batches.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="7" class="py-12 px-5 text-center text-slate-400 font-medium">
                    <p class="text-sm font-bold text-slate-800">Aucun lot actif disponible</p>
                    <p class="text-xs text-slate-400 mt-0.5">Aucun lot ne correspond aux critères de tri actuels.</p>
                </td></tr>`;
            return;
        }

        batches.forEach(lot => {
            const badge = PharmaUI.getBadgeInfo(lot.expiryDate, lot.status);
            const tr    = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/50 transition-colors duration-100';
            tr.setAttribute('data-id',         lot.id);
            tr.setAttribute('data-product-id', lot.productId);

            tr.innerHTML = `
                <td class="py-3.5 px-5">
                    <span class="font-bold text-slate-900 block">${PharmaUI.esc(lot.productName)}</span>
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Stock ID: #${lot.id}</span>
                </td>
                <td class="py-3.5 px-4 font-mono text-slate-400">${PharmaUI.esc(lot.reference || 'N/A')}</td>
                <td class="py-3.5 px-4 font-mono font-semibold text-slate-600">
                    <span class="bg-slate-100/60 px-2 py-0.5 rounded border border-slate-200/40">${PharmaUI.esc(lot.batchNumber)}</span>
                </td>
                <td class="py-3.5 px-4">
                    <span class="font-bold text-slate-800 text-sm batch-qty">${lot.quantity}</span>
                    <span class="text-[10px] text-slate-400 ml-0.5">unités</span>
                </td>
                <td class="py-3.5 px-4 font-mono text-slate-700">${PharmaUI.esc(lot.expiryDate)}</td>
                <td class="py-3.5 px-4">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border ${badge.classes}">${badge.label}</span>
                </td>
                <td class="py-3.5 px-5 text-right space-x-1 whitespace-nowrap">
                    <button class="btn-checkout bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] uppercase font-bold px-2.5 py-1.5 rounded shadow-sm transition-all">
                        Délivrer 1 boîte
                    </button>
                    <button class="btn-expire bg-rose-600 hover:bg-rose-700 text-white text-[10px] uppercase font-bold px-2.5 py-1.5 rounded shadow-sm transition-all">
                        Périmer
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });

        // (ré)attache la délégation après chaque rendu
        attachTableActions();
    }

    // ── Boutons de filtre ──────────────────────────────────────
    function initFilterButtons() {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const criteria = e.currentTarget.getAttribute('data-criteria') || 'all';

                // Style actif
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600', 'active');
                    b.classList.add('text-slate-600', 'border-slate-200', 'hover:bg-slate-50');
                });
                e.currentTarget.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600', 'active');
                e.currentTarget.classList.remove('text-slate-600', 'border-slate-200', 'hover:bg-slate-50');

                loadBatches(criteria);
            });
        });
    }

    // ── Actions tableau : Délivrer & Périmer (délégation) ─────
    function attachTableActions() {
        const tbody = document.getElementById('batch-table-body');
        if (!tbody) return;

        // Remplacer l'ancien handler pour éviter les doublons
        tbody.onclick = null;
        tbody.onclick = async (event) => {
            const target = event.target;

            // Délivrer 1 boîte (FEFO checkout)
            if (target.classList.contains('btn-checkout')) {
                const tr        = target.closest('tr');
                const productId = tr.getAttribute('data-product-id');

                target.disabled    = true;
                target.textContent = '…';

                const fd = new FormData();
                fd.append('produit_id', productId);
                fd.append('quantite',   1);

                try {
                    const res = await PharmaUI.apiFetch('index.php?action=api/v1/batches/checkout', {
                        method: 'POST', body: fd
                    });
                    if (res.success) {
                        PharmaUI.showToast('Déstockage FEFO validé.', 'success');
                        loadBatches(getActiveCriteria());
                    } else {
                        PharmaUI.showToast(res.message, 'error');
                        target.disabled    = false;
                        target.textContent = 'Délivrer 1 boîte';
                    }
                } catch (e) {
                    target.disabled    = false;
                    target.textContent = 'Délivrer 1 boîte';
                }
            }

            // Périmer un lot
            if (target.classList.contains('btn-expire')) {
                const tr      = target.closest('tr');
                const batchId = tr.getAttribute('data-id');

                if (!confirm('Forcer la péremption de ce lot ? La quantité sera mise à zéro.')) return;

                target.disabled    = true;
                target.textContent = '…';

                try {
                    const res = await PharmaUI.apiFetch(
                        `index.php?action=api/v1/batches/${batchId}/expire`,
                        { method: 'PATCH' }
                    );
                    if (res.success) {
                        PharmaUI.showToast(res.message, 'success');
                        // Mise à jour DOM immédiate
                        const qtyEl = tr.querySelector('.batch-qty');
                        if (qtyEl) qtyEl.textContent = '0';
                        tr.style.opacity    = '0.4';
                        tr.style.background = '#f8fafc';
                        target.remove();
                    } else {
                        PharmaUI.showToast(res.message, 'error');
                        target.disabled    = false;
                        target.textContent = 'Périmer';
                    }
                } catch (e) {
                    target.disabled    = false;
                    target.textContent = 'Périmer';
                }
            }
        };
    }

    function getActiveCriteria() {
        const active = document.querySelector('.filter-btn.active');
        return active ? (active.getAttribute('data-criteria') || 'all') : 'all';
    }


    // ═══════════════════════════════════════════════════════════
    // PAGE : ENTRÉE DE LOT (index.php?action=add-batch)
    // ═══════════════════════════════════════════════════════════
    async function initAddBatchPage() {
        await loadProductsSelect('select-produit');
        initAddBatchForm();
    }

    async function loadProductsSelect(selectId) {
        const select = document.getElementById(selectId);
        if (!select) return;

        try {
            const res = await PharmaUI.apiFetch('index.php?action=api/v1/products');
            if (!res.success) return;

            select.innerHTML = '<option value="">-- Choisir un produit --</option>';
            res.data.forEach(p => {
                const opt = document.createElement('option');
                opt.value       = p.id;
                opt.textContent = `${p.nom} (Ref: ${p.reference || 'N/A'})`;
                select.appendChild(opt);
            });
        } catch (e) {
            select.innerHTML = '<option value="">Erreur de chargement</option>';
        }
    }

    function initAddBatchForm() {
        const form = document.getElementById('async-add-batch-form');
        const btn  = document.getElementById('btn-submit-batch');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (btn) { btn.disabled = true; btn.textContent = 'Enregistrement…'; }

            const fd = new FormData(form);

            try {
                const res = await PharmaUI.apiFetch('index.php?action=api/v1/batches', {
                    method: 'POST', body: fd
                });
                if (res.success) {
                    PharmaUI.showToast(res.message, 'success');
                    form.reset();
                    // Remettre le placeholder du select
                    const sel = document.getElementById('select-produit');
                    if (sel) sel.selectedIndex = 0;
                } else {
                    PharmaUI.showToast(res.message, 'error');
                }
            } catch (e) {
                PharmaUI.showToast('Erreur lors de l\'enregistrement du lot.', 'error');
            } finally {
                if (btn) { btn.disabled = false; btn.textContent = 'Enregistrer et Classer dans la file FEFO'; }
            }
        });
    }


    // ═══════════════════════════════════════════════════════════
    // PAGE : SORTIE INTELLIGENTE (index.php?action=dispense)
    // ═══════════════════════════════════════════════════════════
    async function initSortiePage() {
        // Remplir les infos utilisateur dans le header de la page
        try {
            const me = await PharmaUI.apiFetch('index.php?action=api/v1/me');
            const el = document.getElementById('sortie-user-info');
            if (el && me.success) {
                el.innerHTML = `Session active : <span class="text-slate-200">${PharmaUI.esc(me.data.nom)}</span>`;
            }
        } catch(e) {}

        // Charger les produits dans le select
        await loadProductsSelect('sortie-select-produit');

        // Bouton "Analyser Lot"
        const btnAnalyse = document.getElementById('btn-analyser-lot');
        if (btnAnalyse) {
            btnAnalyse.addEventListener('click', handleAnalyserLot);
        }

        // Bouton "Valider Sortie"
        const btnValider = document.getElementById('btn-valider-sortie');
        if (btnValider) {
            btnValider.addEventListener('click', handleValiderSortie);
        }
    }

    async function handleAnalyserLot() {
        const select    = document.getElementById('sortie-select-produit');
        const productId = select ? select.value : '';

        hideSortieBlocks();

        if (!productId) {
            showSortieFeedback('Veuillez sélectionner un produit.', 'error');
            return;
        }

        const btn = document.getElementById('btn-analyser-lot');
        if (btn) { btn.disabled = true; btn.textContent = 'Analyse…'; }

        try {
            // On récupère le lot FEFO via les lots filtrés pour ce produit
            const res = await PharmaUI.apiFetch(
                `index.php?action=api/v1/batches/fefo&produit_id=${productId}`
            );

            if (res.success && res.data) {
                const lot = res.data;
                // Remplir les infos du lot
                document.getElementById('fefo-lot-numero').textContent = lot.batchNumber || '—';
                document.getElementById('fefo-lot-expiry').textContent = lot.expiryDate  || '—';
                document.getElementById('fefo-lot-qty').textContent    = `${lot.quantity} unités`;
                document.getElementById('fefo-lot-max').textContent    = lot.quantity;

                const qtyInput = document.getElementById('sortie-qty-input');
                if (qtyInput) qtyInput.setAttribute('max', lot.quantity);

                const btnValider = document.getElementById('btn-valider-sortie');
                if (btnValider) btnValider.setAttribute('data-lot-id', lot.id);

                document.getElementById('sortie-result').classList.remove('hidden');
            } else {
                document.getElementById('sortie-no-stock').classList.remove('hidden');
            }
        } catch (e) {
            showSortieFeedback('Erreur lors de l\'analyse du lot FEFO.', 'error');
        } finally {
            if (btn) { btn.disabled = false; btn.textContent = 'Analyser Lot'; }
        }
    }

    async function handleValiderSortie() {
        const btn     = document.getElementById('btn-valider-sortie');
        const lotId   = btn ? btn.getAttribute('data-lot-id') : '';
        const qty     = document.getElementById('sortie-qty-input')?.value;
        const max     = parseInt(document.getElementById('fefo-lot-max')?.textContent || '0');

        if (!lotId || !qty || parseInt(qty) <= 0) {
            showSortieFeedback('Veuillez saisir une quantité valide.', 'error');
            return;
        }
        if (parseInt(qty) > max) {
            showSortieFeedback(`Quantité maximale autorisée : ${max} unités.`, 'error');
            return;
        }

        if (btn) { btn.disabled = true; btn.textContent = 'Traitement…'; }

        const fd = new FormData();
        // On envoie directement par lot_id via le checkout — le service FEFO gère la décrémentation
        fd.append('lot_id', lotId);
        fd.append('quantite', qty);

        try {
            const res = await PharmaUI.apiFetch('index.php?action=api/v1/batches/checkout/direct', {
                method: 'POST', body: fd
            });

            if (res.success) {
                showSortieFeedback(`✅ Succès US 3.1 : ${res.message}`, 'success');
                hideSortieBlocks();
                document.getElementById('sortie-qty-input').value = '';
                // Recharger les produits (stock peut avoir changé)
                await loadProductsSelect('sortie-select-produit');
            } else {
                showSortieFeedback(res.message, 'error');
            }
        } catch (e) {
            showSortieFeedback('Erreur lors du déstockage.', 'error');
        } finally {
            if (btn) { btn.disabled = false; btn.textContent = 'Valider Sortie'; }
        }
    }

    function hideSortieBlocks() {
        const result   = document.getElementById('sortie-result');
        const noStock  = document.getElementById('sortie-no-stock');
        if (result)  result.classList.add('hidden');
        if (noStock) noStock.classList.add('hidden');
    }

    function showSortieFeedback(msg, type) {
        const fb = document.getElementById('sortie-feedback');
        if (!fb) return;
        const isSuccess = type === 'success';
        fb.className = `mb-6 p-4 rounded-2xl flex items-center gap-2.5 text-xs font-medium ${
            isSuccess
                ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-300'
                : 'bg-rose-500/10 border border-rose-500/20 text-rose-300'
        }`;
        fb.innerHTML = `<span class="w-1.5 h-1.5 rounded-full shrink-0 ${isSuccess ? 'bg-emerald-500' : 'bg-rose-500'}"></span>${PharmaUI.esc(msg)}`;
        fb.classList.remove('hidden');
    }


    // ═══════════════════════════════════════════════════════════
    // PAGE : GESTION UTILISATEURS (index.php?action=users)
    // ═══════════════════════════════════════════════════════════
    function initUsersPage() {
        loadUsersList();
        initCreateUserForm();
    }

    async function loadUsersList() {
        const tbody = document.getElementById('users-table-body');
        if (!tbody) return;

        try {
            const res = await PharmaUI.apiFetch('index.php?action=api/v1/users');
            if (!res.success) return;

            tbody.innerHTML = '';

            if (!res.data.length) {
                tbody.innerHTML = `<tr><td colspan="3" class="p-6 text-center text-slate-400 text-xs">Aucun utilisateur trouvé.</td></tr>`;
                return;
            }

            res.data.forEach(u => {
                const initiales = (u.prenom[0] || '') + (u.nom[0] || '');
                const tr        = document.createElement('tr');
                tr.className    = 'border-b border-slate-100 hover:bg-slate-50/50 transition-colors font-medium text-slate-700';

                const roleBadge = getRoleBadge(u.role);

                tr.innerHTML = `
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs flex items-center justify-center uppercase border border-emerald-100/50 shrink-0">
                                ${PharmaUI.esc(initiales.toUpperCase())}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 tracking-tight text-xs">${PharmaUI.esc(u.nom + ' ' + u.prenom)}</div>
                                <div class="text-[10px] text-slate-400 font-medium">ID Système : #${u.id}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 font-mono text-[11px] text-slate-500">${PharmaUI.esc(u.email)}</td>
                    <td class="p-4 text-center">${roleBadge}</td>`;
                tbody.appendChild(tr);
            });
        } catch (e) {
            tbody.innerHTML = `<tr><td colspan="3" class="p-6 text-center text-rose-400 text-xs">Erreur lors du chargement.</td></tr>`;
        }
    }

    function getRoleBadge(role) {
        if (role === 'admin') {
            return `<span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 rounded-md text-[10px] font-bold uppercase tracking-wide inline-block">⚡ Super Admin</span>`;
        }
        if (role === 'pharmacien') {
            return `<span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-100 rounded-md text-[10px] font-bold uppercase tracking-wide inline-block">💊 Pharmacien</span>`;
        }
        return `<span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-md text-[10px] font-bold uppercase tracking-wide inline-block">🩺 Préparateur</span>`;
    }

    function initCreateUserForm() {
        const form = document.getElementById('create-user-form');
        const btn  = document.getElementById('btn-create-user');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            setUsersFeedback('', '');

            if (btn) { btn.disabled = true; btn.textContent = 'Création en cours…'; }

            const fd = new FormData(form);

            try {
                const res = await PharmaUI.apiFetch('index.php?action=api/v1/users', {
                    method: 'POST', body: fd
                });

                if (res.success) {
                    setUsersFeedback(res.message, 'success');
                    form.reset();
                    await loadUsersList(); // Rafraîchir le tableau
                } else {
                    setUsersFeedback(res.message, 'error');
                }
            } catch (e) {
                setUsersFeedback('Erreur lors de la création.', 'error');
            } finally {
                if (btn) { btn.disabled = false; btn.textContent = "Créer l'accès sécurisé"; }
            }
        });
    }

    function setUsersFeedback(msg, type) {
        const fb = document.getElementById('users-feedback');
        if (!fb) return;
        if (!msg) { fb.classList.add('hidden'); return; }

        const isSuccess = type === 'success';
        fb.className = `mb-4 p-3.5 rounded-xl flex items-center gap-2 text-xs font-semibold ${
            isSuccess
                ? 'bg-emerald-50 border border-emerald-200 text-emerald-900'
                : 'bg-rose-50 border border-rose-200 text-rose-900'
        }`;
        fb.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0 ${isSuccess ? 'text-emerald-500' : 'text-rose-500'}">
                ${isSuccess
                    ? '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>'}
            </svg>
            ${PharmaUI.esc(msg)}`;
        fb.classList.remove('hidden');
    }


    // ═══════════════════════════════════════════════════════════
    // PAGE : RAPPORT FINANCIER (index.php?action=report)
    // ═══════════════════════════════════════════════════════════
    async function initReportPage() {
        const loading  = document.getElementById('report-loading');
        const totalEl  = document.getElementById('report-total-loss');
        const currency = document.getElementById('report-currency');

        try {
            const res = await PharmaUI.apiFetch('index.php?action=api/v1/report/loss');

            if (loading)  loading.classList.add('hidden');

            if (res.success) {
                if (totalEl)  { totalEl.textContent = res.data.totalLossFormatted; totalEl.classList.remove('hidden'); }
                if (currency) currency.classList.remove('hidden');
            } else {
                if (totalEl)  { totalEl.textContent = 'N/A'; totalEl.classList.remove('hidden'); }
            }
        } catch (e) {
            if (loading) loading.classList.add('hidden');
            if (totalEl) { totalEl.textContent = 'Erreur'; totalEl.classList.remove('hidden'); }
        }
    }


    // ═══════════════════════════════════════════════════════════
    // PAGE : NOTIFICATIONS (index.php?action=notifications)
    // ═══════════════════════════════════════════════════════════
    async function initNotificationsPage() {
        const loadingEl = document.getElementById('notifications-loading');
        const emptyEl   = document.getElementById('notifications-empty');
        const listEl    = document.getElementById('notifications-list');

        try {
            const res = await PharmaUI.apiFetch('index.php?action=api/v1/dashboard/notifications');

            if (loadingEl) loadingEl.classList.add('hidden');

            if (!res.success || !res.data.length) {
                if (emptyEl) emptyEl.classList.remove('hidden');
                return;
            }

            if (listEl) {
                listEl.innerHTML = '';
                res.data.forEach(n => {
                    const item = document.createElement('div');
                    item.className = 'relative group';
                    item.innerHTML = `
                        <div class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-slate-900 border-2 border-orange-500 group-hover:bg-orange-500 transition-colors"></div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 bg-slate-950/60 border border-slate-800/60 rounded-xl hover:border-slate-700 transition-all">
                            <div>
                                <span class="text-xs font-bold text-slate-200 tracking-tight block">${PharmaUI.esc(n.produitNom)}</span>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5">Lot ref: ${PharmaUI.esc(n.numeroLot)}</div>
                            </div>
                            <div class="text-left sm:text-right font-mono text-[11px] shrink-0">
                                <span class="text-orange-400 font-bold">${PharmaUI.esc(n.datePeremption)}</span>
                                <div class="text-[9px] text-slate-500 font-sans mt-0.5">Vol stock: ${n.quantite} units</div>
                            </div>
                        </div>`;
                    listEl.appendChild(item);
                });
                listEl.classList.remove('hidden');
            }
        } catch (e) {
            if (loadingEl) loadingEl.classList.add('hidden');
            if (listEl) {
                listEl.innerHTML = `<p class="text-xs text-rose-400 font-mono">Erreur lors du chargement des notifications.</p>`;
                listEl.classList.remove('hidden');
            }
        }
    }

});
