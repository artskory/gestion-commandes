<?php
// ─── Variables du layout ──────────────────────────────────────────────────────
$pageTitle = 'Nouvelle Commande';
$basePath  = '';

$bodyAttr = '';
if (!empty($this->errors)) {
    $msg = implode('<br>', array_map('htmlspecialchars', $this->errors));
    $bodyAttr = 'data-error-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
}

// Quill CSS chargé dans le <head> via $extraStyles
$extraStyles = '<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">';

// Import Dolibarr (dans $extraScripts comme avant)
$extraScripts = <<<'HTML'
<script>
(function () {
    var p = new URLSearchParams(window.location.search);
    if (p.get('import') !== 'dolibarr') return;

    var map = {
        'societe':             p.get('societe'),
        'n_commande_client':   p.get('numero_commande'),
        'n_devis':             p.get('numero_devis'),
        'reference_article':   p.get('reference_article'),
        'quantite_par_modele': p.get('quantite'),
        'destinataire':        p.get('destinataire'),
        'dossier_suivi_par':   p.get('suivi_par')
    };

    Object.keys(map).forEach(function (id) {
        var val = map[id];
        if (!val) return;
        var el = document.getElementById(id);
        if (!el) return;
        el.value = val;
        el.style.borderColor = '#28a745';
        el.style.borderWidth = '2px';
        setTimeout(function () { el.style.borderColor = ''; el.style.borderWidth = ''; }, 2000);
    });

    // Délai de livraison Dolibarr
    var delaiType  = p.get('delai_type');
    var delaiValue = p.get('delai_value');
    if (delaiType && delaiValue) {
        if (delaiType === 'date') {
            var batDateEl = document.getElementById('delai_bat_date');
            if (batDateEl) {
                batDateEl.value = delaiValue;
                batDateEl.style.borderColor = '#28a745';
                setTimeout(function () { batDateEl.style.borderColor = ''; }, 2000);
            }
        } else {
            var batSel = document.getElementById('delai_bat_liste');
            if (batSel) {
                for (var i = 0; i < batSel.options.length; i++) {
                    if (batSel.options[i].value === delaiValue) {
                        batSel.value = delaiValue;
                        batSel.style.borderColor = '#28a745';
                        setTimeout(function () { batSel.style.borderColor = ''; }, 2000);
                        break;
                    }
                }
            }
        }
    }

    // Descriptif Dolibarr → Quill
    var descriptif = p.get('descriptif');
    if (descriptif && typeof quillEditor !== 'undefined') {
        quillEditor.setText('');
        var lines = descriptif.split('\n');
        var index = 0;
        lines.forEach(function(line, i) {
            quillEditor.insertText(index, line);
            index += line.length;
            if (i < lines.length - 1) {
                quillEditor.insertText(index, '\n');
                index += 1;
            }
        });
    }

    window.history.replaceState({}, document.title, window.location.pathname);
})();
</script>
HTML;

include __DIR__ . '/layout/header.php';
?>

