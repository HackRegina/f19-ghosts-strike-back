<?php namespace LukeTowers\APSS\Controllers;

use BackendMenu;
use ApplicationException;
use Backend\Classes\Controller;

use LukeTowers\APSS\Models\NeedleReport;

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

        BackendMenu::setContext('LukeTowers.APSS', 'apss-reports');
    }

    public function runAjaxHandler($handler)
    {
        if (is_null($this->params)) {
            $this->params = [];
        }

        return parent::runAjaxHandler($handler);
    }

    public function update_onMarkSuccessful($recordId)
    {
        $needlesCollected = input('NeedleReport')['needles_collected'];
        if (empty($needlesCollected)) {
            throw new ApplicationException("Please fill out how many needles were collected before closing this report.");
        }

        $report = NeedleReport::findOrFail($recordId);
    }

    public function update_onMarkUnsuccessful($recordId)
    {
        $report = NeedleReport::findOrFail($recordId);
    }
}
