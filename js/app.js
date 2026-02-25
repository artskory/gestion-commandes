/**
 * app.js — Scripts partagés entre toutes les vues
 * Gestion des Commandes
 */

// ═══════════════════════════════════════════════════════════════
// IDENTIFIANT DE FENÊTRE (toutes les vues)
// Permet au bookmarklet Dolibarr de retrouver cet onglet via
// window.open(url, 'gestion_commandes')
// ═══════════════════════════════════════════════════════════════

window.name = 'gestion_commandes';


// ═══════════════════════════════════════════════════════════════
// DÉLAIS DE FABRICATION (nouvelle-commande + editer-commande)
// Assure la cohérence entre la liste déroulante et le date picker
// ═══════════════════════════════════════════════════════════════

function clearDatePicker() {
    const liste = document.getElementById('delais_liste');
    const date  = document.getElementById('delais_date');
    if (liste && date && liste.value !== '') {
        date.value = '';
    }
}

function clearDropdown() {
    const liste = document.getElementById('delais_liste');
    const date  = document.getElementById('delais_date');
    if (liste && date && date.value !== '') {
        liste.value = '';
    }
}


// ═══════════════════════════════════════════════════════════════
// MODAL DE CONFIRMATION PERSONNALISÉE (index)
// ═══════════════════════════════════════════════════════════════

function afficherModal(titre, contenu, type, onConfirm) {
    const modal      = document.getElementById('custom-modal');
    const overlay    = document.getElementById('modal-overlay');
    const header     = document.getElementById('modal-header');
    const titleElem  = document.getElementById('modal-title');
    const body       = document.getElementById('modal-body');
    const confirmBtn = document.getElementById('modal-confirm-btn');

    if (!modal || !overlay) return;

    // Configuration du header selon le type
    header.className     = 'modal-header-custom';
    confirmBtn.className = 'modal-btn modal-btn-confirm';

    if (type === 'danger') {
        header.classList.add('danger');
        header.querySelector('i').className = 'bi bi-exclamation-triangle-fill';
    } else if (type === 'warning') {
        header.classList.add('warning');
        confirmBtn.classList.add('warning');
        header.querySelector('i').className = 'bi bi-arrow-clockwise';
    }

    titleElem.textContent = titre;
    body.innerHTML        = contenu;

    // Réinitialiser le listener du bouton confirmer
    const newBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
    document.getElementById('modal-confirm-btn').addEventListener('click', function () {
        fermerModal();
        onConfirm();
    });

    overlay.classList.add('show');
    modal.classList.add('show');
}

function fermerModal() {
    const modal   = document.getElementById('custom-modal');
    const overlay = document.getElementById('modal-overlay');
    if (modal)   modal.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
}


// ═══════════════════════════════════════════════════════════════
// CONFIRMATION RECHARGEMENT (index)
// ═══════════════════════════════════════════════════════════════

function confirmerRechargement(event, id) {
    event.preventDefault();

    const titre   = 'Créer une nouvelle version ?';
    const contenu = `
        <p>Vous allez créer une nouvelle version de cette commande.</p>
        <p style="margin-top: 12px; color: #666;">
            <i class="bi bi-info-circle" style="color: #ffc107;"></i>
            Un nouveau fichier CSV sera généré.
        </p>
    `;

    afficherModal(titre, contenu, 'warning', function () {
        window.location.href = 'recharger/' + id;
    });
}


// ═══════════════════════════════════════════════════════════════
// CONFIRMATION SUPPRESSION (index)
// ═══════════════════════════════════════════════════════════════

function getChecked() {
    return document.querySelectorAll('.check-commande:checked');
}

function confirmerSuppressionSelection() {
    const cases = getChecked();
    if (cases.length === 0) return;

    const noms      = Array.from(cases).map(cb => cb.dataset.nom);
    const titre     = 'Supprimer ' + cases.length + ' commande(s) ?';
    const listeHtml = noms.map(nom =>
        `<div class="modal-list-item"><i class="bi bi-file-earmark-text"></i>${nom}</div>`
    ).join('');

    const contenu = `
        <p><strong>Vous êtes sur le point de supprimer ${cases.length} commande(s) :</strong></p>
        <div class="modal-list">${listeHtml}</div>
        <p style="margin-top: 16px; color: #dc3545; font-weight: 500;">
            <i class="bi bi-exclamation-triangle"></i>
            Cette action est irréversible !
        </p>
    `;

    afficherModal(titre, contenu, 'danger', function () {
        const ids = Array.from(cases).map(cb => cb.value).join(',');
        window.location.href = 'supprimer/' + ids;
    });
}


// ═══════════════════════════════════════════════════════════════
// INITIALISATIONS AU CHARGEMENT (guards : actifs uniquement si
// les éléments correspondants existent dans la page courante)
// ═══════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function () {

    // --- Fermeture modal via overlay (index) ---
    const overlay = document.getElementById('modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', fermerModal);
    }

    // --- Hamburger animé (index) ---
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const hamburgerSvg = document.getElementById('hamburger-svg');
    if (hamburgerBtn && hamburgerSvg) {
        hamburgerBtn.addEventListener('shown.bs.dropdown',  () => hamburgerSvg.classList.add('active'));
        hamburgerBtn.addEventListener('hidden.bs.dropdown', () => hamburgerSvg.classList.remove('active'));
    }
});

// --- Fermeture modal via touche Escape (index) ---
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('custom-modal');
        if (modal && modal.classList.contains('show')) fermerModal();
    }
});
