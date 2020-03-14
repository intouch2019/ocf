<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_purchase_order extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $lid;
        var $heid;
        var $dt;
        var $exdt;
       
        function __construct($params=null) {
    //parent::__construct(array());
           $this->currStore = getCurrStore();

           $this->params = $params;

           if ($params && isset($params['lid'])) { 
               $this->lid = $params['lid'];
           }

           if ($params && isset($params['heid'])) { 
               $this->heid = $params['heid'];
           }           
           if ($params && isset($params['dt'])) { 
               $this->dt = $params['dt'];
               $this->exdt = $this->dt;
               $this->exdt = date('d-m-Y', strtotime($this->exdt . ' +1 day'));
           }
//           if ($params && isset($params['exdt'])) { 
//               $this->exdt = $params['exdt'];
//           }
        }

	function extraHeaders() {
        ?>
<style type="text/css" title="currentStyle">
/*table .header-fixed {
  position: fixed;
  //top: 50px;
  left: 0;
  right: 0;
  z-index: 1020;  10 less than .navbar-fixed to prevent any overlap 

}*/
  
/*.table-responsive thead {
    position:fixed;
    width: 100%;
    margin-bottom: 15px;
}*/
    
.table-responsive {     
   width: 100%;
  // height: 100%;
  max-height:325px;
   margin-bottom: 15px;
/*   overflow-x: auto;
   overflow-y: auto;*/
   display: block;
/*   -webkit-overflow-scrolling: touch;*/
   /*-ms-overflow-style: -ms-autohiding-scrollbar;*/
   border: 1px solid #DDD;
}

/* table {
            width: 100%;
        }

        thead, tbody, tr, td, th { display: block; }

        tr:after {
            content: ' ';
            display: block;
            visibility: hidden;
            clear: both;
        }

        thead th {
            height: 30px;

            text-align: left;
        }

        tbody {
            height: 320px;
            overflow-y: auto;
        }

        thead {
             fallback 
        }


        tbody td, thead th {
            width: 19.2%;
            float: left;
        }*/







          /*  @import "js/datatables/media/css/demo_page.css";
            @import "js/datatables/media/css/demo_table.css";*/
/*            @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
            @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";*/
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
  //  $('#tb_po').hide();
    $('#datepicker').datepicker({
        format: 'dd-mm-yyyy',
    //    startDate: '+1d',
        autoclose : true,  
    }).change(dateChanged);    
    
    $('#datepicker2').datepicker({
        format: 'dd-mm-yyyy',
    //    startDate: '+1d',
        autoclose : true,  
    }).change(dateChanged); 
}); 

function dateChanged(ev) {  
       var dt = $("#seldate").val();
       var loc_id = $("#locsel").val();
       //var exdt = dt;
       //exdt = date('d-m-Y',strtotime(exdt+' +1 day'));
//       var exdt = new Date(dt);
//       exdt.setMonth( exdt.getMonth( ) + 1 );
       //var exdt = dt.setDate(date.getDate() + 1);
       window.location.href="purchase/order/lid="+loc_id+"/dt="+dt;
}
//function dateChanged(ev) {              
//}
function loadhubwise(sel_id){
    //sel_id - selected hub or event id
//    alert(sel_id);
    var dt = $("#seldate").val();
    var loc_id = $("#locsel").val();
    window.location.href="purchase/order/lid="+loc_id+"/dt="+dt+"/heid="+sel_id;
//    alert(loc_id);
//    alert(dt);
//    if(loc_id == null || loc_id == "" || dt == null || dt == "" || dt =="Select Date"){
//        alert("Select Location and Date First");
//    }else{
////        alert("here2");
//        var ajaxurl = "ajax/getpoentry.php?selid="+sel_id+"&loc_id="+loc_id;
////            alert(ajaxurl);
//            $.ajax({
//                    url: ajaxurl,
//                    dataType: 'json',
//                    success: function (result) {
////                             alert(result);
//                             $('#tab').append(result);               
//                    }
//            });
//                      $('#tb_po').show();
//    } 
}


function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("tab");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}


