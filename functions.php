<?php
/**
 *
 * DO NOT EDIT THIS FILE
 * Any changes you make to this file will be lost during the theme upgrade
 * To customise things, create a new WordPress plugin and add your changes there
 *
 */
// phpcs:ignoreFile -- this file is a WordPress theme file and will not run in Magento

define('FISHPIG_THEME_OPTION_NAME', 'fishpig-theme-hash');
define('FISHPIG_THEME_HASH', '20cf64fc1d7bd660d0131bef8b14f7bb');
define('FISHPIG_CLASS_PREFIX', 'FishPig\WordPress\X\\');

/**
 * Register the autoloader for FishPig theme classes
 * All classes are prefixed with FishPig\WordPress\X
 */
spl_autoload_register(function($className) {
    if (strpos($className, FISHPIG_CLASS_PREFIX) === false) {
        return false;
    }

    $baseName = trim(
        preg_replace_callback(
            '/[A-Z]{1}/', 
            function($x) {
                return '-' . strtolower($x[0]);
            },
            str_replace(FISHPIG_CLASS_PREFIX, '', $className)
        ),
        '-'
    );

    foreach (['includes', 'add-ons'] as $dir) {
        $file = __DIR__ . '/' . $dir . '/' . $baseName . '.php';

        if (is_file($file)) {
            require_once($file);
            return true;
        }
    }
    
    return false;
});

/**
 * Initialise base objets
 */
$fishpigObjects = [
    new FishPig\WordPress\X\Setup(),
    new FishPig\WordPress\X\AuthorisationKey(),
    new FishPig\WordPress\X\Api(),
    new FishPig\WordPress\X\Previews(),
    new FishPig\WordPress\X\Fpc(),
    new FishPig\WordPress\X\Misc(),
];

/**
 * Init add-on files
 */
$addonsDir = __DIR__ . '/add-ons';

if (is_dir($addonsDir)) {
    foreach (scandir($addonsDir) as $file) {
        if (strpos($file, '.php') !== false) {
            $className = FISHPIG_CLASS_PREFIX . str_replace(
                [' ', '.php'],
                '',
                ucwords(
                    str_replace('-', ' ', $file)
                )
            );
            
            $fishpigObjects[$className] = new $className();
        }
    }
}
