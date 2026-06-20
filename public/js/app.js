document.addEventListener("DOMContentLoaded", () => {

    // ─────────────────────────────────────────────────────────
    // INITIALISATION
    // ─────────────────────────────────────────────────────────
    loadDashboardAlerts();   
    initFilterButtons();    
    initActionButtons();     
    
       function loadDashboardAlerts() {
        fetch('index.php?action=api/v1/dashboard/alerts')
            .then(res => window.PharmaUI.handleHttpError(res))
            .then(res => {
                if (res.success && res.data.expiringNextMonth > 0) {
                    const banner   = document.getElementById('async-alert-banner');
                    const textSpan = document.getElementById('async-alert-text');
                    if (banner && textSpan) {
                        textSpan.innerText = `⚠️ Prévoyance FEFO : ${res.data.expiringNextMonth} produit(s) expirent le mois prochain.`;
                        banner.classList.remove('hidden');
                    }
                }
            })
            .catch(err => console.error("Erreur alerte dashboard :", err));
    }
})