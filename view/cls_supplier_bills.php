<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_supplier_bills extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $status = "";
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->params = $params;
            if(isset($this->params["status"]) != ""){
                $this->status = $this->params["status"];
            }else{
                $this->status = SupplierBillStatus::Open;
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
    var url = "ajax/tb_supplier_bills.php?status="+<?php echo $this->status; ?>  
     //var url = "ajax/tb_purchase_order.php"; 
    //alert(url);
    oTable = $('#tb_bills').dataTable( {
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
      
function deleteSupplierBill(id){
    var r = confirm("Are you sure you want to delete the Bill");
    if(r){
     var remarks = $('#remarks').val(); 
     var ajaxURL = "ajax/deleteSupplierBill.php?id=" + id;
         //alert(ajaxURL);
         $.ajax({
         url:ajaxURL,
             dataType: 'json',
             success:function(data){
                 if (data.error == "1") {
                     alert(data.msg);
                 } else {
                     window.location.href = "supplier/bills/status="+<?php echo SupplierBillStatus::Deleted;?>;
                 }
             }
         });
    }
}

function editSupplierBill(id){
    window.location.href = "supplier/bill/item/entry/billid="+id;
}     

function viewSupplierBill(id){
    window.location.href = "supplier/bill/view/billid="+id;
} 

function changeStatus(status){
    window.location.href = "supplier/bills/status="+status;
}

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "supplierbill";
            include "sidemenu.".$this->currStore->usertype.".php";
//            $dbl = new DBLogic();            
            //$poarray = POStatus::getAll();
            //print_r($poarray);
            $sbbillarray = SupplierBillStatus::getAll();
            
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="statussel" name="statussel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="changeStatus(this.value);">
                <option value=""></option>
                <?php foreach($sbbillarray as $key => $value){ 
                    $selected = "";
                    if($key == $this->status){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Supplier Bill's</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_bills" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Supplier</th>
                                <th>Bill no</th>
                                <th>Bill Date</th>
                                <th>Gate Entry No</th>                                
                                <th>PO No</th>
                                <th>Created by</th>                                
                                <th>Created Date</th>                                                                
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="8" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
             </div>
         </div>
     </div>
 </div>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


