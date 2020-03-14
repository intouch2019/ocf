<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_cr_stock_report extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
        var $grnstatus = "";
        var $date;
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            $this->params = $params;
            
            $dbl = new DBLogic();
            if(isset($this->params["crid"]) != ""){
                $this->crid = $this->params["crid"];
            }else if($this->currStore->usertype == UserType::RFC){
                 $objcr = $dbl->getCRDetailsByUserId($this->currStore->id);
                 $this->crid = $this->currStore->crid;
            }else{
                $this->crid = 0;
            }
            
            if(isset($this->params["date"]) && $this->params["date"] != null){
                $this->date = $this->params["date"];
            }else{
                $this->date = "";
            }
            
//            print_r($this->date);
            
//            else if($this->currStore->usertype == UserType::DC){
//                $this->dcid = $this->currStore->id;
//            }else{
//                $this->dcid = 0;
//            }
                
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
//    var date = $("#date").val();  
    var date = <?php echo json_encode($this->date);?>;
//    alert(date);
    if(date == ""){ 
        var url = "ajax/tb_crstockreport.php?crid="+<?php echo $this->crid;?>;
    }else{
        var url = "ajax/tb_crstockreport.php?crid="+<?php echo $this->crid;?>+"&date="+date;
        $("#date").val(date);
    }
    
   // alert(url);
    oTable = $('#tb_crstockreporttable').dataTable( {     
	"bProcessing": true,  
	"bServerSide": true, 
        "aoColumns": [null,null,null,null,null,null,null,null], 
	"sAjaxSource": url,
        "aaSorting": [], 
        "iDisplayLength" : 50
    } );
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){ 
	if (e.which == 13){                     
		oTable.fnFilter($(this).val(), null, false, true);
	}
    });    
    
    $('#date').datepicker({  
             format: 'dd-mm-yyyy'
    });
});  

function getstock(crid){
    //alert("hereeee");
    window.location.href = "cr/stock/report/crid="+crid; 
}

function genAggExcelRep(){
   var uploaddate = $("#dates").val(); 
   var crid = <?php echo $this->crid;?>;
   var date = $("#date").val();
//   alert(crid); 
//   return;
    if(crid == 0){
        var cridSel = $("#dccode").val();
        if(cridSel == -1){
            alert("Please Select Consignment Retailer.");
        }else{
            crid = cridSel;
            if(date == ""){
            window.location.href="formpost/genAggCRStockSummayExcel.php?crid="+crid;
            }else{
                if(moment(date, 'DD-MM-YYYY',true).isValid()){
                    window.location.href="formpost/genAggCRStockSummayExcel.php?crid="+crid+"&date="+date;
                }else{
                    alert("Please select correct date format 'DD-MM-YYYY' ");
                }
            }
        }
    }else{
//        window.location.href="formpost/genAggCRStockSummayExcel.php?crid="+crid+"&uploaddate="+uploaddate;
            if(date == ""){
            window.location.href="formpost/genAggCRStockSummayExcel.php?crid="+crid;
            }else{
                if(moment(date, 'DD-MM-YYYY',true).isValid()){
                    window.location.href="formpost/genAggCRStockSummayExcel.php?crid="+crid+"&date="+date;
                }else{
                    alert("Please select correct date format 'DD-MM-YYYY' ");
                }
            }
    }
}