<style>
.bat-type-card {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .4rem; padding: 1.25rem 1rem; border: 2px solid var(--color-border);
    border-radius: .75rem; cursor: pointer; transition: all .2s;
    background: var(--color-white-soft); color: var(--color-dark); width: 100%; min-height: 90px;
}
.bat-type-card input[type="radio"] { display: none; }
.bat-type-card.selected { border-color: var(--color-primary); background: rgba(93,135,255,.08); }
.bat-type-icon { font-size: 1.75rem; color: var(--color-primary); }
.ql-toolbar { border-color: var(--color-border) !important; border-radius: .5rem .5rem 0 0 !important; background: var(--color-white-soft); }
.ql-container { border-color: var(--color-border) !important; border-radius: 0 0 .5rem .5rem !important; font-size: .9rem; }
.ql-editor { min-height: 140px; color: var(--color-dark); }
.bat-drop-zone {
    border: 2px dashed var(--color-border); border-radius: .75rem; padding: 2rem 1rem;
    text-align: center; cursor: pointer; transition: all .2s;
    background: rgba(93,135,255,.02); color: var(--color-dark);
}
.bat-drop-zone:hover, .bat-drop-zone.drag-active { border-color: var(--color-primary); background: rgba(93,135,255,.07); }
.bat-drop-icon { font-size: 2.5rem; color: var(--color-primary); opacity: .6; display: block; margin-bottom: .5rem; }
.bat-file-item {
    display: flex; align-items: center; gap: .5rem; padding: .45rem .75rem;
    background: var(--color-white-soft); border: 1px solid var(--color-border);
    border-radius: .5rem; margin-bottom: .35rem; transition: background .15s;
}
.bat-file-item.dragging { opacity: .35; }
.bat-file-item.drag-over { border-color: var(--color-primary); background: rgba(93,135,255,.07); }
.bat-file-handle { cursor: grab; color: var(--color-border); font-size: 1.1rem; flex-shrink: 0; }
.bat-file-handle:active { cursor: grabbing; }
.bat-file-name { font-size: .85rem; color: var(--color-dark); word-break: break-all; flex: 1; }
.bat-file-size { font-size: .78rem; white-space: nowrap; color: var(--color-border); flex-shrink: 0; }
.bat-step-indicator { display: flex; align-items: center; gap: .5rem; margin-bottom: 1.5rem; font-size: .85rem; }
.bat-step-active { font-weight: 700; color: var(--color-primary); }
.bat-step-inactive { color: var(--color-border); }
.bat-section-title {
    font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    color: var(--color-border); margin: 1.5rem 0 1rem; display: flex; align-items: center; gap: .5rem;
}
.bat-section-title::after { content: ''; flex: 1; height: 1px; background: var(--color-border); opacity: .4; }
</style>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <!-- Indicateur d'étapes -->
            <div class="bat-step-indicator">
                <span class="bat-step-active"><i class="bi bi-1-circle-fill me-1"></i>Informations &amp; fichiers</span>
                <i class="bi bi-chevron-right text-muted"></i>
                <span class="bat-step-inactive"><i class="bi bi-2-circle me-1"></i>Vérifications</span>
            </div>

            <div class="card">
                <form method="POST" action="<?php echo $appBase; ?>/nouvelle" id="form-bat" enctype="multipart/form-data">
                    <input type="hidden" name="bat_submit" value="1">
                    <input type="hidden" name="descriptif" id="descriptif">

                    <!-- En-tête -->
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h2 class="font-bold">Nouvelle Commande</h2>
                        <div class="show-above-1024">
                            <a href="./" class="btn btn-danger"><i class="bi bi-x-circle icons"></i>Annuler</a>
                        </div>
                        <div class="show-below-1024 dropdown">
                            <button class="btn-hamburger" type="button" data-bs-toggle="dropdown">
                                <svg class="bars" viewBox="0 0 100 100">
                                    <path class="line top" d="m 30,33 h 40 c 13.100415,0 14.380204,31.80258 6.899646,33.421777 -24.612039,5.327373 9.016154,-52.337577 -12.75751,-30.563913 l -28.284272,28.284272"></path>
                                    <path class="line middle" d="m 70,50 c 0,0 -32.213436,0 -40,0 -7.786564,0 -6.428571,-4.640244 -6.428571,-8.571429 0,-5.895471 6.073743,-11.783399 12.286435,-5.570707 6.212692,6.212692 28.284272,28.284272 28.284272,28.284272"></path>
                                    <path class="line bottom" d="m 69.575405,67.073826 h -40 c -13.100415,0 -14.380204,-31.80258 -6.899646,-33.421777 24.612039,-5.327373 -9.016154,52.337577 12.75751,30.563913 l 28.284272,-28.284272"></path>
                                </svg>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="./" class="btn btn-danger mt-2"><i class="bi bi-x-circle icons"></i>Annuler</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body">

                        <!-- Section informations -->
                        <div class="bat-section-title"><i class="bi bi-info-circle"></i>Informations</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <i class="bi bi-buildings icon-primary icons"></i>
                                    <label for="societe" class="form-label font-medium">Société *</label>
                                    <input type="text" class="form-control text-input" id="societe" name="societe"
                                           value="<?php echo isset($_POST['societe']) ? htmlspecialchars($_POST['societe']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-hash icon-primary icons"></i>
                                    <label for="n_commande_client" class="form-label font-medium">N° Commande Client *</label>
                                    <input type="text" class="form-control text-input" id="n_commande_client" name="n_commande_client"
                                           value="<?php echo isset($_POST['n_commande_client']) ? htmlspecialchars($_POST['n_commande_client']) : ''; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-calendar icon-primary icons"></i>
                                    <label for="date_commande" class="form-label font-medium">Date</label>
                                    <input type="date" class="form-control text-input date-wrapper" id="date_commande" name="date_commande"
                                           value="<?php echo isset($_POST['date_commande']) ? htmlspecialchars($_POST['date_commande']) : date('Y-m-d'); ?>">
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-stack icon-primary icons"></i>
                                    <label for="quantite_par_modele" class="form-label font-medium">Quantité par Modèle</label>
                                    <input type="number" class="form-control text-input" id="quantite_par_modele" name="quantite_par_modele"
                                           value="<?php echo isset($_POST['quantite_par_modele']) ? htmlspecialchars($_POST['quantite_par_modele']) : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <i class="bi bi-person icon-primary icons"></i>
                                    <label for="destinataire" class="form-label font-medium">Destinataire</label>
                                    <input type="text" class="form-control text-input" id="destinataire" name="destinataire"
                                           value="<?php echo isset($_POST['destinataire']) ? htmlspecialchars($_POST['destinataire']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-file-earmark-text icon-primary icons"></i>
                                    <label for="reference_article" class="form-label font-medium">Référence Article</label>
                                    <input type="text" class="form-control text-input" id="reference_article" name="reference_article"
                                           value="<?php echo isset($_POST['reference_article']) ? htmlspecialchars($_POST['reference_article']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-box-seam icon-primary icons"></i>
                                    <label for="n_devis" class="form-label font-medium">Notre N° de Devis</label>
                                    <input type="text" class="form-control text-input" id="n_devis" name="n_devis"
                                           value="<?php echo isset($_POST['n_devis']) ? htmlspecialchars($_POST['n_devis']) : ''; ?>">
                                </div>
                                <div class="mb-3">
                                    <i class="bi bi-person icon-primary icons"></i>
                                    <label for="dossier_suivi_par" class="form-label font-medium">Dossier Suivi Par</label>
                                    <input type="text" class="form-control text-input" id="dossier_suivi_par" name="dossier_suivi_par" value="Matthieu">
                                </div>
                            </div>
                        </div>

                        <!-- Délais -->
                        <div class="mb-3">
                            <i class="bi bi-clock icon-primary icons"></i>
                            <label class="form-label font-medium">Délais de Fabrication</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-select text-input" id="delai_bat_liste" name="delai_bat_liste" onchange="clearDatePicker()">
                                        <option value="">Sélectionner un délai</option>
                                        <?php
                                        foreach (['J+0','J+1','J+2','J+3','J+4','J+5','J+6','J+7','J+8','J+9','J+10','J+15'] as $opt) {
                                            $sel = (isset($_POST['delai_bat_liste']) && $_POST['delai_bat_liste'] == $opt) ? 'selected' : '';
                                            echo "<option value=\"$opt\" $sel>$opt</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="date" class="form-control text-input date-wrapper" id="delai_bat_date" name="delai_bat_date"
                                           value="<?php echo isset($_POST['delai_bat_date']) ? htmlspecialchars($_POST['delai_bat_date']) : ''; ?>"
                                           onchange="clearDropdown()">
                                </div>
                            </div>
                        </div>

                        <!-- Statut fichier -->
                        <div class="mb-3">
                            <label class="form-label font-medium d-block">
                                <i class="bi bi-filetype-pdf icon-primary icons"></i>Statut du Fichier
                            </label>
                            <div class="form-check form-switch rounded-xl border mb-3">
                                <input class="form-check-input" type="radio" name="fichier_statut" id="fichier_cree" value="cree"
                                       <?php echo (!isset($_POST['fichier_statut']) || $_POST['fichier_statut'] == 'cree') ? 'checked' : ''; ?>>
                                <label class="form-check-label text-input" for="fichier_cree">Fichier créé</label>
                            </div>
                            <div class="form-check form-switch rounded-xl border">
                                <input class="form-check-input" type="radio" name="fichier_statut" id="fichier_fourni" value="fourni"
                                       <?php echo (isset($_POST['fichier_statut']) && $_POST['fichier_statut'] == 'fourni') ? 'checked' : ''; ?>>
                                <label class="form-check-label text-input" for="fichier_fourni">Fichier fourni</label>
                            </div>
                        </div>

                        <!-- Type BAT -->
                        <div class="bat-section-title"><i class="bi bi-printer"></i>Type de BAT</div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="bat-type-card selected" id="card-print">
                                    <input type="radio" name="bat_type" value="print"
                                           <?php echo (!isset($_POST['bat_type']) || $_POST['bat_type'] === 'print') ? 'checked' : ''; ?>>
                                    <i class="bi bi-printer-fill bat-type-icon"></i>
                                    <span class="fw-semibold">Print</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="bat-type-card" id="card-label">
                                    <input type="radio" name="bat_type" value="label"
                                           <?php echo (isset($_POST['bat_type']) && $_POST['bat_type'] === 'label') ? 'checked' : ''; ?>>
                                    <i class="bi bi-tag-fill bat-type-icon"></i>
                                    <span class="fw-semibold">Label</span>
                                </label>
                            </div>
                        </div>

                        <!-- Descriptif (Print uniquement) -->
                        <div id="section-descriptif" class="mb-3">
                            <div class="bat-section-title"><i class="bi bi-text-paragraph"></i>Descriptif de la commande</div>
                            <div id="quill-editor"></div>
                        </div>

                        <!-- Fichiers PDF -->
                        <div class="bat-section-title">
                            <i class="bi bi-file-earmark-pdf"></i>Fichiers PDF <span class="text-danger ms-1">*</span>
                        </div>
                        <div id="drop-zone" class="bat-drop-zone mb-2">
                            <i class="bi bi-cloud-upload bat-drop-icon"></i>
                            <p class="fw-semibold mb-1">Glissez vos fichiers PDF ici</p>
                            <p class="text-muted small mb-0">ou cliquez pour parcourir</p>
                            <input type="file" id="file-input" multiple accept=".pdf,application/pdf" style="display:none">
                        </div>
                        <div id="file-list" style="display:none;"></div>
                        <div id="file-count" class="mt-1 small text-muted">Aucun fichier sélectionné</div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="./" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Annuler</a>
                            <button type="submit" id="btn-suivant" class="btn btn-primary px-4">
                                <i class="bi bi-arrow-right me-2"></i>Suivant
                            </button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


$extraScripts .= <<<'ENDJS'
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
var quillEditor = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Saisissez le descriptif de la commande\u2026',
    modules: {
        toolbar: [
            [{ size: ['small', false, 'large', 'huge'] }],
            ['bold', 'italic', 'underline'],
            [{ color: [] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['clean']
        ]
    }
});

// ── Type de BAT ──
function updateBatType() {
    var isPrint = document.querySelector('input[name="bat_type"][value="print"]').checked;
    document.getElementById('card-print').classList.toggle('selected', isPrint);
    document.getElementById('card-label').classList.toggle('selected', !isPrint);
    document.getElementById('section-descriptif').style.display = isPrint ? 'block' : 'none';
}

document.getElementById('card-print').addEventListener('click', function() {
    document.querySelector('input[name="bat_type"][value="print"]').checked = true;
    updateBatType();
});
document.getElementById('card-label').addEventListener('click', function() {
    document.querySelector('input[name="bat_type"][value="label"]').checked = true;
    updateBatType();
});
updateBatType();

// ── Fichiers ──
var batFiles = [];
var dragSrc  = null;
var dropZone = document.getElementById('drop-zone');
var fileInput = document.getElementById('file-input');
var fileList  = document.getElementById('file-list');
var fileCount = document.getElementById('file-count');

dropZone.addEventListener('click', function() { fileInput.click(); });
fileInput.addEventListener('change', function(e) { addFiles(e.target.files); fileInput.value = ''; });

dropZone.addEventListener('dragover', function(e) {
    if (!document.body.classList.contains('bat-reordering')) { e.preventDefault(); dropZone.classList.add('drag-active'); }
});
dropZone.addEventListener('dragleave', function(e) {
    if (!dropZone.contains(e.relatedTarget)) dropZone.classList.remove('drag-active');
});
dropZone.addEventListener('drop', function(e) {
    e.preventDefault(); dropZone.classList.remove('drag-active'); addFiles(e.dataTransfer.files);
});

function addFiles(files) {
    Array.from(files).forEach(function(f) {
        if (!f.name.toLowerCase().endsWith('.pdf') && f.type !== 'application/pdf') return;
        if (!batFiles.find(function(x) { return x.name === f.name && x.size === f.size; })) batFiles.push(f);
    });
    renderList();
}

function removeFile(i) { batFiles.splice(i, 1); renderList(); }

function renderList() {
    fileList.innerHTML = '';
    batFiles.forEach(function(f, i) {
        var item = document.createElement('div');
        item.className = 'bat-file-item'; item.draggable = true; item.dataset.idx = i;
        item.innerHTML =
            '<span class="bat-file-handle"><i class="bi bi-grip-vertical"></i></span>' +
            '<i class="bi bi-file-earmark-pdf text-danger"></i>' +
            '<span class="bat-file-name">' + esc(f.name) + '</span>' +
            '<span class="bat-file-size">' + fmtSize(f.size) + '</span>' +
            '<button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 ms-1" onclick="removeFile(' + i + ')"><i class="bi bi-x"></i></button>';
        item.addEventListener('dragstart', function(e) {
            dragSrc = i; e.dataTransfer.effectAllowed = 'move';
            item.classList.add('dragging'); document.body.classList.add('bat-reordering');
        });
        item.addEventListener('dragend', function() {
            item.classList.remove('dragging'); document.body.classList.remove('bat-reordering');
            document.querySelectorAll('.bat-file-item').forEach(function(el) { el.classList.remove('drag-over'); });
        });
        item.addEventListener('dragover', function(e) {
            e.preventDefault(); e.stopPropagation();
            document.querySelectorAll('.bat-file-item').forEach(function(el) { el.classList.remove('drag-over'); });
            item.classList.add('drag-over');
        });
        item.addEventListener('drop', function(e) {
            e.preventDefault(); e.stopPropagation(); item.classList.remove('drag-over');
            if (dragSrc !== null && dragSrc !== i) {
                var moved = batFiles.splice(dragSrc, 1)[0];
                batFiles.splice(i, 0, moved); dragSrc = null; renderList();
            }
        });
        fileList.appendChild(item);
    });
    fileList.style.display = batFiles.length > 0 ? 'block' : 'none';
    fileCount.textContent = batFiles.length > 0
        ? batFiles.length + ' fichier' + (batFiles.length > 1 ? 's' : '') + ' sélectionné' + (batFiles.length > 1 ? 's' : '')
        : 'Aucun fichier sélectionné';
}

function fmtSize(b) { return b < 1048576 ? (b/1024).toFixed(1)+' Ko' : (b/1048576).toFixed(1)+' Mo'; }
function esc(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

// ── Soumission ──
document.getElementById('form-bat').addEventListener('submit', function(e) {
    e.preventDefault();
    // Les alertes sont gérées via showCustomAlert

    if (batFiles.length === 0) {
        showCustomAlert('Veuillez ajouter au moins un fichier PDF.', 'danger');
        return;
    }

    var isPrint = document.querySelector('input[name="bat_type"][value="print"]').checked;
    document.getElementById('descriptif').value = isPrint ? quillEditor.root.innerHTML : '';

    var fd = new FormData(this);
    fd.delete('fichiers[]');
    batFiles.forEach(function(f) { fd.append('fichiers[]', f, f.name); });

    var btn = document.getElementById('btn-suivant');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Traitement\u2026';

    fetch(this.action, { method: 'POST', body: fd })
        .then(function(r) { if (!r.ok) throw new Error('Erreur ' + r.status); return r.json(); })
        .then(function(data) {
            if (data.success) { window.location.href = data.redirect; }
            else {
                btn.disabled = false; btn.innerHTML = orig;
                if (data.errors) {
                    showCustomAlert(data.errors.join(' | '), 'danger');
                }
            }
        })
        .catch(function() {
            btn.disabled = false; btn.innerHTML = orig;
            showCustomAlert('Une erreur est survenue. Veuillez réessayer.', 'danger');
        });
});
</script>
ENDJS;

<?php include __DIR__ . '/layout/footer.php'; ?>
