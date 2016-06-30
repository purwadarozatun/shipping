<?php namespace Octommerce\Shipping\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Packages Back-end Controller
 */
class Packages extends Controller
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

        BackendMenu::setContext('Octommerce.Shipping', 'shipping', 'packages');
    }
}