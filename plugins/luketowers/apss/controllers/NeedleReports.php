<?php namespace LukeTowers\APSS\Controllers;

use Flash;
use Backend;
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
    public $listConfig = [
        'index'   => 'config_list_index.yaml',
        'history' => 'config_list_history.yaml'
    ];


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

        $report->needles_collected = $needlesCollected;
        $report->status = 'successful';
        $report->save();

        Flash::success("Completed report, logged $needlesCollected needles collected");

        return Backend::redirect('luketowers/apss/needlereports');
    }

    public function update_onMarkUnsuccessful($recordId)
    {
        $report = NeedleReport::findOrFail($recordId);

        $report->status = 'unsuccessful';
        $report->save();

        Flash::success("Marked report as unsuccessful");

        return Backend::redirect('luketowers/apss/needlereports');
    }

    public function history()
    {
        BackendMenu::setContext('LukeTowers.APSS', 'apss-history');
        return $this->asExtension('Backend.Behaviors.ListController')->index();
    }

    public function listExtendQuery($query, $definition)
    {
        if ($definition === 'index') {
            return $query->where('status', 'submitted');
        } else {
            return $query->whereIn('status', ['successful', 'unsuccessful']);
        }
    }
}
