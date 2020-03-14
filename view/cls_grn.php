<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_grn extends cls_renderer{

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
            if(isset($this->params["grnstatus"]) != ""){
                $this->grnstatus = $this->params["grnstatus"];
            }else if($this->currStore->usertype == UserType::Director){
                $this->grnstatus = 1;
            }else{
                $this->grnstatus = 0;
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
    var url = "ajax/tb_grn.php?status="+<?php echo $this->grnstatus; ?>  
    //alert(url);
    oTable = $('#tb_grntable').dataTable( {  
	"bProcessing": true,  
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,null,null,null,{bSortable:false}], 
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
    
function createGRN(){ 
    window.location.href = "grn/create";  
}     
  
function editGRN(grnid,uom){ 
    window.location.href = "grn/additem/grnid="+grnid+"/uom="+uom;
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
                     window.location.href = "grn/grnstatus="+<?php echo GRNStatus::Deleted;?>;
                 }
             }
         });
    }
}

function changeStatus(status){ 
    window.location.href = "grn/grnstatus="+status; 
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "grn";
            include "sidemenu.".$this->currStore->usertype.".php";
//            $dbl = new DBLogic();            
            $array = GRNStatus::getAll();
            //print_r($poarray);
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="grnstatussel" name="grnstatussel" class="selectpicker form-control" data-show-subtext="true" 
                    data-live-search="true" onchange="changeStatus(this.value);">
                <option value=""></option>
                <?php foreach($array as $key => $value){ 
                    if($this->currStore->usertype == UserType::Director){ 
                        if($key == GRNStatus::Created || $key == GRNStatus::Deleted){
                        $selected = "";
                        if($key == $this->grnstatus){ $selected = "selected"; }?>
                        <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                    <?php }}else{
                    $selected = "";
                    if($key == $this->grnstatus){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php }} ?>
            </select>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <!--<button type="button" class="btn btn-primary pull-right" onclick="uploadProdFn();">Upload Products</button>-->
        </div>
        <?php if($this->currStore->usertype == UserType::GRN || $this->currStore->usertype == UserType::PurchaseOfficer){ ?>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="createGRN();">Create New GRN</button>
        </div>
        <?php }?>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;GRN List</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_grntable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>GRN No</th>
                                <th>Supplier</th>
                                <th>PO Number</th>
                                <th>Invoice Number</th>
                                <th>Invoice Date</th>
                                <th>GRN Qty</th>
                                <th>GRN Value</th>
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


