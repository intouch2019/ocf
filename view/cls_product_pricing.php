<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_product_pricing extends cls_renderer{

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
        var $currCRId = -1;
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->currCRId = getCurrStoreId();
            $this->params = $params;
            $this->status = 0;
            if(isset($this->params["crid"]) != ""){
                $this->crid = $this->params["crid"];
            }else{
                if($this->currStore->usertype == UserType::RFC){
                  $this->crid =   $this->currStore->crid;
                }
            }
            if(isset($this->params["status"]) != ""){
                $this->status = $this->params["status"];
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
    var uploaddate = $("#dates").val();
    var url = "ajax/tb_product_pricing.php?crid="+<?php if($this->currStore->usertype == UserType::RFC){ echo $this->crid; }else{ echo $this->crid; }?>+"&status="+<?php if($this->currStore->usertype == UserType::RFC){ echo ProductPriceStatus::Approved; }else{ echo $this->status; }?>+"&uploaddate="+uploaddate;
   // alert(url); 
    oTable = $('#tb_product_pricing').dataTable( {    
	"bProcessing": true, 
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,null,null,null],
	"sAjaxSource": url,
        'iDisplayLength': 500,  
        "aaSorting": []
    } );
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
	if (e.which == 13){                     
		oTable.fnFilter($(this).val(), null, false, true);
	}
    });     
});
     
function uploadProductPrice(){
    window.location.href = "product/pricing/upload";
}    

function editProduct(id){
    window.location.href = "product/edit/prodid="+id;
}     

function selectCR(){
    var crid = $("#crsel").val();
    var status = $("#statussel").val();
    window.location.href = "product/pricing/crid="+crid+"/status="+status;
}

function statusChange(){
    var crid = $("#crsel").val();
    var status = $("#statussel").val();
    //alert(crid+" "+status);
    if(crid >= 0){
        window.location.href = "product/pricing/crid="+crid+"/status="+status;
    }else{
        window.location.href = "product/pricing/status="+status;
    }
} 
function dateChange(date){
    var crid = $("#crsel").val();
    var status = $("#statussel").val();
    var uploaddate= $("#dates").val();
       alert(crid+" "+status);
    if(crid >= 0){
        window.location.href = "product/pricing/crid="+crid+"/status="+status+"/uploaddate="+uploaddate;
    }else{
        window.location.href = "product/pricing/status="+status;
    }
}
function genPricingExcelRep(){

   var crid = <?php echo $this->crid;?>;
  
  
    

   window.location.href="formpost/genCRProductPricingExcel.php?CRid="+crid;
       
      
}

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "products";
            include "sidemenu.".$this->currStore->usertype.".php";
            $dbl = new DBLogic();

            $obj_crs = $dbl->getCRList();
            $obj_dates = $dbl->getPriceApprovalDates($this->crid);
            
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <?php if($this->currStore->usertype == UserType::HO){?>
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
            <?php }?>
        </div>
        <div class="col-md-3">
            <?php if($this->currStore->usertype == UserType::HO || $this->currStore->usertype == UserType::RFC){?>
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
            <?php }?>
        </div>
        <div class="col-md-3">
            <?php if($this->currStore->usertype == UserType::HO || $this->currStore->usertype == UserType::RFC){?>
            <select id="dates" name="dates" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" 
                    onchange="dateChange(this.value);">
                <?php foreach($obj_dates as $date){ ?>
                    <option value="<?php echo ($date->applicable_date);?>"><?php echo $date->applicable_date;?></option>
                <?php } ?>
            </select>
            <?php }?>
        </div>
        <div class="col-md-3">
            <?php if($this->currStore->usertype == UserType::HO){?>
            <button type="button" class="btn btn-primary pull-right" onclick="uploadProductPrice();">Upload Product Price</button>
            <?php }?>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="genPricingExcelRep();">Export to Excel</button>
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
                                <th>Category</th>
                                <th>Short Name</th>
                                <th>Desc 1</th>
                                <th>Desc 2</th>
                                <th>Thickness</th>
                                <th>Price</th>
                                <th>Date</th>
                                <th>Approval Status</th>
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
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" />       
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


