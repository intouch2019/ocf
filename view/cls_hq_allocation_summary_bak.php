<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once 'lib/db/DBLogic.php';

class cls_hq_allocation_summary extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $hid ="";

       
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
//               print_r($params);
		$this->params = $params;
	        
                if($params && isset($params['hid'])){
                    $this->hid= $params['hid'];
//                    print  $this->locid;
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
	
	***********************************************************************************************************/	

</script> -->
<script type="text/javaScript">    
$(function(){   
    var url = "ajax/tb_hqallocationsummary.php?hqid=<?php echo $this->hid;?>";
//      var url = "ajax/tb_hqallocation.php";
//    alert(url);
    oTable = $('#tb_allocation').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null],
//        "aoColumns": [{bSortable:false},{bSortable:false},{bSortable:false},{bSortable:false}],
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

function load(hqid){
//    alert("in fun");
    window.location.href="hq/allocation/summary/hid="+hqid;
}

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "hq_allocation_summary";//pagecode
            include "sidemenu.php";     
            $dbl = new DBLogic();
?>
<div class="container-section">
     <div class="row">
        <div class="col-md-4">
            <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php echo date('d-m-Y');  ?>">    
        </div>
        <div class="col-md-4">
            <select class="form-control" id="selhqallocation" name="selhqallocation" onchange="load(this.value);">
                <?php 
                $hqs = $dbl->getHQAllocation();
                if(!empty($hqs)){
                    ?>
                    <option value="">Select HQ Allocation</option>
                    <?php
                        foreach($hqs as $hq){ 
                            if(isset($hq) && !empty($hq) && $hq != null){
                                $selected = "";
                                if($hq->id == $this->hid){
                                    $selected = "selected";
                                }
                    ?>
                    <option value="<?php echo $hq->id;?>" <?php echo $selected; ?>><?php echo $hq->hq_no;?></option>
                <?php       } 
                        }
                }
                ?>
            </select>
        </div> 
        <div class="col-md-4">
            <!--<a href="create/user" class="btn btn-primary" role="button" style="width:150px;height: 30px;">Create User</a><br><br>-->
            <!--<button type="button" class="btn btn-primary" style="float:right" onclick="uploadStockFn();">Upload File</button>-->
            <br/><br/>
        </div>
    </div>
    <div class="row">
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="common-content-block"> 
                    <table id="tb_allocation" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>HQAllocation Number</th>
                                <th>Total Quantity In Packets</th>
                                <th>Total Quantity In Kgs</th>
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


