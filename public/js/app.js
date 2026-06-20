// public/js/app.js
// Utilitaires globaux PharmaFEFO — chargé sur toutes les pages

// ═══════════════════════════════════════════════════════════════
// PHARMAUI — Classe utilitaire globale
// ═══════════════════════════════════════════════════════════════
class PharmaUI {

    /**
     * Affiche un toast de notification dans le coin supérieur droit.
     * @param {string} message
     * @param {'success'|'error'|'warning'} type
     */
    static showToast(message, type = 'success') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.style.cssText = `
            padding:14px 20px;border-radius:8px;font-size:12px;font-weight:700;
            font-family:sans-serif;box-shadow:0 4px 12px rgba(0,0,0,0.15);
            min-width:280px;max-width:380px;transition:all 0.3s ease;
            opacity:0;transform:translateY(-20px);pointer-events:auto;
        `;
        const styles = {
            success: { bg:'#ecfdf5', color:'#065f46', border:'5px solid #10b981' },
            warning: { bg:'#fffbeb', color:'#92400e', border:'5px solid #f59e0b' },
            error:   { bg:'#fef2f2', color:'#991b1b', border:'5px solid #ef4444' },
        };
        const s = styles[type] || styles.error;
        toast.style.background  = s.bg;
        toast.style.color       = s.color;
        toast.style.borderLeft  = s.border;
        toast.innerText         = message;

        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; }, 50);
        setTimeout(() => {
            toast.style.opacity   = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.remove(), 300);
        }, 4500);
    }

    /**
     * Gestion centralisée des erreurs HTTP fetch().
     * Retourne la promesse JSON si OK, sinon lance une exception.
     */
    static async handleResponse(response) {
        if (response.status === 401) {
            window.location.href = 'index.php?action=login';
            throw new Error('401 Non authentifié');
        }
        if (response.status === 403) {
            PharmaUI.showToast('Accès refusé : droits insuffisants.', 'error');
            throw new Error('403 Forbidden');
        }
        if (response.status === 404) {
            PharmaUI.showToast('Ressource introuvable (404).', 'error');
            throw new Error('404 Not Found');
        }
        // Pour 422 et 409 : on laisse le caller lire le JSON d'erreur
        const data = await response.json();
        if (!response.ok && response.status !== 422 && response.status !== 409) {
            PharmaUI.showToast(data.message || 'Erreur serveur inattendue.', 'error');
            throw new Error(data.message || 'HTTP ' + response.status);
        }
        return data;
    }

    /**
     * Helper fetch() avec gestion d'erreur intégrée.
     */
    static async apiFetch(url, options = {}) {
        const response = await fetch(url, options);
        return PharmaUI.handleResponse(response);
    }

    /**
     * Échappe le HTML pour éviter les injections XSS.
     */
    static esc(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /**
     * Affiche un spinner de chargement dans un élément.
     */
    static showSpinner(el, color = 'emerald') {
        if (!el) return;
        el.innerHTML = `
            <div class="flex flex-col items-center gap-2 py-8 text-slate-400">
                <div class="w-5 h-5 border-2 border-${color}-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs">Chargement…</span>
            </div>`;
    }

    /**
     * Formate une date ISO en dd/mm/yyyy.
     */
    static formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        if (isNaN(d)) return dateStr;
        return d.toLocaleDateString('fr-FR', { day:'2-digit', month:'2-digit', year:'numeric' });
    }

    /**
     * Retourne les classes Tailwind du badge selon le statut / jours restants.
     */
    static getBadgeInfo(expiryDate, status) {
        if (status === 'EXPIRED') {
            return { classes: 'bg-rose-100 text-rose-900 border-rose-300', label: 'Périmé (Expired)' };
        }
        const today = new Date(); today.setHours(0,0,0,0);
        const exp   = new Date(expiryDate); exp.setHours(0,0,0,0);
        const diff  = Math.floor((exp - today) / 86400000);

        if (diff <= 0)  return { classes: 'bg-rose-50 text-rose-700 border-rose-200',     label: '🔴 Périmé (Expired)' };
        if (diff < 30)  return { classes: 'bg-rose-50 text-rose-700 border-rose-200',     label: '🔴 Critique (<30j)' };
        if (diff < 90)  return { classes: 'bg-amber-50 text-amber-700 border-amber-200',  label: '🟠 Vigilance (<90j)' };
        return             { classes: 'bg-emerald-50 text-emerald-700 border-emerald-200/60', label: '🟢 Conforme (>6m)' };
    }
}

// ═══════════════════════════════════════════════════════════════
// INITIALISATION COMMUNE — lancée sur chaque page connectée
// ═══════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    initDateDisplay();
    initSidebarUserInfo();
});

/**
 * Affiche la date du jour dans le header sans PHP.
 */
function initDateDisplay() {
    const now   = new Date();
    const opts  = { day: '2-digit', month: 'short', year: 'numeric' };
    const full  = now.toLocaleDateString('fr-FR', opts);
    const short = now.toLocaleDateString('fr-FR', { day:'2-digit', month:'2-digit', year:'numeric' });

    const elFull  = document.getElementById('header-date-text');
    const elShort = document.getElementById('header-date-short');
    const elFullW = document.getElementById('header-date-full');

    if (elFull)  { elFull.textContent = full; elFullW.classList.remove('hidden'); }
    if (elShort) elShort.textContent  = short;
}

/**
 * Charge les infos utilisateur depuis /api/v1/me et remplit la sidebar.
 */
async function initSidebarUserInfo() {
    const userBlock  = document.getElementById('sidebar-user-block');
    const adminLinks = document.getElementById('nav-admin-links');
    const btnAdmin   = document.getElementById('btn-add-collaborateur');

    // Page login n'a pas de sidebar
    if (!userBlock) return;

    try {
        const res = await PharmaUI.apiFetch('index.php?action=api/v1/me');
        if (!res.success) return;

        const { nom, role } = res.data;

        // Avatar initiales
        const initiales = nom.split(' ').map(w => w[0] || '').join('').toUpperCase().slice(0, 2);
        document.getElementById('sidebar-user-avatar').textContent = initiales;
        document.getElementById('sidebar-user-name').textContent   = nom;
        document.getElementById('sidebar-user-role').textContent   = role;
        userBlock.classList.remove('hidden');

        // Afficher les liens admin
        if (role === 'admin') {
            if (adminLinks) adminLinks.classList.remove('hidden');
            if (btnAdmin)   btnAdmin.classList.remove('hidden');
        }
    } catch (e) {
        // Silencieux — session expirée, redirection gérée par apiFetch
    }
}

// Exposer globalement
window.PharmaUI = PharmaUI;
