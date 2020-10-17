<?php
/**
 * 2014 PLATON PAYMENT MODULE
 *
 */
include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');
include(dirname(__FILE__) . '/platon.php');

$platon = new Platon();

$cart = Context::getContext()->cart;
$currency = new Currency((int) $cart->id_currency);
$gateway_url = 'https://secure.platononline.com/payment/auth';

if (!Validate::isLoadedObject($cart) || !Validate::isLoadedObject($currency)) {
    Logger::addLog('Issue loading cart and/or currency data');
    die('An unrecoverable error occured while retrieving you data');
}
?>

<center>
    <img src="<?php echo $url; ?>img/platon.png" height="31" width="110"/>
    </br>
    <?php echo $platon->l('You will redirect to Platon Gateway'); ?>
</center>

<?php
$data['key'] = Configuration::get('PLATON_CLIENT_KEY'); // Client's KEY
$data['url'] = $response_url = 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'index.php?fc=module&module=platon&controller=confirmation';

/* Prepare product data for coding */
$amount = number_format($cart->getordertotal(true),2, '.','');
$data['data'] = base64_encode(
        json_encode(
                array('amount' => $amount,
                    'name' => $platon->l('Your order in ') . Configuration::get('PS_SHOP_NAME'),
                    'currency' => $currency->iso_code
                )
        )
);

$data['order'] = (int) $cart->id;

$hash_str = strtoupper(strrev($data['key']) .
        strrev($data['data']) .
        strrev($data['url']) .
        strrev(Configuration::get('PLATON_CLIENT_PASSWORD'))
);
/* Calculation of signature */
$sign = md5($hash_str);
?>

<form action="<?php echo $gateway_url ?>" method="post" id="platon_form" name="platon_form" >
    <input type="hidden" name="order" value="<?php echo $data['order'] ?>" />
    <input type="hidden" name="key" value="<?php echo $data['key'] ?>" />
    <input type="hidden" name="url" value="<?php echo $data['url'] ?>" />
    <input type="hidden" name="data" value="<?php echo $data['data'] ?>" />
    <input type="hidden" name="sign" value="<?php echo $sign ?>" />
</form>

<script type="text/javascript">
    window.onload = function () {
        document.platon_form.submit();
    };
</script>
