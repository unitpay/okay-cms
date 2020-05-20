<form name="unitpay" action="https://unitpay.ru/pay/{$public_key}" method="get">
    <input type="hidden" name="sum"      value="{$sum}">
    <input type="hidden" name="account"  value="{$account}">
    <input type="hidden" name="desc"     value="{$desc}">
    <input type="hidden" name="currency" value="{$currency_code}">
    <input type="hidden" name="backUrl"  value="{$back_url}">
    <input type="hidden" name="signature"  value="{$signature}">

    <input type="submit" class="button" value="{$lang->form_to_pay}">
</form>