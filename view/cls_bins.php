<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_bins extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
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
	
	************************************************************************************************************/	

</script> -->
<script type="text/javaScript">    
$(function(){      
    var url = "ajax/tb_bins.php";
//    alert(url);
    oTable = $('#tb_bins').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,{bSortable:false}],
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

function createBinFn(){
    window.location.href = "bin/create";
}
function editBin(bid){
      window.location.href = "bin/edit/bid="+bid;
}
</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "bins";//pagecode
            include "sidemenu.php";    
?>
<div class="container-section">
    <div class="row">
        <div class="col-md-12">
            <!--<a href="create/user" class="btn btn-primary" role="button" style="width:150px;height: 30px;">Create User</a><br><br>-->
            <button type="button" class="btn btn-primary" onclick="createBinFn();">Create Bin</button>
    <br/><br/>
        </div>
    </div>
    <div class="row">
        
        <div class="col-md-12">
            <div class="panel panel-default">
                <h5>&nbsp;&nbsp;<b>Bins Master List</b></h5>
                <div class="common-content-block"> 
                    <table id="tb_bins" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>               
                                <th>Location</th>
                                <th>bin</th>
                                <th>Createtime</th>      
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
</div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


