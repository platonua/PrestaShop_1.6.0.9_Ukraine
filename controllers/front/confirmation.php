<?php

/**
 * 2014 PLATON PAYMENT MODULE
 *
 */
class PlatonConfirmationModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();

        $this->context = Context::getContext();
        $platon = new Platon();

        $this->context->smarty->assign(
                array(
                    'main_url' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__,
                )
        );

        $this->setTemplate('confirmation.tpl');
    }

}
