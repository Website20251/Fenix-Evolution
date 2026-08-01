<?php
$navigationItems = [
  ['href' => '#inicio', 'label' => 'Inicio'],
  ['href' => '#nosotros', 'label' => 'Nosotros'],
  ['href' => '#servicios', 'label' => 'Servicios'],
  ['href' => '#galeria', 'label' => 'Galería'],
  ['href' => '#equipo', 'label' => 'Equipo'],
  ['href' => '#testimonios', 'label' => 'Testimonios'],
  ['href' => '#ubicacion', 'label' => 'Ubicación'],
  ['href' => '#contacto', 'label' => 'Contacto'],
];
?>
<nav class="site-nav" id="primary-navigation" aria-label="Principal">
  <?php foreach ($navigationItems as $item): ?>
    <a href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></a>
  <?php endforeach; ?>
</nav>
