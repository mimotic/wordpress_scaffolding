<?php
/**
 * Elementor + caché de página (WP Super Cache)
 *
 * Elementor guarda el CSS de cada página en uploads/elementor/css y lo borra entero
 * al terminar cualquier actualización (upgrader_process_complete), también las de
 * traducciones, que updates.sh reinstala a menudo aunque no cambien. Después lo
 * regenera página a página cuando PHP las pinta. Con caché de página, nginx sigue
 * sirviendo el HTML guardado, que pide CSS que ya no existen: la web se ve sin
 * estilos y cada CSS ausente acaba en un 404 de WordPress (lento).
 *
 * 1. Las actualizaciones de traducciones no borran el CSS de Elementor (lo mismo que
 *    hace su función experimental "Optimized CSS Files", en fase alfa).
 * 2. Cuando Elementor sí borra su CSS (actualización real, ajustes globales,
 *    "Regenerar CSS"...), se vacía también la caché de página.
 *
 * Visto en tascalafarmacia.com el 2026-11-22.
 */

add_action('upgrader_process_complete', function ($upgrader, $hookExtra) {
    if (!is_array($hookExtra)) {
        return;
    }
    $isTranslation = ('translation' === ($hookExtra['type'] ?? null)) || !empty($hookExtra['translations']);
    if (!$isTranslation || !class_exists('\Elementor\Plugin') || empty(\Elementor\Plugin::$instance->files_manager)) {
        return;
    }
    remove_action(
        'upgrader_process_complete',
        [\Elementor\Plugin::$instance->files_manager, 'on_upgrader_process_complete'],
        10
    );
}, 5, 2);

add_action('elementor/core/files/clear_cache', function () {
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }
});
