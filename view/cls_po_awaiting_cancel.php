<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_po_awaiting_cancel extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $postatus = "";
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["postatus"]) != ""){
                $this->postatus = $this->params["postatus"];
            }else{
                $this->postatus = POStatus::AwaitingCancel;
            }
        }

function extraHeaders() { ?>
<style type="text/css" title="currentStyle">
    /*  @import "js/datatables/media/css/demo_page.css";
      @import "js/datatables/media/css/demo_table.css";*/
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
$(function(){     
    var url = "ajax/tb_purchase_order_cancel.php?status="+<?php echo $this->postatus; ?>   
     //var url = "ajax/tb_purchase_order.php"; 
    //alert(url);
    oTable = $('#tb_pos').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,{bSortable:false}],
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
     
function createPO(){
    window.location.href = "po/create";
}    

function editPO(poid){
    window.location.href = "po/additem/poid="+poid;
}    

function editSupplier(id){
    window.location.href = "supplier/edit/suppid="+id;
}     

function changeStatus(status){
    window.location.href = "po/postatus="+status;
}

function viewPO(poid){
    window.location.href = "po/approvecancel/poid="+poid;
}

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "approvepo";
            include "sidemenu.".$this->currStore->usertype.".php";
//            $dbl = new DBLogic();            
            $poarray = POStatus::getAll();
            //print_r($poarray);
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <!--<button type="button" class="btn btn-primary pull-right" onclick="uploadProdFn();">Upload Products</button>-->
        </div>
        <div class="col-md-3">
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Purchase Order Awaiting cancel</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_pos" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>PO No</th>
                                <th>Supplier</th>
                                <th>PO Qty</th>
                                <th>PO Value</th>
                                <th>Created by</th>                                
                                <th>Created Date</th>                                                                
                                <th>Action</th>
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
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              -->
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


