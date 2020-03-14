<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_cr_stock_pull extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $poid="";
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["po_alloc_id"]) != ""){
                $this->po_alloc_id = $this->params["po_alloc_id"];
            }
        }

	function extraHeaders() {
        ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style type="text/css" title="currentStyle">

    .hidden{display: none;}
          /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
<script type="text/javaScript">   
    $(function () {  
        $('#exdate').datepicker({ 
             format:'dd-mm-yyyy' 
         });

        // $("#save").hide();
        // $("#supplier").hide();
        
    });
      
    function setCtg(ctgValue){   
        if(ctgValue == -1){ 
            $("#addctg").show();
        }else{
            $("#addctg").hide(); 
        } 
    }

    function setSpec(specValue){
        if(specValue == -1){
            $("#addspec").show();
        }else{
            $("#addctg").hide();
        }
    }
    
    function calcValue(v){
        var qt = parseFloat($("#qty").val());
        var rate = parseFloat($("#rate").val());
        var value = qt * rate;
        //alert(value);
        $("#value").val(value);
    }
    
    function approvePO(poid){ 
      var r = confirm("Are you sure you want to approve this PO");
       if(r){ 
        var remarks = $('#remarks').val(); 
        var ajaxURL = "ajax/approvePO.php?poid=" + poid+"&remarks="+remarks;
            //alert(ajaxURL);
            $.ajax({
            url:ajaxURL,
                dataType: 'json',
                success:function(data){
                    //alert(data.error);
                    if (data.error == "1") {
                        alert(data.msg);
                    } else {
                        alert("PO Sucessfully Approved.");   
                        window.location.href = "po/postatus="+<?php echo POStatus::Approved;?>;
                    }
                }
            });
       }
    }
    
    function rejectPO(poid){
       var r = confirm("Are you sure you want to reject this PO");
       if(r){
        var remarks = $('#remarks').val();
        var ajaxURL = "ajax/rejectPO.php?poid=" + poid + "&remarks="+remarks;
                //alert(ajaxURL);
                $.ajax({
                url:ajaxURL,
                    dataType: 'json',
                    success:function(data){
                        if (data.error == "1") {
                            alert(data.msg);
                        } else {
                            //window.location.href = "po/awaiting/approvals";
                            window.location.href = "po/postatus="+<?php echo POStatus::Rejected; ?>;
                        }
                    }
                }); 
       }
    }

    function showSupplierChange(){
        // alert("here");
        document.getElementById('save').style.display = "inline-block";
        document.getElementById('supplierDiv').style.display = "inline-block";
        
        // $('#save').show();
        // $('#navigation ul li').css('display', 'inline-block');
        // $("#save").css("display", "block");
        // $("#Supplier").css("display", "block");
    }

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "crstockpull";//pagecode
            include "sidemenu.php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
            
            $obj_categories = $dbl->getAllActiveCategories();
            $obj_products = $dbl->getAllActiveProducts();
            $obj_suppliers = $dbl->getAllActiveSuppliers();
            $obj_specifications = $dbl->getAllActiveSpecifications();
            $po_alloc_obj = $dbl->getPoAllocationDetailsById($this->po_alloc_id);
            $obj_sttrns = $dbl->getStockTransferDetails($po_alloc_obj->transferid);
        //print_r($obj_sttrns);
            $obj_stockitems = $dbl->getPOAllocationItems($this->po_alloc_id);
            // $obj_stockitems = $dbl->getStockTransferItems($po_alloc_obj->transferid);
            $obj_stockcurrent = $dbl->getStockcurrentDetails($obj_sttrns->from_location_id,$obj_sttrns->from_location_type);

            $result = $dbl->checkDeliveryNote($this->po_alloc_id);
            $flag = false;
            if(!isset($result) && $this->currStore->usertype == UserType::Director){
                $flag = true;
            }
            $obj_dc_master = $dbl->getAllDCMasters();
            // print_r($result);
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Purchase Order Details</h2>
                            <div class="common-content-block">
                                <input type="hidden" name="transferid" id="transferid" value="<?php echo $po_alloc_obj->transferid; ?>"/>
                                <div class="box box-primary"><br>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label>Purchase Order No : <?php echo $obj_sttrns->transferno; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>PO Allocation No : <?php echo $po_alloc_obj->allocation_num; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Supplier Location : <?php echo $po_alloc_obj->fromloc; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Stockist Location : <?php echo $po_alloc_obj->toloc; ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <!-- <label>PO Total Quantity : <?php echo $po_alloc_obj->tot_qty; ?></label> -->
                                        </div>
                                        <div class="col-md-3">
                                            <label>PO Allocation Quantity : <?php echo $po_alloc_obj->order_qty; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Fullfilled Quantity : <?php echo $po_alloc_obj->fullfilled_qty; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            
                                            <label>Purchase Order Date : <?php echo $obj_sttrns->transferdate; ?></label>
                                        </div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                </div>
                <div  class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="title-bar">Purchase Order Items</h2>
                        <div class="common-content-block">
                            <div class="col-md-12">
                                <table class="table">
                                        <thead>
                                            <tr>
                                                <td>Sl.No.</td>
                                                <td>Product</td>
                                            
                                                <td>Ordered Quantity</td>
                                               
<!--                                                <td>Action</td>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $srno = 1;
                                            $tot_qty = 0;
                                            $itemCount = 0;
                                            setlocale(LC_MONETARY,"en_IN");
                                            $total_value = 0;
                                            if ($obj_stockitems != NULL) {
                                                foreach ($obj_stockitems as $item) {
                                                    $itemCount++;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $srno; ?></td>
                                                        <td><?php
                                                             $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                             $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                             $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                             $itemname = $item->prod.$desc1.$desc2.$thickness;
                                                               echo $itemname;
                                                            ?>
                                                        </td>
                                                        
                                                        <td><?php echo $item->req_qty; ?></td>
                                                       
                                                        <!-- <?php 
                                                        $itemtotalval =  $item->qty * $item->value;?> -->
                                                        
<!--                                                        <td><input class="btn btn-primary" type="button" name="deleteItem" id="deleteItem" value="Delete" onclick="deletePOItem(<?php echo $item->id; ?>);" /></td>-->
                                                    </tr>
                                                            <?php
                                                            $srno = $srno + 1;
                                                            $tot_qty = $tot_qty + $item->req_qty;
                                                            // $total_value = $total_value + $itemtotalval;
                                                }
                                            }
                                            ?>
                                        </tbody> 
                                        <tfoot>
                                            <tr>
                                                <td>Total</td>
                                                <td></td>
                                                
                                                <td><?php echo $tot_qty; ?></td>
                                                <td></td>
                                                
<!--                                                <td></td>-->
                                            </tr>
                                        </tfoot>
                                    </table>
                            </div>
                            <div class="col-md-12">
                                <form  role="form" id="createstocktransfer" name="createstocktransfer" enctype="multipart/form-data" method="post" action="formpost/crstockpull.php">
                                <input type="hidden" name="transferid" id="transferid" value="<?php echo $po_alloc_obj->transferid;?>">
                                <div class="col-md-12">
                                    <?php if($this->currStore->usertype == UserType::RFC && $obj_sttrns->status == StockTransferStatus::AwaitingIn){?>
                                    <!-- <button type="submit" class="btn btn-primary">Save Stock</button> -->
                                    <?php } ?>
<!--                                    <button onclick="rejectPO(<?php echo $this->poid;?>);" class="btn btn-primary">Reject</button>-->
                                </div>
                                </form>
                            </div>
                            <?php if($flag){  ?>
                            <div class="col-md-12">
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-primary" onclick="showSupplierChange();">Change Supplier</button>
                                        </div>
                                        <form id="changeSupplier" name="changeSupplier" enctype="multipart/form-data" method="post" action="formpost/changeSupplier.php">
                                        <div class="col-md-8" id="supplierDiv" style="display: none;">
                                            <select id="supplier" name="supplier" class="selectpicker form-control"  data-show-subtext="true" data-live-search="true" >
                                            <option value="-1">Select Supplier</option>
                                              <?php foreach($obj_dc_master as $dcmaster){
                                                 $selected = "";
                                                if($dcmaster->id == $this->dcid){ $selected = "selected"; }?>
                                                 <option value="<?php echo $dcmaster->id;?>" <?php echo $selected;?>><?php echo $dcmaster->dc_name;?></option>
                                              <?php } ?>
                                           </select>
                                        </div>
                                        <!-- <div class="col-md-3">
                                            <label>Supplier Location : <?php echo $obj_sttrns->fromloc; ?></label>
                                        </div> -->
                                        <div class="col-md-1">
                                            
                                                <input type="hidden" name="po_alloc_id" id="po_alloc_id" value="<?php echo $this->po_alloc_id;?>">
                                                <input type="hidden" name="form_id" id="form_id" value="changeSupplier">
                                                <button type="submit" id="save" name="save" class="btn btn-primary pull-right" style="display: none;" >Save Details</button>
                                            
                                        </div>
                                        </form>
                            </div>
                            <div class="col-md-12">
                                                <?php if ($formResult->form_id == 'changeSupplier') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        </div>
                    </div>
                </div> <!--Add Items>   
            </div>
        </div> 
 </div><!-- end -->
           

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


