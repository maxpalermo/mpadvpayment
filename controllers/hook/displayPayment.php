<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MpAdvPaymentDisplayPaymentController
 *
 * @author imprendo
 */
class MpAdvPaymentDisplayPaymentController {
    public $context;
    public $module;
    public $file;
    public $_path;
    
    /**
     * 
     * @param ModuleCore $module
     * @param string $file
     * @param string $path
     */
    public function __construct($module, $file, $path) {
        $this->file = $file;
        $this->module = $module;
        $this->_path = $path;
        $this->context = Context::getContext();
    }
    
    public function run($params)
    {
        $this->context->controller->addCSS($this->_path.'views/css/displayPayment.css', 'all');
        return $this->module->display($this->file, 'displayPayment.tpl');
    }
}
