<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once 'lib/db/DBLogic.php';

class cls_location_product_mapping extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $locid ="";
        var $status ="";
       
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
//               print_r($params);
		$this->params = $params;
	        
                if($params && isset($params['locid'])){
                    $this->locid = $params['locid'];
                }
                if($params && isset($params['status'])){
                    $this->status = $params['status'];
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
    var url = "ajax/tb_mapping_products.php?locid=<?php echo $this->locid;?>&status=<?php echo $this->status ;?>";
//    alert(url);
    oTable = $('#tb_mapping_products').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,{"bSortable": false}],
	"sAjaxSource": url,
        "aaSorting": []
    } );
// search on pressing Enter key only
    $('.dataTables_filter input').unbind('keyup').bind('keyup', function(e){
	if (e.which == 13){                     
		oTable.fnFilter($(this).val(), null, false, true);
	}
    });  

    $("#selectallmap").click(function () {
       // alert("mark mapped");
       // $('.case1').attr('checked', this.checked);
       if($(".case1").length == $(".case1:checked").length) {
           // $("#selectallmap").attr("checked", "checked");
          $('.case1').prop('checked',false); 
        }else{
          $('.case1').prop('checked', 'checked'); 
        }
       // $("#selectall_unmap").removeAttr("checked");
         $('.prettycheckbox2').prop('checked', false);
    });

  // if all radio btns are selected, check the selectall checkbox
  // and viceversa
//    $(".case1").click(function(){
//        alert("here");
////        var l1 = $(".case1").length;
////        var l2 = $(".case1:checked").length;
////        alert(" CASE LENGTH: "+l1+" CASE CHECKED LENGTH: "+l2);
//        if($(".case1").length == $(".case1:checked").length) {
//           // $("#selectallmap").attr("checked", "checked");
//           $('.prettycheckbox1').prop('checked', true);
//        } else {
//            alert();
////            $("#selectallmap").removeAttr("checked");
//              $('.prettycheckbox1').prop('checked', false);
//              $('.prettycheckbox2').prop('checked', false);
//        }
//
//    });

    $("#selectall_unmap").click(function () {
       // alert("remove mapped");
         // $('.case2').attr('checked', this.checked);
         if($(".case2").length == $(".case2:checked").length) {
           // $("#selectallmap").attr("checked", "checked");
          $('.case2').prop('checked',false); 
        }else{
          $('.case2').prop('checked', 'checked'); 
        }  
         //  $("#selectallmap").removeAttr("checked");
         $('.prettycheckbox1').prop('checked', false);
    });

  // if all non ttk radio btns are selected, check the selectall checkbox
  // and viceversa
//    $(".case2").click(function(){
//
//        if($(".case2").length == $(".case2:checked").length) {
//            //$("#selectall_unmap").attr("checked", "checked");
//            $('.prettycheckbox2').prop('checked', true  );
//        } else {            
////            $("#selectall_unmap").removeAttr("checked");
//            $('.prettycheckbox1').prop('checked', false);
//            $('.prettycheckbox2').prop('checked', false);
//        }
//
//    });  
});
function loadlocwise(loc_id){
    window.location.href="location/product/mapping/locid="+loc_id;
}
function loadmappingwise(status){ 
   var loc_id = $("#locsel").val();
//   alert(loc_id);
   if(loc_id == "" || loc_id == null || loc_id == "0"){
      alert("Please select Location first"); 
   }else{
        window.location.href="location/product/mapping/locid="+loc_id+"/status="+status;
   }
}
function markItem(theForm){
//    $("#selectallmap").removeAttr("checked");
//    $("#selectall_unmap").removeAttr("checked");
    $('.prettycheckbox1').prop('checked', false);
    $('.prettycheckbox2').prop('checked', false); 
    var formName = theForm.name;        
    var params = $(theForm).serialize();
   // alert(params);
    var ajaxURL = "ajax/changeProductMapping.php?"+params;    
//    alert(ajaxURL);
    $.ajax({
        url:ajaxURL,
        dataType: 'json',
        success:function(data){
           // alert(data);
//             $("#results").hide();
            console.log(data);  
            if(data.error==1){
                alert(data.message);                            
            }else{
                alert(data.message);    
            }
            redirect(params);
            // window.location.reload();
        }
    });
}
function remSel(prodid){   
//    alert(prodid);
//    $("#selectallmap").removeAttr("checked");
//    $("#selectall_unmap").removeAttr("checked");
//     $('.prettycheckbox1').prop('checked', false);
//     $('.prettycheckbox2').prop('checked', false);
//         
     var l1 = $(".case1").length;
        var l2 = $(".case1:checked").length;
      //  alert(" CASE LENGTH: "+l1+" CASE CHECKED LENGTH: "+l2);
        if($(".case1").length == $(".case1:checked").length) {
           // $("#selectallmap").attr("checked", "checked");
        //   alert("case1 satisfied");
          // $('.prettycheckbox1').attr('checked', true);
           $('.prettycheckbox1').prop('checked',true);
        } else {
           // alert();
//            $("#selectallmap").removeAttr("checked");
              $('.prettycheckbox1').prop('checked', false);
            //  $('.prettycheckbox2').prop('checked', false);
        }
        
        
        if($(".case2").length == $(".case2:checked").length) {
            //$("#selectall_unmap").attr("checked", "checked");
           // alert("case2 satisfied");
            $('.prettycheckbox2').prop('checked',true);
        } else {
//            $("#selectall_unmap").removeAttr("checked");
         //   $('.prettycheckbox1').prop('checked', false);
            $('.prettycheckbox2').prop('checked', false);
        }
}

