<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_stock extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $locid ="";
        var $binid ="";
       
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
//               print_r($params);
		$this->params = $params;
	        
                if($params && isset($params['locid'])){
                    $this->locid = $params['locid'];
//                    print  $this->locid;
                }
                if($params && isset($params['binid'])){
                    $this->binid = $params['binid'];
//                    print $this->binid;
                }
        }

	function extraHeaders() {
        ?>
<style type="text/css" title="currentStyle">
          /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
        </style>
<!-- <script src="js/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajax-dynamic-list.js">
	/************************************************************************************************************
	(C) www.dhtmlgoodies.com, April 2006
	
	This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	
	
	Terms of use:
	You are free to use this script as long as the copyright message is kept intact. However, you may not
	redistribute, sell or repost it without our permission.
	
	Thank you!
	
	www.dhtmlgoodies.com
	Alf Magne Kalleland
	
	********************************uploadStockFn****************************************************************************/	

</script> -->
<script type="text/javaScript">    
$(function(){  
    var url = "ajax/tb_stock.php?locid=<?php echo $this->locid;?>&binid=<?php echo $this->binid ;?>";
//    alert(url);
    oTable = $('#tb_stock').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null],
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
function loadlocwise(loc_id){
    window.location.href="stock/locid="+loc_id;
}
function loadbinwise(bin_id){
   var loc_id = $("#locsel").val();
   if(loc_id ==null || loc_id == "0"){
      alert("Please select Location first"); 
   }
//   alert("stock/locid="+loc_id+"&binid="+bin_id);
   window.location.href="stock/locid="+loc_id+"/binid="+bin_id;
}
function uploadStockFn(){
     window.location.href = "stock/upload";
//   var loc_id = $("#locsel").val();
//   var bin_id = $("#binsel").val();
//   if(loc_id == "" || loc_id == null || loc_id == "0"){
//      alert("Please select Location"); 
//   }else if(bin_id =="" || bin_id ==null || bin_id == "0"){
//       alert("Please select Bin"); 
//   }else{
//        window.location.href = "stock/upload/locid="+loc_id+"/binid="+bin_id;
//    }
}

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "stock";//pagecode
            include "sidemenu.php";    
            $db = new DBConn();
?>
<div class="container-section">
     <div class="row">
        <div class="col-md-4">
            <select class="form-control" id="locsel" name="locsel" onchange="loadlocwise(this.value);" >
                <option value="">All Location</option>
                <!--<option value="" disabled selected>Select Location</option>-->
                <?php
                $query = "select id,name from it_locations";
//                                                    echo $query;
                $lobjs = $db->fetchObjectArray($query);
                if (isset($lobjs)) {
                    foreach ($lobjs as $lobj) {
                        if($lobj->id == $this->locid){ $selected="selected";}else{ $selected = "";}
                        ?>
                        <option value="<?php echo $lobj->id ?>"<?php echo $selected; ?>><?php echo $lobj->name; ?></option>
                    <?php
                    }
                }
                ?>
            </select> 
        </div>
        <div class="col-md-4">
           <select class="form-control" id="binsel" name="binsel"onchange="loadbinwise(this.value);" >
                <option value="">All Bin</option>
                <!--<option value="" disabled selected>Select Bin</option>-->
                <?php
                $query = "select id,bin from it_bins where location_id = $this->locid";
//                echo $query;
                $bobjs = $db->fetchObjectArray($query);
                if (isset($bobjs)) {
                    foreach ($bobjs as $bobj) {
                         if($bobj->id == $this->binid){ $selected="selected";}else{ $selected = "";}
                        ?>
                        <option value="<?php echo $bobj->id ?>"<?php echo $selected; ?>><?php echo $bobj->bin; ?></option>
                    <?php
                    }
                }
                ?>
            </select> 
            <br>
        </div>
    <!--</div>-->
    <!--<div class="row">-->
        <div class="col-md-4">
            <!--<a href="create/user" class="btn btn-primary" role="button" style="width:150px;height: 30px;">Create User</a><br><br>-->
            <button type="button" class="btn btn-primary" style="float:right" onclick="uploadStockFn();">Upload Stock</button>
            <br/><br/>
        </div>
    </div>
    <div class="row">
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="common-content-block"> 
                    <table id="tb_stock" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>               
                                <th>Location</th>
                                <th>Bin</th>
                                <th>Product</th>      
                                <th>Quantity(In Packets)</th>
                                <!--<th>UOM</th>-->
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
</div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}


