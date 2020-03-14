<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_product_stock_approve extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $crid = -1;
        var $status = 0;
        var $uploaddate = "";
        var $currCRId = -1;
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->currCRId = getCurrStoreId();
            $this->params = $params;
            if(isset($this->params["crid"]) != ""){
                $this->crid = $this->params["crid"];
            }
            if(isset($this->params["status"]) != ""){
                $this->status = $this->params["status"];
            }
            if(isset($this->params["uploaddate"]) != ""){
                $this->uploaddate = $this->params["uploaddate"];
            }
            
        }

function extraHeaders() { ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style type="text/css" title="currentStyle">
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
$(function(){ 
    
    var url = "ajax/tb_product_stock_approve.php?crid="+<?php if($this->currStore->usertype == UserType::RFC){ echo $this->currCRId; }else{ echo $this->crid; }?>+"&status="+<?php if($this->currStore->usertype == UserType::RFC){ echo ProductPriceStatus::Approved; }else{ echo $this->status; }?>;
     
    oTable = $('#tb_product_pricing').dataTable( { 
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,null,null,null,null,null],
	"sAjaxSource": url,
        'iDisplayLength': 10,
        "aaSorting": []
    } );
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){ 
	if (e.which == 13){                     
		oTable.fnFilter($(this).val(), null, false, true);
	}
    });    
});
     


function approve(prodid,id,addedstock,approvestatus){ 
  
   
    var r = confirm("Are you sure you want to approve the price");
    if(r){
            var crid = $("#crsel").val();
            var status = $("#statussel").val();
     var ajaxURL = "ajax/approveStockAdjustment.php?crid="+crid+"&id="+id+"&prodid="+prodid+"&addedstock="+addedstock;
       
         $.ajax({
         url:ajaxURL,
             dataType: 'json',
             success:function(data){
                 
                 if (data.error == "0") {
                     alert(data.msg);
                     window.location.href = "product/stock/approve/crid="+crid+"/status="+approvestatus;
                 } else {
                     alert(data.msg);
                     
                 }
             }
         });
    }    
}


function disapprove(prodid,id,disapprovestatus){ 
   
    var uploaddate = $("#dates").val();
    var r = confirm("Are you sure you want to disapprove the price");
    if(r){
    var crid = $("#crsel").val();
    var status = $("#statussel").val();
        
     var ajaxURL = "ajax/disapproveStockAdjustment.php?crid="+crid+"&id="+id+"&prodid="+prodid+"&uploaddate="+uploaddate;
             $.ajax({
             url:ajaxURL,
             dataType: 'json',
             success:function(data){
                 if (data.error == "1") {
                     alert(data.msg);
                 } else {
                     window.location.href = "product/stock/approve/crid="+crid+"/status="+disapprovestatus;
                 }
             }
         });
    }    
}

function selectCR(){
    var crid = $("#crsel").val();
    var status = $("#statussel").val();
    window.location.href = "product/stock/approve/crid="+crid+"/status="+status;
    
}

function statusChange(){
    var crid = $("#crsel").val();
    var status = $("#statussel").val();
    
    if(crid >= 0){
        window.location.href = "product/stock/approve/crid="+crid+"/status="+status;
    }else{
        window.location.href = "product/stock/approve/status="+status;
    }
} 


</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            
            $menuitem = "products";
//            include "sidemenu.".$this->currStore->usertype.".php";
            include "sidemenu.php";
            $dbl = new DBLogic();            

            $obj_crs = $dbl->getCRList();
            $obj_categories = $dbl->getAllActiveCategories();
            $obj_products = $dbl->getAllActiveProducts();
            $obj_suppliers = $dbl->getAllActiveSuppliers();
            $obj_specifications = $dbl->getAllActiveSpecifications();
         
            
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="crsel" name="crsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" 
                    onchange="selectCR(this.value);">
                <option value="-1">Select CR</option>
                <option value="0" <?php if($this->crid == 0){ ?> selected <?php }?>>All</option>
                <?php  
                foreach($obj_crs as $cr){ $selected = ""; 
                    if($this->crid == $cr->id){ $selected = "selected"; }?>
                   <option value="<?php echo $cr->id?>" <?php echo $selected;?>><?php echo strtoupper($cr->dispname); ?></option>   
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <select id="statussel" name="statussel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" 
                    onchange="statusChange(this.value);">
                <?php $statusarray = ProductPriceStatus::getAll();
                foreach($statusarray as $key => $value){ 
                    $selected = "";
                    if($key == $this->status){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <?php if(ProductPriceStatus::Pending == $this->status){ ?>

            <?php }?>
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Today's Product Master</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_product_pricing" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Desc 1</th>
                                <th>Desc 2</th>
                                <th>Thickness</th>
                                <th>Length</th>
                                <th>Current Stock</th>
                                <th>Added stock</th>
                                <th>Total</th>
                                <th>Date</th>
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
        

            <?php 
	}
}
?>


