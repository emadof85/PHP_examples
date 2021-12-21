<section class="section section_custom">
    <div class="section-header">
        <h1><i class="fab fa-facebook"></i> <?php echo $page_title; ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><?php echo $this->lang->line("Ecommerceexternal"); ?></div>
            <div class="breadcrumb-item"><a href="<?php echo base_url('ecommerceexternal'); ?>"><?php echo $this->lang->line("ecommerceexternal Integration"); ?></a></div>
            <div class="breadcrumb-item"><?php echo $page_title; ?></div>
        </div>
    </div>
    <?php
    if($this->session->flashdata('importing_data')==1)
        echo "<div class='alert alert-success text-center'><i class='fas fa-check-circle'></i> ".$this->lang->line("Your data is currently importing, please refresh the page in few moments.")."</div>";
    ?>
    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body data-card">

                        <div class="table-responsive2">
                            <table class="table table-bordered" id="mytable">
                                <thead>
                                <tr>
                                    <th><?php echo $this->lang->line("ID"); ?></th>
                                    <th><?php echo $this->lang->line("Category ID"); ?></th>
                                    <th><?php echo $this->lang->line("Name"); ?></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>





<script>
    var base_url="<?php echo site_url(); ?>";

    $(document).ready(function() {

        var perscroll;
        var table = $("#mytable").DataTable({
            serverSide: true,
            processing:true,
            bFilter: true,
            order: [[ 2, "desc" ]],
            pageLength: 10,
            ajax: {
                url: base_url+'ecommerceexternal/get_categories_data',
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