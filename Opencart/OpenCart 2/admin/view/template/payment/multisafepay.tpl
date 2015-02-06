<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
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
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-multisafepay" class="form-horizontal">

                    <!--Module status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_status" id="input-status" class="form-control">
                                <?php if ($multisafepay_status) { ?>
                                <option value="1" selected="selected">Enabled</option>
                                <option value="0">Disabled</option>
                                <?php } else { ?>
                                <option value="1">Enabled</option>
                                <option value="0" selected="selected">Disabled</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                    <!--MultiSafepay environment-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="environment"><?php echo $entry_environment; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_environment" id="environment" class="form-control">
                                <option value="0" <?php if($multisafepay_environment == 0) echo 'selected'; ?>>Live account</option>
                                <option value="1" <?php if($multisafepay_environment == 1) echo 'selected'; ?>>Test account</option>
                            </select>
                        </div>
                    </div>

                    <!--MultiSafepay account type-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="account"><?php echo $entry_environment; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_account_type" id="account" class="form-control">
                                <option value="connect" <?php if($multisafepay_account_type == 'connect') echo 'selected'; ?>>Connect</option>
                                <option value="fastcheckout" <?php if($multisafepay_account_type == 'fastcheckout') echo 'selected'; ?>>FastCheckout</option>
                            </select>
                        </div>
                    </div>

                    <!--MultiSafepay fco button-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="fcobutton"><?php echo $enable_checkout_button; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_enable_checkout_button_connect" id="fcobutton" class="form-control">
                                <option value="true" <?php if($multisafepay_enable_checkout_button_connect == 'true') echo 'selected'; ?>><?php echo $yes; ?></option>
                                <option value="false" <?php if($multisafepay_enable_checkout_button_connect == 'false') echo 'selected'; ?>><?php echo $no; ?></option>
                            </select>
                        </div>
                    </div>

                    <!--MultiSafepay enable redirect-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="redirect"><?php echo $entry_multisafepay_redirect_url; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_redirect_url" id="redirect" class="form-control">
                                <option value="true" <?php if($multisafepay_redirect_url == 'true') echo 'selected'; ?>>Enabled</option>
                                <option value="false" <?php if($multisafepay_redirect_url == 'false') echo 'selected'; ?>>Disabled</option>
                            </select>
                        </div>
                    </div>

                    <!--Account settings-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="accountid"><span><?php echo $entry_multisafepay_merchantid; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_merchant_id" value="<?php echo $multisafepay_merchant_id; ?>" id="accountid" class="form-control" />
                        </div>
                    </div>

                    <!--siteid settings-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="siteid"><span><?php echo $entry_multisafepay_siteid; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_site_id" value="<?php echo $multisafepay_site_id; ?>" id="siteid" class="form-control" />
                        </div>
                    </div>

                    <!--site secure code-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="sitesecurecode"><span><?php echo $entry_multisafepay_secure_code; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_secure_code" value="<?php echo $multisafepay_secure_code; ?>" id="sitesecurecode" class="form-control" />
                        </div>
                    </div>
                    <!--fco tax percentage-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="daysactive"><span><?php echo $entry_multisafepay_days_active; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_days_active" value="<?php echo $multisafepay_days_active; ?>" id="daysactive" class="form-control" />
                        </div>
                    </div>
                    
                    
                    
                    <!--FCO B2B-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="b2b"><?php echo $entry_multisafepay_b2b; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_b2b" id="b2b" class="form-control">
                                <option value="true" <?php if($multisafepay_b2b == 'true') echo 'selected'; ?>>Enabled</option>
                                <option value="false" <?php if($multisafepay_b2b == 'false') echo 'selected'; ?>>Disabled</option>
                            </select>
                        </div>
                    </div>
                    
                    
                    

                    <!--fco tax percentage-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="fcotax"><span><?php echo $entry_multisafepay_fco_tax; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_fco_tax_percent" value="<?php echo $multisafepay_fco_tax_percent; ?>" id="fcotax" class="form-control" />
                        </div>
                    </div>

                    <!--fco free ship amount-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="freeship"><span><?php echo $entry_multisafepay_fco_free_ship; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_fco_free_ship" value="<?php echo $multisafepay_fco_free_ship; ?>" id="freeship" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="minamount"><span><?php echo $text_min_amount; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_min_amount" value="<?php echo $multisafepay_min_amount; ?>" id="minamount" class="form-control" />
                        </div>
                    </div>

                    <!--fco tax percentage-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="maxamount"><span><?php echo $text_max_amount; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_max_amount" value="<?php echo $multisafepay_max_amount; ?>" id="maxamount" class="form-control" />
                        </div>
                    </div>

                    <!--set initialized status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="initialized"><?php echo $entry_multisafepay_order_status_id_initialized; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_initialized" id="initialized" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_initialized) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--set completed status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="completed"><?php echo $entry_multisafepay_order_status_id_completed; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_completed" id="completed" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_completed) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--set uncleared status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="uncleared"><?php echo $entry_multisafepay_order_status_id_uncleared; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_uncleared" id="uncleared" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_uncleared) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--set reserved status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="reserved"><?php echo $entry_multisafepay_order_status_id_reserved; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_reserved" id="reserved" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_reserved) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--set void status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="void"><?php echo $entry_multisafepay_order_status_id_void; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_void" id="void" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_void) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--set refunded status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="refunded"><?php echo $entry_multisafepay_order_status_id_refunded; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_refunded" id="refunded" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_refunded) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--set declined status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="declined"><?php echo $entry_multisafepay_order_status_id_declined; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_declined" id="declined" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_declined) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--set expired status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="expired"><?php echo $entry_multisafepay_order_status_id_expired; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_expired" id="expired" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_expired) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--set shipped status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="shipped"><?php echo $entry_multisafepay_order_status_id_shipped; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_shipped" id="shipped" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_shipped) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    <!--set partial_refunded status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="partial_refunded"><?php echo $entry_multisafepay_order_status_id_partial_refunded; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_order_status_id_partial_refunded" id="partial_refunded" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $multisafepay_order_status_id_partial_refunded) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $text_all_zones; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $multisafepay_geo_zone_id) { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!--Sorting-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_sort_order" value="<?php echo $multisafepay_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>