<?php namespace LukeTowers\APSS\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Needle Reports Back-end Controller
 */
class NeedleReports extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('LukeTowers.APSS', 'apss', 'needlereports');
    }
}
