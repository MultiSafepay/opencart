<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-multisafepay_klarna" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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

                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-multisafepay_klarna" class="form-horizontal">
	                <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-default" data-toggle="tab">Default Store</a></li>
                        <?php foreach ($stores as $store) { ?>
                        	<li><a href="#tab-store-<?php echo $store['store_id']; ?>" data-toggle="tab">Store <?php echo $store['name']; ?></a></li>
                        <?php }?>
                    </ul>
                    
                    <div class="tab-content">
					 <?php foreach ($stores as $store) { ?>
					 		<div class="tab-pane" id="tab-store-<?php echo $store['store_id']; ?>">
					 			<!--MultiSafepay environment-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="environment"><?php echo $entry_environment; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_klarna_environment_<?php echo $store['store_id'];?>" id="environment" class="form-control">
                                <option value="0" <?php if(${'multisafepay_klarna_environment_'.$store['store_id']} == 0) echo 'selected'; ?>>Live account</option>
                                <option value="1" <?php if(${'multisafepay_klarna_environment_'.$store['store_id']} == 1) echo 'selected'; ?>>Test account</option>
                            </select>
                        </div>
                    </div>


                    <!--Account settings-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="accountid"><span><?php echo $entry_multisafepay_merchantid; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_merchant_id_<?php echo $store['store_id'];?>" value="<?php echo ${'multisafepay_klarna_merchant_id_'.$store['store_id']}; ?>" id="accountid" class="form-control" />
                        </div>
                    </div>

                    <!--siteid settings-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="siteid"><span><?php echo $entry_multisafepay_siteid; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_site_id_<?php echo $store['store_id'];?>" value="<?php echo ${'multisafepay_klarna_site_id_'.$store['store_id']}; ?>" id="siteid" class="form-control" />
                        </div>
                    </div>

                    <!--site secure code-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="sitesecurecode"><span><?php echo $entry_multisafepay_secure_code; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_secure_code_<?php echo $store['store_id'];?>" value="<?php echo ${'multisafepay_klarna_secure_code_'.$store['store_id']}; ?>" id="sitesecurecode" class="form-control" />
                        </div>
                    </div>

                    <!--fco tax percentage-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="minamount"><span><?php echo $text_multisafepay_klarna_min_amount; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_min_amount_<?php echo $store['store_id'];?>" value="<?php echo ${'multisafepay_klarna_min_amount_'.$store['store_id']}; ?>" id="minamount" class="form-control" />
                        </div>
                    </div>

                    <!--fco tax percentage-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="maxamount"><span><?php echo $text_multisafepay_klarna_max_amount; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_max_amount_<?php echo $store['store_id'];?>" value="<?php echo ${'multisafepay_klarna_max_amount_'.$store['store_id']}; ?>" id="maxamount" class="form-control" />
                        </div>
                    </div>

                    <!--IP validation -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="ipvalidation"><?php echo $text_multisafepay_klarna_ip_validation_option; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_klarna_ip_validation_enabler_<?php echo $store['store_id'];?>" id="ipvalidation" class="form-control">
                                <option value="0" <?php if(${'multisafepay_klarna_ip_validation_enabler_'.$store['store_id']} == 0) echo 'selected'; ?>>Disabled</option>
                                <option value="1" <?php if(${'multisafepay_klarna_ip_validation_enabler_'.$store['store_id']} == 1) echo 'selected'; ?>>Enabled</option>
                            </select>
                        </div>
                    </div>

                    <!--aip addresses-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="ipaddresses"><span><?php echo $text_multisafepay_klarna_ip_validation_address; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_ip_validation_address_<?php echo $store['store_id'];?>" value="<?php echo ${'multisafepay_klarna_ip_validation_address_'.$store['store_id']}; ?>" id="ipaddresses" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $text_all_zones; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_klarna_geo_zone_id_<?php echo $store['store_id'];?>" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == ${'multisafepay_klarna_geo_zone_id_'.$store['store_id']}) { ?>
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
                        <a href="../../../language/english/payment/multisafepay_klarna.php"></a>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_sort_order_<?php echo $store['store_id'];?>" value="<?php echo ${'multisafepay_klarna_sort_order_'.$store['store_id']}; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
					 		</div>
					 <?php } ?>
					 
					 
	                <div class="tab-pane active" id="tab-default">
                    <!--Module status-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_klarna_status" id="input-status" class="form-control">
                                <?php if ($multisafepay_klarna_status) { ?>
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
                            <select name="multisafepay_klarna_environment_0" id="environment" class="form-control">
                                <option value="0" <?php if($multisafepay_klarna_environment == 0) echo 'selected'; ?>>Live account</option>
                                <option value="1" <?php if($multisafepay_klarna_environment == 1) echo 'selected'; ?>>Test account</option>
                            </select>
                        </div>
                    </div>


                    <!--Account settings-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="accountid"><span><?php echo $entry_multisafepay_merchantid; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_merchant_id_0" value="<?php echo $multisafepay_klarna_merchant_id; ?>" id="accountid" class="form-control" />
                        </div>
                    </div>

                    <!--siteid settings-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="siteid"><span><?php echo $entry_multisafepay_siteid; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_site_id_0" value="<?php echo $multisafepay_klarna_site_id; ?>" id="siteid" class="form-control" />
                        </div>
                    </div>

                    <!--site secure code-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="sitesecurecode"><span><?php echo $entry_multisafepay_secure_code; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_secure_code_0" value="<?php echo $multisafepay_klarna_secure_code; ?>" id="sitesecurecode" class="form-control" />
                        </div>
                    </div>

                    <!--fco tax percentage-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="minamount"><span><?php echo $text_multisafepay_klarna_min_amount; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_min_amount_0" value="<?php echo $multisafepay_klarna_min_amount; ?>" id="minamount" class="form-control" />
                        </div>
                    </div>

                    <!--fco tax percentage-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="maxamount"><span><?php echo $text_multisafepay_klarna_max_amount; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_max_amount_0" value="<?php echo $multisafepay_klarna_max_amount; ?>" id="maxamount" class="form-control" />
                        </div>
                    </div>

                    <!--IP validation -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="ipvalidation"><?php echo $text_multisafepay_klarna_ip_validation_option; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_klarna_ip_validation_enabler_0" id="ipvalidation" class="form-control">
                                <option value="0" <?php if($multisafepay_klarna_ip_validation_enabler == 0) echo 'selected'; ?>>Disabled</option>
                                <option value="1" <?php if($multisafepay_klarna_ip_validation_enabler == 1) echo 'selected'; ?>>Enabled</option>
                            </select>
                        </div>
                    </div>

                    <!--aip addresses-->
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="ipaddresses"><span><?php echo $text_multisafepay_klarna_ip_validation_address; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_ip_validation_address_0" value="<?php echo $multisafepay_klarna_ip_validation_address; ?>" id="ipaddresses" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $text_all_zones; ?></label>
                        <div class="col-sm-10">
                            <select name="multisafepay_klarna_geo_zone_id_0" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                <?php if ($geo_zone['geo_zone_id'] == $multisafepay_klarna_geo_zone_id) { ?>
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
                        <a href="../../../language/english/payment/multisafepay_klarna.php"></a>
                        <div class="col-sm-10">
                            <input type="text" name="multisafepay_klarna_sort_order_0" value="<?php echo $multisafepay_klarna_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>