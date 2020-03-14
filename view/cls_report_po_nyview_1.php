<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_report_po_nyview extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $lid;
        var $heid;
        var $dt;
       
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
           }
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
}); 
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
//    alert(poid);
    var r = confirm("Are you sure you want to confirm ? ");
    if(r){
        var myWindow = window.open('',"_blank");
        var ajaxurl = "ajax/markPOCompleted.php?poid="+poid+"&lid=<?php echo $this->lid; ?>";
//                alert(ajaxurl);
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
            $po_id = -1;
            $po_date = date('Y-m-d');
            $query = "select id from it_purchase_orders where pur_location_id = ".$this->currStore->location_id." and po_date = '$po_date' and status = ".POStatus::Completed;
            $pobj = $db->fetchObject($query);
            
?>

<div class="container-section">    
    <?php  if(isset($pobj) && ! empty($pobj) && $pobj!= null ){ ?>
    <div id="tb_po" >          
        <div class="col-md-12">              
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Purchase Order </b></h7>
                     <p class="pull-right">
                        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Search for products..">
                        </p>
                        <table class="table table-responsive" id="tab">
                            <?php
                                $col_seq_id = array();
                                //to fetch products
                                $query = "select pi.product_id,p.name as product from it_purchase_order_items pi , it_products p  where pi.product_id = p.id and pi.po_id = $pobj->id group by product_id order by product";//limit 10                                    
                                $prodobjs = $db->fetchAllObjects($query);
                                
                                //qry for dynamic cols
                                $query = "select l.name,parent_location_id from it_purchase_order_items p , it_locations l where   p.parent_location_id = l.id  and   po_id = $pobj->id  group by parent_location_id order by parent_location_id ";
                                $cobjs = $db->fetchAllObjects($query);
   
                                ?>
                            <thead>
                                <th>Product Name</th>                                
                                <?php
                                    foreach ($cobjs as $cobj){
                                        echo "<th>$cobj->name</th>";                                        
                                    }
                                ?>                               
                            </thead>
                            <tbody>
                            <?php
                               
                                foreach($prodobjs as $prodobj){
                                    //fetch data for this item
                                    $query = "select id,product_id,parent_location_id, sum(qty_in_packets) as tot_packets from it_purchase_order_items where po_id = $pobj->id and product_id = $prodobj->product_id group by product_id,parent_location_id order by parent_location_id ";
                                    $dobjs = $db->fetchAllObjects($query);        
                                ?>
                                <tr>
                                    <td><?php echo $pobj->product ;?></td>
                                <?php
                                    foreach($dobjs as $dobj ){
                                         if(isset($dobj) && !empty($dobj) && $dobj!= null){
                                 ?>
                                
                                    <td><?php echo $dobj->tot_packets; ?></td>                                    
                                    <?php
                                         }
                                    }
                                   ?>
                                </tr> 
                                <?php } ?>
                            </tbody>    
                                                      
                        </table>                                         
            </div>
        </div>        
        
    </div>
     <?php } ?>        
</div> 