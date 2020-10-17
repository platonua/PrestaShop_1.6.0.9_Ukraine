<link href="{$css|escape:'htmlall':'UTF-8'}platon.css" rel="stylesheet" type="text/css">
<div class="configuration_platon">
    <h3>{l s='Settings from Platon Gateway' mod='platon'}</h3>
    <form method="post">
        <ul>
            <li><label for="PLATON_CLIENT_KEY" class="label_platon">{l s='Platon Client key' mod='platon'}:</label></li>
            <li><input id="PLATON_CLIENT_KEY" class="full input_platon" name="PLATON_CLIENT_KEY" type="text" value="{$PLATON_CLIENT_KEY}" /></li>
            <li><span class="caption">{l s='Enter your client key from Platon' mod='platon'}</span></li>
        </ul>
        <ul> 
            <li><label for="PLATON_CLIENT_PASSWORD" class="label_platon">{l s='Platon Client password' mod='platon'}:</label></li>
            <li><input id="PLATON_CLIENT_PASSWORD"  class="full input_platon" name="PLATON_CLIENT_PASSWORD" type="text" value="{$PLATON_CLIENT_PASSWORD}" /></li>
            <li><span class="caption">{l s='Enter your client password from Platon' mod='platon'}</span></li>
        </ul>
        <ul>
            <li><input id="submit_{$module_name}" name="submit_{$module_name}" type="submit" class="md-btn button-form_platon" value="{l s='Update' mod='platon'}" /></li>
        </ul>
    </form>
</div>