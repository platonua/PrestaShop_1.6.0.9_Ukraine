<?php

/**
 * 2014 PLATON PAYMENT MODULE
 *
 */
class PlatonResponseModuleFrontController extends ModuleFrontController {

    public function initContent() {
        $this->addLog('Platon Callback called: ' . var_export($_POST, TRUE));

        if (!$_POST) {
            $this->addLog('Platon Callback ERROR: Empty POST');
            die("ERROR: Empty POST");
        }

        parent::initContent();

        $this->context = Context::getContext();
        $platon = new Platon();
        $order_id = $_REQUEST['order'];
        $cart = new Cart((int) $order_id);

        // log callback data
        $callbackParams = $_POST;

        // generate signature from callback params
        $sign = md5(strtoupper(
                        strrev($callbackParams['email']) .
                        Configuration::get('PLATON_CLIENT_PASSWORD') .
                        $order_id .
                        strrev(substr($callbackParams['card'], 0, 6) . substr($callbackParams['card'], -4))
        ));

        // verify signature
        if ($callbackParams['sign'] !== $sign) {
            $this->addLog('Platon Callback ERROR: Invalid signature');
            die("ERROR: Invalid signature");
        }

        if (!Validate::isLoadedObject($cart)) {
            $this->addLog('Platon Callback ERROR: Invalid Cart ID');
            die("ERROR: Invalid Cart ID");
        }
        $currency_cart = new Currency((int) $cart->id_currency);

        if ($cart->orderExists()) {
            $order = new Order((int) Order::getOrderByCartId($cart->id));
        } else {
            $customer = new Customer((int) $cart->id_customer);
            Context::getContext()->customer = $customer;
            Context::getContext()->currency = $currency_cart;
            $platon->validateOrder((int) $cart->id, (int) Configuration::get('PS_OS_PAYMENT'), (float) $cart->getordertotal(true), 'Platon', null, array(), (int) $currency_cart->id, false, $customer->secure_key);
            $order = new Order((int) Order::getOrderByCartId($cart->id));
        }

        switch ($callbackParams['status']) {
            case 'SALE':
                $state = Configuration::get('PS_OS_PAYMENT');
                break;
            case 'REFUND':
                $state = Configuration::get('PS_OS_REFUND');
                foreach ($order->getProductsDetail() as $product) {
                    StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], + (int) $product['product_quantity'], $order->id_shop);
                }
                break;
            case 'CHARGEBACK':
                break;
            default:
                $this->addLog('Platon Callback ERROR: Empty POST');
                die("ERROR: Invalid callback data");
        }

        $history = new OrderHistory();
        $history->id_order = (int) $order->id;
        $history->changeIdOrderState((int) Configuration::get($state), $order, true);
        $history->addWithemail(true);

        $this->addLog('Platon Callback: Order succesfully processed');

        // answer with success response
        exit("OK");
    }
    
    private function addLog($msg) {
        $log = _PS_ROOT_DIR_ . '/log/platon_callback.log';
        file_put_contents($log, date('d.m.Y H:i:s').' | ' . $msg . "\n\n", FILE_APPEND);
        
    }

}
