<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_challans_in extends cls_renderer{

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
            if(isset($this->params["challanstatus"]) != ""){
                $this->StockTransferChallanStatus = $this->params["challanstatus"];
            }else if($this->currStore->usertype == UserType::PurchaseOfficer){
                $this->StockTransferChallanStatus = 1;
            }else{
                $this->StockTransferChallanStatus = 2;
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
    var url = "ajax/tb_challans.php?status="+<?php echo $this->StockTransferChallanStatus; ?>   
    // alert(url);
    oTable = $('#tb_challanin').dataTable( {      
	"bProcessing": true,  
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,null,null,null,null,{bSortable:false}],  
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
    window.location.href = "stocktransfer/create"; 
}     
  
function pullChallan(id){
    window.location.href = "cr/challan/pull/challanid="+id;
}    

function viewChallan(id){
    window.location.href = "cr/challan/pull/challanid="+id;
}

function editChallan(id,challan_id){
    // alert(po_alloc_id);
    window.location.href = "challan/additem/transferid="+id+"/challan_id="+challan_id;
}

function changeStatus(status){  
    //alert(status);
    window.location.href = "challans/in/challanstatus="+status;
}
 

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "challanin";
            include "sidemenu.php";
//            $dbl = new DBLogic();            
            $array = StockTransferChallanStatus::getAll();
            //print_r($array);
?>

                      
<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="stockstatussel" name="stockstatussel" class="selectpicker form-control" data-show-subtext="true" 
                    data-live-search="true" onchange="changeStatus(this.value);">
                <!-- <option value=""></option> -->
                <?php foreach($array as $key => $value){
                        if($this->currStore->usertype == UserType::RFC && $key  == StockTransferChallanStatus::BeingCreated){
                        }else{
                            $selected = "";
                        if($key == $this->StockTransferChallanStatus){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                        }                         
                    <?php }}?>
            </select>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <!--<button type="button" class="btn btn-primary pull-right" onclick="uploadProdFn();">Upload Products</button>-->
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Delivery Note List</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_challanin" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Delivery Note No</th>
                                <th>PO Allocation No</th>
                                <th>Purchase Order No</th>
                                <th>Supplier</th>
                                <th>Stockist</th>
                                <th>PO Total Qty</th>
                                <th>PO Allocation Qty</th>
                                <th>Delivery Note Qty</th>
                                <th>Created by</th>                                
                                <th>Created Date</th>                                                                
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="10" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>
             
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


