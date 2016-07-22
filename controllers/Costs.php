<?php namespace Octommerce\Shipping\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Octommerce\Shipping\Models\Cost;

/**
 * Costs Back-end Controller
 */
class Costs extends Controller
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

        BackendMenu::setContext('Octommerce.Shipping', 'shipping', 'costs');
    }

    public function update($recordId, $context = null)
    {
        $this->vars['cost_mode'] = $this->getCostMode($recordId);

        $this->addJs('/plugins/octommerce/shipping/assets/js/app.js');

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    /**
     * Get cost mode (Such as flat, dynamic and range)
     *
     * @param $recordId
     * @return $costMode string
     **/
    protected function getCostMode($recordId)
    {
        $costMode = '';
        $cost = Cost::find($recordId);

        if ($cost->is_per_kg) {
            $costMode = $cost->min == 0 && $cost->max == 0 ? 'flat' : 'dynamic';
        }
        else {
            $costMode = 'range'; 
        }

        return $costMode;
    }
}
