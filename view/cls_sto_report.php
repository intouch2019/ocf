<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_sto_report extends cls_renderer{
    

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
            $dbl = new DBLogic();
            if(isset($this->params["crid"]) != ""){
                $this->crid = $this->params["crid"];
                echo $this->crid;
            }else if($this->currStore->usertype == UserType::RFC){
                 $objcr = $dbl->getCRDetailsByUserId($this->currStore->id);
             
                 $this->crid = $objcr->id;
            }else{
                $this->crid = 1;
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
    var url = "ajax/tb_sto_report.php" 
//    alert(url);
    
    oTable = $('#tb_crsalesreporttable').dataTable( {      
	"bProcessing": true, 
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null], 
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
}); 

function genAggSTOReport(){ 
        window.location.href="formpost/genSTOHeaderReport.php";
}

function genDetailsSTORep(){

        window.location.href="formpost/genSTODetailsReport.php";
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "storeport";
            include "sidemenu.php";
            $dbl = new DBLogic();
            $obj_cr_master = $dbl->getCRList();
            
         
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-6">
            <button type="button" class="btn btn-primary pull-left" onclick="genAggSTOReport();">Aggregate STO Report</button>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-primary pull-right" onclick="genDetailsSTORep();">STO Details Report</button>
        </div>
        

    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Stock Transfer Order Report</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_crsalesreporttable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Transfer No.</th>
                                <th>Transfer Date</th>
                                <th>From location</th>
                                <th>To location</th>
                                <th>Total Qty(MT)</th>
                                                   
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
           <?php 
	}
}
?>




