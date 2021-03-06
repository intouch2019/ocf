<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_supplier_bill_view extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $billid="";
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["billid"]) != ""){
                $this->billid = $this->params["billid"];
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
        $('#receiveddate').datepicker({
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

    function fetchPOValues(id){
        var billid = $("#billid").val();
        var ajaxURL = "ajax/fetchPOValues.php?polineid="+id+"&billid="+billid;
            //alert(ajaxURL);
            $.ajax({
            url:ajaxURL,
                dataType: 'json',
                success:function(data){
                    //alert(data.error);
                    if (data.error == "1") {
                        alert(data.msg);
                    } else {
                        var product = data.name; 
                        var qty = data.qty;
                        var rate = data.rate;
                        var expected_date = data.expected_date;
                        var prodid = data.prodid;
                        $("#poqty").val(qty);
                        $("#porate").val(rate);
                        $("#poexdate").val(expected_date);
                        $("#polineid").val(id);
                        $("#prodid").val(prodid);
                    }
                }
            });
    }

function back(status){
    window.location.href = "supplier/bills/status="+status;
}

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
        }

        public function pageContent() {
            $menuitem = "supplierbill";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj = $dbl->getSupplierBillDetails($this->billid);
            $obj_po_products = $dbl->getPOProductsBySupplierBill($this->billid);
            $obj_poitems = null;
            $obj_supplier_bill_items = $dbl->getSupplierBillItems($this->billid);
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Bill Details</h2>
                        <div class="common-content-block">
                            <input type="hidden" name="billid" id="billid" value="<?php echo $this->billid;?>"/>
                             <div class="box box-primary"><br>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Supplier : <?php echo $obj->company_name;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>PO No : <?php echo $obj->pono;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>PO Date : <?php echo ddmmyy($obj->submittedtime);?></label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Bill No : <?php echo $obj->billno;?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bill Date : <?php echo ddmmyy($obj->bill_date);?></label>
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>GateEntry No : <?php echo $obj->gatentryno;?></label>                                                                            </div>
                                    <div class="col-md-4">
                                        <label>Gate Entry Date : <?php echo ddmmyy($obj->gateentry_date);?></label>
                                    </div>
                                    <div class="col-md-4">
                                    </div>
                                </div>
                             </div>   
                        </div>
                    </div>
                </div>
                <div  class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="title-bar">Bill Items</h2>
                        <div class="common-content-block">
                             <div class="box box-primary"><br>
                            <form role="form" id="suppbilladditem" name="suppbilladditem" enctype="multipart/form-data" method="post" action="formpost/suppbilladditem.php">
                                <input type="hidden" name="billid" id="billid" value="<?php echo $this->billid;?>"/>
                                <input type="hidden" name="polineid" id="polineid" value=""/>
                                <input type="hidden" name="prodid" id="prodid" value=""/>
                                <?php if ($formResult->form_id == 'createdc') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                            </form>                                 
                             </div>  
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <td>Sl.No.</td>
                                            <td>Product</td>
                                            <td>Qty</td>
                                            <td>Rate</td>
                                            <td>Value</td>
                                            <td>Received Date</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $srno = 1;
                                        $tot_qty = 0;
                                        $total_value = 0;
                                        if($obj_supplier_bill_items != NULL){
                                        foreach($obj_supplier_bill_items as $item){?>
                                        <tr>
                                            <td><?php echo $srno;?></td>
                                            <td><?php echo $item->product;?></td>
                                            <td><?php echo $item->qty;?></td>
                                            <td><?php echo $item->rate;?></td>
                                            <td><?php echo $item->value;?></td>
                                            <td><?php echo ddmmyy($item->receiveddate);?></td>
                                        </tr>
                                        <?php $srno = $srno + 1;
                                              $tot_qty = $tot_qty + $item->qty;
                                              $total_value = $total_value + $item->value;
                                        }}?>
                                    </tbody> 
                                    <tfoot>
                                        <tr>
                                            <td>Total</td>
                                            <td></td>
                                            <td><?php echo $tot_qty;?></td>
                                            <td></td>
                                            <td><?php echo $total_value?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary" onclick="back(<?php echo $obj->status;?>)">Back</button>
                            </div>    
                        </div>
                    </div>
                </div> <!--Add Items>   
            </div>
        </div> 
 </div><!-- end -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


