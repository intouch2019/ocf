<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_creditnote extends cls_renderer{

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
                $this->status = 0;
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
     var url = "ajax/tb_creditnote.php?status="+<?php echo $this->status; ?>   
     //var url = "ajax/tb_purchase_order.php";   
    //alert(url); 
    oTable = $('#tb_creditnote').dataTable( {
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
    
function changeStatus(status){
    window.location.href = "creditnote/status="+status;
}

function createSale(){
//    alert("here");
    window.location.href = "creditnote/create";
}

function editCN(id){
    window.location.href = "creditnote/additem/cnid="+id; 
}

function showPDF(cnid){
    //alert('ajax/printCreditNote.php?cnid='+cnid);
    var myWindow = window.open('',"_blank");
    myWindow.location.href = 'ajax/printCreditNote.php?invid='+cnid;                       
    myWindow.focus();    
}


</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "sales";
            include "sidemenu.".$this->currStore->usertype.".php";
//            $dbl = new DBLogic();            
            $array = CreditNoteStatus::getAll();
            //print_r($poarray);
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select id="statussel" name="statussel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="changeStatus(this.value);">
                <option value=""></option>
                <?php foreach($array as $key => $value){ 
                    $selected = "";
                    if($key == $this->status){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <!--<button type="button" class="btn btn-primary pull-right" onclick="uploadProdFn();">Upload Products</button>-->
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="createSale();">Create New Credit Note</button>
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Credit Note List</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_creditnote" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Credit Note No</th>
                                <th>Credit Note Date</th>
                                <th>Customer Name</th>
                                <th>Customer Phone</th>
                                <th>Total Quantity</th>
                                <th>Credit Note Value</th>                                                                
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

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


