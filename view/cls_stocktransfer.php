<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_stocktransfer extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $grnstatus = "";
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["stockstatus"]) != ""){
                $this->StockTransferStatus = $this->params["stockstatus"];
            }else if($this->currStore->usertype == UserType::Director){
                $this->StockTransferStatus = 2;
            }else if($this->currStore->usertype == UserType::PurchaseOfficer){
              $this->StockTransferStatus = 2;
            }else if($this->currStore->usertype == UserType::RFC){
              $this->StockTransferStatus = 1;
            }else{
                $this->StockTransferStatus = 2;
            }
        }

function extraHeaders() { ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" /> 
<style type="text/css" title="currentStyle">
    /*  @import "js/datatables/media/css/demo_page.css";
      @import "js/datatables/media/css/demo_table.css";*/
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
$(function(){       
    
    <?php if(UserType::PurchaseOfficer == $this->currStore->usertype || UserType::Director == $this->currStore->usertype){ ?>    
    var url = "ajax/tb_stocktransfer.php?status="+<?php echo $this->StockTransferStatus; ?>
    <?php }else{ ?>   
    var url = "ajax/tb_stocktransfer_rfc.php?status="+<?php echo $this->StockTransferStatus; ?>
    <?php } ?>       
    // alert(url);
    oTable = $('#tb_stocktransfertable').dataTable( {      
	"bProcessing": true, 
	"bServerSide": true,
    <?php if(UserType::PurchaseOfficer == $this->currStore->usertype || UserType::Director == $this->currStore->usertype){ ?>    
    "aoColumns": [null,null,null,null,null,null,null,{bSortable:false}], 
    <?php }else{ ?>   
    "aoColumns": [null,null,null,null,null,{bSortable:false}], 
    <?php } ?>  
    
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
    
function createStockTransfer(){
    window.location.href = "stocktransfer/additem"; 
}      
  
function editStockTransfer(id){
    window.location.href = "stocktransfer/additem/transferid="+id;
}    

function pullStockTransfer(id){
    window.location.href = "cr/stock/pull/po_alloc_id="+id;
}

function showPODetails(id){
    window.location.href = "po/details/transferid="+id;
}  

function deleteGRN(grnid){ 
    var r = confirm("Are you sure you want to delete this GRN");
    if(r){ 
     var remarks = $('#remarks').val();
     var ajaxURL = "ajax/deleteGRN.php?grnid=" + grnid;
         //alert(ajaxURL);
         $.ajax({
         url:ajaxURL,
             dataType: 'json',
             success:function(data){
                 //alert(data.error);
                 if (data.error == "1") { 
                     alert(data.msg);
                 } else {
                     window.location.href = "stocktransfer/stockstatus="+<?php echo GRNStatus::Deleted;?>;
                 }
             }
         });
    }
}

function changeStatus(status){  
    window.location.href = "stocktransfer/stockstatus="+status;
}

function editChallan(id){
  window.location.href = "challan/additem/transferid="+id; 
}
function viewChallan(id){
  window.location.href = "cr/challan/pull/challanid="+id;
}

function createChallan(id,po_alloc_id){
  // alert(po_alloc_id);
  // return;
// var confirm = confirm("Are you sure you want to Create Challan");
  // if(confirm){
    var ajaxURL = "ajax/createChallan.php?transferid=" + id + "&po_alloc_id=" + po_alloc_id;
          // alert(ajaxURL);
         $.ajax({
         url:ajaxURL,
             dataType: 'json',
             success:function(data){
                 // alert(data.error);
                 // alert(JSON.stringify(data));
                 if (data.error == "1") { 
                     alert(data.msg);
                 } else {
                    // alert("here");
                    window.location.href = "challan/additem/transferid="+id+"/challan_id="+data.challan_id; 
                    // alert("challan/additem/transferid="+id+"/challan_id="+data.challan_id);
                 }
             }
         });
  
 // window.location.href = "challan/additem/transferid="+id;
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "stocktransfer";
            include "sidemenu.php";
//            $dbl = new DBLogic();            
            $array = StockTransferStatus::getAll();
            // if($this->currStore->usertype != UserType::HO){
            //   $array = array_slice($array ,1);
            // }
            // print_r($this->currStore);
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="stockstatussel" name="stockstatussel" class="selectpicker form-control" data-show-subtext="true" 
                    data-live-search="true" onchange="changeStatus(this.value);">
                <!-- <option value=""></option> -->
                <?php foreach($array as $key => $value){
                  if($this->currStore->usertype == UserType::HO || $this->currStore->usertype == UserType::PurchaseOfficer && $key  == StockTransferStatus::BeingCreated){
                  }else{
                    $selected = "";
                    if($key == $this->StockTransferStatus){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php } }?>
            </select>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <?php if( $this->currStore->usertype == UserType::RFC){ ?>
        <div class="col-md-3">
            <form  role="form" id="createstocktransfer" name="createstocktransfer" enctype="multipart/form-data" method="post" action="formpost/createstocktransfer.php">
                <button class="btn btn-primary pull-right" type="submit">Create New Purchase Order</button>
            </form>
        </div>
        <?php }?>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Purchase Order List</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_stocktransfertable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                
                             <?php if(UserType::PurchaseOfficer == $this->currStore->usertype || UserType::Director == $this->currStore->usertype){?>

                                <th>Purchase Order No</th>
                                <th>PO Allocation No</th>
                                <th>Assigned To</th>
                                <th>PO Allocation Qty</th>
                                <th>Fullfilled Qty</th>
                                <th>Created By</th>                                
                                <th>Created Date</th>                
                                <th>Action</th>

                                
                            <?php }else{ ?>

                                <th>Purchase Order No</th>
                                <th>Assigned To</th>
                                <th>PO Qty</th>
                                <th>Created By</th>                                
                                <th>Created Date</th>                
                                <th>Action</th>

                            <?php } ?>
                                
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="7" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


