<div id="multisafepay-order-tab">
    <table class="table table-striped table-bordered">
        <tbody>
        <tr>
            <td><?php echo $row_total; ?></td>
            <td><?php echo $total; ?></td>
        </tr>
        </tbody>
    </table>
    <?php if($status == 'completed' || $status == 'shipped' || $status == 'initialized' || $status == 'expired') { ?>
        <div class="text-right">
            <?php if($status == 'completed' || $status == 'shipped') { ?>
                <button id="button-refund" data-toggle="tooltip" title="<?php echo $button_refund; ?>" class="btn btn-danger"><i class="fa fa-step-backward"></i> <?php echo $button_refund; ?></button>
            <?php } ?>
            <?php if($status == 'initialized') { ?>
                <button id="button-cancel" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-danger"><i class="fa fa-times"></i> <?php echo $button_cancel; ?></button>
            <?php } ?>
            <?php if($status == 'completed' || $status == 'initialized') { ?>
                <button id="button-shipped" data-toggle="tooltip" title="<?php echo $button_shipped; ?>" class="btn btn-info"><i class="fa fa-plane"></i> <?php echo $button_shipped; ?></button>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<?php if($status == 'completed' || $status == 'shipped' || $status == 'initialized' || $status == 'expired') { ?>
    <?php if($status == 'completed' || $status == 'shipped') { ?>
        <script type="text/javascript"><!--
            $('#button-refund').on('click', function(e) {
                e.preventDefault();
                if (confirm('<?php echo $text_refund_confirm; ?>')) {
                    $.ajax({
                        url: 'index.php?route=<?php echo $extension_route; ?>/refundOrder&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>',
                        dataType: 'json',
                        async: false,
                        beforeSend: function() {
                            $('#button-refund').button('loading');
                        },
                        complete: function() {
                            $('#button-refund').button('reset');
                        },
                        success: function(json) {
                            if (json['error']) {
                                $('#tab-multisafepay-order').append('<div class="alert alert-danger alert-dismissible">' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                            }
                            if (json['success']) {
                                $('#history').load('index.php?route=sale/order/history&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>');
                                $( '#tab-multisafepay-order' ).load( 'index.php?route=<?php echo $extension_route; ?>/refreshOrderTab&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>', function() {
                                    $( '#tab-multisafepay-order' ).prepend('<div class="alert alert-success alert-dismissible">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                });
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            });
            //--></script>
    <?php } ?>
    <?php if($status == 'initialized') { ?>
        <script type="text/javascript"><!--
            $('#button-cancel').on('click', function(e) {
                e.preventDefault();
                if (confirm('<?php echo $text_cancelled_confirm; ?>')) {
                    $.ajax({
                        url: 'index.php?route=<?php echo $extension_route; ?>/changeMultiSafepayOrderStatusTo&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&type=cancelled',
                        dataType: 'json',
                        async: false,
                        beforeSend: function() {
                            $('#button-cancel').button('loading');
                        },
                        complete: function() {
                            $('#button-cancel').button('reset');
                        },
                        success: function(json) {
                            if (json['error']) {
                                $('#tab-multisafepay-order').append('<div class="alert alert-danger alert-dismissible">' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                            }
                            if (json['success']) {
                                $('#history').load('index.php?route=sale/order/history&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>');
                                $( '#tab-multisafepay-order' ).load( 'index.php?route=<?php echo $extension_route; ?>/refreshOrderTab&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>', function() {
                                    $( '#tab-multisafepay-order' ).prepend('<div class="alert alert-success alert-dismissible">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                });
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            });
            //--></script>
    <?php } ?>
    <?php if($status == 'completed' || $status == 'initialized') { ?>
        <script type="text/javascript"><!--
            $('#button-shipped').on('click', function(e) {
                e.preventDefault();
                if (confirm('<?php echo $text_shipped_confirm; ?>')) {
                    $.ajax({
                        url: 'index.php?route=<?php echo $extension_route; ?>/changeMultiSafepayOrderStatusTo&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&type=shipped',
                        dataType: 'json',
                        async: false,
                        beforeSend: function() {
                            $('#button-shipped').button('loading');
                        },
                        complete: function() {
                            $('#button-shipped').button('reset');
                        },
                        success: function(json) {
                            if (json['error']) {
                                $('#tab-multisafepay-order').append('<div class="alert alert-danger alert-dismissible">' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                            }
                            if (json['success']) {
                                $('#history').load('index.php?route=sale/order/history&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>');

                                $( '#tab-multisafepay-order' ).load( 'index.php?route=<?php echo $extension_route; ?>/refreshOrderTab&<?php echo $token_name; ?>=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>', function() {
                                    $( '#tab-multisafepay-order' ).prepend('<div class="alert alert-success alert-dismissible">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                });
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            });
            //--></script>
    <?php } ?>
<?php } ?>