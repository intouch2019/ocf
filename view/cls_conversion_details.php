<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_conversion_details extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid ="";
       
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
//               print_r($params);
		$this->params = $params;
	        
                if($params && isset($params['cid'])){
                    $this->cid = $params['cid'];
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
   var url = "ajax/tb_conversion_details.php?cid=<?php echo $this->cid; ?>";
//                 alert(url);
    var rTable = $('#tb_conversion_details').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "aoColumns": [null, null, null, null,null, null, null, null],
        "aaSorting": [],
        "bDestroy": true,
        "sAjaxSource": url
    });
    rTable.fnDraw();
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function (e) {
        if (e.which == 13) {
            rTable.fnFilter($(this).val(), null, false, true);
        }
    });
});


</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "conversion_details";//pagecode
            include "sidemenu.php";    
            $db = new DBConn(); 
?>
<div class="container-section">     
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="common-content-block"> 
                    <table id="tb_conversion_details" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>               
                                <th>PO Number</th>
                                <th>Product Name</th>
                                <th>Purchase Quantity</th>
                                <th>Pack Size</th>
                                <th>Reqired Packets</th>
                                <th>Actual Packets</th>
                                <th>Difference</th>
                                <th>Reason</th>
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


