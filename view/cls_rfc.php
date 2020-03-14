<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_rfc extends cls_renderer{

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
    var url = "ajax/tb_rfc.php";  
//    alert(url);
    oTable = $('#tb_rfc').dataTable( { 
	"bProcessing": true,
	"bServerSide": true,  
        "aoColumns": [null,null,null,null,null,null,null,null,null,null,{bSortable:false},{bSortable:false}],
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

function createCR(){
    window.location.href = "cr/create";
}

function approve(crid,createdbyid){ 
//    alert(createdbyid);
    var r = confirm("Are you sure you want to approve the CR");
    if(r){
    var ajaxURL = "ajax/approveCR.php?crid="+crid+"&createdbyid="+createdbyid;
//    alert(ajaxURL);
         $.ajax({
         url:ajaxURL,
             dataType: 'json',
             success:function(data){
                 //alert(data.error);
                 if (data.error == "1") {
                     alert(data.msg);
                 } else {
                     window.location.href = "rfc";
                 }
             }
         });
    }    
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php }

        public function pageContent() {
            $menuitem = "rfc";
            include "sidemenu.".$this->currStore->usertype.".php";
//            $dbl = new DBLogic();            
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
        </div>
        <div class="col-md-3">
            <?php //if($this->currStore->usertype == UserType::RFCManager){ ?>
            <button type="button" class="btn btn-primary pull-right" onclick="createCR();">Create CR</button>
             <?php// } ?>   
        </div>
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;CR Master List </b></h7>
                <div class="common-content-block">                     
                    <table id="tb_rfc" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>  
                                <th>ID</th>
                                <th>CR Name</th>
                                <th>RFC Name</th>
                                <th>Contact Person</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Phone No.</th>
                                <th>GST No.</th>
                                <th>PAN No.</th>
                                <th>State</th>
                                <th>Create Date</th>
                                <?php if($this->currStore->usertype == UserType::HO){ ?>
                                    <th>Action</th>
                                <?php } else if($this->currStore->usertype == UserType::RFCManager){ ?>
                                    <th>Status</th>
                                <?php } ?>     
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
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>              
<!-- <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           -->
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


