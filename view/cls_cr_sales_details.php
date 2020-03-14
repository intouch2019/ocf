<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_cr_sales_details extends cls_renderer{

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
                $this->status = 1;
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
   
  
function generateReport(){
    //var daterange = $('#datepicker').val();
    var crid = $('#crid').val();
    var status = $('#statussel').val();
    if(crid == "-1"){
        alert("Please select CR first"); 
    }else{ 
    var url = "ajax/tb_crsalesdetails.php?crid="+crid+"&status="+status;         
    //alert(url); 
    oTable = $('#tb_sales').dataTable( {     
	"bProcessing": true,
	"bServerSide": true,
        //"aoColumns": [null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,{bSortable:false}],
        "aoColumns": [null,null,null,null,null,null,{bSortable:false}],
	"sAjaxSource": url,
        "aaSorting": [],
        "destroy" : true
    } );
    }
}

function changeStatus(status){
    window.location.href = "cr/sales/details/status="+status;
}

//function showCRPDF(salesid,crid){
//    var myWindow = window.open('',"_blank");
//    myWindow.location.href = 'ajax/printInvoicepdf.php?invid='+salesid+'&crid='+crid;
//    myWindow.focus();    
//}

function showPDF(salesid,crid){
   
    var myWindow = window.open('',"_blank");
    myWindow.location.href = 'ajax/printBillPDFTst.php?invid='+salesid+'&crid='+crid;                       
    myWindow.focus();    
}
function showPDFOld(salesid,crid){
    
    var myWindow = window.open('',"_blank");
    myWindow.location.href = 'ajax/printBillPDFTstOld.php?invid='+salesid+'&crid='+crid;                       
    myWindow.focus();    
}

function showPDFOLD(salesid,crid){
    var myWindow = window.open('',"_blank");
    myWindow.location.href = 'ajax/printBillPDFTst_old.php?invid='+salesid+'&crid='+crid;                       
    myWindow.focus();    
} 

function showPDFJAN(salesid,crid){
   
    var myWindow = window.open('',"_blank");
    myWindow.location.href = 'ajax/printBillPDFTst_jan.php?invid='+salesid+'&crid='+crid;                       
    myWindow.focus();
}


</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "sales";
            include "sidemenu.".$this->currStore->usertype.".php";
            $dbl = new DBLogic();            
            $obj_crs = $dbl->getCRList();
            $array = InvoiceStatus::getAll();
            //print_r($poarray);
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="crid" name="crid" class="selectpicker form-control" data-show-subtext="true"
                    data-live-search="true" >
                <option value="-1">Select CR</option>
                  <?php foreach($obj_crs as $crmaster){
                     $selected = "";
                    if($crmaster->id == $this->crid){ $selected = "selected"; }?>
                     <option value="<?php echo $crmaster->id;?>" <?php echo $selected;?>"><?php echo $crmaster->dispname;?></option>
                  <?php } ?>
               </select>        
        </div>
        <div class="col-md-3">
            <select id="statussel" name="statussel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" >
                <option value=""></option>
                <?php foreach($array as $key => $value){ 
                    $selected = "";
                    if($key == $this->status){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary" onclick="generateReport();">Generate Details</button>
        </div>
        <div class="col-md-3">
            <!--<button type="button" class="btn btn-primary pull-right" onclick="uploadProdFn();">Upload Products</button>-->
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;CR Sales Details</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_sales" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Customer Name</th>
                                <th>Customer Phone</th>
                                <th>Total Quantity</th>
                                <th>Invoice Value</th>                                                                
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

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


