<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_hq_allocation extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $locid ="";
        var $alloctndt ="";
       
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
//               print_r($params);
		$this->params = $params;
	        
                if($params && isset($params['locid'])){
                    $this->locid = $params['locid'];
//                    print  $this->locid;
                }
                if($params && isset($params['alloctndt'])){
                    $this->alloctndt = $params['alloctndt'];
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
	
	***********************************************************************************************************/	

</script> -->
<script type="text/javaScript">        
$(function(){
    $('#datepicker').datepicker({
        format: 'dd-mm-yyyy',
    //    startDate: '+1d',
        autoclose : true,  
//        onChange: function() {        
//        }
    }).change(dateChanged);
    
    var url = "ajax/tb_hqallocation.php?locid=<?php echo $this->locid;?>&alloctndt=<?php echo $this->alloctndt;?>";  
//      var url = "ajax/tb_hqallocation.php";
//    alert(url);
    oTable = $('#tb_allocation').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null],
	"sAjaxSource": url,
        "aaSorting": []
    } );
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
	if (e.which == 13){                     
		oTable.fnFilter($(this).val(), null, false, true);
	}
    }); 
//    $("#dialogAllocationDetails").dialog({
//                    autoOpen: false,
//                    width: 800,
//                    height: 500,
//                    resizable: true   
//    });
});
function dateChanged(ev) {   
    var dt = $("#seldate").val();
//    dt =  dt.replace(/\//g, "-");
//    alert("DT: "+dt);
    var loc_id = $("#locsel").val();
//    alert(loc_id);
    if(loc_id == ''){
//        alert("here");
        window.location.href="hq/allocation/alloctndt="+dt;
    }else{
        window.location.href="hq/allocation/locid="+loc_id+"/alloctndt="+dt;
    }              
}
function loadlocwise(loc_id){
   var dt = $("#seldate").val();
//   dt =  dt.replace(/\//g, "-");
//   alert(dt);
   if(dt == "select" || dt == "Select Date" || dt ==null || dt == "0"){
//      alert("Please select Date first"); 
        window.location.href="hq/allocation/locid="+loc_id;
   }else{
//      alert("hq/allocation/locid="+loc_id+"/alloctndt="+dt);
        window.location.href="hq/allocation/locid="+loc_id+"/alloctndt="+dt;
   }
}
function uploadStockFn(){
     window.location.href = "hq/allocation/upload";
}
//function showDetails(id){
//    alert(id);
////    window.location.href="hq/allocation/items/hqid="+id;
//}
 function showAllocationDetails(hqid) {
     window.location.href = "hq/allocation/details/hqid="+hqid;
    //alert(hqid);
//    $("#dialogAllocationDetails").dialog('open');
  //  $('#addModal').modal('show');
//    $.ajax({
//        url: "ajax/getHQDetails.php?hqid="+hqid,
//        dataType: 'json',
//        success: function (result) {
//                     alert(result);
////            $('#spc_locname').html(result.locname);
//              $('#spc_hqno').html(result.hqno);
//              $('#spc_allctdttm').html(result.allctdttm);
//
//        }
//    });
//    var url = "ajax/tb_HQ_allocation_items.php?hqid="+hqid;
////                 alert(url);
//    var rTable = $('#tb_allocation_items').dataTable({
//        "bProcessing": true,
//        "bServerSide": true,
//        "aoColumns": [null, null, null, null],
//        "aaSorting": [],
//        "bDestroy": true,
//        "sAjaxSource": url
//    });
//    rTable.fnDraw();
//    $('.dataTables_filter input').unbind('keyup').bind('keyup', function (e) {
//        if (e.which == 13) {
//            rTable.fnFilter($(this).val(), null, false, true);
//        }
//    });
}

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "hq_allocation";//pagecode
            include "sidemenu.php";    
            $db = new DBConn(); 
?>
<div class="container-section">
     <div class="row">
        <div class="col-md-4">
            <div class="input-group date" id="datepicker" >
                <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php if(isset($this->alloctndt) && trim($this->alloctndt)!=""){ echo $this->alloctndt; }else{ echo "Select Date"; } ?>">
                <div class="input-group-addon" >
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div> 
        </div>
        <div class="col-md-4">
            <select class="form-control" id="locsel" name="locsel" onclick="loadlocwise(this.value);" >
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
            <!--<a href="create/user" class="btn btn-primary" role="button" style="width:150px;height: 30px;">Create User</a><br><br>-->
            <button type="button" class="btn btn-primary" style="float:right" onclick="uploadStockFn();">Upload File</button>
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
                                <th>Location</th>
                                <th>HQ_Number</th>
                                <th>Allocation_dttm</th>
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
    <div id="addModal" class="modal fade" role="dialog" aria-hidden="true" aria-labelledby="addModal" tabindex="-1" >
        <div class="modal-dialog">
               <div class="modal-content">
               </div>
        </div>
    </div>
    <!--<div id="dialogAllocationDetails" class="modal fade" role="dialog" aria-hidden="true" aria-labelledby="addModal" tabindex="-1" title="HQ Allocation Details">-->   
<!--        <table>
            <tr><th>Location</th><td id="spc_locname"></td>
                <th>HQ_Number</th><td id="spc_hqno"></td>
                <th>Allocation_dttm</th><td id="spc_allctdttm"></td>
            </tr>

        </table>       -->
<!--        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tb_allocation_items">
            <thead>
                <tr>
                    <th>Dispatch Location</th>
                    <th>Product name</th>
                    <th>Quantity</th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody class="cleardatatable">
                <tr>
                    <td colspan="7" class="dataTables_empty">Loading data from server</td>
                </tr>
            </tbody>
        </table>-->
    <!--</div>-->
</div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}


