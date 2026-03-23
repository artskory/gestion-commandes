<?php
$pageTitle   = 'Page introuvable – 404';
$basePath    = '/gestion-commandes/';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>404 — Page Non Approuvée</title>
  <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="icon" type="image/png" href="<?php echo $basePath; ?>image/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/svg+xml" href="<?php echo $basePath; ?>image/favicon.svg">
  <link rel="shortcut icon" href="<?php echo $basePath; ?>image/favicon.ico">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $basePath; ?>image/apple-touch-icon.png">
  <link rel="manifest" href="<?php echo $basePath; ?>image/site.webmanifest">
  <style>
    :root {
      --ink: #1a1008;
      --paper: #f0e8d5;
      --paper-dark: #d9cdb4;
      --red-stamp: #c0201a;
      --cyan: #00aeef;
      --magenta: #ec008c;
      --yellow: #fff200;
      --black: #231f20;
      --blue-ink: #1a3a6e;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: #8a8070;
      font-family: 'IBM Plex Mono', monospace;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px;
      background-image:
        repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(0,0,0,0.03) 40px, rgba(0,0,0,0.03) 41px),
        repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(0,0,0,0.03) 40px, rgba(0,0,0,0.03) 41px);
    }

    /* === SHEET === */
    .sheet {
      background: var(--paper);
      width: 100%;
      max-width: 860px;
      min-height: 640px;
      position: relative;
      padding: 60px 70px 50px;
      box-shadow:
        0 2px 0 rgba(0,0,0,0.15),
        4px 8px 30px rgba(0,0,0,0.4),
        0 0 0 1px rgba(0,0,0,0.1);
      animation: sheetIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
      overflow: hidden;
    }

    /* paper texture grain */
    .sheet::before {
      content: '';
      position: absolute; inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='0.06'/%3E%3C/svg%3E");
      pointer-events: none; z-index: 0;
    }

    @keyframes sheetIn {
      from { opacity: 0; transform: translateY(18px) rotate(-0.5deg); }
      to   { opacity: 1; transform: translateY(0) rotate(0deg); }
    }

    /* === CROP MARKS === */
  
.crop {
  position: absolute;
  width: 40px;
  height: 40px;
  pointer-events: none;
}

.crop::before,
.crop::after {
  content: "";
  position: absolute;
  background: black;
}

.crop::before { 
  width: 28px; 
  height: 1px; 
}

.crop::after {
  width: 1px;
  height: 28px;
}

.crop.tl {
top: -20px;
left: -20px;
}

.crop.tl::before {
  left: 0;
  bottom: 0;
}

.crop.tl::after {
  right: 0;
  top: 0;
}

.crop.tr {
  top: -20px;
  right: -20px;
}

.crop.tr::before {
  right: 0;
  bottom: 0; 
}

.crop.tr::after {
  left: 0; 
  top: 0;
}

.crop.bl {
  bottom: -20px;
  left: -20px;
}

.crop.bl::before {
  left: 0px;
  top: 0px;
}

.crop.bl::after {
  left: 38px;
  bottom: 0px;
}

.crop.br {
  bottom: -20px;
  right: -20px;
}
.crop.br::before {
  left: 12px;
  top: 0px;
}

