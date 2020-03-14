<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_stock_in extends cls_renderer{

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
            }else if($this->currStore->usertype == UserType::RFC){
                $this->StockTransferStatus = 2;
            }else{
                $this->StockTransferStatus = 1;
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
    var url = "ajax/tb_stockin.php?status="+<?php echo $this->StockTransferStatus; ?>   
    //alert(url);
    oTable = $('#tb_stockin').dataTable( {      
	"bProcessing": true,  
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,null,{bSortable:false}],  
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
  
function pullStockTransfer(id){
    window.location.href = "cr/stock/pull/transferid="+id;
}    


function changeStatus(status){  
    //alert(status);
    window.location.href = "stock/in/stockstatus="+status;
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "stocktin";
            include "sidemenu.".$this->currStore->usertype.".php";
//            $dbl = new DBLogic();            
            $array = StockTransferStatus::getAll();
            //print_r($array);
?>

                      
<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="stockstatussel" name="stockstatussel" class="selectpicker form-control" data-show-subtext="true" 
                    data-live-search="true" onchange="changeStatus(this.value);">
                <option value=""></option>
                <?php foreach($array as $key => $value){ 
                    if($this->currStore->usertype == UserType::RFC){ 
                        if($key == StockTransferStatus::AwaitingIn || $key == StockTransferStatus::Completed){
                        $selected = "";
                        if($key == $this->StockTransferStatus){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                    <?php }} }?>
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
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Stock Transfer List</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_stockin" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Stock Transfer No</th>
                                <th>From Location</th>
                                <th>To Location</th>
                                <th>Qty</th>
                                <th>Value</th>
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