function genExcelRep(){
   var uploaddate = $("#dates").val(); 
   var crid = <?php echo $this->crid;?>;
   var date = $("#date").val();
   //alert(uploaddate);
    //var dtrange = $("#dateselect").val();
    if(crid == 0){
        var cridSel = $("#dccode").val();
        if(cridSel == -1){
            alert("Please Select Consignment Retailer.");
        }else{
            crid = cridSel;
            if(date == ""){
            window.location.href="formpost/genCRStockSummayExcel.php?crid="+crid;
            }else{
                if(moment(date, 'DD-MM-YYYY',true).isValid()){
                    window.location.href="formpost/genCRStockSummayExcel.php?crid="+crid+"&date="+date;
                }else{
                    alert("Please select correct date format 'DD-MM-YYYY' ");
                }
            }
        }
    }else{
        
        if(date == ""){
            window.location.href="formpost/genCRStockSummayExcel.php?crid="+crid;
        }else{
            if(moment(date, 'DD-MM-YYYY',true).isValid()){
                window.location.href="formpost/genCRStockSummayExcel.php?crid="+crid+"&date="+date;
            }else{
                alert("Please select correct date format 'DD-MM-YYYY' ");
            }
        }
    }   
}

function generateReport(){ 
        var date = $("#date").val();
        <?php if($this->currStore->usertype == UserType::RFC){ ?>
            var crid = <?php echo $this->crid;?>;
        <?php }else{ ?>
            var crid = $("#dccode").val();
         <?php } ?>   
        if(crid == -1 || crid == 0){
            alert("Please Select Consignment Retailer.");
            return;
        }
        // if(moment(date, 'DD-MM-YYYY',true).isValid()){
            window.location.href="cr/stock/report/crid="+crid;
            // window.location.href="cr/stock/report/date="+date+"/crid="+crid;
        // }else{
        //     alert("Please select correct date format 'DD-MM-YYYY' ");
        // }
        
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "stockreport";
//            include "sidemenu.".$this->currStore->usertype.".php";
            include "sidemenu.php";
            $dbl = new DBLogic();
            $obj_cr_master = $dbl->getCRList();
            if($this->crid != 0){
                $obj_stock = $dbl->getTotalStock($this->crid);
            }
            
//            $obj_dates = $dbl->getPriceApprovalDates($this->crid);
            //$array = StockTransferStatus::getAll();
            //print_r($array);
?>

<div class="container-section">
    <div class="row">
        <?php if($this->currStore->usertype != UserType::RFC) {?>
        <div class="col-md-3">
            <!--<select id="dccode" name="dccode" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="getstock(this.value);" >-->
            <select id="dccode" name="dccode" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" >
                <option value="-1">Select Stockist</option>
                  <?php foreach($obj_cr_master as $crmaster){
                     $selected = "";
                    if($crmaster->id == $this->crid){ $selected = "selected"; }?>
                     <option value="<?php echo $crmaster->id;?>" <?php echo $selected;?> ><?php echo $crmaster->dispname;?></option>
                  <?php } ?>
               </select>
        </div>
        
        <!-- <div class="col-md-3">
            <input type="text" autocomplete="off" placeholder="Select Date" class="form-control pull-right" id="date"/>
        </div> -->
        <div class="col-md-2">
                 <button type="submit" class="btn btn-primary" onclick="generateReport();">Generate Report</button>
        </div>
        <?php }?>
        <div class="col-md-2">
            <!-- <button type="button" class="btn btn-primary pull-right" onclick="genAggExcelRep();">Aggregate Stock Report</button> -->
        </div>
        <div class="col-md-2">
            <!-- <button type="button" class="btn btn-primary pull-right" onclick="genExcelRep();">Export to Excel</button> -->
        </div>
        
    </div>
    
    <br/>
    <div class="row"> 
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Stockist Stock List</b></h7>
                <?php if($this->crid != 0){ ?>
                <h7 class ="pull-right"><b>Total Stock : <?php echo $obj_stock->stock ?>&nbsp;&nbsp;&nbsp;&nbsp;</b></h7>
            <?php } ?>
                <div class="common-content-block">                     
                    <table id="tb_crstockreporttable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Attributes</th>
                                <th>Product Id</th>
                                <th>CV</th>
                                <th>PAC</th>
                                <th>Product weight(gm/case)</th>
                                <th>Price(Rs/case)</th>
                                <th>Batchcode</th>
                                <th>Qty</th>
<!--                                <th>Createtime</th>-->
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


