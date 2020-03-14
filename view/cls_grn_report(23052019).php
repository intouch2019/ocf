<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_grn_report extends cls_renderer{

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
            if(isset($this->params["dcid"]) != ""){
                $this->dcid = $this->params["dcid"];
            }else{
                 $this->dcid = 0;
            }
//            else if($this->currStore->usertype == UserType::DC){
//                $this->dcid = $this->currStore->id;
//            }else{
//                $this->dcid = 0;
//            }
                
        }

function extraHeaders() { ?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
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
    var url = "ajax/tb_grnreport.php?dcid="+<?php echo $this->dcid;?>       
    //alert(url);
    oTable = $('#tb_grnreporttable').dataTable( {    
	"bProcessing": true, 
	"bServerSide": true,
//        "aoColumns": [null,null,null,null,null,null,null,null,null,null,null,null,null,null,{bSortable:false}], 
        "aoColumns": [null,null,null,null,null,null,null,{bSortable:false}],   
	"sAjaxSource": url,
        "aaSorting": [],
        "iDisplayLength" : 10
    } );
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
	if (e.which == 13){                     
		oTable.fnFilter($(this).val(), null, false, true);
	}
    });    
}); 

function getstock(dcid){
    //alert("hereeee");
    window.location.href = "grn/report/dcid="+dcid; 
}

function genExcelRep(){
   var dccode = <?php echo $this->dcid;?>;
   //alert(dccode);
    //var dtrange = $("#dateselect").val();
    if(dccode == 0){
        alert("Please select DC first");
    }else{
        window.location.href="formpost/genGrnSummayExcel.php?dcid="+dccode; 
    }   
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "grnreports";
            include "sidemenu.".$this->currStore->usertype.".php";
            $dbl = new DBLogic();
            $obj_dc_master = $dbl->getAllDCMasters();
            
            //$array = StockTransferStatus::getAll();
            //print_r($array);
?>

<div class="container-section">
    <div class="row">
        
        <div class="col-md-3">
            <select id="dccode" name="dccode" class="selectpicker form-control" data-show-subtext="true"
                    data-live-search="true" onchange="getstock(this.value);" >
                <option value="-1">Select Distributer Center</option>
                  <?php foreach($obj_dc_master as $dcmaster){
                     $selected = "";
                    if($dcmaster->id == $this->dcid){ $selected = "selected"; }?>
                     <option value="<?php echo $dcmaster->id;?>" <?php echo $selected;?>"><?php echo $dcmaster->dc_name;?></option>
                  <?php } ?>
               </select>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="genExcelRep();">Export to Excel</button>
        </div>
        
        <?php //if($this->currStore->usertype == UserType::GRN){ ?>
<!--        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="createStockTransfer();">Create New Stock Transfer</button>
        </div>-->
        <?php// }?>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;DC Stock Report</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_grnreporttable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>DC</th>
                                <th>GRN No</th>
                                <th>Product</th>
                                <th>Batchcode</th>
<!--                                <th>Desc1</th>
                                <th>Desc2</th>
                                <th>Thickness</th>
                                <th>HSN</th>-->
                                <th>Qty</th>
                                <th>No of Pcs</th>
<!--                            <th>Rate(RS./Kg)</th>
                                <th>CGST</th>
                                <th>SGST</th>-->
                                <th>Value(RS)</th>
                                <th>Createtime</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="15" class="dataTables_empty">Loading data from server</td>
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


