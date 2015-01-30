

<form action="<?php echo $action; ?>" method="post">
    <input type="hidden" name="cartId" value="<?php echo $MSP_CARTID; ?>" />
    <input type='hidden' name='gateway' value='<?php echo $gateway ?>' />
    <div id="multisafepayideal_payment" class="checkout-content">
        <img src="./image/msp/ideal.png" alt="iDEAL" title="iDEAL" style="vertical-align: middle;" />	
        <?php echo $ISSUER_SELECT; ?>
    </div>
    <div class="buttons">
        <div class="pull-right">
            <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
        </div>
    </div>
</form>

