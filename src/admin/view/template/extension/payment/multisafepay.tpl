<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="multisafepay-admin-page">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <?php if($stores) { ?>
                    <div class="input-group" id="store-switcher">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-home"></i> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <?php foreach ($stores as $store) { ?>
                                    <li><a href="<?php echo $store['href']; ?>"><?php echo $store['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <input type="text" class="form-control disabled" aria-label="..." value="<?php echo $stores[$store_id]['name']; ?>" disabled>
                    </div>
                <?php } ?>
                <button type="submit" form="form-multisafepay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if($error_php_version) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_php_version; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if($maintenance) { ?>
            <div class="alert alert-danger" id="maintenance-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $text_maintenance_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if($needs_upgrade) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $text_needs_upgrade_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?> </h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-multisafepay" class="form-horizontal">
                    <input type="hidden" name="multisafepay_account_type" value="connect" />
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
                        <li><a href="#tab-account" data-toggle="tab"><?php echo $tab_account; ?></a></li>
                        <li><a href="#tab-payment-methods" data-toggle="tab"><?php echo $tab_payment_methods; ?></a></li>
                        <li><a href="#tab-status" data-toggle="tab"><?php echo $tab_order_status; ?></a></li>
                        <li><a href="#tab-options" data-toggle="tab"><?php echo $tab_options; ?></a></li>
                        <?php if($maintenance) { ?>
                            <li><a href="#tab-maintenance" data-toggle="tab"><?php echo $tab_maintenance; ?></a></li>
                        <?php } ?>
                        <li><a href="#tab-support" data-toggle="tab"><?php echo $tab_support; ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <?php // Tab General ?>
                        <div class="tab-pane active" id="tab-general">
                            <?php // Module status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-status"><?php echo $entry_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_status" id="payment-multisafepay-status" class="form-control">
                                        <option value="1" <?php if($multisafepay_status == 1) { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0" <?php if($multisafepay_status == 0) { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // Sort Order ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-sort-order"><?php echo $entry_sort_order; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="multisafepay_sort_order" value="<?php echo $multisafepay_sort_order; ?>" id="payment-multisafepay-sort-order" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" />
                                </div>
                            </div>
                            <?php // MultiSafepay debug mode ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-debug-mode"><span data-toggle="tooltip" title="<?php echo $text_help_debug; ?>"><?php echo $entry_debug_mode; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_debug_mode" id="payment-multisafepay-debug-mode" class="form-control">
                                        <option value="0" <?php if($multisafepay_debug_mode == 0) { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                        <option value="1" <?php if($multisafepay_debug_mode == 1) { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php // Tab Account ?>
                        <div class="tab-pane" id="tab-account">
                            <?php // MultiSafepay environment ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-environment"><?php echo $entry_environment; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_environment" id="payment-multisafepay-environment" class="form-control">
                                        <option value="1" <?php if($multisafepay_environment == 1) { ?> selected <?php } ?>><?php echo $text_test; ?></option>
                                        <option value="0" <?php if($multisafepay_environment == 0) { ?> selected <?php } ?>><?php echo $text_live; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // Sandbox api key ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-sandbox-api-key"><?php echo $entry_multisafepay_sandbox_api_key; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="multisafepay_sandbox_api_key" value="<?php echo $multisafepay_sandbox_api_key; ?>" id="payment-multisafepay-sandbox-api-key" placeholder="<?php echo $entry_multisafepay_sandbox_api_key; ?>" class="form-control" />
                                    <?php if($error_sandbox_api_key) { ?>
                                        <div class="text-danger"><?php echo $error_sandbox_api_key; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php // Api key ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-api-key"><?php echo $entry_multisafepay_api_key; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="multisafepay_api_key" value="<?php echo $multisafepay_api_key; ?>" id="payment-multisafepay-api-key" placeholder="<?php echo $entry_multisafepay_api_key; ?>" class="form-control" />
                                    <?php if($error_api_key) { ?>
                                        <div class="text-danger"><?php echo $error_api_key; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-options">
	                        <?php // Order Description ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-description"><span data-toggle="tooltip" title="<?php echo $text_help_order_description; ?>"><?php echo $entry_multisafepay_order_description; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="multisafepay_order_description" value="<?php echo $multisafepay_order_description; ?>" id="payment-multisafepay-order-description" placeholder="<?php echo $entry_multisafepay_order_description; ?>" class="form-control" />
                                </div>
                            </div>
                            <?php // Days active ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-unit-lifetime-payment-link"><span data-toggle="tooltip" title="<?php echo $text_help_lifetime_payment_link; ?>"><?php echo $entry_multisafepay_lifetime_payment_link; ?></span></label>
                                <div class="col-sm-5">
                                    <input type="text" name="multisafepay_days_active" value="<?php echo $multisafepay_days_active; ?>" id="payment-multisafepay-days-active" placeholder="<?php echo $entry_multisafepay_time_active; ?>" class="form-control" />
                                    <?php if($error_days_active) { ?>
                                        <div class="text-danger"><?php echo $error_days_active; ?></div>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-5">
                                    <select name="multisafepay_unit_lifetime_payment_link" id="payment-multisafepay-unit-lifetime-payment-link" class="form-control">
                                        <option value="days" <?php if($multisafepay_unit_lifetime_payment_link == 'days') { ?> selected <?php } ?>><?php echo $text_days; ?></option>
                                        <option value="hours" <?php if($multisafepay_unit_lifetime_payment_link == 'hours') { ?> selected <?php } ?>><?php echo $text_hours; ?></option>
                                        <option value="seconds" <?php if($multisafepay_unit_lifetime_payment_link == 'seconds') { ?> selected <?php } ?>><?php echo $text_seconds; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // Use Second Chance ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-second-chance"><span data-toggle="tooltip" title="<?php echo $text_help_second_chance; ?>"><?php echo $entry_multisafepay_second_chance; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_second_chance" id="payment-multisafepay-second-chance" class="form-control">
                                        <option value="1" <?php if($multisafepay_second_chance == '1') { ?> selected <?php } ?>><?php echo $text_yes; ?></option>
                                        <option value="0" <?php if($multisafepay_second_chance == '0') { ?> selected <?php } ?>><?php echo $text_no; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // Use logo's during checkout ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-use-payment-logo"><?php echo $entry_multisafepay_use_payment_logo; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_use_payment_logo" id="payment-multisafepay-use-payment-logo" class="form-control">
                                        <option value="0" <?php if($multisafepay_use_payment_logo == '0') { ?> selected <?php } ?>><?php echo $text_no; ?></option>
                                        <option value="1" <?php if($multisafepay_use_payment_logo == '1') { ?> selected <?php } ?>><?php echo $text_yes; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php //  Disable Shopping Cart ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-shopping-cart-disabled"><?php echo $entry_multisafepay_shopping_cart_disabled; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_shopping_cart_disabled" id="payment-multisafepay-shopping-cart-disabled" class="form-control">
                                        <option value="0" <?php if($multisafepay_shopping_cart_disabled == '0') { ?> selected <?php } ?>><?php echo $text_no; ?></option>
                                        <option value="1" <?php if($multisafepay_shopping_cart_disabled == '1') { ?> selected <?php } ?>><?php echo $text_yes; ?></option>
                                    </select>
                                    <p class="help-block"><?php echo $text_help_shopping_cart_disabled; ?></p>
                                </div>
                            </div>
                            <?php //  Generate Payment Links from admin ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-generate-payment-links-status"><?php echo $entry_multisafepay_generate_payment_links_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_generate_payment_links_status" id="payment-multisafepay-generate-payment-links-status" class="form-control">
                                        <option value="0" <?php if($multisafepay_generate_payment_links_status == '0') { ?> selected <?php } ?>><?php echo $text_no; ?></option>
                                        <option value="1" <?php if($multisafepay_generate_payment_links_status == '1') { ?> selected <?php } ?>><?php echo $text_yes; ?></option>
                                    </select>
                                    <p class="help-block"><?php echo $text_help_generate_payment_links_status; ?></p>
                                </div>
                            </div>
                            <?php // set order status related with generate payment links ?>
                            <div class="form-group" id="initialize-payment-request-form-group" <?php if ($multisafepay_generate_payment_links_status != '1') { ?> style="display:none;" <?php } else { ?> style="display:block;" <?php } ?>>
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-initialize-payment-request"><span data-toggle="tooltip" title="" data-original-title="<?php echo $text_help_initialize_payment_request; ?>"><?php echo $entry_order_status_id_initialize_payment_request; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_initialize_payment_request" id="payment-multisafepay-order-status-id-initialize-payment-request" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <option value="<?php echo $order_status['order_status_id']; ?>"<?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_initialize_payment_request) { ?> selected <?php } ?>><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php // Set custom order totals keys ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-custom-order-totals-keys"><span data-toggle="tooltip" title="<?php echo $text_help_custom_order_total_keys; ?>"><?php echo $entry_custom_order_total_keys; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="multisafepay_custom_order_total_keys" value="<?php echo $multisafepay_custom_order_total_keys; ?>" id="payment-multisafepay-custom-order-totals-keys" placeholder="<?php echo $entry_custom_order_total_keys; ?>" class="form-control" />
                                </div>
                            </div>
                            <?php // Set Payment Component Template ID ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-payment-component-template-id"><?php echo $entry_payment_component_template_id ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="multisafepay_payment_component_template_id" value="<?php echo $multisafepay_payment_component_template_id ?>" id="payment-multisafepay-payment-component-template-id" placeholder="<?php echo $entry_payment_component_template_id ?>" class="form-control" />
                                    <p class="help-block"><?php echo $text_help_payment_component_template_id ?></p>
                                </div>
                            </div>
                        </div>
                        <?php // Tab Order Status ?>
                        <div class="tab-pane" id="tab-status">
                            <?php // set initialized status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-initialized"><span data-toggle="tooltip" title="" data-original-title="<?php echo $text_help_order_status_id_initialized; ?>"><?php echo $entry_order_status_id_initialized; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_initialized" id="payment-multisafepay-order-status-id-initialized" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_initialized) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_initialized) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_initialized) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set completed status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-completed"><?php echo $entry_order_status_id_completed; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_completed" id="payment-multisafepay-order-status-id-completed" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_completed) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_completed) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_completed) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set uncleared status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-uncleared"><?php echo $entry_order_status_id_uncleared; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_uncleared" id="payment-multisafepay-order-status-id-uncleared" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_uncleared) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_uncleared) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_uncleared) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set reserved status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-reserved"><?php echo $entry_order_status_id_reserved; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_reserved" id="payment-multisafepay-order-status-id-reserved" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_reserved) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_reserved) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_reserved) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set void status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-void"><?php echo $entry_order_status_id_void; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_void" id="payment-multisafepay-order-status-id-void" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_void) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_void) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_void) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set declined status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-declined"><?php echo $entry_order_status_id_declined; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_declined" id="payment-multisafepay-order-status-id-declined" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_declined) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_declined) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_declined) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set expired status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-expired"><?php echo $entry_order_status_id_expired; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_expired" id="payment-multisafepay-order-status-id-expired" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_expired) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_expired) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_expired) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set shipped status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-shipped"><?php echo $entry_order_status_id_shipped; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_shipped" id="payment-multisafepay-order-status-id-shipped" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_shipped) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_shipped) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_shipped) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set refunded status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-refunded"><?php echo $entry_order_status_id_refunded; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_refunded" id="payment-multisafepay-order-status-id-refunded" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_refunded) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_refunded) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_refunded) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set partial_refunded status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-partial-refunded"><?php echo $entry_order_status_id_partial_refunded; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_partial_refunded" id="payment-multisafepay-order-status-id-partial-refunded" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_partial_refunded) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_partial_refunded) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_partial_refunded) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php // set cancelled status ?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="payment-multisafepay-order-status-id-cancelled"><?php echo $entry_order_status_id_cancelled; ?></label>
                                <div class="col-sm-10">
                                    <select name="multisafepay_order_status_id_cancelled" id="payment-multisafepay-order-status-id-cancelled" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_cancelled) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                            <?php if ($order_status['order_status_id'] != $multisafepay_order_status_id_cancelled) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        <option value="0"<?php if ('0' == $multisafepay_order_status_id_cancelled) { ?> selected <?php } ?>><?php echo $text_do_nothing; ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php // Tab Payment Methods ?>
                        <div class="tab-pane" id="tab-payment-methods">
                            <div class="well payment-methods-filter-panel">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <select name="filter_payment_method" id="input-filter-payment-method" class="form-control">
                                                <option value="*"><?php echo $text_show_all_payment_methods; ?></option>
                                                <option value="gateway"><?php echo $text_show_gateways; ?></option>
                                                <option value="giftcard"><?php echo $text_show_gift_cards; ?></option>
                                                <option value="generic"><?php echo $text_show_generic; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="dragula-container" class="fields-rows">
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <?php foreach ($gateways as $gateway) { ?>
                                        <div class="panel panel-default payment-method-panel payment-type-<?php echo $gateway['type']; ?>">
                                            <div class="panel-heading" role="tab" id="heading-payment-method-<?php echo $gateway['code']; ?>">
                                                <h4 class="panel-title">
                                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#payment-method-<?php echo $gateway['code']; ?>" aria-expanded="true" aria-controls="payment-method-<?php echo $gateway['code']; ?>" class="collapsed <?php if(isset($error_gateway[$gateway['code']])) { ?> has-warning <?php } ?>">
                                                        <span class="status<?php if($payment_methods_fields_values[$gateway['code']]['status'] == 1) { ?> active<?php } ?>">

                                                        </span>
                                                        <span class="drag-and-drop-control" data-toggle="tooltip" title="" data-original-title="<?php echo $text_help_drag_and_drop; ?>">

                                                        </span>
                                                        <span class="title">
                                                            <?php echo $gateway['description']; ?>
                                                        </span>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div class="panel-collapse collapse" role="tabpanel" id="payment-method-<?php echo $gateway['code']; ?>"  aria-labelledby="heading-payment-method-<?php echo $gateway['code']; ?>">
                                                <div class="panel-body">
                                                    <?php if($gateway['brief_description']) { ?>
                                                        <p>
                                                            <?php if($gateway['image']) { ?>
                                                                <img src="../image/catalog/multisafepay/<?php echo $gateway['image']; ?>.png" class="payment-logo" alt="<?php echo $gateway['description']; ?>" title="<?php echo $gateway['description']; ?>" />
                                                            <?php } ?>
                                                            <?php echo $gateway['brief_description']; ?>
                                                        </p>
                                                        <hr>
                                                    <?php } ?>
                                                    <?php if(isset($error_gateway[$gateway['code']])) { ?>
                                                        <div class="alert alert-danger"><?php echo $error_gateway[$gateway['code']]; ?></div>
                                                    <?php } ?>
                                                    <?php // Status ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-status"><?php echo $entry_status; ?></label>
                                                        <div class="col-sm-10">
                                                            <select name="multisafepay_<?php echo $gateway['code']; ?>_status" id="payment-multisafepay-<?php echo $gateway['code']; ?>-status" class="form-control">
                                                                <option value="0" <?php if($payment_methods_fields_values[$gateway['code']]['status'] == 0) { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                                                <option value="1" <?php if($payment_methods_fields_values[$gateway['code']]['status'] == 1) { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php // Payment Component ?>
                                                    <?php if(in_array($gateway['id'], $configurable_payment_component, true)) { ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-payment-component"><?php echo $entry_payment_component; ?></label>
                                                        <div class="col-sm-10">
                                                            <select name="multisafepay_<?php echo $gateway['code']; ?>_payment_component" id="payment-multisafepay-<?php echo $gateway['code']; ?>-payment-component" class="form-control">
                                                                <option value="0" <?php if($payment_methods_fields_values[$gateway['code']]['payment_component'] == 0) { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                                                <option value="1" <?php if($payment_methods_fields_values[$gateway['code']]['payment_component'] == 1) { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <?php // Tokenization ?>
                                                    <?php if(in_array($gateway['id'], $configurable_tokenization, true)) { ?>
                                                    <div id="payment-multisafepay-<?php echo $gateway['code']; ?>-tokenization-field" data-gateway="<?php echo $gateway['code']; ?>" class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-tokenization"><?php echo $entry_tokenization; ?></label>
                                                        <div class="col-sm-10">
                                                            <select name="multisafepay_<?php echo $gateway['code']; ?>_tokenization" id="payment-multisafepay-<?php echo $gateway['code']; ?>-tokenization" class="form-control">
                                                                <option value="0" <?php if($payment_methods_fields_values[$gateway['code']]['tokenization'] == 0) { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                                                <option value="1" <?php if($payment_methods_fields_values[$gateway['code']]['tokenization'] == 1) { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
	                                                <?php // Generic ?>
                                                    <?php if ($gateway['type'] === 'generic') { ?>
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-name"><?php echo $entry_name; ?></label>
                                                            <div class="col-sm-10">
                                                                <input type="text" name="multisafepay_<?php echo $gateway['code']; ?>_name" value="<?php echo $payment_generic_fields_values[$gateway['code']]['name'] ?>" id="payment-multisafepay-<?php echo $gateway['code']; ?>-name" placeholder="<?php echo $entry_name ?>" class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-code"><?php echo $entry_code ?></label>
                                                            <div class="col-sm-10">
                                                                <input type="text" name="multisafepay_<?php echo $gateway['code']; ?>_code" value="<?php echo $payment_generic_fields_values[$gateway['code']]['code'] ?>" id="payment-multisafepay-<?php echo $gateway['code']; ?>-code" placeholder="<?php echo $entry_code ?>" class="form-control" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-image"><span data-toggle="tooltip" title="" data-original-title="<?php echo $text_help_image_generic; ?>"><?php echo $entry_image ?></span></label>
                                                            <div class="col-sm-10">
                                                                <a href="" id="thumb-payment-multisafepay-<?php echo $gateway['code']; ?>-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $payment_generic_fields_values[$gateway['code']]['thumb'] ?>" alt="" title="" data-placeholder=""/></a>
                                                                <input type="hidden" name="multisafepay_<?php echo $gateway['code']; ?>_image" value="<?php echo $payment_generic_fields_values[$gateway['code']]['image'] ?>" id="input-image-payment-multisafepay-<?php echo $gateway['code']; ?>-image"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-require-shopping-cart"><span data-toggle="tooltip" title="" data-original-title="<?php echo $text_help_require_shopping_cart; ?>"><?php echo $entry_require_shopping_cart; ?></span></label>
                                                            <div class="col-sm-10">
                                                                <select name="multisafepay_<?php echo $gateway['code']; ?>_require_shopping_cart" id="payment-multisafepay-<?php echo $gateway['code']; ?>-require-shopping-cart" class="form-control">
                                                                    <option value="0" <?php if($payment_generic_fields_values[$gateway['code']]['require_shopping_cart'] == 0) { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                                                    <option value="1" <?php if($payment_generic_fields_values[$gateway['code']]['require_shopping_cart'] == 1) { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php // Min amount ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-min-amount"><span data-toggle="tooltip" title="" data-original-title="<?php echo $text_help_min_amount; ?>"><?php echo $entry_min_amount; ?></span></label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="multisafepay_<?php echo $gateway['code']; ?>_min_amount" value="<?php echo $payment_methods_fields_values[$gateway['code']]['min_amount']; ?>" id="payment-multisafepay-<?php echo $gateway['code']; ?>-min-amount" placeholder="<?php echo $entry_min_amount; ?>" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <?php // Max amount ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-max-amount"><span data-toggle="tooltip" title="" data-original-title="<?php echo $text_help_max_amount; ?>"><?php echo $entry_max_amount; ?></span></label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="multisafepay_<?php echo $gateway['code']; ?>_max_amount" value="<?php echo $payment_methods_fields_values[$gateway['code']]['max_amount']; ?>" id="payment-multisafepay-<?php echo $gateway['code']; ?>-max-amount" placeholder="<?php echo $entry_max_amount; ?>" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <?php // Currency ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $text_help_currency; ?>"><?php echo $entry_currency; ?></span></label>
                                                        <div class="col-sm-10">
                                                            <div class="well well-sm" style="height: 100px; overflow: auto;">
                                                                <?php foreach ($currencies as $currency) { ?>
                                                                    <div class="checkbox">
                                                                        <label>
                                                                            <?php if(isset($payment_methods_fields_values[$gateway['code']]['currency']) && in_array($currency['currency_id'], $payment_methods_fields_values[$gateway['code']]['currency'])) { ?>
                                                                                <input type="checkbox" name="multisafepay_<?php echo $gateway['code']; ?>_currency[]" value="<?php echo $currency['currency_id']; ?>" checked="checked" />
                                                                                <?php echo $currency['title']; ?> (<?php echo $currency['code']; ?>)
                                                                            <?php } else { ?>
                                                                                <input type="checkbox" name="multisafepay_<?php echo $gateway['code']; ?>_currency[]" value="<?php echo $currency['currency_id']; ?>" />
                                                                                <?php echo $currency['title']; ?> (<?php echo $currency['code']; ?>)
                                                                            <?php } ?>
                                                                        </label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php // Customer Groups ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $text_help_customer_group; ?>"><?php echo $entry_customer_group; ?></span></label>
                                                        <div class="col-sm-10">
                                                            <div class="well well-sm" style="height: 100px; overflow: auto;">
                                                                <?php foreach ($customer_groups as $customer_group) { ?>
                                                                    <div class="checkbox">
                                                                        <label>
                                                                            <?php if(isset($payment_methods_fields_values[$gateway['code']]['customer_group_id']) && in_array($customer_group['customer_group_id'], $payment_methods_fields_values[$gateway['code']]['customer_group_id'])) { ?>
                                                                                <input type="checkbox" name="multisafepay_<?php echo $gateway['code']; ?>_customer_group_id[]" value="<?php echo $customer_group['customer_group_id']; ?>" checked="checked" />
                                                                                <?php echo $customer_group['name']; ?>
                                                                            <?php } else { ?>
                                                                                <input type="checkbox" name="multisafepay_<?php echo $gateway['code']; ?>_customer_group_id[]" value="<?php echo $customer_group['customer_group_id']; ?>" />
                                                                                <?php echo $customer_group['name']; ?>
                                                                            <?php } ?>
                                                                        </label>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php // Geo-Zone ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-geo-zone-id"><?php echo $entry_geo_zone; ?></label>
                                                        <div class="col-sm-10">
                                                            <select name="multisafepay_<?php echo $gateway['code']; ?>_geo_zone_id" id="payment-multisafepay-<?php echo $gateway['code']; ?>-geo-zone-id" class="form-control">
                                                                <option value="0"><?php echo $text_all_zones; ?></option>
                                                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                                                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"<?php if($geo_zone['geo_zone_id'] == $payment_methods_fields_values[$gateway['code']]['geo_zone_id']) { ?> selected <?php } ?>><?php echo $geo_zone['name']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php // Custom initialized status ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-order-status-id-initialized"><?php echo $entry_order_status_id_initialized; ?></label>
                                                        <div class="col-sm-10">
                                                            <select name="multisafepay_<?php echo $gateway['code']; ?>_order_status_id_initialized" id="payment-multisafepay-<?php echo $gateway['code']; ?>-order-status-id-initialized" class="form-control">
                                                                <option value="0" <?php if('0' == $payment_methods_fields_values[$gateway['code']]['order_status_id_initialized']) { ?> selected <?php } ?>><?php echo $text_order_status_id_initialized_default; ?></option>
                                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                                    <option value="<?php echo $order_status['order_status_id']; ?>"<?php if($order_status['order_status_id'] == $payment_methods_fields_values[$gateway['code']]['order_status_id_initialized']) { ?> selected <?php } ?>><?php echo $order_status['name']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <?php // Switch to redirect -- ?>
                                                    <?php if ($gateway['redirect_switch']) { ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-redirect"><?php echo $entry_redirect_switch; ?></label>
                                                        <div class="col-sm-10">
                                                            <select name="multisafepay_<?php echo $gateway['code']; ?>_redirect" id="payment-multisafepay-<?php echo $gateway['code']; ?>-redirect" class="form-control">
                                                                <option value="0" <?php if($payment_methods_fields_values[$gateway['code']]['redirect'] == 0) { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                                                <option value="1" <?php if($payment_methods_fields_values[$gateway['code']]['redirect'] == 1) { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
	                                                <?php } ?>
                                                    <?php // Sort Order ?>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label" for="payment-multisafepay-<?php echo $gateway['code']; ?>-sort-order"><?php echo $entry_sort_order; ?></label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="multisafepay_<?php echo $gateway['code']; ?>_sort_order" value="<?php echo $payment_methods_fields_values[$gateway['code']]['sort_order']; ?>" id="payment-multisafepay-<?php echo $gateway['code']; ?>-sort-order" placeholder="<?php echo $entry_sort_order; ?>" class="form-control sort-order" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php // Maintenance Tab ?>
                        <?php if($maintenance) { ?>
                            <div class="tab-pane" id="tab-maintenance">
                                <div id="multisafepay-maintenance" class="multisafepay-maintenance-information">
                                    <p><?php echo $text_old_files; ?></p>
                                    <div class="table-responsive table-wrapper-scroll table-scroll">
                                        <table class="table table-bordered table-hover" id="multisafepay-maintenance-table">
                                            <thead>
                                            <tr>
                                                <td class="text-left">File</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($files as $file) { ?>
                                                <tr>
                                                    <td class="text-left"><?php echo $file; ?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="pull-right">
                                        <button id="remove-old-files" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-times"></i> <?php echo $button_remove; ?></button>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php // Support Tab ?>
                        <div class="tab-pane" id="tab-support">
                            <div id="multisafepay-support" class="multisafepay-support-information">
                                <table id="version-table">
                                    <tr class="no-padding-top">
                                        <td class="version-table-row">
                                            <?php echo $text_row_title_multisafepay_version; ?>
                                        </td>
                                        <td class="version-table-row multisafepay-bold">
                                            <?php echo $text_row_value_multisafepay_version; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="version-table-row">
                                            <?php echo $text_row_title_multisafepay_version_oc_supported; ?>
                                        </td>
                                        <td class="version-table-row multisafepay-bold">
                                            <?php echo $text_row_value_multisafepay_version_oc_supported; ?>
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <h2 id="multisafepay-title"><?php echo $text_title_documentation; ?></h2>
                                <p><?php echo $text_read_more_documentation; ?></p>
                                <ul>
                                    <li>
                                        <?php echo $text_manual_link; ?>
                                    </li>
                                    <li>
                                        <?php echo $text_changelog_link; ?>
                                    </li>
                                    <li>
                                        <?php echo $text_faq_link; ?>
                                    </li>
                                </ul>
                                <p><?php echo $text_read_more_documentation_developers; ?></p>
                                <ul>
                                    <li>
                                        <?php echo $text_api_documentation_link; ?>
                                    </li>
                                    <li>
                                        <?php echo $text_multisafepay_github_link; ?>
                                    </li>
                                </ul>
                                <h2 id="multisafepay-title"><?php echo $text_title_account; ?></h2>
                                <p style="font-weight: bold">
                                    <?php echo $text_need_account; ?>
                                </p>
                                <ul>
                                    <li><?php echo $text_create_test_account; ?></li>
                                    <li><?php echo $text_create_live_account; ?>
                                        <ul class="multisafepay-sales-contact-list">
                                            <li><strong><?php echo $text_country_netherlands; ?></strong>
                                                <ul>
                                                    <li>
                                                        <?php echo $text_sales_telephone_netherlands; ?>
                                                    </li>
                                                    <li>
                                                        <?php echo $text_sales_email_netherlands; ?>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><strong><?php echo $text_country_belgium; ?></strong>
                                                <ul>
                                                    <li>
                                                        <?php echo $text_sales_telephone_belgium; ?>
                                                    </li>
                                                    <li>
                                                        <?php echo $text_sales_email_belgium; ?>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><strong><?php echo $text_country_spain; ?></strong>
                                                <ul>
                                                    <li>
                                                        <?php echo $text_sales_telephone_spain; ?>
                                                    </li>
                                                    <li>
                                                        <?php echo $text_sales_email_spain; ?>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><strong><?php echo $text_country_italy; ?></strong>
                                                <ul>
                                                    <li>
                                                        <?php echo $text_sales_telephone_italy; ?>
                                                    </li>
                                                    <li>
                                                        <?php echo $text_sales_email_italy; ?>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                                <h2 id="multisafepay-title"><?php echo $text_title_contact; ?></h2>
                                <p>
                                    <?php echo $text_contact_assistance_text; ?>
                                </p>
                                <ul>
                                    <li>
                                        <?php echo $text_assistance_telephone; ?>
                                    </li>
                                    <li>
                                        <?php echo $text_assistance_email; ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
    removeOldFilesVersion('<?php echo $text_remove_old_files_confirm; ?>', '<?php echo $token_name; ?>', '<?php echo $token; ?>', '<?php echo $text_empty_old_files; ?>', '<?php echo $oc_version; ?>');
    //--></script>
<?php echo $footer; ?>
