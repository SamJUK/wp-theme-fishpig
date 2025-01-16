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
    const HASH = '37dcbe05580be159f433b99b814a830a';

    /**
     *
     */
    const UPDATE_FLAG = 'update.flag';

    /**
     *
     */
    const UPDATE_AVAILABLE_HASH_OPTION_NAME = 'fishpig-theme-update-available-hash';

    /**
     *
     */
    const UPDATE_AVAILABLE_URL_OPTION_NAME = 'fishpig-theme-update-available-url';

    /**
     *
     */
    public function __construct()
    {
        add_action('after_setup_theme', function() {
            if ($this->isThemeUpdated()) {
                $this->update();
            }

            //
            add_filter(
                'wp_prepare_themes_for_js',
                function ($prepared_themes) {
                    if ($this->isThemeUpdateAvailable()) {
                        $prepared_themes['fishpig']['hasUpdate'] = true;

                        if ($themeUpdateUrl = $this->getAvailableThemeUpdateUrl()) {
                            $prepared_themes['fishpig']['update'] = <<<END
<br><p><a href="{$themeUpdateUrl}" class="button button-primary button-download">Download</a></p>
<p>Download the ZIP file using the button above and then <a href="{$this->getThemeUploadUrl()}">upload it</a>.
END;
                        } else {
                            $prepared_themes['fishpig']['update'] = <<<END
<br>
<p>The FishPig theme is auto generated by Magento 2.</p>
<p>To download the latest version go to:</p>
<p>https://www.YOUR-MAGENTO-URL.com/wordpress/theme/latest.zip</p>
<p>Or you can generate the ZIP using the CLI:</p>
<p>bin/magento fishpig:wordpress:theme --zip</p>
END;
                        }
                    }

                    return $prepared_themes;
                }
            );
        });

        // Show 'Update Available' message
        add_filter(
            'upgrader_package_options',
            function ($options) {
                if (!isset($options['hook_extra']['type']) || $options['hook_extra']['type'] !== 'theme') {
                    return $options;
                }

                if (!isset($options['hook_extra']['action']) || $options['hook_extra']['action'] !== 'install') {
                    return $options;
                }

                if (strpos(basename($options['package']), 'fishpig') === false) {
                    return $options;
                }

                $options['abort_if_destination_exists'] = false;
                $options['clear_working'] = true;
                $options['clear_destination'] = true;

                return $options;
            }
        );
    }

    /**
     *
     */
    private function getThemeUploadUrl(): string
    {
        return is_multisite()
                ? network_admin_url('theme-install.php')
                : get_admin_url(null, 'theme-install.php');
    }

    /**
     *
     */
    public function isThemeUpdateAvailable(): bool
    {
        return ($localThemeHash = get_option(self::UPDATE_AVAILABLE_HASH_OPTION_NAME))
                && $localThemeHash !== self::HASH;
    }

    /**
     *
     */
    public function getAvailableThemeUpdateUrl(): ?string
    {
        return $this->isThemeUpdateAvailable() ? get_option(self::UPDATE_AVAILABLE_URL_OPTION_NAME) : '';
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
        update_option(self::HASH_OPTION_NAME, self::HASH);
        flush_rewrite_rules(false);
        do_action('fishpig/wordpress/theme/updated');

        if ($this->isUpdateFlagPresent()) {
            @unlink($this->getUpdateFlagFile());
        }
    }
}
// phpcs:ignoreFile -- this file is a WordPress theme file and will not run in Magento
