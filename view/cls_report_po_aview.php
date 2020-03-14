<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";

class cls_report_po_aview extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
        var $poid;        
        var $dt;
       
        function __construct($params=null) {
    //parent::__construct(array());
           $this->currStore = getCurrStore();

           $this->params = $params;

            if ($params && isset($params['poid'])) { 
                $this->poid = $params['poid'];
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
    var poid = $("#poid").val();
    var pono = $("#pono").val();
    //alert("POID: "+poid+" PONO "+pono);
    window.location.href = "formpost/genPOExcel.php?poid="+poid+"&pono="+pono;
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
        <div class="row">         
         <div class="col-md-4">
            <div class="input-group date" id="datepicker" >
                <input type="text" class="form-control" name ="seldate" id = "seldate" value="<?php if(isset($this->dt) && trim($this->dt)!=""){ echo $this->dt; }else{ echo "Select Date"; } ?>" onchange="loadDTWise(this.value);">
                <div class="input-group-addon" >
                    <span class="glyphicon glyphicon-th"></span>
                </div>
            </div> 
        </div>
        <div class="col-md-3">
            <select id="selpo" name="selpo" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="loadpowise(this.value);" >
                <option value="">Select PO</option>
                <?php
                if(isset($this->dt) && trim($this->dt)!=""){
                    $poobjs = $dblogic->getPOs($this->dt);  // function added in clsLocation                 
//                    print_r($hubobjs);
                    if(!empty($poobjs)){
                        foreach($poobjs as $poobj){
                            if(isset($poobj) && !empty($poobj) && $poobj != null){
                                $selected="";
                                if($poobj->id == $this->poid){
                                    $selected = "selected";
                                }
                    ?>
                <option value="<?php echo $poobj->id; ?>" <?php echo $selected; ?> ><?php echo $poobj->po_no; ?></option>
                    <?php
                            }
                        }
                    }
                }
                ?>
            </select>
        </div>  
    </div>

    <?php  if(isset($this->dt) && trim($this->dt)!="" && isset($this->poid) && trim($this->poid)!=""){ 
            $query = "select id,po_no,po_date from it_purchase_orders where id = $this->poid";
            $pobj = $db->fetchObject($query);
            
            if(isset($pobj) && !empty($pobj) && $pobj!= null){
    ?>
<br><br>
<div class="col-md-12"> 
<button class="btn btn-primary" onclick="genExcel();">Excel Download</button>
</div>
<br><br>
    <div id="tb_po" >         
        <div class="col-md-12">              
            <div class="panel panel-default">
                <h7><b>&nbsp;&nbsp;&nbsp;&nbsp;Purchase Order Details</b>
                <b>&nbsp;&nbsp;&nbsp;&nbsp;Date: <?php echo ddmmyy($pobj->po_date); ?></b></h7>
                     <p class="pull-right">
                        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Search for products..">
                        </p>
                        <table class="table table-responsive" id="tab">
                            <?php
                                $col_seq_id = array();
                                $colcnt=0;
                                //to fetch products
                                $query = "select pi.product_id,p.name as product,p.uom_id,u.name as uom, p.pack_size_id, ps.pack_size from it_purchase_order_items pi , it_products p , it_uom u , it_pack_size ps  where pi.product_id = p.id and p.uom_id = u.id and p.pack_size_id = ps.id and pi.po_id = $pobj->id group by product_id order by product";//limit 10                                    
                                $prodobjs = $db->fetchAllObjects($query);
                                
                                //qry for dynamic cols
                                $query = "select l.name,parent_location_id from it_purchase_order_items p , it_locations l where   p.parent_location_id = l.id  and   po_id = $pobj->id  group by parent_location_id order by parent_location_id ";
                                $cobjs = $db->fetchAllObjects($query);
                                
                                ?>
                            <thead>
                                <th>Product Name</th>                                
                                <th>Product UOM</th>
                                <?php
                                    foreach ($cobjs as $cobj){                                        
                                        echo "<th>$cobj->name</th>";                                        
                                        $colcnt++;
                                    }
                                ?> 
                                <th>Total in Packets</th>
                                <th>Total </th>
                            </thead>
                            <tbody>
                            <input type="hidden" name="poid" id="poid" value="<?php echo $this->poid; ?>">
                            <input type="hidden" name="pono" id="pono" value="<?php echo $pobj->po_no; ?>">
                            <?php
                               // }
                                foreach($prodobjs as $prodobj){
                                    //fetch data for this item
                                    $query = "select id,product_id,parent_location_id, sum(qty_in_packets) as tot_packets from it_purchase_order_items where po_id = $pobj->id and product_id = $prodobj->product_id group by product_id,parent_location_id order by parent_location_id ";
                                    $dobjs = $db->fetchAllObjects($query);        
                                ?>
                                <tr>
                                    <td><?php echo $prodobj->product ;?></td>
                                    <td><?php echo $prodobj->uom ;?></td>
                                <?php
                                 $tot_packets = 0;
                                 $datacnt = 0;
                                    foreach($dobjs as $dobj ){
                                        
                                         if(isset($dobj) && !empty($dobj) && $dobj!= null){
                                 ?>
                                
                                    <td><?php echo $dobj->tot_packets; ?><br/><a href="report/po/aview/details/poid=<?php echo $pobj->id; ?>/lid=<?php echo $dobj->parent_location_id; ?>">Details</a></td>                                    
                                    <?php
                                       $tot_packets = $tot_packets + $dobj->tot_packets;
                                       $datacnt ++;
                                         }
                                    }
                                    
                                    if($datacnt < $colcnt){
                                        $diff = $colcnt - $datacnt;
                                        for($i=0;$i<$diff;$i++){
                                            ?>
                                         <td><?php echo "-";?></td>
                                    <?php
                                        }
                                    }
                                   ?>
                                    <td><?php echo $tot_packets; ?></td>
                                    <?php
                                            //conversion logic
                                    //as of now only gm-kg conversion will be done
                                    //step 1 : check uom
//                                    print "<br> PROD NAME: $prodobj->product :: UOM: $prodobj->uom ";
                                    $tot_kg = "-";
                                    if(strcmp($prodobj->uom, "gms") == 0 || strcmp($prodobj->uom, "gm") ==  0 || strcmp($prodobj->uom, "grams") == 0){
                                        $pack_size_arr = explode(" ", $prodobj->pack_size);
                                        $pack_size_no = $pack_size_arr[0];
                                        
                                        $tot_grms = $tot_packets * $pack_size_no;
                                        $tot_kg = round(($tot_grms/1000),2);
                                        $tot_kg .= " (kg) ";
                                        
                                    }else{
                                        $tot_kg = $tot_packets;
                                        $tot_kg .= " (".$prodobj->uom.") ";
                                    }
                                    
                                    ?>
                                    
                                    <td><?php echo $tot_kg; ?></td>
                                </tr>    
                                    
                                
                                <?php                                   
                                }
                            ?>
                            </tbody>                                                              
                        </table>                       
                 
            </div>
        </div>        
    </div>
    <?php } }else{ ?>
<!--            <h5>No Purchase Order for today's purchasing generated Yet. !!</h5>-->
     <?php }?>


            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>
</div> 

