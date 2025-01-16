<?php
/**
 * @package FishPig_WordPress
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/
 */
namespace FishPig\WordPress\X;

class Theme
{
    /**
     *
     */
    const HASH_OPTION_NAME = 'fishpig-theme-hash';

    /**
     *
     */
    const HASH = '9d991924ad879e77904f2ea201eca1b9';

    /**
     *
     */
    const UPDATE_FLAG = 'update.flag';

    /**
     *
     */
    public function __construct()
    {
        add_action('after_setup_theme', function() {
            if ($this->isThemeUpdated()) {
                $this->update();
            }
        });
    }

    /**
     *
     */
    public function isThemeUpdated(): bool
    {
        return $this->isThemeUpdateRequest()
                || $this->isUpdateFlagPresent()
                || $this->isHashMismatch();
    }

    /**
     *
     */
    private function isThemeUpdateRequest(): bool
    {
        return isset($_GET['_fishpig']) && $_GET['_fishpig'] === 'theme.update';
    }

    /**
     *
     */
    private function isUpdateFlagPresent(): bool
    {
        return is_file($this->getUpdateFlagFile());
    }

    /**
     *
     */
    private function isHashMismatch(): bool
    {
        return !self::HASH || get_option(self::HASH_OPTION_NAME) !== self::HASH;
    }

    /**
     *
     */
    private function getUpdateFlagFile(): string
    {
        return __DIR__ . '/../' . self::UPDATE_FLAG;
    }

    /**
     *
     */
    public function update(): void
    {
        file_put_contents(
            ABSPATH . 'theme-update.log',
            date('Y-m-d H:i:s') . ' ' . self::HASH . "\n",
            FILE_APPEND
        );
        update_option(self::HASH_OPTION_NAME, self::HASH);
        flush_rewrite_rules(false);
        do_action('fishpig/wordpress/theme/updated');

        if ($this->isUpdateFlagPresent()) {
            @unlink($this->getUpdateFlagFile());
        }
    }
}
// phpcs:ignoreFile -- this file is a WordPress theme file and will not run in Magento
