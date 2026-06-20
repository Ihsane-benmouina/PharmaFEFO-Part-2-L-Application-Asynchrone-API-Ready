document.addEventListener("DOMContentLoaded", () => {

    // ─────────────────────────────────────────────────────────
    // INITIALISATION
    // ─────────────────────────────────────────────────────────
    loadDashboardAlerts();   // Tâche 5 : bannière d'alerte async
    initFilterButtons();     // Tâche 4 : filtres dynamiques
    initActionButtons();     // Tâches 6 & 7 : délivrer + périmer
    initAddBatchForm();      // Tâche 3 : formulaire add-batch async

      function initAddBatchForm() {
        // Sélectionner le formulaire add-batch (présent sur la page add-batch)
        const form = document.getElementById('async-add-batch-form');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled    = true;
                submitBtn.textContent = 'Enregistrement…';
            }

            const formData = new FormData(form);

            fetch('index.php?action=api/v1/batches', {
                method: 'POST',
                body: formData
            })
            .then(res => window.PharmaUI.handleHttpError(res))
            .then(res => {
                if (res.success) {
                    window.PharmaUI.showToast(res.message, "success");
                    form.reset();
                }
            })
            .catch(err => {
                if (err && typeof err.json === 'function') {
                    err.json().then(json => window.PharmaUI.showToast(json.message, "error"));
                } else {
                    window.PharmaUI.showToast("Erreur lors de l'enregistrement du lot.", "error");
                }
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'Enregistrer le Lot via AJAX';
                }
            });
        });
    }
});
