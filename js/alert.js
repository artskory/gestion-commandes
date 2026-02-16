/**
 * Système d'alertes personnalisées avec animations
 * Version 1.21
 */

// Fonction pour afficher une alerte
function showCustomAlert(message, type = 'success') {
    // Supprimer les anciennes alertes
    $('.custom-alert').remove();
    
    // Déterminer l'icône selon le type
    let icon = 'fa-check-circle';
    if (type === 'danger' || type === 'error') {
        icon = 'fa-times-circle';
    } else if (type === 'warning') {
        icon = 'fa-exclamation-circle';
    }
    
    // Créer l'alerte
    const alertHTML = `
        <div class="custom-alert alert-${type} hide">
            <span class="alert-icon fas ${icon}"></span>
            <span class="alert-msg">${message}</span>
            <div class="alert-close-btn">
                <i class="fas fa-times"></i>
            </div>
        </div>
    `;
    
    // Ajouter l'alerte au body
    $('body').append(alertHTML);
    
    // Afficher l'alerte avec animation
    setTimeout(function() {
        $('.custom-alert').addClass('show');
        $('.custom-alert').removeClass('hide');
        $('.custom-alert').addClass('showAlert');
    }, 100);
    
    // Auto-fermeture après 5 secondes
    setTimeout(function() {
        $('.custom-alert').removeClass('show');
        $('.custom-alert').addClass('hide');
        
        // Supprimer l'élément après l'animation
        setTimeout(function() {
            $('.custom-alert').remove();
        }, 1000);
    }, 5000);
}

// Gestion du bouton de fermeture
$(document).on('click', '.alert-close-btn', function() {
    $(this).parent().removeClass('show');
    $(this).parent().addClass('hide');
    
    // Supprimer l'élément après l'animation
    setTimeout(function() {
        $('.custom-alert').remove();
    }, 1000);
});

// Afficher les alertes PHP au chargement de la page
$(document).ready(function() {
    // Récupérer les messages depuis les attributs data
    const successMsg = $('body').data('success-msg');
    const errorMsg = $('body').data('error-msg');
    const warningMsg = $('body').data('warning-msg');
    
    if (successMsg) {
        showCustomAlert(successMsg, 'success');
    }
    if (errorMsg) {
        showCustomAlert(errorMsg, 'danger');
    }
    if (warningMsg) {
        showCustomAlert(warningMsg, 'warning');
    }
});
