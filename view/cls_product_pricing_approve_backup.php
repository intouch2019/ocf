<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_product_pricing_approve extends cls_renderer{

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
    var url = "ajax/tb_product_pricing.php";
    //alert(url);
    oTable = $('#tb_product_pricing').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,{bSortable:false},null,null],
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
    
function uploadProductPrice(){
    window.location.href = "product/pricing/upload";
}    

function editProduct(id){
    window.location.href = "product/edit/prodid="+id;
}    


</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "products";
            include "sidemenu.".$this->currStore->usertype.".php";
            $dbl = new DBLogic();            
            $obj_price_list = $dbl->getCurrentProductPriceList()
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <!--<button type="button" class="btn btn-primary pull-right" onclick="uploadProdFn();">Upload Products</button>-->
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary pull-right" onclick="uploadProductPrice();">Upload Product Price</button>
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Today's Product Master</b></h7>
                <!--<div class="panel-body">-->
                <div class="common-content-block"> 

                    
                <!--<div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <td>Sl.No.</td>
                                <td>Category</td>
                                <td>Product Description</td>
                                <td>Specification</td>
                                <td>Price</td>
                                <td>Applicable Date</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $slno = 0;
                            if($obj_price_list != NULL){
                            foreach($obj_price_list as $item){ 
                                $slno++;?>
                            <tr>
                                <td><?php echo $slno;?></td>
                                <td><?php echo $item->ctg;?></td>
                                <td><?php echo $item->itemname;?></td>
                                <td><?php echo $item->spec;?></td>
                                <td><?php echo $item->price;?></td>
                                <td><?php echo ddmmyy($item->applicable_date);?></td>
                                <td>
                                    <input type="radio" name="approve" value="approve"/>Approve<br>
                                    <input type="radio" name="approve" value="disapprove"/>Dis Approve
                                </td>
                            </tr>
                            <?php }} ?>
                        </tbody>
                    </table>
                </div>-->
         </div>
        <!--</div>-->
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


