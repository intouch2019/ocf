<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_report_po_aview_details extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $poid;        
        var $lid;  
        var $dt;
       
        function __construct($params=null) {
    //parent::__construct(array());
           $this->currStore = getCurrStore();

           $this->params = $params;

            if ($params && isset($params['poid'])) { 
                $this->poid = $params['poid'];
            }

         
            if ($params && isset($params['lid'])) { 
               $this->lid = $params['lid'];
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
    });  
}); 

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

function genExcel(){
    window.location.href = "formpost/genPONyViewExcel.php";
}

function loadDTWise(seldt){
//    alert(seldt);
    window.location.href = "report/po/aview/dt="+seldt;
}

function loadpowise(poid){    
    var seldt = $("#seldate").val();
    window.location.href = "report/po/aview/dt="+seldt+"/poid="+poid;
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
            
            
?>    
<div class="container-section">    
    <?php if(isset($this->poid) && trim($this->poid)!="" && isset($this->lid) && trim($this->lid)!=""){ 
                $poiobjs = $dblogic->fetchPOItems($this->poid,$this->lid);
                if(!empty($poiobjs)){
                    $qry = "select l.name as locname from it_locations l where l.id = $this->lid ";
                    $lobj = $db->fetchObject($qry);
                    $lname="-";
                    if(isset($lobj) && !empty($lobj) && $lobj != null){
                        $lname = $lobj->locname;
                    }
                    
                      //$lobj = $clsLocation->  
//                    foreach($poiobjs as $poiobj){
//                        if(isset($poiobj) && !empty($poiobj) && $poiobj != null){
      ?>
    <div id="tb_po" >         
        <div class="col-md-12">              
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lname; ?></b></h7>
                     <p class="pull-right">
                        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Search for products..">
                        </p>
                        <table class="table table-responsive" id="tab">
                            <?php
//                                $col_seq_id = array();
//                                //to fetch products
                                $query = "select p.name as product, pi.product_id from it_purchase_order_items pi , it_products p where pi.product_id = p.id and pi.po_id = $this->poid and pi.parent_location_id = $this->lid group by pi.product_id";//limit 10                                    
                                $prodobjs = $db->fetchAllObjects($query);
                                
                                //qry for dynamic cols
                                $query = "select l.name,child_location_id from it_purchase_order_items p left join it_locations l on  p.child_location_id = l.id  where   p.po_id = $this->poid and p.parent_location_id = $this->lid  group by child_location_id order by child_location_id desc ";
                                $cobjs = $db->fetchAllObjects($query);
                                
                                ?>
                            <thead>
                                <th>Product Name</th>                                
                                <!--<th>Product UOM</th>-->
                                <?php
                                    foreach ($cobjs as $cobj){
                                        $colname="";
                                        if(trim($cobj->child_location_id) == "-1"){
                                            $colname =  "Home Delivery";
                                        }else if(trim($cobj->child_location_id) == "-2"){
                                            $colname =  "Buffer";
                                        }else{
                                            $colname = $cobj->name;
                                        }
                                        echo "<th>$colname</th>";                                        
                                    }
                                ?> 
                                <th>Total</th>                                
                            </thead>
                            <tbody>
                            <?php
                               // }
                                foreach($prodobjs as $prodobj){
                                    //fetch data for this item
                                    $query = "select id,po_id,parent_location_id,child_location_id,product_id,qty_in_packets from it_purchase_order_items where po_id = $this->poid and parent_location_id = $this->lid and product_id = $prodobj->product_id order by child_location_id desc ";
                                    $dobjs = $db->fetchAllObjects($query);        
                                ?>
                                <tr>
                                    <td><?php echo $prodobj->product ;?></td>                                    
                                <?php
                                 $tot_packets = 0;
                                    foreach($dobjs as $dobj ){
                                        
                                         if(isset($dobj) && !empty($dobj) && $dobj!= null){
                                 ?>
                                
                                    <td><?php echo $dobj->qty_in_packets; ?></td>                                    
                                    <?php
                                       $tot_packets = $tot_packets + $dobj->qty_in_packets;
                                         }
                                    }
                                   ?>
                                    <td><?php echo $tot_packets; ?></td>                                    
                                </tr>    
                                    
                                
                                <?php                                   
                                }
                            ?>
                            </tbody>                                                              
                        </table>                       
                 
            </div>
        </div>        
    </div>
                    <?php //} } 
                    
                                    } } ?>
    
<!--            <h5>No Purchase Order for today's purchasing generated Yet. !!</h5>-->
     


            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>
</div> 

