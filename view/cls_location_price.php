<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_location_price extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $cid;
        var $uid;
        var $pid;
        var $sid = -1;
       
        function __construct($params=null) {
 //parent::__construct(array());
        $this->currStore = getCurrStore();
        
        $this->params = $params;
        
        if ($params && isset($params['cid'])) { 
            $this->cid = $params['cid'];
        }
        
        if ($params && isset($params['uid'])) { 
            $this->uid = $params['uid'];
        }
        
        if ($params && isset($params['pid'])) { 
            $this->pid = $params['pid'];
        }
        
        
        if ($params && isset($params['sid'])) { 
            $this->sid = $params['sid'];
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
	
	************************************************************************************************************/	

</script> -->
<script type="text/javaScript">     
$(function(){      
    
    var url = "ajax/tb_location_price.php";
    //alert(url);
    oTable = $('#tb_location_price').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,null,{bSortable:false}],
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

function uploadLpFn(){
    window.location.href = "location/price/upload";
}

function editLocationPrice(locprid){
//    alert("product/edit/pid="+prodid);
    window.location.href = "location/price/edit/lprid="+locprid;
}

function ctgWise(ctgid){  
  var uom = $("#seluom").val(); 
  var psz = $("#selpz").val();
  var status = $("#selstatus").val();
  var aClause = "";
  //alert("UOM: "+uom+"PSZ: "+psz+"STATUS: "+status);
  if(uom != ""){ //uom
      aClause += "/uid="+uom;
  }
  
  if(psz != ""){ //pack size
      aClause += "/pid="+psz;
  }
  
  if(status != ""){ //status
      aClause += "/sid="+status;
  }
    
  if(ctgid!=""){
    window.location.href = "products/cid="+ctgid+aClause;
  }else{
    window.location.href = "products"+aClause;
  }
  
}


function uomWise(uid){  
  var ctgid = $("#selctg").val(); 
  var psz = $("#selpz").val();
  var status = $("#selstatus").val();
  var aClause = "";
  //alert("UOM: "+uom+"PSZ: "+psz+"STATUS: "+status);
  if(ctgid != ""){ //ctgid
      aClause += "/cid="+ctgid;
  }
  
  if(psz != ""){ //pack size
      aClause += "/pid="+psz;
  }
  
  if(status != ""){ //status
      aClause += "/sid="+status;
  }
    
  if(uid!=""){
    window.location.href = "products/uid="+uid+aClause;
  }else{
    window.location.href = "products"+aClause;
  }
  
}


function pszWise(pszid){  
  var uom = $("#seluom").val(); 
  var ctgid = $("#selctg").val();
  var status = $("#selstatus").val();
  var aClause = "";
  //alert("UOM: "+uom+"PSZ: "+psz+"STATUS: "+status);
  if(uom != ""){ //uom
      aClause += "/uid="+uom;
  }
  
  if(ctgid != ""){ //pack ctgid
      aClause += "/cid="+ctgid;
  }
  
  if(status != ""){ //status
      aClause += "/sid="+status;
  }
    
  if(pszid!=""){
    window.location.href = "products/pid="+pszid+aClause;
  }else{
    window.location.href = "products"+aClause;
  }
  
}

function statusWise(sid){
  var uom = $("#seluom").val(); 
  var psz = $("#selpz").val();
  var ctgid = $("#selctg").val();
  var aClause = "";
  //alert("UOM: "+uom+"PSZ: "+psz+"STATUS: "+status);
  if(uom != ""){ //uom
      aClause += "/uid="+uom;
  }
  
  if(psz != ""){ //pack size
      aClause += "/pid="+psz;
  }
  
  if(ctgid != ""){ //ctgid
      aClause += "/cid="+ctgid;
  }
    
  if(sid!=""){
    window.location.href = "products/sid="+sid+aClause;
  }else{
    window.location.href = "products"+aClause;
  }
}
</script>
<link rel="stylesheet" href="js/chosen/chosen.css" />
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />


        
        <?php
        }

        public function pageContent() {
           // print_r($_SESSION);
            //$currUser = getCurrUser();
            $menuitem = "currjorder";//pagecode
            include "sidemenu.php";  
            $dbl = new DBLogic();            
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-primary pull-right" onclick="uploadLpFn();">Upload Prices</button>
        </div>
        <br><br>
<!--        <div class="col-md-3">
            <select id="selctg" name="selctg" class="chzn-select" style="width:100%;" onchange="ctgWise(this.value);">
                <option value="">All Categories</option>
                <?php
                    $ctgobjs = $dbl->getAllActiveCtgs();
                    if(!empty($ctgobjs)){
                        foreach($ctgobjs as $ctgobj){
                            if(isset($ctgobj) && !empty($ctgobj) && $ctgobj != null){
                                $selected="";
                                if($ctgobj->id == $this->cid){
                                    $selected = "selected";
                                }
                    ?>
                <option value="<?php echo $ctgobj->id; ?>" <?php echo $selected; ?>><?php echo $ctgobj->name; ?></option>
                    <?php
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select id="seluom" name="seluom" class="chzn-select" style="width:100%;" onchange="uomWise(this.value);">
                <option value="">All UOM</option>
                <?php
                    $uomobjs = $dbl->getAllUOM();
                    if(!empty($uomobjs)){
                        foreach($uomobjs as $uomobj){
                            if(isset($uomobj) && !empty($uomobj) && $uomobj != null){
                                $selected="";
                                if($uomobj->id == $this->uid){
                                    $selected = "selected";
                                }
                    ?>
                <option value="<?php echo $uomobj->id; ?>" <?php echo $selected; ?>><?php echo $uomobj->name; ?></option>
                    <?php
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select id="selpz" name="selpz" class="chzn-select" style="width:100%;" onchange="pszWise(this.value);">
                <option value="">All Pack Size</option>                
                 <?php
                    $pszobjs = $dbl->getAllPackSize();
                    if(!empty($pszobjs)){
                        foreach($pszobjs as $pszobj){
                            if(isset($pszobj) && !empty($pszobj) && $pszobj != null){
                                if($pszobj->id == $this->pid){
                                    $selected = "selected";
                                }
                    ?>
                <option value="<?php echo $pszobj->id; ?>" <?php echo $selected; ?>><?php echo $pszobj->pack_size; ?></option>
                    <?php
                            }
                        }
                    }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <select id="selstatus" name="selstatus" class="chzn-select" style="width:100%;" onchange="statusWise(this.value);">                
                <option value="">All Status</option>
                <option value="1" <?php if(trim($this->sid) == 1){ echo "selected"; } ?>>Active</option>
                <option value="0" <?php if(trim($this->sid) == 0){ echo "selected"; } ?>>Inactive</option>
            </select>
        </div>-->
    </div>
    
    <br/>
    <div class="row">
        
        <div class="col-md-12">
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Current Price Master List </b></h7>
                <div class="common-content-block"> 
                    <table id="tb_location_price" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>                                  
                                <th>Distribution Channel</th>
                                <th>Shopify Name</th>
                                <th>Product Name</th>                                
                                <th>Applicable Date</th>
                                <th>Price</th>          
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
 <script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>           
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


