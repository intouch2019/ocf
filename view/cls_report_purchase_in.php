<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_packet_conversion extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $purid ="";
        var $purdt ="";
       
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        //$this->currStore = getCurrStore();
//               print_r($params);
		$this->params = $params;
	        
                if($params && isset($params['purid'])){
                    $this->purid = $params['purid'];
//                    print  $this->locid;
                }
                if($params && isset($params['purdt'])){
                    $this->purdt = $params['purdt'];
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
        var url = "ajax/tb_purchase_conversion.php?purid=<?php echo $this->purid;?>&purdt=<?php echo $this->purdt;?>";
//      var url = "ajax/tb_hqallocation.php";
//        alert(url);
        oTable = $('#tb_purchase_conversion').dataTable( {
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
    
    
    $('#datepicker').datepicker({ 
        format: 'dd-mm-yyyy',
    //    startDate: '+1d',
        autoclose : true,  
    }).change(dateChanged);
    // $("#tablebox").hide();   
});
function dateChanged(ev) {   
    var dt = $("#seldate").val();
//    dt =  dt.replace(/\//g, "-");
//    alert("DT: "+dt);
    var pur_id = $("#pursel").val();
//    alert(loc_id);
    if(pur_id == ''){
//        alert("here");
        window.location.href="packet/conversion/purdt="+dt;
    }else{
        window.location.href="packet/conversion/purid="+pur_id+"/purdt="+dt;
    }              
}
function loadpurwise(pur_id){
    var dt = $("#seldate").val();
    if(dt == "select" || dt == "Select Date" || dt ==null || dt == "0"){
         alert("Please select Date first"); 
    }else{
        window.location.href="packet/conversion/purid="+pur_id+"/purdt="+dt;
    }
}
function calculate_diff(rid,aid,did,req_qty,act_qty){ 
//    alert("RID: "+rid+" ACT ID: "+aid+" DIFF ID: "+did+" REQ QTY:"+req_qty+" ACT QTY: "+act_qty);
//    alert("id:"+did);
    if(isNaN(act_qty)){
        alert("Only Numeric Values allowed");   
        $("#"+aid).val(" ");
    }
    else{
        //calculate difference
        var diff = parseInt(req_qty-act_qty);
        //should check diff < req_qty
//        alert("#"+did);
        $("#"+did).val(diff);
        if(diff < 0){
            alert("Actual packet value can't be more than pacekts to be made value"); 
            $("#"+aid).val(" ");
            $("#"+did).val(" ");
        }else if(diff == 0 ){
//            alert("id:"+rid);  
            var str = "NA";
//            document.getElementById(rid).value=str;
            $("#"+rid).val(str);
            $("#"+rid).attr("readonly",true);
        }else{
            $("#"+rid).val(" ");
            $("#"+rid).attr("readonly",false);
        }
    } 
}
function submitdata(theForm){
//    alert("submit");
    
        var elements = document.getElementsByClassName("required");
        var i;
        var validate = true;
    //    var elearr = new array("reason not given for");
        for (i = 0; i < elements.length; i++) {
    //        alert(elements[i].value);
            if (elements[i].value == "" || elements[i].value == " ") {
                if (validate == true) {
                   validate = false;
    //               elements[i].style.borderColor = "#f67575"; ;
                }
    //            elements[i].style.borderColor = "#f67575"; ;
    //            elearr.push(elements[i]); //push product name
            }
        }
        if(validate == true){
            var params = $(theForm).serialize();
    //                alert(params);   
            var r = confirm("Do You want really want to submit the conversion?");
            if (r == true) {
                window.location.href="formpost/packet_conversion.php?params="+params;
            }else{//do nothing
            }
        }else{
             alert ("empty field not allowed");
         }       
}
</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        
        <?php
        }

        public function pageContent() {
            //$currUser = getCurrUser();
            $menuitem = "packet_conversion";//pagecode
            include "sidemenu.php";    
            require_once "lib/core/strutil.php";
            $formResult = $this->getFormResult();
//            print_r($formResult);
            $db = new DBConn(); 
?>
<div class="container-section">
    <form name="packetcnvrt" id="packetcnvrt"  method="post" action="" onsubmit="submitdata(this);return false;">
        <input type = "hidden" name="form_id" id="form_id" value="packetcnvrt">
        <!--<form name="packetcnvrt" id="packetcnvrt" enctype="multipart/form-data" method="post" action="formpost/packet_conversion.php">-->
     <div class="row">
        <div class="col-md-4">
            <div class="input-group date" id="datepicker" >
                <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php if(isset($this->purdt) && trim($this->purdt)!=""){ echo $this->purdt; }else{ echo "Select Date"; } ?>">
                <div class="input-group-addon" >
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div> 
        </div>
        <div class="col-md-4">
            <select class="form-control" id="pursel" name="pursel" onchange="loadpurwise(this.value);" >
                <option value="">Select Purchase Number</option>
                <?php
                    $dt = yymmdd($this->purdt);
                    $allotdate_db = $db->safe($dt);
                    $query = "select id,pur_in_no from it_purchase_in where date(purin_dt) = $allotdate_db and status !=".PurchaseInStatus::converted;
//                                                    echo $query;
                    $pobjs = $db->fetchObjectArray($query);
                    if (isset($pobjs)) {
                        foreach ($pobjs as $pobj) {
                            if($pobj->id == $this->purid){ $selected="selected";}else{ $selected = "";}
                            ?>
                            <option value="<?php echo $pobj->id ?>"<?php echo $selected; ?>><?php echo $pobj->pur_in_no; ?></option>
                        <?php
                        }
                    }
                ?>
            </select> 
        </div>
        <br/><br/> 
    </div>
    <?php if(isset($this->purid) && trim($this->purid)!=""){ ?>
    <div class="row" id="tablebox">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="common-content-block"> 
                    <table id="tb_purchase_conversion" class="table table-striped table-bordered dt-responsive nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>               
                                <th>Product</th>
                                <th>Purchased Qty</th>
                                <th>Pack Size</th>
                                <th>Packets to be Made</th>
                                <th>Actual Packets Made</th>                                
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
        <br/><br/>    
        <input type ="submit" class="btn btn-primary"  id="submit" name="submit" style="float:right" value="Submit" >
    </div> 
        <?php  } ?>
    </form></br></br>
    <?php if ($formResult->form_id == 'packetcnvrt') { ?>
        <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
            <h4> <?php echo $formResult->status; ?>
        </div>
    <?php  } ?>
</div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}


