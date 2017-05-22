<form action="<?php echo $action; ?>" method="post">
    <input type="hidden" name="cartId" value="<?php echo $MSP_CARTID; ?>" />
    <input type='hidden' name='gateway' value='<?php echo $gateway ?>' />
    <div class="buttons">
        <div class="pull-right">
            <input type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
        </div>
    </div>
</form>