function downloadMappingExcel(){
//    alert("in dwnld");
    var locid= '<?php echo $this->locid;?>';
    var status= '<?php echo $this->status ;?>';
    if(locid!=""){
//            alert("download");
//        window.location.href="formpost/summaryExcel.php?hqid="+hqid;
//alert("formpost/productmappingExcel.php?locid="+locid+"/status="+status);
        window.location.href="formpost/productmappingExcel.php?locid="+locid+"&status="+status;
    }else{
        alert("Select Location First");
    }
}

</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<!--<link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "location_product_mapping";//pagecode
            include "sidemenu.php";  
            include 'lib/locations/clsLocation.php';
//            $db = new DBConn();
            $clsLocation = new clsLocation();
?>
<div class="container-section">
    <form name="productmapping" id="productmapping" action="" method="post" onsubmit="markItem(this); return false;">
        <!--<input type="hidden" name="locid" id="distid" value="<?php echo $this->distid;?>">-->   
                   
        <div class="row">
           <div class="col-md-4"> 
               <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="locsel" name="locsel" onchange="loadlocwise(this.value);" >
                   <option value="">Select Location</option>
                   <?php
                   $lobjs = $clsLocation->getLocations();
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
              <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="selmapping" name="selmapping"onchange="loadmappingwise(this.value);" >
                   <!--<option value="">All</option>-->
                   <?php
                   $statusarr =  LocationProductStatus::getAll();
   //                print_r($statusarr);
                   if (isset($statusarr)) {
                       foreach ($statusarr as $key=>$value) {
                            if($key == $this->status){ $selected="selected";}else{ $selected = "";}
                           ?>
                           <option value="<?php echo $key ?>"<?php echo $selected; ?>><?php echo$value; ?></option>
                       <?php
                       }
                   }
                   ?>
               </select> 
               <br>
           </div>
           <div class="col-md-4">
               <button type="button" class="btn btn-primary" style="float:right" onclick="downloadMappingExcel();">Download</button>
               <br/><br/>
           </div>
        </div>
         <?php if(isset($this->locid) && trim($this->locid)!=""){ ?>
        <div class="row">     
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="common-content-block"> 
                        <h7><b>Location Products Mapping</b></h7>
                        <h5>Note: After marking the items Please click on the below 'Save' button</h5>
                            <table id="tb_mapping_products" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Category</th> 
                                        <th>Product Name</th>
                                        <th>Action<br/>
                                        <input type='checkbox' name='selectallmap' id='selectallmap' class='prettycheckbox1' />Map All  &nbsp;&nbsp;<input type='checkbox' name='selectall_unmap' id='selectall_unmap' class='prettycheckbox2'/>Unmap All
                                    </th>                            
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
        <input class="btn btn-primary" style="float:right" type="Submit" name="Submit" value="Save" />   
      <?php  } ?>
    </form>        
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           
            <?php // }else{ print "You are not authorized to access this page";}
	}
}


