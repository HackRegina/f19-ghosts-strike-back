<?php namespace LukeTowers\APSS;

use Event;
use Backend;
use System\Classes\PluginBase;

/**
 * APSS Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Aids Program South Saskatchewan',
            'description' => 'Plugin powering the APSS online tools',
            'author'      => 'Luke Towers',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        // Add the custom favicon details
        Event::listen('backend.layout.extendHead', function ($layoutFile) {
            return '
                <link rel="apple-touch-icon" sizes="180x180" href="/plugins/luketowers/apss/assets/favicon/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="/plugins/luketowers/apss/assets/favicon/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="/plugins/luketowers/apss/assets/favicon/favicon-16x16.png">
                <link rel="manifest" href="/plugins/luketowers/apss/assets/favicon/site.webmanifest">
                <link rel="mask-icon" href="/plugins/luketowers/apss/assets/favicon/safari-pinned-tab.svg" color="#e2242d">
                <link rel="shortcut icon" href="/plugins/luketowers/apss/assets/favicon/favicon.ico">
                <meta name="msapplication-TileColor" content="#ffffff">
                <meta name="msapplication-config" content="/plugins/luketowers/apss/assets/favicon/browserconfig.xml">
                <meta name="theme-color" content="#ffffff">
            ';
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'LukeTowers\APSS\Components\ReportNeedles' => 'reportNeedles',
        ];
    }

    /**
     * Registers any RainLab.Pages Snippets
     */
    public function registerPageSnippets()
    {
        return [
            'LukeTowers\APSS\Components\ReportNeedles' => 'reportNeedles',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'luketowers.apss.manage_reports' => [
                'tab' => 'Aids Program South Saskatchewan',
                'label' => 'Manage reports'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'apss' => [
                'label'       => 'APSS',
                'url'         => Backend::url('luketowers/apss/needlereports'),
                'icon'        => 'icon-leaf',
                'permissions' => ['luketowers.apss.*'],
                'order'       => 500,
                'sideMenu'    => [
                    'needlereports' => [
                        'label'       => 'APSS',
                        'url'         => Backend::url('luketowers/apss/needlereports'),
                        'icon'        => 'icon-flag',
                        'permissions' => ['luketowers.apss.*'],
                        'order'       => 500,
                    ],
                ],
            ],
        ];
    }
}
