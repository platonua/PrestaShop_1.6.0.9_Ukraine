<?php

/**
 * 2014 PLATON PAYMENT MODULE
 *
 */
if (!defined('_PS_VERSION_'))
    exit;

class Platon extends PaymentModule {

    private $_postErrors = array();

    public function __construct() {
        $this->name = 'platon';
        $this->tab = 'payments_gateways';
        $this->version = '0.1.0';
        $this->author = 'PlatOn';
        $this->need_instance = 0;
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        parent::__construct();

        $this->displayName = $this->l('Platon');
        $this->description = $this->l('Payment gateway for Platon');
    }

    public function install() {
        if (
                !parent::install() ||
                !$this->registerHook('payment') ||
                !$this->registerHook('paymentReturn')
        ) {
            return false;
        }
        return true;
    }

    public function uninstall() {
        if (
                !parent::uninstall() ||
                !Configuration::deleteByName('PLATON_CLIENT_KEY') ||
                !Configuration::deleteByName('PLATON_CLIENT_PASSWORD')
        ) {
            return false;
        }
        return true;
    }

    public function getContent() {
        $html = '';

        if (Tools::isSubmit('submit_' . $this->name)) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_saveConfiguration();
                $html .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                foreach ($this->_postErrors as $err) {
                    $html .= $this->displayError($err);
                }
            }
        }
        return $html . $this->_displayConfigurationTpl();
    }

    private function _displayConfigurationTpl() {
        $this->context->smarty->assign(array(
            'PLATON_CLIENT_KEY' => Configuration::get('PLATON_CLIENT_KEY'),
            'PLATON_CLIENT_PASSWORD' => Configuration::get('PLATON_CLIENT_PASSWORD'),
            'css' => '../modules/'.$this->name.'/css/'
        ));
        return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');
    }

    private function _postValidation() {
        if (!Tools::getValue('PLATON_CLIENT_KEY') || !Validate::isCleanHtml(Tools::getValue('PLATON_CLIENT_KEY')) || !Validate::isGenericName(Tools::getValue('PLATON_CLIENT_KEY'))) {
            $this->_postErrors[] = $this->l('You must indicate the client key');
        }

        if (!Tools::getValue('PLATON_CLIENT_PASSWORD') || !Validate::isCleanHtml(Tools::getValue('PLATON_CLIENT_PASSWORD')) || !Validate::isGenericName(Tools::getValue('PLATON_CLIENT_PASSWORD'))) {
            $this->_postErrors[] = $this->l('You must indicate the client password');
        }
    }

    private function _saveConfiguration() {
        Configuration::updateValue('PLATON_CLIENT_KEY', (string) Tools::getValue('PLATON_CLIENT_KEY'));
        Configuration::updateValue('PLATON_CLIENT_PASSWORD', (string) Tools::getValue('PLATON_CLIENT_PASSWORD'));
    }

    public function hookPayment($params) {
        if (!$this->active)
            return;

        $this->context->smarty->assign(array(
            'module_dir' => _PS_MODULE_DIR_ . $this->name . '/'
        ));

        return $this->display(__FILE__, 'views/templates/hook/platon_payment.tpl');
    }

}
