<?php

if (!function_exists('get_plugins')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

class Vindi_Dependencies
{
    /**
     * @var array
     **/
    private static $active_plugins;

    /**
     * @return  boolean
     */
    public static function check()
    {
        if (!self::$active_plugins) {
            self::init();
        }
        $plugins = [
            [
                'path' => 'woocommerce/woocommerce.php',
                'plugin' => [
                    'name' => 'WooCommerce',
                    'url' => 'https://wordpress.org/extend/plugins/woocommerce/',
                    'version' => [
                        'validation' => '>=',
                        'number' => '3.0'
                    ]
                ]
            ],
            [
                'path' => 'woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php',
                'plugin' => [
                    'name' => 'WooCommerce Extra Checkout Fields for Brazil',
                    'url' => 'https://wordpress.org/extend/plugins/woocommerce-extra-checkout-fields-for-brazil/',
                    'version' => [
                        'validation' => '>=',
                        'number' => '3.5'
                    ]
                ]
            ]
        ];

        foreach ($plugins as $plugin) {
            if (!self::is_plugin_right_version($plugin)) {
                self::missing_notice($plugin['plugin']['name'],
                    $plugin['plugin']['version']['number'],
                    $plugin['plugin']['url']);
                return false;
            }
        }

        return true;
    }

    /**
     * Init Vindi_Dependencies.
     */
    public static function init()
    {
        self::$active_plugins = get_option('active_plugins', array());

        if (is_multisite()) {
            self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
    }


    /**
     * Verifica se o plugin esta instalado e esta na versão correta
     * @param $plugin
     *
     * @return bool
     */
    public static function is_plugin_right_version(array $plugin)
    {
        return in_array($plugin['path'], self::$active_plugins) && version_compare(
                get_plugin_data(plugin_dir_path(__DIR__) . "../" . $plugin['path'])['Version'],
                $plugin['plugin']['version']['number'],
                $plugin['plugin']['version']['validation']
            );
    }

    /**
     * @param string $name
     * @param $version
     * @param string $link
     *
     * @return  string
     */
    public static function missing_notice($name, $version, $link)
    {
        add_action('admin_notices', function () use ($name, $version, $link) {
            echo '<div class="error"><p>' . sprintf(__('O  Plugin Vindi WooCommerce depende da versão %s do %s para funcionar!', VINDI_IDENTIFIER), $version, "<a href=\"{$link}\">" . __($name, VINDI_IDENTIFIER) . '</a>') . '</p></div>';
        });
    }

    /**
     * @return boolean
     **/
    public static function is_wc_subscriptions_activated()
    {
        return self::is_plugin_right_version([
            'path' => 'woocommerce-subscriptions/woocommerce-subscriptions.php',
            'plugin' => [
                'name' => 'WooCommerce Subscriptions',
                'url' => 'http://www.woothemes.com/products/woocommerce-subscriptions/',
                'version' => [
                    'validation' => '>=',
                    'number' => '2.2'
                ]
            ]
        ]);
    }

    /**
     * @return  boolean
     */
    public static function is_wc_memberships_activated()
    {
        return self::is_plugin_right_version([
            'path' => 'woocommerce-memberships/woocommerce-memberships.php',
            'plugin' => [
                'name' => 'WooCommerce Memberships',
                'url' => 'http://www.woothemes.com/products/woocommerce-memberships/',
                'version' => [
                    'validation' => '>=',
                    'number' => '1.0'
                ]
            ]
        ]);
    }
}
