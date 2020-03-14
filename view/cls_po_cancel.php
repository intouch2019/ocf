<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_po_cancel extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $poid="";
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["poid"]) != ""){
                $this->poid = $this->params["poid"];
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
<script type="text/javaScript">   
    $(function () {  
        $('#exdate').datepicker({
             format:'dd-mm-yyyy'
         });
        
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
    
    function cancelPO(poid){  
       var r = confirm("Are you sure you want to Cancel this PO");
       if(r){
        var remarks = $('#remarks').val();
        var ajaxURL = "ajax/cancelPO.php?poid=" + poid + "&remarks="+remarks;
            //alert(ajaxURL);
            $.ajax({
            url:ajaxURL,
                dataType: 'json',
                success:function(data){
                    //alert(data.error);
                    if (data.error == "1") {
                        alert(data.msg);
                    } else {
                        window.location.href = "po/postatus="+<?php echo POStatus::AwaitingCancel;?>;
                        //var location = "formpost/generatePopdf.php?poid="+<?php echo $this->poid;?>; 
                        //window.open(location,'_blank'); 
                        
                    }
                }
            });
       }
    }
</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "cancelpo";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
            
            $obj_categories = $dbl->getAllActiveCategories();
            $obj_products = $dbl->getAllActiveProducts();
            $obj_suppliers = $dbl->getAllActiveSuppliers();
            $obj_specifications = $dbl->getAllActiveSpecifications();
            $obj_po = $dbl->getPODetails($this->poid);
            $obj_poitems = $dbl->getPOItems($this->poid);
            //print_r($obj_po);
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">PO Details</h2>
                        <div class="common-content-block">
                            <input type="hidden" name="poid" id="poid" value="<?php echo $this->poid;?>"/>
                             <div class="box box-primary"><br>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Supplier Code : <?php echo $obj_po->supplier_code;?></label>
                                        
                                    </div>
                                    <div class="col-md-4">
                                        <label>Payment Terms : <?php echo $obj_po->pmterm;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>PO No : <?php echo $obj_po->pono;?></label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Supplier Name : <?php echo $obj_po->company_name;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Delivery Terms : <?php echo $obj_po->dtterm;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>PO Date : <?php echo ddmmyy($obj_po->createtime);?></label>
                                    </div>
                                </div>
<!--                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Buyer Code : <?php echo $obj_po->buyer_code;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>DC Code : <?php echo $obj_po->dccode;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Contract No : <?php echo $obj_po->supp_contract_no;?></label>
                                    </div>
                                </div>-->
                                <div class="col-md-12">
<!--                                    <div class="col-md-4">
                                        <label>Buyer Name : <?php echo $obj_po->buyer_name;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Delivery Name : <?php echo $obj_po->delivery_name;?></label>
                                    </div>-->
                                    <div class="col-md-4">
                                        <label>Transit Insurance : <?php echo $obj_po->titerm;?></label>
                                    </div>
                                </div>
                             </div>   
                        </div>
                    </div>
                </div>
                <div  class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="title-bar">PO Items</h2>
                        <div class="common-content-block">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <td>Sl.No.</td>
                                            <td>Product</td>
                                            <td>Qty (Kg)</td>
                                            <td>Rate (Rs./Kg)</td>
                                            <td>Total Value (Rs.)</td>
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $srno = 1;
                                        $tot_qty = 0;
                                        setlocale(LC_MONETARY,"en_IN");
                                        $total_value = 0;
                                        if($obj_poitems != NULL){
                                        foreach($obj_poitems as $item){?>
                                        <tr>
                                            <td><?php echo $srno;?></td>
                                            <td><?php 
                                                $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                $itemname = $item->prod.$desc1.$desc2.$thickness;
                                                echo $itemname;
                                                ?>
                                            </td>
                                            <td><?php echo $item->qty; ?></td>
                                                        <td><?php echo $item->totalrate; ?></td>
                                                        <td><?php echo $item->totalvalue;
                                                            ?></td>
                                           
                                        </tr>
                                        <?php $srno = $srno + 1;
                                              $tot_qty = $tot_qty + $item->qty;
                                              $total_value = $total_value + $item->totalvalue;
                                        }}?>
                                    </tbody> 
                                    <tfoot>
                                        <tr>
                                            <td>Total</td>
                                            <td></td>
                                            <td><?php echo $tot_qty;?></td>
                                            <td></td>
                                            <td><?php echo money_format('%!i',$total_value);?></td>
                                            
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <input type="hidden" name="pid" id="pid" value="<?php echo $this->poid;?>">
                                <div class="col-md-12">
                                    <label>Reson for Cancel</label><br>
                                    <textarea id="remarks" name="remarks" cols="50"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button onclick="cancelPO(<?php echo $this->poid;?>);" class="btn btn-primary">Send For Approve Cancellation</button>
<!--                                    <button onclick="rejectPO(<?php echo $this->poid;?>);" class="btn btn-primary">Reject</button>-->
                                </div>
                            </div>    
                        </div>
                    </div>
                </div> <!--Add Items>   
            </div>
        </div> 
 </div><!-- end -->
<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           -->

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


