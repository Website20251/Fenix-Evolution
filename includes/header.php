<?php
$siteTitle = $siteTitle ?? 'Fisiotraining';
$siteDescription = $siteDescription ?? 'Fisioterapia deportiva, entrenamiento personal y nutrición deportiva.';
$bodyClass = $bodyClass ?? '';
?>
<header class="site-header" id="top">
  <div class="container header-bar">
    <a class="brand" href="#inicio" aria-label="Fisiotraining inicio">
      <img src="assets/favicon.svg" alt="Logo de Fisiotraining" width="44" height="44">
      <span>
        <strong>Fisiotraining</strong>
        <small><?php echo htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8'); ?></small>
      </span>
    </a>
    <?php include __DIR__ . '/navbar.php'; ?>
    <a class="nav-cta" href="#contacto">Reservar cita</a>
  </div>
</header>
