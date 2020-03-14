<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/supplier/clsSupplier.php";
require_once "lib/db/DBLogic.php";

class cls_purchase_in extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $prin_id = "-1";
        
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
        if($params && isset($params['prin_id'])){
                 $this->prin_id = $params['prin_id']; 
//                 print $this->productid;
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
    //var pur_in_id = $("#purin_id").val();
    //alert(pur_in_id);
    //var url = "ajax/tb_purchase_in.php?pur_in_id="+pur_in_id;
    var url = "ajax/tb_purchase_in.php?pur_in_id=<?php echo $this->prin_id; ?>";
    //alert(url);
    oTable = $('#tb_purchase_in').dataTable( {
	"bProcessing": true,
	"bServerSide": true,
        "aoColumns": [null,null,null,null,null,{bSortable:false}],
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

function addPurIn(theForm){    
    var formName = theForm.name;
    var params = $(theForm).serialize();
    var ajaxUrl = "formpost/addPurchaseInEntry.php?"+params;
   // alert(ajaxUrl); 
     $.getJSON(ajaxUrl, function(data){
      //       alert(data);
              var error = data['error'];
              var msg = data['message'];
              if(error==1){
                  alert(msg);
              }else{
                 // window.location.reload();
                 var pur_in_id = $("#purin_id").val();
                  window.location.href="purchase/in/prin_id="+pur_in_id;
                // window.location.href = "purchase/in/"
              }
          });   
//    $.ajax({
//           url: ajaxUrl,
//           dataType: 'json',
//           success: function(data) { 
//               
//               alert(data);
//           }
//       });   
}

function editPurchaseIn(penid){
    window.location.href = "purchase/in/entry/edit/pid="+penid;
}

function confmPurIn(){
    //alert("here");
    var r = confirm("Are you sure you want to confirm");
    if(r){
       // alert("Confirmed");
        var pur_in_id = $("#purin_id").val();
        var ajaxUrl = "ajax/confirmPurchaseIn.php?pur_in_id="+pur_in_id;
         //alert(ajaxUrl);
            $.ajax({
           url: ajaxUrl,
           dataType: 'json',
           success: function(data) { 
              var error = data['error'];
              var msg = data['message'];
              if(error==1){
                  alert(msg);
              }else{
                //  window.location.reload();
                  //var pur_in_id = $("#purin_id").val();
                  window.location.href="purchase/in";
              }
           }
       });   
    }
}
</script>
<link rel="stylesheet" href="js/chosen/chosen.css" />
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "purin";//pagecode
            include "sidemenu.php";   
            $formResult = $this->getFormResult();
            $clsSupplier = new clsSupplier();
            $dbLogic = new DBLogic();
          //  print_r($formResult);
//                        include "sidemenu.".$this->currStore->usertype.".php";    
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
            $currdt = date('d-m-Y');
            $currdt2 = date('Y-m-d');
            $lprid = "-1";
            $lpurobj = $dbLogic->fetchLatestPurInObj($currdt2,PurchaseInStatus::increation);
//            print_r($lpurobj);
            if(isset($lpurobj) && ! empty($lpurobj) && $lpurobj != null){
//                print "<br>INSIDE IF<br>";
                $lprid = $lpurobj->id; 
                $this->prin_id = $lpurobj->id;
            }else{
                //insert
//                print "<br>INSIDE ELSE<br>";
                $lprid = $dbLogic->insertPurchaseIn($currdt2,PurchaseInStatus::increation,$this->currStore->id,$this->currStore->location_id);
                $this->prin_id = $lprid;
            }
            
//            echo "PRIN ID: ".$this->prin_id." LPRID: ".$lprid;
?>      
                
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Purchase In Entry for ( <?php echo $currdt; ?> )</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary"><br>
                                <form role="form" id="purchaseInForm" name="purchaseInForm" onsubmit="addPurIn(this); return false;" method="post" action="">
                                    <input type = "hidden" name="form_id" id="form_id" value="purchaseInForm">
                                    <div class="box-body">                                        
                                            <?php  ?>
                                            <input type="hidden" id="purindt" name="purindt"  value="<?php echo $currdt; ?>">                                                                                
                                            <input type="hidden" id="purin_id" name="purin_id"  value="<?php echo $lprid; ?>">                                                                                
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control chzn-select" id="selsupp" name="selsupp" style="width:75%;" >                                                    
                                                        <option value="" disabled selected>Select Supplier</option>
                                                        <?php
                                                        $sobjs = $clsSupplier->getAllActiveSuppliers();
                                                        if(!empty($sobjs)){
                                                           foreach($sobjs as $sobj){
                                                              if(isset($sobj) && !empty($sobj) && $sobj != null){
                                                            ?>
                                                        <option value="<?php echo $sobj->id; ?>"><?php echo $sobj->name; ?></option>
                                                            <?php      
                                                              } 
                                                           }
                                                        }
                                                        ?>                                                            
                                                    </select> 
                                                </div>    
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                <select class="form-control chzn-select" id="selprod" name="selprod" style="width:75%;" >                                                    
                                                    <option value="" disabled selected>Select Product</option>
                                                    <?php
                                                    $pobjs = $dbLogic->getAllActiveProducts();
                                                    if(!empty($pobjs)){
                                                       foreach($pobjs as $pobj){
                                                          if(isset($pobj) && !empty($pobj) && $pobj != null){
                                                        ?>
                                                    <option value="<?php echo $pobj->id; ?>"><?php echo $pobj->name; ?></option>
                                                        <?php      
                                                          } 
                                                       }
                                                    }
                                                    ?>                                                            
                                                </select> 
                                                 </div>   
                                            </div>                                        
                                            <div class="col-md-4">
                                                <div class="form-group" >
                                                <input type="text" id="qty" name="qty" placeholder="Enter Quantity" style="width:75%;">
                                                </div>
                                            </div>  
                                            <div class="col-md-4">
                                                <div class="form-group" >
                                                    <select class="form-control chzn-select" id="seluom" name="seluom" style="width:75%;" >                                                    
                                                    <option value="" disabled selected>Select UOM</option>
                                                    <?php
                                                    $uobjs = $dbLogic->getAllUOM();
                                                    if(!empty($uobjs)){
                                                       foreach($uobjs as $uobj){
                                                          if(isset($uobj) && !empty($uobj) && $uobj != null){
                                                        ?>
                                                    <option value="<?php echo $uobj->id; ?>"><?php echo $uobj->name; ?></option>
                                                        <?php      
                                                          } 
                                                       }
                                                    }
                                                    ?>                                                            
                                                </select> 
                                                </div>
                                            </div> 
                                            <div class="col-md-4">
                                                <div class="form-group" >
                                                <input type="text" id="rate" name="rate" placeholder="Enter Amount" style="width:75%;">
                                                </div>
                                            </div>                                                                                               
                                                                                                                      
                                    </div>
                                    <div class="box-footer">                                        
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                                <?php if ($formResult->form_id == 'createbin') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php  } ?>
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div> 
     <!--Table for purchase in details-->
     <div class="row">
         <div class="col-md-12">
             <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Puchase In Details</b></h7>
                <div class="common-content-block"> 
                    <table id="tb_purchase_in" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>                                                                  
                                <th>Product</th>                                
                                <th>Supplier</th>
                                <th>Total Qty</th>   
                                <th>UOM</th>
                                <th>Total Amount</th>                                                                   
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                             <td colspan="7" class="dataTables_empty">Loading data from server</td>
                         </tr>
                     </tbody>
                 </table>
                    <button  class="btn btn-primary" type="button" name="confmbtn" id="confmbtn" onclick="confmPurIn();">Confirm</button>
             </div>
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