.crop.br::after {
  left: 0;
  bottom: 0;
}

    /* registration mark */
    .reg {
      position: absolute;
      width: 18px; height: 18px;
      display: flex; align-items: center; justify-content: center;
    }
    .reg svg { width: 18px; height: 18px; }
    .reg-t { top: 10px; left: 50%; transform: translateX(-50%); }
    .reg-b { bottom: 10px; left: 50%; transform: translateX(-50%); }
    .reg-l { left: 10px; top: 50%; transform: translateY(-50%); }
    .reg-r { right: 10px; top: 50%; transform: translateY(-50%); }

    /* === COLOR BAR === */
    .color-bar {
      position: absolute;
      bottom: 26px; left: 70px; right: 70px;
      height: 10px;
      display: flex;
      gap: 2px;
    }
    .cb { flex: 1; }
    .cb-c  { background: var(--cyan); }
    .cb-m  { background: var(--magenta); }
    .cb-y  { background: var(--yellow); }
    .cb-k  { background: var(--black); }
    .cb-r  { background: #ff3300; }
    .cb-g  { background: #00b050; }
    .cb-b  { background: #0032a0; }
    .cb-10 { background: rgba(0,0,0,0.1); }
    .cb-25 { background: rgba(0,0,0,0.25); }
    .cb-50 { background: rgba(0,0,0,0.5); }
    .cb-75 { background: rgba(0,0,0,0.75); }
    .cb-90 { background: rgba(0,0,0,0.9); }
    .cb-w  { background: #fff; border: 1px solid rgba(0,0,0,0.1); }

    /* === HEADER === */
    .header {
      position: relative; z-index: 1;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 1.5px solid var(--ink);
      padding-bottom: 14px;
      margin-bottom: 36px;
    }
    .header-left { display: flex; flex-direction: column; gap: 3px; }
    .doc-label {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 0.2em;
      color: #888;
    }
    .doc-title {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 18px;
      letter-spacing: 0.12em;
      color: var(--ink);
    }
    .doc-meta {
      font-size: 8.5px;
      color: #999;
      letter-spacing: 0.05em;
    }
    .header-right {
      text-align: right;
      display: flex;
      flex-direction: column;
      gap: 3px;
    }
    .version-badge {
      font-size: 8px;
      border: 1px solid var(--ink);
      padding: 2px 6px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--ink);
    }

    /* === MAIN CONTENT === */
    .content {
      position: relative; z-index: 1;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 40px;
      align-items: start;
    }

    /* === 404 NUMBER === */
    .error-num {
      font-family: 'Bebas Neue', sans-serif;
      font-size: clamp(100px, 18vw, 180px);
      line-height: 0.88;
      color: var(--ink);
      letter-spacing: -0.02em;
      position: relative;
      user-select: none;
    }
    /* halftone dot texture on 404 */
    .error-num::after {
      content: attr(data-text);
      position: absolute; inset: 0;
      color: transparent;
      background-image: radial-gradient(circle, rgba(0,0,0,0.18) 1px, transparent 1px);
      background-size: 5px 5px;
      -webkit-background-clip: text;
      background-clip: text;
      pointer-events: none;
    }

    .error-body {
      padding-top: 8px;
    }

    .error-eyebrow {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 0.25em;
      color: #999;
      margin-bottom: 10px;
    }

    .error-title {
      font-family: 'Special Elite', cursive;
      font-size: 22px;
      line-height: 1.3;
      color: var(--ink);
      margin-bottom: 18px;
    }

    .error-desc {
      font-size: 11px;
      color: #666;
      line-height: 1.8;
      max-width: 340px;
      margin-bottom: 28px;
    }

    /* === ANNOTATIONS === */
    .annotation {
      font-family: 'Special Elite', cursive;
      font-size: 12px;
      color: var(--blue-ink);
      position: relative;
      padding-left: 18px;
      line-height: 1.6;
      margin-bottom: 6px;
    }
    .annotation::before {
      content: '✎';
      position: absolute;
      left: 0;
      color: var(--blue-ink);
    }

    /* === CHECKLIST === */
    .checklist {
      margin: 22px 0;
      border: 1px solid var(--paper-dark);
      padding: 14px 18px;
      background: rgba(255,255,255,0.3);
    }
    .checklist-title {
      font-size: 8.5px;
      text-transform: uppercase;
      letter-spacing: 0.2em;
      color: #aaa;
      margin-bottom: 10px;
    }
    .check-item {
      display: flex;
      gap: 10px;
      font-size: 10.5px;
      color: var(--ink);
      padding: 3px 0;
      align-items: center;
    }
    .check-box {
      width: 13px; height: 13px;
      border: 1.5px solid var(--ink);
      display: inline-flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      font-size: 10px;
    }
    .check-box.checked { color: var(--ink); }
    .check-box.cross { border-color: var(--red-stamp); color: var(--red-stamp); }
    .check-label.striked { text-decoration: line-through; color: #bbb; }

    /* === STAMP === */
    .stamp-wrap {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
    }

    .stamp {
      width: 140px;
      height: 140px;
      border: 4px solid var(--red-stamp);
      border-radius: 4px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      transform: rotate(-8deg);
      position: relative;
      padding: 10px;
      animation: stampIn 0.5s 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
      box-shadow: inset 0 0 0 2px var(--red-stamp);
    }

    /* ink bleed on stamp */
    .stamp::before {
      content: '';
      position: absolute; inset: -4px;
      border: 4px solid var(--red-stamp);
      border-radius: 4px;
      opacity: 0.15;
    }

    @keyframes stampIn {
      from { opacity: 0; transform: rotate(-8deg) scale(2.5); }
      to   { opacity: 1; transform: rotate(-8deg) scale(1); }
    }

    .stamp-top {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 11px;
      letter-spacing: 0.3em;
      color: var(--red-stamp);
      text-align: center;
      border-bottom: 1.5px solid var(--red-stamp);
      padding-bottom: 5px;
      margin-bottom: 5px;
      width: 100%;
    }
    .stamp-main {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 30px;
      letter-spacing: 0.05em;
      color: var(--red-stamp);
      text-align: center;
      line-height: 1.1;
    }
    .stamp-sub {
      font-size: 8.5px;
      color: var(--red-stamp);
      letter-spacing: 0.2em;
      text-align: center;
      border-top: 1.5px solid var(--red-stamp);
      padding-top: 5px;
      margin-top: 5px;
      width: 100%;
    }

    /* second small stamp */
    .stamp-small {
      width: 100px;
      border: 2.5px double var(--blue-ink);
      border-radius: 50%;
      padding: 10px 6px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      transform: rotate(12deg);
      animation: stampIn 0.5s 0.9s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
      height: 100px;
    }
    .stamp-small span {
      font-family: 'Special Elite', cursive;
      font-size: 9px;
      color: var(--blue-ink);
      text-align: center;
      line-height: 1.4;
    }
    .stamp-small strong {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 16px;
      letter-spacing: 0.08em;
      color: var(--blue-ink);
      display: block;
    }

    /* === BUTTON === */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-family: 'IBM Plex Mono', monospace;
      font-size: 10.5px;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: var(--paper);
      background: var(--ink);
      border: none;
      padding: 12px 22px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.15s;
      position: relative;
      overflow: hidden;
    }
    .btn::after {
      content: '';
      position: absolute; inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
    }
    .btn:hover { background: #3a2e1f; }
    .btn-arrow { font-size: 14px; }

    /* === DIVIDER === */
    .divider {
      border: none;
      border-top: 1px solid var(--paper-dark);
      margin: 28px 0;
    }

    /* === FOOTER === */
    .footer {
      position: relative; z-index: 1;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1.5px solid var(--ink);
      padding-top: 12px;
      margin-top: 40px;
    }
    .footer-info {
      font-size: 8px;
      color: #aaa;
      letter-spacing: 0.1em;
      text-transform: uppercase;
    }
    .footer-cmyk {
      display: flex;
      gap: 6px;
      align-items: center;
    }
    .cmyk-dot {
      width: 12px; height: 12px;
      border-radius: 50%;
    }
    .cmyk-c { background: var(--cyan); }
    .cmyk-m { background: var(--magenta); }
    .cmyk-y { background: var(--yellow); border: 1px solid rgba(0,0,0,0.1); }
    .cmyk-k { background: var(--black); }

    /* === FOLD LINE (decorative) === */
    .fold {
      position: absolute;
      top: 0; bottom: 0;
      left: 50px;
      width: 1px;
      background: repeating-linear-gradient(
        to bottom,
        rgba(0,0,0,0.08) 0px,
        rgba(0,0,0,0.08) 6px,
        transparent 6px,
        transparent 12px
      );
    }

    /* flicker on load for stamp */
    @keyframes flicker {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.85; }
    }
    .stamp { animation: stampIn 0.5s 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) both, flicker 4s 1.2s ease-in-out infinite; }

    /* === RESPONSIVE === */
    @media (max-width: 620px) {
      .sheet { padding: 40px 28px 50px; }
      .content { grid-template-columns: 1fr; }
      .stamp-wrap { flex-direction: row; justify-content: center; }
      .error-num { font-size: 110px; }
    }
  </style>
</head>
<body>

<div class="sheet">

  <!-- Fold line -->
  <div class="fold"></div>

  <!-- Crop marks -->
  <div class="crop tl"></div>
  <div class="crop tr"></div>
  <div class="crop bl"></div>
  <div class="crop br"></div>

  <!-- Registration marks -->
  <div class="reg reg-t">
    <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="9" cy="9" r="7" stroke="#1a1008" stroke-width="0.8"/>
      <circle cx="9" cy="9" r="3" stroke="#1a1008" stroke-width="0.8"/>
      <line x1="9" y1="0" x2="9" y2="18" stroke="#1a1008" stroke-width="0.8"/>
      <line x1="0" y1="9" x2="18" y2="9" stroke="#1a1008" stroke-width="0.8"/>
    </svg>
  </div>
  <div class="reg reg-b">
    <svg viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="#1a1008" stroke-width="0.8"/><circle cx="9" cy="9" r="3" stroke="#1a1008" stroke-width="0.8"/><line x1="9" y1="0" x2="9" y2="18" stroke="#1a1008" stroke-width="0.8"/><line x1="0" y1="9" x2="18" y2="9" stroke="#1a1008" stroke-width="0.8"/></svg>
  </div>
  <div class="reg reg-l">
    <svg viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="#1a1008" stroke-width="0.8"/><circle cx="9" cy="9" r="3" stroke="#1a1008" stroke-width="0.8"/><line x1="9" y1="0" x2="9" y2="18" stroke="#1a1008" stroke-width="0.8"/><line x1="0" y1="9" x2="18" y2="9" stroke="#1a1008" stroke-width="0.8"/></svg>
  </div>
  <div class="reg reg-r">
    <svg viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="#1a1008" stroke-width="0.8"/><circle cx="9" cy="9" r="3" stroke="#1a1008" stroke-width="0.8"/><line x1="9" y1="0" x2="9" y2="18" stroke="#1a1008" stroke-width="0.8"/><line x1="0" y1="9" x2="18" y2="9" stroke="#1a1008" stroke-width="0.8"/></svg>
  </div>

  <!-- Header -->
  <header class="header">
    <div class="header-left">
      <span class="doc-label">Bon à Tirer — Document de contrôle qualité</span>
      <span class="doc-title">Épreuve de validation — Atelier Web</span>
      <span class="doc-meta">Réf. ERR-404 · Épr. 001/001 · Imprimé le <script>document.write(new Date().toLocaleDateString('fr-FR'))</script></span>
    </div>
    <div class="header-right">
      <span class="version-badge">Version 1.0 — Non validée</span>
      <span class="doc-meta">Conducteur : Système</span>
      <span class="doc-meta">Presse : HTTP/1.1</span>
    </div>
  </header>

  <!-- Main content -->
  <div class="content">
    <div>
      <!-- Error number -->
      <div class="error-num" data-text="404">404</div>

      <!-- Body text -->
      <div class="error-body">
        <div class="error-eyebrow">Code d'erreur de mise en page</div>
        <h1 class="error-title">Cette page n'a pas<br>passé le contrôle qualité.</h1>
        <p class="error-desc">
          L'épreuve demandée est introuvable dans notre atelier. Elle a peut-être été retirée de la chaîne graphique, déplacée vers un autre support, ou n'a jamais été mise en fabrication.
        </p>

        <!-- Annotations in blue ink -->
        <div class="annotation">URL incorrecte ou page supprimée</div>
        <div class="annotation">Vérifier le bon de commande et l'imposition</div>
        <div class="annotation">Contacter le conducteur de machine</div>

        <!-- Checklist -->
        <div class="checklist">
          <div class="checklist-title">Contrôle pré-impression — BAT</div>
          <div class="check-item">
            <span class="check-box checked">✓</span>
            <span class="check-label">Résolution des images (300 dpi min.)</span>
          </div>
          <div class="check-item">
            <span class="check-box checked">✓</span>
            <span class="check-label">Profil colorimétrique CMJN validé</span>
          </div>
          <div class="check-item">
            <span class="check-box cross">✗</span>
            <span class="check-label striked">Page trouvée sur le serveur</span>
          </div>
          <div class="check-item">
            <span class="check-box cross">✗</span>
            <span class="check-label striked">URL correctement renseignée</span>
          </div>
          <div class="check-item">
            <span class="check-box cross">✗</span>
            <span class="check-label striked">Contenu approuvé pour impression</span>
          </div>
        </div>
        <a href="<?php echo $basePath; ?>" class="btn btn-home">
            <i class="bi bi-house-door me-2"></i>Retour à l'atelier
        </a>
      </div>
    </div>

    <!-- Stamps -->
    <div class="stamp-wrap">
      <div class="stamp">
        <div class="stamp-top">Imprimerie</div>
        <div class="stamp-main">NON<br>APPROUVÉ</div>
        <div class="stamp-sub">Réf. 404</div>
      </div>
      <div class="stamp-small">
        <span>Contrôle</span>
        <strong>REJET</strong>
        <span>Atelier Web</span>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-info">Épreuve contractuelle · Usage interne uniquement</div>
    <div class="footer-cmyk">
      <div class="cmyk-dot cmyk-c"></div>
      <div class="cmyk-dot cmyk-m"></div>
      <div class="cmyk-dot cmyk-y"></div>
      <div class="cmyk-dot cmyk-k"></div>
    </div>
    <div class="footer-info">Page 1 / 0</div>
  </footer>

  <!-- Color calibration bar -->
  <div class="color-bar">
    <div class="cb cb-c"></div>
    <div class="cb cb-m"></div>
    <div class="cb cb-y"></div>
    <div class="cb cb-k"></div>
    <div class="cb cb-r"></div>
    <div class="cb cb-g"></div>
    <div class="cb cb-b"></div>
    <div class="cb cb-w"></div>
    <div class="cb cb-10"></div>
    <div class="cb cb-25"></div>
    <div class="cb cb-50"></div>
    <div class="cb cb-75"></div>
    <div class="cb cb-90"></div>
    <div class="cb cb-k"></div>
  </div>

</div>

</body>
</html>
