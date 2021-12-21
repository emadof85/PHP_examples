<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fas fa-cogs"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><?php echo $this->lang->line("Settings"); ?></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerceexternal'); ?>"><?php echo $this->lang->line("Ecommerce Integration"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <form action="<?php echo base_url("ecommerceexternal/store_settings_action"); ?>" method="POST">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <button class="btn btn-primary btn-lg" type="button" id="disconnectStore"><i class="fas fa-plug"></i> <?php echo $this->lang->line("Disconnect Store");?></button>
                                </div>
                            </div>
                            <hr>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form action="<?php echo base_url("ecommerceexternal/store_settings_save"); ?>" method="POST">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for=""><i class="fa fa-usd"></i> <?php echo $this->lang->line("Payment Method"); ?></label>
                                        <select name="selected_payment" class="form-control">
                                            <?php
                                            foreach ($payment_methods as $payment_method) {
                                                echo '<option value="'.$payment_method['id'].'" '.(!empty($selected_payment) && $selected_payment == $payment_method['id'] ? "selected" : "").'>'.$payment_method['title'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="change_settings" class="btn btn-info"><?php echo $this->lang->line("Save"); ?></button>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                <label for=""><i class="fa fa-globe"></i> <?php echo $this->lang->line("Shipping Method"); ?></label>
                                <select name="selected_shipping" class="form-control">
                                    <?php
                                    foreach ($shipping_methods as $shipping_method) {
                                        echo '<option value="'.$shipping_method['id'].'" '.(!empty($selected_shipping) && $selected_shipping == $shipping_method['id'] ? "selected" : "").'>'.$shipping_method['title'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    var base_url="<?php echo site_url(); ?>";

    $(document).ready(function() {

        $('#disconnectStore').on('click', function(e) {
            e.preventDefault();

            var jqxhr = $.ajax( base_url+'ecommerceexternal/disconnect_store_action' )
                .done(function() {
                    window.location.href = base_url+'ecommerceexternal';
                })
                .fail(function() {
                    alert( "error" );
                });
        });
        var perscroll;
        var table = $("#mytable").DataTable({
            serverSide: true,
            processing:true,
            bFilter: true,
            order: [[ 2, "desc" ]],
            pageLength: 10,
            ajax: {
                url: base_url+'ecommerceexternal/get_products_data',
                type: 'POST'
            },
            language:
                {
                    url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
                },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
                if(areWeUsingScroll)
                {
                    if (perscroll) perscroll.destroy();
                    perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
                }
            },
            scrollX: 'auto',
            fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again
                if(areWeUsingScroll)
                {
                    if (perscroll) perscroll.destroy();
                    perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
                }
            }

        });
    });
</script>
