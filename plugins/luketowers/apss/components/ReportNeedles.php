<?php namespace LukeTowers\APSS\Components;

use DB;
use Redirect;
use Cms\Classes\ComponentBase;
use Backend\Classes\WidgetManager;

class ReportNeedles extends ComponentBase
{
    use \Backend\Traits\FormModelSaver;

    /**
     * @var Controller The form controller instance
     */
    protected $formController;

    public function componentDetails()
    {
        return [
            'name'        => 'Report Needles',
            'description' => 'Form for reporting needles'
        ];
    }

    public function init()
    {
        $this->initFormController();
    }

    public function initFormController()
    {
        if ($this->formController) {
            return;
        }

        // Register the form widgets:
        WidgetManager::instance()->registerFormWidgets(function ($manager) {
            $manager->registerFormWidget('Backend\FormWidgets\CodeEditor', 'codeeditor');
            $manager->registerFormWidget('Backend\FormWidgets\RichEditor', 'richeditor');
            $manager->registerFormWidget('Backend\FormWidgets\MarkdownEditor', 'markdown');
            $manager->registerFormWidget('Backend\FormWidgets\FileUpload', 'fileupload');
            $manager->registerFormWidget('Backend\FormWidgets\Relation', 'relation');
            $manager->registerFormWidget('Backend\FormWidgets\DatePicker', 'datepicker');
            $manager->registerFormWidget('Backend\FormWidgets\TimePicker', 'timepicker');
            $manager->registerFormWidget('Backend\FormWidgets\ColorPicker', 'colorpicker');
            $manager->registerFormWidget('Backend\FormWidgets\DataTable', 'datatable');
            $manager->registerFormWidget('Backend\FormWidgets\RecordFinder', 'recordfinder');
            $manager->registerFormWidget('Backend\FormWidgets\Repeater', 'repeater');
            $manager->registerFormWidget('Backend\FormWidgets\TagList', 'taglist');
            $manager->registerFormWidget('Backend\FormWidgets\MediaFinder', 'mediafinder');
            $manager->registerFormWidget('Backend\FormWidgets\NestedForm', 'nestedform');
        });

        // Initialize the Request model
        $model = new \LukeTowers\APSS\Models\NeedleReport;

        // Build a backend form with the context of 'frontend'
        $formController = new \LukeTowers\APSS\Controllers\NeedleReports();
        $formController->initForm($model, 'frontend');
        $formController->create('frontend');

        $this->formController = $this->page['form'] = $formController;

        $aliasesToProxy = array_keys(get_object_vars($this->formController->widget));

        $this->controller->bindEvent('ajax.beforeRunHandler', function ($handler) use ($aliasesToProxy) {
            if (strpos($handler, '::')) {
                list($componentAlias, $handlerName) = explode('::', $handler);

                if (in_array($componentAlias, $aliasesToProxy)) {
                    return $this->formController->runAjaxHandler($handler);
                }
            }
        });
    }

    public function onRun()
    {
        // Load the required assets
        $this->addJs('/modules/system/assets/ui/storm-min.js', 'core');
        $this->addJs('/modules/backend/assets/js/october-min.js', 'core');
        $this->addJs('/modules/system/assets/js/lang/lang.en.js', 'core'); // required for datepicker

        $this->addCss('/plugins/luketowers/apss/assets/css/reportneedles.css');

        foreach ($this->formController->getAssetPaths() as $type => $assets) {
            foreach ($assets as $asset){
                $this->{'add' . ucfirst($type)}($asset);
            }
        }
    }

    public function onSave()
    {
        $this->initFormController();

        $model = $this->formController->formGetModel();
        $form  = $this->formController->formGetWidget();
        $formData = $form->getSaveData();

        // Save the request
        $modelsToSave = $this->prepareModelsToSave(
            $model,
            $formData
        );
        DB::transaction(function () use ($modelsToSave) {
            foreach ($modelsToSave as $modelToSave) {
                $modelToSave->save(null, $this->formController->formGetSessionKey());
            }
        });

        // Notify the user that their request has been received
        return Redirect::to($this->controller->pageUrl($this->property('redirect')));
    }
}