function completePO(){
    var poid = $("#po_id").val();
    var po_date = $("#po_date").val();
    var ex_date = $("#ex_date").val();
//    alert(poid);
    var r = confirm("Are you sure you want to confirm ? ");
    if(r){
        var myWindow = window.open('',"_blank");
        var ajaxurl = "ajax/markPOCompleted.php?poid="+poid+"&lid=<?php echo $this->lid; ?>&po_date="+po_date+"&ex_date="+ex_date;
              //  alert(ajaxurl);
        $.ajax({
                url: ajaxurl,
                dataType: 'json',
                success: function (result) {
    //                             alert(result);  
                    alert(result.msg);
                    //window.location.href = "formpost/genPOExcel.php";                   
                    window.location.href="purchase/order";
                    myWindow.location.href = "formpost/genPOExcel.php?poid="+poid+"&pono="+result.pono;                       
                    //myWindow.focus(); 
                }
        });
    }
}


function setTotValue(eleid,entered_val){
    //alert(eleid);
    var tot_amt = 0;
    tot_amt = parseInt(tot_amt);
//    console.log(eleid);
    //alert(eleid+" "+entered_val);
    var arr = eleid.split("_");
//    alert(arr[0]+" "+arr[1]+" "+arr[2]);
    var totid = "tot_"+arr[2];   
//    alert(totid);
//    var tot_val = document.getElementById(""+totid).value;
////    alert("TOT value: "+tot_val);
//    if(tot_val != '' && tot_val != 'undefined' ){
//        alert("here1");
//      if(entered_val != ''){
//          alert("in if");
//       tot_val = parseInt(tot_val) ;
//       entered_val = parseInt(entered_val);
//       tot_amt = tot_amt + tot_val;
//       tot_amt = tot_amt + entered_val;
//       document.getElementById(""+totid).value = tot_amt;
//      }else{
//          alert("in if else");
//         tot_val = parseInt(tot_val) ; 
//         document.getElementById(""+totid).value = tot_val;
//      } 
//    }else if(entered_val != ''){
//        alert("here2");
//       entered_val = parseInt(entered_val); 
//       tot_amt = tot_amt + entered_val;        
//       document.getElementById(""+totid).value = tot_amt; 
//    }else{
//        alert("here3");
//       document.getElementById(""+totid).value = "" ;
//    }
    var cname = "prod_"+arr[2];
    var elements = document.getElementsByClassName(""+cname);    
    
    var i;  
    var prev_ptype = "";
    for (i = 0; i < elements.length; i++) {  
       // console.log(elements[i]);
      var e_amt = elements[i].value;
      
       if(e_amt != ''){         
           e_amt = parseInt(e_amt);          
           if(e_amt < 0){
            alert("Amount cannot be negative");   
           }
//           else if(e_amt == 0){
//            alert("Amount cannot be zero");      
//           }
           else{
             tot_amt = tot_amt + e_amt
            
              
           }
       }

    }
    
//    if(entered_val != ''){
//       entered_val = parseInt(entered_val); 
//       tot_amt = tot_amt + entered_val;
//    }
    
    document.getElementById(""+totid).value = tot_amt; 
    
}
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
     
        <?php
        }

        public function pageContent() {
           // print_r($_SESSION);
            //$currUser = getCurrUser();
            $menuitem = "purorder";//pagecode
            include "sidemenu.php";  
            include 'lib/locations/clsLocation.php';
            $formResult = $this->getFormResult();
//            print_r($formResult);
            $clsLocation = new clsLocation();  
            $db = new DBConn();
            $dblogic = new DBLogic();
            $last_po_id = -1;
            $po_id = -1;
            $ex_day_of_week = "";
            if(isset($this->lid) && trim($this->lid)!="" && isset($this->dt) && trim($this->dt)!="" ){ //&& isset($this->heid) && trim($this->heid)!=""
                $dt = yymmdd($this->dt);
                $upobj = $dblogic->fetchUnprocessedPO($this->lid,$dt); //,POStatus::Current
                if(isset($upobj) && !empty($upobj) && $upobj != null){
                    $po_id = $upobj->id;
                }else{
                    //insert
                    $dt = yymmdd($this->dt);
                    $po_id = $dblogic->insertUnprocessedPO($this->lid,$dt,POStatus::Current,$this->currStore->id,$this->currStore->location_id);
                }
                $ex_day_of_week =  date('w', strtotime($this->exdt));
                if(trim($ex_day_of_week)!=""){
                    $qry = "select po_id from it_po_day_account where execution_day = $ex_day_of_week and po_id != $po_id order by id desc limit 1";
                    $last_po_obj = $db->fetchObject($qry);
                    if(isset($last_po_obj) && !empty($last_po_obj) && $last_po_obj != null){
                        $last_po_id = $last_po_id->po_id;
                    }
                }
            }
            
            $qry = "select id,parent_location_id,child_location_id,product_id,qty_in_packets from it_purchase_order_items where po_id = $po_id and parent_location_id = $this->heid";
//            print "<br> $qry";
            $poiobjs = $db->fetchAllObjects($qry);
            
            
            //to fetch last po_items
             $qry = "select id,parent_location_id,child_location_id,product_id,qty_in_packets from it_purchase_order_items where po_id = $last_po_id and parent_location_id = $this->heid";
//            print "<br> $qry";
            $last_poiobjs = $db->fetchAllObjects($qry);
            
             $lqry="select name from it_locations where id in(select distinct(parent_location_id) from it_purchase_order_items where po_id = $po_id)";
//            print"$lqry";
           $locnameobj = $db->fetchAllObjects($lqry);
//            if(!empty($poiobjs)){
//                print_r($poiobjs);
//            }
            //fetch po_id
            
//            if($currUser->usertype == UserType::Admin || $currUser->usertype == UserType::CKAdmin){
?>

<div class="container-section">
    <div class="row">
        <div class="col-md-3">
            <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="locsel" name="locsel" >
                <option value="" disabled selected>Select Location</option>
                <?php
                $type = "5,7";
                $lobjs = $clsLocation->getLocations($type);
//                print_r($lobjs);
                if (isset($lobjs)) {
                    foreach ($lobjs as $lobj) {
                        $selected = "";
                        if($this->lid == $lobj->id){
                            $selected = "selected";
                        }
                        ?>
                        <option value="<?php echo $lobj->id; ?>" <?php echo $selected; ?> > <?php echo $lobj->name; ?></option>
                    <?php
                    }
                }
                ?>
            </select>  
        </div>    
         <div class="col-md-3">
            <!--        <div class="col-md-4">
           <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php //echo date('d-m-Y',strtotime("tomorrow"));  ?>">    
       </div>--> 
             
            <div class="input-group date" id="datepicker" >
                <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php if(isset($this->dt) && trim($this->dt)!=""){ echo $this->dt; }else{ echo "Select Date"; } ?>">
                <div class="input-group-addon" >
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div> 
        </div>
         <div class="col-md-3">
            <!--        <div class="col-md-4">
           <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php //echo date('d-m-Y',strtotime("tomorrow"));  ?>">    
       </div>--> 
             
            <div class="input-group date" id="datepicker2" >
                <input type="text" class="form-control" name ="execdate" id = "execdate" value="<?php if(isset($this->exdt) && trim($this->exdt)!=""){ echo $this->exdt; }else{ echo "Execution Date"; } ?>">
                <div class="input-group-addon" >
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div> 
        </div>
        <div class="col-md-3">
            <select id="selhub" name="selhub" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="loadhubwise(this.value);" >
                <option value="">All Hub/Event</option>
                <?php
                    $hubobjs = $clsLocation->getHubEventLocations();  // function added in clsLocation                 
//                    print_r($hubobjs);
                    if(!empty($hubobjs)){
                        foreach($hubobjs as $hubobj){
                            if(isset($hubobj) && !empty($hubobj) && $hubobj != null){
                                $selected="";
                                if($hubobj->id == $this->heid){
                                    $selected = "selected";
                                }
                    ?>
                <option value="<?php echo $hubobj->id; ?>" <?php echo $selected; ?> ><?php echo $hubobj->name; ?></option>
                    <?php
                            }
                        }
                    }
                ?>
            </select>
        </div>  
    </div>
    
    <br/>
    <?php //if(isset($this->hid) && trim($this->hid)!=""){  class="row"?>
    <?php  if(isset($this->lid) && trim($this->lid)!="" && isset($this->dt) && trim($this->dt)!="" && isset($this->heid) && trim($this->heid)!=""){ ?>
    <div id="tb_po" >  
        <form name="poform" id="poform" method="post" action="formpost/savePOInfo.php">
        <div class="col-md-12"> 
            <?php   if(!empty($locnameobj)){ ?>
               Order set for :
               <?php
                   $cnt = count($locnameobj);
                   $lcnt = 0;
                   foreach($locnameobj as $locnameobj){
                       $lcnt++;
                       if($lcnt<$cnt){
                           echo $locnameobj->name.", ";  
                       }else{
                           echo $locnameobj->name;  
                       }
                   }
               }
           ?> 
            
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Purchase Order </b></h7>
<!--                <div class="table-area">-->
                <!--<div class=" table table-responsive">-->   
                    <!--<div class="table-responsive table-fixedheader">-->  
                     <p class="pull-right">
                        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Search for products..">
                        </p>
                        <table class="table table-responsive" id="tab">
                            <?php
                                $col_seq_id = array();
                                //default
                                $query1 = "select p.id, p.name , 0 as stkqty from it_products p , it_location_products lp where lp.product_id = p.id and lp.is_mapped = 1 and lp.location_id = $this->lid order by p.name ";//limit 10                                    
                                
                                //fetch latest stock for the selected hub
                                    $qry = "select id from it_hub_stock where location_id = $this->heid order by id desc limit 1";
                                    $hsobj = $db->fetchObject($qry);
                                    if(isset($hsobj) && !empty($hsobj) && $hsobj != null){
                                      $query1 = "select p.id, p.name , hs.qty as stkqty from it_products p , it_location_products lp left join it_hub_stock_items hs on lp.product_id = hs.product_id and hs.hub_stock_id = $hsobj->id where lp.product_id = p.id and lp.is_mapped = 1 and lp.location_id = $this->lid order by p.name ";//limit 10                                      
                                    }
//                                    print "<br> QUERY 1: $query1";
                                //if(isset($this->lid) && trim($this->lid)!="" && isset($this->dt) && trim($this->dt)!="" && isset($this->heid) && trim($this->heid)!=""){
                                    //$query1 = "select p.id, p.name from it_products p , it_location_products lp where lp.product_id = p.id and lp.is_mapped = 1 and lp.location_id = $this->lid order by p.name ";//limit 10                                    
                                   // $query3 = "select l.id,l.name from it_locations l, it_location_dependancy ld where l.id=ld.child_location_id and ld.parent_location_id = $this->heid";
                                    $query3 = "select l.id,l.name from it_locations l, it_location_dependancy ld , it_events_info e where l.id=ld.child_location_id and ld.child_location_id = e.location_id and ld.parent_location_id = $this->heid and e.day_of_week = $ex_day_of_week ";
//                                    print $query3;

                                    $pobjs = $db->fetchObjectArray($query1);                                    
                                    $eobjs = $db->fetchObjectArray($query3);
                                ?>
                            <thead>
                                <th>Product Name</th>
                                <th>Current Stock</th>
                                <?php
                                    foreach ($eobjs as $eobj){
                                        echo "<th>$eobj->name</th>";                                        
                                    }
                                ?>
                                <th>Home Delivery</th>
                                <th>Buffer</th>
                                <th>Total</th>
                            </thead>
                            <tbody>
                            <?php
                               // }
                                foreach($pobjs as $pobj){
                                 ?>
                                <tr>
                                    <td>
                                        
                                        <input type="hidden" name="prod[<?php echo $pobj->id; ?>][id]"  id="prod_<?php echo $pobj->id; ?>" value="<?php echo $pobj->name; ?>">                                        
                                            <?php echo $pobj->name; ?>
                                    </td>
                                    <td>
                                        <input type="text" class = "col-xs-12" name="prod[<?php echo $pobj->id; ?>][avl_stock]" id="avl_stock_<?php echo $pobj->id; ?>" value="<?php echo $pobj->stkqty;?>" readonly="">
                                    </td>
                                    <?php
                                    $hdobj=null;
                                    $bobj=null;
                                    $tot = 0;
                                    foreach ($eobjs as $eobj){
                                            $robj = null; //default
                                            if(!empty($poiobjs)){
//                                                print_r($poiobjs);
                                                foreach($poiobjs as $poiobj){
                                                    if(isset($poiobj) && !empty($poiobj) && $poiobj != null){
//                                                        print "<br>PROD ID: $pobj->id :: CLID: $poiobj->child_location_id :: EOBJ : $eobj->id<br> ";
                                                        if($poiobj->child_location_id != "-1" && $poiobj->child_location_id != "-2"){
//                                                            print "<br> IN IF";
                                                            if($poiobj->child_location_id == $eobj->id && $poiobj->product_id == $pobj->id){
                                                                $robj = $poiobj; 
                                                                //$tot = $tot+$robj->qty_in_packets;
//                                                                break;
                                                            }                                                            
                                                        }else if($poiobj->child_location_id == "-1" && $poiobj->product_id == $pobj->id){
//                                                            print "<br> IN ELSE";
                                                            $hdobj = $poiobj;
                                                            //$tot = $tot+$hdobj->qty_in_packets;
                                                        }else if($poiobj->child_location_id == "-2" && $poiobj->product_id == $pobj->id){
//                                                            print "<br> IN ELSE";
                                                            $bobj = $poiobj;
//                                                            $tot = $tot+$bobj->qty_in_packets;
                                                        }//
                                                    }
                                                }
                                            }else{
                                                //to fetch auto last event data
                                                if(!empty($last_poiobjs)){
                                                    foreach($last_poiobjs as $last_poiobj){
                                                        if(isset($last_poiobj) && !empty($last_poiobj) && $last_poiobj != null){
    //                                                        print "<br>PROD ID: $pobj->id :: CLID: $poiobj->child_location_id :: EOBJ : $eobj->id<br> ";
                                                            if($last_poiobj->child_location_id != "-1" && $last_poiobj->child_location_id != "-2"){
    //                                                            print "<br> IN IF";
                                                                if($last_poiobj->child_location_id == $eobj->id && $last_poiobj->product_id == $pobj->id){
                                                                    $robj = $last_poiobj; 
                                                                    //$tot = $tot+$robj->qty_in_packets;
    //                                                                break;
                                                                }                                                            
                                                            }else if($last_poiobj->child_location_id == "-1" && $last_poiobj->product_id == $pobj->id){
    //                                                            print "<br> IN ELSE";
                                                                $hdobj = $last_poiobj;
                                                                //$tot = $tot+$hdobj->qty_in_packets;
                                                            }else if($last_poiobj->child_location_id == "-2" && $last_poiobj->product_id == $pobj->id){
    //                                                            print "<br> IN ELSE";
                                                                $bobj = $last_poiobj;
    //                                                            $tot = $tot+$bobj->qty_in_packets;
                                                            }//
                                                        }
                                                    }
                                                }
                                                
                                            }
//                                            print "<br> TOT : $tot :: QTY: $robj->qty_in_packets ";
//                                            $tot=$tot+$robj->qty_in_packets; 
//                                            print "<br> TOTAL: $tot";
                                        ?>
                                        <td>
                                            <input type="text" class = "col-xs-12 prod_<?php echo $pobj->id; ?>" name="prod[<?php echo $pobj->id; ?>][<?php echo $eobj->id; ?>]" id="event_<?php echo $eobj->id; ?>_<?php echo $pobj->id; ?>"  onkeyup="setTotValue(this.id,this.value);" value="<?php if(isset($robj) && !empty($robj) && $robj!= null){ $tot=$tot+$robj->qty_in_packets; echo $robj->qty_in_packets; } ?>">
                                    </td>
                                    <?php
//                                    print "HOME D:";
//                     print_r($hdobj);
                                        }
                                        
                                        if(empty($eobjs)){
                                            foreach($poiobjs as $poiobj){
                                                if(isset($poiobj) && !empty($poiobj) && $poiobj != null){
                                                    if($poiobj->child_location_id == "-1" && $poiobj->product_id == $pobj->id){
//                                                            print "<br> IN ELSE";
                                                        $hdobj = $poiobj;
                                                        //$tot = $tot+$hdobj->qty_in_packets;
                                                    }else if($poiobj->child_location_id == "-2" && $poiobj->product_id == $pobj->id){
//                                                            print "<br> IN ELSE";
                                                        $bobj = $poiobj;
//                                                            $tot = $tot+$bobj->qty_in_packets;
                                                    }
                                                }
                                            }
                                        }
                                        
                                    ?>
                                    <td>
                                        <input type="text" class = "col-xs-12 prod_<?php echo $pobj->id; ?>" name="prod[<?php echo $pobj->id; ?>][hd]" id="hd_1_<?php echo $pobj->id; ?>" onkeyup="setTotValue(this.id,this.value);" value="<?php if(isset($hdobj) && !empty($hdobj) && $hdobj!= null){ $tot=$tot+$hdobj->qty_in_packets; echo $hdobj->qty_in_packets; } ?>">
                                    </td>
                                    <td>
                                        <input type="text" class = "col-xs-12 prod_<?php echo $pobj->id; ?>" name="prod[<?php echo $pobj->id; ?>][buffer]" id="buffer_1_<?php echo $pobj->id; ?>" onkeyup="setTotValue(this.id,this.value);" value="<?php if(isset($bobj) && !empty($bobj) && $bobj!= null){ $tot=$tot+$bobj->qty_in_packets; echo $bobj->qty_in_packets; } //else{ //echo "0";} ?>">
                                    </td>
                                    <td>
                                        <input type="text" class = "col-xs-12" name="prod[<?php echo $pobj->id; ?>][tot]" id="tot_<?php echo $pobj->id; ?>" value="<?php if(trim($tot)>0){ echo $tot;} ?>" readonly="">
                                    </td>
                                </tr>
                                <?php
                                   unset($hdobj);
                                   unset($bobj);
                                }
                            ?>
                            </tbody>    
                            <!--table created dynamically-->
                              
                        </table>
                        <input type = "hidden" name="form_id" id="form_id" value="poform">
                        <input type="hidden" name="dtsel" id="dtsel" value="<?php echo $this->dt; ?>" >
                        <input type="hidden" name="lid" id="lid" value="<?php echo $this->lid; ?>" >
                        <input type="hidden" name="parent_location_id" id="parent_location_id" value="<?php echo $this->heid; ?>" >
                        <input type="hidden" name="po_id" id="po_id" value="<?php echo $po_id; ?>">
                        <input type="hidden" name="po_date" id="po_date" value="<?php echo $dt; ?>">
                        <input type="hidden" name="ex_date" id="ex_date" value="<?php echo $this->exdt; ?>">
                        <input type ="submit" class="btn btn-primary"  id="save" name="save" style="float:right" value="Save" >
                    <!--</div>-->
                <!--</div>-->
                 
            </div>
        </div>
        <!--<input type ="button" class="btn btn-primary"  id="save" name="save" style="float:right" value="Save" onclick="saveinfo()">-->
        <!--<input type ="button" class="btn btn-primary"  id="submit" name="submit" style="float:left" value="Confirm" >-->
        </form>
        
    </div>
     <?php } ?>
    
     <?php  if ($formResult->form_id == 'poform') { ?>
            <br>
            <div class="col-md-12">              
            <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                <h4> <?php echo $formResult->status; ?>
            </div>
            </div>    
        <?php } if(isset($this->lid) && trim($this->lid)!="" && isset($this->dt) && trim($this->dt)!="" && ((isset($this->heid) && trim($this->heid)!="") || ($formResult->form_id == 'poform'))){  ?>
                <input type="hidden" name="po_id" id="po_id" value="<?php echo $po_id; ?>">
                <input type ="button" class="btn btn-primary"  id="submit" name="submit" style="float:left" value="Confirm" onclick="completePO();" >    
        <?php   } ?>
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


