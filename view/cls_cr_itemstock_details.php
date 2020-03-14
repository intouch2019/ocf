<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_cr_itemstock_details extends cls_renderer{

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
      @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
      @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
</style>
<script type="text/javaScript">    
   
  $crID=0;
function generateReport(){
    
    var crid = $('#crid').val();
    $crID=crid;
        
    if(crid == "-1"){
        alert("Please select CR first"); 
    }else{ 
    var url = "ajax/tb_crstockdetails.php?crid="+crid;         
//    alert(url);
    oTable = $('#tb_sales').dataTable( {     
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,null,null,{bSortable:false}],
	"sAjaxSource": url,
        "aaSorting": [],
        "destroy" : true
    } ); 
    }
}

function changeStatus(status){
    window.location.href = "cr/itemstock/details/status="+status;
}
function getNumValue(status){
    window.location.href = "cr/itemstock/details/status="+status;
}

function updateStock(id){
       var prodId=id 
       var crid = $('#crid').val();
       var addQty = $('#t_'+id).val();
       
       alert("reuest sent");
                      
    var ajaxURL = 'ajax/updateStock.php?prodId='+prodId+'&crid='+crid+'&addQty='+addQty;      
//    alert(ajaxURL);
                  $.ajax({
                          url:ajaxURL, 
                          dataType:'JSON',
                          success:function (ress){
                                 
                            } 
                      });
                   }

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />

<?php }

        public function pageContent() {
            $menuitem = "sales";
            include "sidemenu.".$this->currStore->usertype.".php";
            $dbl = new DBLogic();            
            $obj_crs = $dbl->getCRList();
            $array = InvoiceStatus::getAll();
            
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
<!--        
                <?php foreach($array as $key => $value){ 
                    $selected = "";
                    if($key == $this->status){ $selected = "selected"; }?>
                    <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                <?php } ?>
            </select>
        </div>-->
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary" onclick="generateReport();">Generate Details</button>
        </div>
        <div class="col-md-3">
            
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;CR Stock Details</b></h7>
                <div class="common-content-block">                     
                    <table id="tb_sales" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>Name</th>
                                <th>createdtime</th>
                                <th>stockQty</th>
                                <th>desc1</th>
                                <th>desc2</th>
                                <th>thickness</th>
                                <th>length</th>
                                <th style="width:5%">Add Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="9" class="dataTables_empty">Loading data from server</td>
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


