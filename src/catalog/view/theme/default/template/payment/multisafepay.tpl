<?php if($test_mode) { ?>
    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $text_testmode; ?></div>
<?php } ?>
<form action="<?php echo $action; ?>" method="post" class="form-horizontal" id="multisafepay-form">
    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
    <input type="hidden" name="type" value="<?php echo $type; ?>" />
    <?php if($gateway) { ?>
        <input type="hidden" name="gateway" value="<?php echo $gateway; ?>" />
    <?php } ?>
    <?php if($gateway_info) { ?>
        <input type="hidden" name="gateway_info" value="<?php echo $gateway_info; ?>" />
    <?php } ?>
    <?php if(!empty($fields['payment_component_enabled'])) { ?>
    <input type="hidden" name="payload" value="" />
    <?php } ?>
    <?php if($issuers) { ?>
        <fieldset>
            <legend><?php echo $text_legend; ?></legend>
            <div class="form-group form-group-issuer-id">
                <label class="col-sm-2 control-label" for="input-issuer-id"><?php echo $entry_issuer; ?> </label>
                <div class="col-sm-10">
                    <select name="issuer_id" id="input-issuer-id" class="form-control gateways-with-issuers">
                        <option value=""><?php echo $text_select; ?></option>
                        <?php foreach ($issuers as $issuer) { ?>
                            <option value="<?php echo $issuer['code']; ?>"><?php echo $issuer['description']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </fieldset>
    <?php } ?>
    <?php if(!empty($fields)) { ?>
        <fieldset>
            <legend><?php echo $text_legend; ?></legend>
            <?php if(!empty($fields['payment_component_enabled'])) { ?>
            <div id="multisafepay-payment"></div>
            <?php } ?>
            <?php if(isset($fields['gender'])) { ?>
                <div class="form-group required form-group-gender">
                    <label class="col-sm-2 control-label" for="input-gender"><?php echo $entry_gender; ?> </label>
                    <div class="col-sm-10">
                        <select name="gender" id="input-gender" class="form-control">
                            <option value=""><?php echo $text_select; ?></option>
                            <option value="male"><?php echo $text_mr; ?></option>
                            <option value="female"><?php echo $text_mrs; ?></option>
                            <option value="female"><?php echo $text_miss; ?></option>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($fields['sex'])) { ?>
            <div class="form-group required form-group-gender">
                <label class="col-sm-2 control-label" for="input-gender"><?php echo $entry_gender; ?> </label>
                <div class="col-sm-10">
                    <select name="gender" id="input-gender" class="form-control">
                        <option value=""><?php echo $text_select; ?></option>
                        <option value="male"><?php echo $text_mr; ?></option>
                        <option value="female"><?php echo $text_mrs; ?></option>
                    </select>
                </div>
            </div>
            <?php } ?>
            <?php if(isset($fields['birthday'])) { ?>
                <div class="form-group required form-group-birthday">
                    <label class="col-sm-2 control-label" for="input-birthday"><?php echo $entry_date_of_birth; ?> </label>
                    <div class="col-sm-10">
                        <div class="input-group date">
                            <input type="text" name="birthday" value="" placeholder="<?php echo $placeholder_date_of_birth; ?>" id="input-birthday" data-date-format="YYYY-MM-DD" class="form-control" />
                            <span class="input-group-btn">
                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                    </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($fields['bankaccount'])) { ?>
                <div class="form-group required form-group-bankaccount">
                    <label class="col-sm-2 control-label" for="input-bankaccount"><?php echo $entry_bank_account; ?> </label>
                    <div class="col-sm-10">
                        <input type="text" name="bankaccount" value="" placeholder="<?php echo $placeholder_bank_account; ?>" id="input-bankaccount" class="form-control" />
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($fields['account_holder_name'])) { ?>
                <div class="form-group required form-group-account-holder-name">
                    <label class="col-sm-2 control-label" for="input-account-holder-name"><?php echo $entry_account_holder_name; ?> </label>
                    <div class="col-sm-10">
                        <input type="text" name="account_holder_name" value="" placeholder="<?php echo $entry_account_holder_name; ?>" id="input-account-holder-name" class="form-control" />
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($fields['account_holder_iban'])) { ?>
                <div class="form-group required form-group-account-holder-iban">
                    <label class="col-sm-2 control-label" for="input-account-holder-iban"><?php echo $entry_account_holder_iban; ?> </label>
                    <div class="col-sm-10">
                        <input type="text" name="account_holder_iban" value="" placeholder="<?php echo $placeholder_account_holder_iban; ?>" id="input-account-holder-iban" class="form-control" />
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($fields['afterpay_terms'])) { ?>
                <div class="form-group required form-group-afterpay-terms">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="afterpay_terms" value="1"> <?php echo $entry_afterpay_terms; ?>
                            </label>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(isset($fields['emandate'])) { ?>
                <input type="hidden" name="emandate" value="<?php echo $order_id; ?>" />
            <?php } ?>
        </fieldset>
    <?php } ?>
    <div class="buttons">
            <div class="pull-right">
                <input id="button-confirm" type="submit" value="<?php echo $button_confirm; ?>" class="btn btn-primary" />
            </div>
    </div>
</form>
<?php if($gateway === 'APPLEPAY') { ?>
    <script type="text/javascript"><!--
        $( document ).ready(function() {
            if (window.ApplePaySession && ApplePaySession.canMakePayments()) {
                $('#button-confirm').prop('disabled', false);
            } else {
                $('#multisafepay-form').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $text_error_apple_pay; ?></div>');
                $('#button-confirm').prop('disabled', true);
            }
        });
        //--></script>
<?php } ?>
<?php if(isset($fields['birthday']) && isset($datepicker)) { ?>
    <script type="text/javascript"><!--
        $('.date').datetimepicker({
            language: '<?php echo $datepicker; ?>',
            pickTime: false
        });
        //--></script>
<?php } else { ?>
    <script type="text/javascript"><!--
        $('.date').datetimepicker({
            language: 'en-gb',
            pickTime: false
        });
        //--></script>
<?php } ?>
<?php if($fields) { ?>
    <script type="text/javascript"><!--
        $( document ).ready(function() {
            $('#multisafepay-form').on('click', '#button-confirm', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $.ajax({
                    'url': 'index.php?route=<?php echo $route; ?>/validateForm',
                    type: 'post',
                    data: $('#multisafepay-form :input, #multisafepay-form select'),
                    dataType: 'json',
                    cache: false,
                    beforeSend: function() {
                        $('#multisafepay-form .alert-danger').remove();
                        $('#multisafepay-form .text-danger').remove();
                        $('#multisafepay-form .form-group').removeClass('has-error');
                        $('#button-confirm').button('loading');
                    },
                    complete: function() {
                        $('#button-confirm').button('reset');
                    },
                    success: function(json) {
                        if(!json['error']) {
                            $("#multisafepay-form").submit();
                        }
                        if(json['error']) {
                            $('#multisafepay-form').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $text_error_on_submit; ?></div>');
                            $.each( json['error'], function( index, value ){
                                $('.form-group-'+ index + ' .col-sm-10').append('<div class="text-danger">' + value + '</div>');
                                $('.form-group-'+ index).addClass('has-error');
                            });
                        }
                    }
                });
            });
        });
        //--></script>
    <?php if(!empty($fields['payment_component_enabled'])) { ?>
    <script type="text/javascript"><!--
        function createMultiSafepayPaymentComponents() {
            var config = {
            <?php if(isset($env)) { ?>
                env: '<?php echo $env; ?>',
            <?php } ?>
                apiToken: '<?php echo $apiToken; ?>',
                orderData: <?php echo $order_data; ?>
            };
            var multisafepay_payment_component = new MultiSafepayPaymentComponent(config, '<?php echo $gateway; ?>');
            $('#multisafepay-form').submit(function(event) {
                multisafepay_payment_component.onSubmitCheckoutForm(event);
            });
        }

        $(document).ready(function () {
            createMultiSafepayPaymentComponents();
        });
        //--></script>
    <?php } ?>
<?php } ?>
<?php if($issuers) { ?>
    <script type="text/javascript"><!--
        $(document).ready(function () {
            $('.gateways-with-issuers').select2({
                width: '100%'
            });
        });
        //--></script>
<?php } ?>
