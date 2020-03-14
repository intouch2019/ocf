<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_stock_transfer_report extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $cid;
    var $uid;
    var $pid;
    var $sid = -1;
    var $dtrng;

    function __construct($params = null) {
        //parent::__construct(array());
        $this->currStore = getCurrStore();

        $this->params = $params;

        if ($params && isset($params['cid'])) {
            $this->cid = $params['cid'];
        }

        if ($params && isset($params['uid'])) {
            $this->uid = $params['uid'];
        }

        if ($params && isset($params['pid'])) {
            $this->pid = $params['pid'];
        }


        if ($params && isset($params['sid'])) {
            $this->sid = $params['sid'];
        }
    }

    function extraHeaders() {
        ?>
        <style type="text/css" title="currentStyle">
            /*  @import "js/datatables/media/css/demo_page.css";
              @import "js/datatables/media/css/demo_table.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
        <!-- <script src="js/datatables/media/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/ajax.js"></script>
        <script type="text/javascript" src="js/ajax-dynamic-list.js">
        /************************************************************************************************************
        (C) www.dhtmlgoodies.com, April 2006

        This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	

        Terms of use:
        You are free to use this script as long as the copyright message is kept intact. However, you may not
        redistribute, sell or repost it without our permission.

        Thank you!

        www.dhtmlgoodies.com
        Alf Magne Kalleland

        ************************************************************************************************************/	

        </script> -->
        <script type="text/javaScript">           
            $(function(){
                 $("#div_id").hide();
             $('#startdt').datepicker({ 
             format:'yyyy-mm-dd'
            });
                   
            $('#enddt').datepicker({
             format:'yyyy-mm-dd'
            });

            var url = "ajax/tb_sortingloss_report.php?cid=<?php echo $this->cid; ?>&uid=<?php echo $this->uid; ?>&pid=<?php echo $this->pid; ?>&sid=<?php echo $this->sid; ?>";
            //alert(url);
            oTable = $('#tb_products').dataTable( {
            "bProcessing": true,
            "bServerSide": true,
            "aoColumns": [null,null,null,null,null,null,null,null,{bSortable:false}],
            "sAjaxSource": url,
            "aaSorting": []
            } ); 
            // search on pressing Enter key only
            $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
            if (e.which == 13){                     
            oTable.fnFilter($(this).val(), null, false, true);
            }
            });    
            });

            function uploadProdFn(){
            window.location.href = "product/upload";
            }

            function editProduct(prodid){
            //    alert("product/edit/pid="+prodid);
            window.location.href = "product/edit/pid="+prodid;
            }
 
            function loadlocwise(){  
            //    alert(lid);
            var startdt = $("#startdt").val();
            var enddt = $("#enddt").val();
            var fromlocid = $("#locsel").val();
            var tolocid = $("#locsel2").val();
            
            if(startdt == "Click here to select date" || enddt == "Click here to select date" || enddt == "" || startdt =="" || enddt == null || startdt ==null || fromlocid == "Select Location" || tolocid == "Select Location" || fromlocid == null || tolocid == null || fromlocid == "" || tolocid == ""){
                
                alert("Please Select Date Range/Locations");
                return; 
            }
            $("#div_id").hide();
            // alert(lid);
            //    var div_id ="div_id";
            //    alert(div_id);
            //    alert("formpost/genLocStkRepExcel.php?lid="+lid);
            //    var ajaxurl = "ajax/checklocstk.php?lid="+lid;
            var ajaxurl = "ajax/checkStockTransferData.php?fromlocid="+fromlocid+"&tolocid="+tolocid+"&startdt="+startdt+"&enddt="+enddt;
//            alert(ajaxurl); 
//            return;
            $.ajax({
            url: ajaxurl,
            dataType: 'text',
            success: function (result){           
            if(result == 'success'){
            //                window.location.href = "formpost/genLocStkRepExcel.php?lid="+lid;
//                            alert("formpost/genSortingLossExcel.php?lid="+locid+"&startdt="+startdt+"&enddt="+enddt);
//                            alert("formpost/genSortingLossExcel.php?lid="+locid+"&startdt="+startdt+"&enddt="+enddt);
            window.location.href = "formpost/genStockTransferReport.php?fromlocid="+fromlocid+"&tolocid="+tolocid+"&startdt="+startdt+"&enddt="+enddt;
 
            }else{
//                alert("else");
            $("#div_id").show(); 
            }
            } 
            });  
            }
            
            function selectBinLocations(locid){
//                alert(locid);
                 var ajaxURL = "ajax/getBinLocations.php?locid=" + locid;  
//                var ajaxURL = "ajax/getBatchcodeByProduct.php?stockcurrid=" + prodid+"&fromlocid="+fromlocid+"&fromloctype="+fromloctype; 
//                alert(ajaxURL);
                $.ajax({
                    url:ajaxURL,
                    //dataType: 'json',
                    cache: false,
                    success:function(html){
//                        alert(html);
                    $(".selectpicker2").html(html);
//                    document.getElementById("locsel2").innerHTML(html);
                    }
                });
            }

        </script>
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
        <!--<link rel="stylesheet" href="js/chosen/chosen.css" />
        <link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->



        <?php
    }

    public function pageContent() {
        // print_r($_SESSION);
        //$currUser = getCurrUser();
        $menuitem = "stocktransfer"; //pagecode
        include "sidemenu.php";
        $dbl = new DBLogic();
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
        ?>

        <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary pull-right" onclick="loadlocwise();">Excel Download</button>
                </div>
            </div>
            <div class="row">
                <div class='col-sm-3'>
                    <label id="labelitemname" class=" control-label"> Select Start Date :</label>
                    <!--Date and time range--> 
                    <div class="form-group">
                        <!--<label>Select Delivery Date:</label>-->
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="startdt" name ="startdt" value="Click here to select date"/>
                        </div><!-- /.input group -->
                    </div><!-- /.form group -->
                </div>
                <div class='col-sm-3'>
                    <label id="labelitemname" class=" control-label"> Select End Date :</label>
                    <!--Date and time range--> 
                    <div class="form-group">
                        <!--<label>Select Delivery Date:</label>-->
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="enddt" name ="endt" value="Click here to select date"/>
                        </div><!-- /.input group -->
                    </div><!-- /.form group -->
                </div>
                <div class="col-md-3">
                    <label>From Location:</label>
                    <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="locsel" name="locsel" onchange="selectBinLocations(this.value);">
                        <option value="" disabled selected>Select Location</option>
                        <?php
                        $lobjs = $dbl->getAllLoc();
                        //                print_r($lobjs);
                        if (isset($lobjs)) {
                            foreach ($lobjs as $lobj) {
                                ?>
                                <option value="<?php echo $lobj->id; ?>" > <?php echo $lobj->name; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>  
                </div>
                <div class="col-md-3">
                    <label>To Location</label><br>  
                    <select id="locsel2" name="locsel2" class="selectpicker2 form-control" data-show-subtext="true" data-live-search="true" >
                        <option selected="selected">Select Location</option>
                    </select>
                </div>
      
            </div>
            

             <br>
    <br>
    <div class="alert alert-dismissible" id="div_id" style="background-color: white;" > 
        <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
    <?php echo "Information not available for selected Location/Date Range"; ?>
    </div>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
             <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
            <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              
          <!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
          <script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php
            // }else{ print "You are not authorized to access this page";}
        }

    }
    ?>
</div>

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

