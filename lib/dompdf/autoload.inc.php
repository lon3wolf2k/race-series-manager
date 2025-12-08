<?php
/**
 * Dompdf compatibility autoloader.
 *
 * The plugin expects an autoload.inc.php entry point in lib/dompdf.
 * This wrapper ensures the bundled Composer autoloader is loaded when needed.
 */

if ( ! class_exists( '\Dompdf\Dompdf' ) ) {
    $composer_autoload = __DIR__ . '/vendor/autoload.php';
    if ( file_exists( $composer_autoload ) ) {
        require_once $composer_autoload;
    }
}
