<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_products extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
       
        function __construct($params=null) {
            $this->currStore = getCurrStore();
            //print_r($this->currStore);
            //echo $this->currStore->usertype;
            $this->params = $params;
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
    var url = "ajax/tb_products.php";
    //alert(url); 
    oTable = $('#tb_products').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,{bSortable:false},null],
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
    
function createProduct(){
    window.location.href = "product/create";
}    

function uploadProducts(){
    window.location.href = "products/upload";
}    

function editProduct(id){
    window.location.href = "product/edit/prodid="+id;
}    

function excelExport(){
    window.location.href = "formpost/generateProdExcel.php";
} 

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "products";
            include "sidemenu.".$this->currStore->usertype.".php";
//            $dbl = new DBLogic();            
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="excelExport();">Export to Excel</button>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="uploadProducts();">Upload Products</button>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="createProduct();">Create New Product</button>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Product Master List </b></h7>
                <div class="common-content-block">                     
                    <table id="tb_products" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>ID</th>
                                <th>Category</th>
                                <th>Product Description</th>
                                <th>Specification</th>
                                <th>Status</th>
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
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


