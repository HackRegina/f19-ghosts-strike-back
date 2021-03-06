<?php namespace LukeTowers\APSS;

use Event;
use Backend;
use BackendAuth;
use System\Classes\PluginBase;
use System\Classes\CombineAssets;
use Backend\Models\User as BackendUserModel;
use Backend\Controllers\Users as BackendUserController;

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

        /*
         * Register asset bundles
         */
        CombineAssets::registerCallback(function ($combiner) {
            $combiner->registerBundle('$/luketowers/apss/assets/less/reportneedles.less');
        });

        $user = BackendAuth::getUser();
        BackendUserModel::extend(function($model) use ($user) {
            $model->bindEvent('model.beforeValidate', function () use ($model) {
                if (!$model->isSuperUser()) {
                   $model->login = $model->email;
                }
            });
        });

        // Disable the groups functionality in the user lists page
        BackendUserController::extend(function ($controller) {
            $controller->listConfig = $controller->makeConfig($controller->listConfig);
            $controller->listConfig->toolbar = array_merge($controller->listConfig->toolbar, ['buttons' => '$/luketowers/apss/partials/toolbar.users.htm']);
        });

        BackendUserController::extendFormFields(function($form, $model, $context) {
            if (!($model instanceof BackendUserModel)) {
                return;
            }
            $user = BackendAuth::getUser();

            // Disable the groups functionality
            $form->removeField('groups');

            // Remove the login field functionality
            if (!$user->isSuperUser()) {
                $form->removeField('login');
                $form->getFields()['email']->span = 'full';
                $form->removeField('permissions');
            }
        });

        BackendUserController::extendListColumns(function ($list, $model) {
            if (!($model instanceof BackendUserModel)) {
                return;
            }

            $user = BackendAuth::getUser();

            // Remove the login column for non superusers
            if (!$user->isSuperUser()) {
                $list->removeColumn('login');
                $list->removeColumn('is_superuser');
            }

            // Disable the groups functionality
            $list->removeColumn('groups');

            // Remove role & last login columns to read-add at the end
            $list->removeColumn('last_login');
            $list->removeColumn('role');

            $list->addColumns([
                'full_name'  => [
                    'label'      => 'backend::lang.user.full_name',
                    'select'     => "concat(first_name, ' ', last_name)",
                    'searchable' => true,
                ],
                'role'       => [
                    'label'      => 'backend::lang.user.role.name',
                    'relation'   => 'role',
                    'select'     => 'name',
                    'sortable'   => true,
                    'searchable' => true,
                ],
                'last_login' => [
                    'label'      => 'backend::lang.user.last_login',
                    'searchable' => true,
                    'type'       => 'datetime',
                    'invisible'  => true,
                ],
            ]);
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
            'apss-reports' => [
                'label'       => 'Needle Reports',
                'url'         => Backend::url('luketowers/apss/needlereports'),
                'icon'        => 'icon-flag',
                'permissions' => ['luketowers.apss.*'],
                'order'       => 100,
            ],
            'apss-history' => [
                'label'       => 'History',
                'url'         => Backend::url('luketowers/apss/needlereports/history'),
                'icon'        => 'icon-bar-chart',
                'permissions' => ['luketowers.apss.*'],
                'order'       => 105,
            ],
        ];
    }
}
