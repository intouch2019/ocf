 <?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_stocktransfer_additem extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $poid = "";

    function __construct($params = null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
        $this->params = $params;
        if (isset($this->params["transferid"]) != "") {
            $this->transferid = $this->params["transferid"];
        }
    }

    function extraHeaders() {
        ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>
<link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css" rel="stylesheet" type="text/css" /> 
<style type="text/css" title="currentStyle">
    /*  @import "js/datatables/media/css/demo_page.css";
      @import "js/datatables/media/css/demo_table.css";*/
    @import "https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css";
    @import "https://cdn.datatables.net/responsive/2.1.1/css/responsive.bootstrap.min.css";
    div.alert *
    {
        color: red;
    }
</style>
<script type="text/javaScript">   

$(function () {  


    $('.selectpicker').selectpicker({
    dropupAuto: false
});

    
            $('#batchcodes').on('change', function(){  
                var selected = $(this).find("option:selected");
                var arrSelected = []; 
                var count = 0;  
                var totQty = 0; 
                var pieces = 0;  
                selected.each(function(){ 
                    var v = $(this).val().split("::");
                    var qt = parseFloat(v[3]);
                    //alert("qty"+qt);
                    var prodsel = $("#transferitem").val();
                    var length = parseFloat(v[2]);
                    //alert("length"+length);
                    var arr = prodsel.split("::"); 
                    var prodid = arr[1];
                    var kgperpc = parseFloat(arr[2]); 
                    if(kgperpc > 0 && qt > 0 && length > 0){
                         var piece = qt / ((length / 1000) * kgperpc); 
                         piece = Math.round(piece);
                    }
                    pieces = pieces + piece;
                    count++;
                    if(count > 1){
                        document.getElementById('qty').readOnly = true;
                        document.getElementById('pieces').readOnly = true;
                    }else{
                        document.getElementById('qty').readOnly = false;
                        document.getElementById('pieces').readOnly = false;
                    }
                    totQty = totQty + qt;
                    $("#qty").val(totQty);
                    $("#length").val(length);
                    $("#pieces").val(pieces);
                    $("#availableqty").val(totQty);
                });
                //alert(count);
                $("#selectedbatches").val(count);
                $("#qty").val(totQty);
                var qt = parseFloat($("#qty").val());
                var selectedBatches = $("#selectedbatches").val();
             
             });
             
             $('#batchcodes1').on('change', function(){
                var selected = $(this).find("option:selected");
                var arrSelected = [];
                var count = 0;
                var totQty = 0;
                var pieces = 0;
                selected.each(function(){
                    var v = $(this).val().split("::");
                    var qt = parseFloat(v[3]);
                    var prodsel = $("#transferitem").val();
                    var length = parseFloat(v[2]); 
                    //alert(length);
                    var arr = prodsel.split("::"); 
                    var prodid = arr[1];
                    var batchcodepieces = parseFloat(v[4]);
                    var kgperpc = parseFloat(arr[2]);
//                    if(kgperpc > 0 && batchcodepieces > 0 && length > 0){
//                         var piece = batchcodepieces * length * (kgperpc/1000);  
//                         alert("pieces "+piece);
//                         piece = Math.round(piece);
//                    }
                    pieces = pieces + batchcodepieces;
                    count++;
                    if(count > 1){
                        document.getElementById('qty2').readOnly = true;
                        document.getElementById('pieces2').readOnly = true;
                    }else{
                        document.getElementById('qty').readOnly = false;
                        document.getElementById('pieces').readOnly = false;
                    }
                    totQty = totQty + qt;
                    $("#length").val(length);
                    $("#qty2").val(totQty);
                    $("#pieces2").val(pieces);
                    $("#availablepcs").val(pieces);
                    $("#availableqty").val(totQty);
                });
                //alert(count);
                //$("#selectedbatches").val(count);
                //$("#qty").val(totQty);
                //var qt = parseFloat($("#qty").val());
                //var selectedBatches = $("#selectedbatches").val();
             
             }); 
             
             $("#qty").click(function(){
                if ( $('#qty').is('[readonly]') ) { 
                   alert("You cannot change qty when multiple batchcode is selected"); 
                }
            });
      });        

  function calcValue(v){ 
                var prodsel = $("#poitem").val(); 
                var length = parseFloat($("#length").val());    
                var arr = prodsel.split("::");   
                var prodid = arr[1]; 
                var kgperpc = parseFloat(arr[2]);
                var qt = parseFloat($("#qty").val());
                var rate = parseFloat($("#rate").val());
                var lccharge = parseFloat($("#lcrate").val());   
                if(isNaN(lccharge)){
                     lccharge = 0;
                }
                
                if(rate > 0){
                    //var lcrate = rate + 0.15;
                    var lcrate = rate + lccharge; 
                    var sgst = lcrate * 0.09;
                    sgst = roundToTwo(sgst);
                    $("#sgst").val(sgst);
                    var cgst = lcrate * 0.09;
                    cgst = roundToTwo(cgst);
                    $("#cgst").val(cgst);
                    var totalrate = parseFloat(lcrate) + parseFloat(sgst) + parseFloat(cgst);
                    //var totalrate = lcrate + sgst + cgst;
                    totalrate = roundToTwo(totalrate);
                    $("#totalrate").val(totalrate);
                    var value = qt * totalrate;
                    //alert(value);
                    $("#value").val(roundToTwo(value));
                }

                if(kgperpc > 0 && qt > 0 && length > 0){
                    var piece = qt / ((length / 1000) * kgperpc);
                    piece = Math.round(piece);
                    $("#pieces").val(piece);
                }
            }

    function deleteItem(itemid){ 
//        alert(itemid);
      var r = confirm("Are you sure you want to delete this item");
         if(r){ 
          var remarks = $('#remarks').val();  
          var ajaxURL = "ajax/deleteSTOitem.php?itemid=" + itemid;  
              //alert(ajaxURL);
              $.ajax({
              url:ajaxURL,
                  dataType: 'json',
                  success:function(data){
                      //alert(data.error);
                      if (data.error == "1") {
                          alert(data.msg);
                      } else {
                          alert("STO item deleted successfully.")
                          window.location.href = "stocktransfer/additem/transferid="+<?php echo $this->transferid;?>;  
                      }
                  }
              });
        }
    }

    function roundToThree(num) {    
      return +(Math.round(num + "e+3")  + "e-3");
    }

    function roundToFour(num) {    
      return +(Math.round(num + "e+4")  + "e-4");
    } 
            

    function selectStockItem(id){

        var arr = id.split("::");
        var stockid = arr[0];
        var length = arr[1];
        var fromlocid = $("#frmlocid").val();
        var fromloctype = $("#frmloctype").val();
        var ajaxURL = "ajax/getStockItemValue.php?stockcurrid=" + stockid+"&fromlocid="+fromlocid+"&fromloctype="+fromloctype;  
        $.ajax({
        url:ajaxURL,
            dataType: 'json',
            success:function(data){
                if (data.error == "1") { 
                    alert(data.msg);
                } else {
                    //$("#availableqty").val(data.availableqty);  
                    //$("#length").val(length);
                    //$("#batchcode").val(data.batchcode);
                }
            }
        });
    }
    
    function selectBatchCode(stockcurrid){ 
        //alert(stockcurrid);
        var arr = stockcurrid.split("::");
        var stockid = arr[0];
        var prodid = arr[1];
        var fromlocid = $("#frmlocid").val();
        var fromloctype = $("#frmloctype").val();
        /*var ajaxURL = "ajax/getGRNItemValue.php?prodid=" + prodid +"&grnid="+grnid; */
        var ajaxURL = "ajax/getBatchcodeByProduct.php?stockcurrid=" + prodid+"&fromlocid="+fromlocid+"&fromloctype="+fromloctype; 
        // alert(ajaxURL);
        $.ajax({
        url:ajaxURL,
            //dataType: 'json',
            cache: false,
            success:function(html){
                //alert(html);
                    $('#batchcodes').append(html); 
                    $('#batchcodes1').append(html);
                    $("#batchcodes").selectpicker('refresh');
                    $("#batchcodes1").selectpicker('refresh'); 
                    
//                     $(document).on('change', '.selectpicker', function () {
//                       $('.selectpicker').selectpicker('refresh'); 
//                     });
            }
        });
    }
    
    function checkqty(){
        var prodsel = $("#transferitem").val();
        var length = parseFloat($("#length").val()); 
        //alert(length);
        var arr = prodsel.split("::"); 
        var prodid = arr[1];
        var kgperpc = parseFloat(arr[2]);
        var enterqty = parseFloat($("#qty").val());
        var availableqty = parseFloat($("#availableqty").val());
        //alert(availableqty);
        if(kgperpc > 0 && enterqty > 0 && length > 0){
            //alert(kgperpc);
            //alert(enterqty);
           // alert(length);
            var piece = enterqty / ((length / 1000) * kgperpc); 
            piece = Math.round(piece);
            //alert(piece);
            $("#pieces").val(piece);
        }
        if(enterqty > availableqty){
            alert("Please enter qty less than available qty");
            $("#pieces").val("");
            $("#qty").val("");
            return false;
        }else{ 
            return true;
        }
    }
    
    function checkpieces(){
        //alert("here");
        var prodsel = $("#transferitem").val();
        //alert(prodsel);
        var length = parseFloat($("#length").val()); 
        var arr = prodsel.split("::"); 
        var prodid = arr[1];
        var kgperpc = parseFloat(arr[2]);
        var pieces = parseFloat($("#pieces2").val());
        var availablepcs = parseFloat($("#availablepcs").val());
        if(pieces > availablepcs ){
            alert("Please enter pieces less than available pieces"); 
            $("#pieces2").val("");
            $("#qty2").val("");
        }
        var availableqty = parseFloat($("#availableqty").val()); 
        if(kgperpc > 0 && pieces > 0 && length > 0){
            
            var qty = pieces * length * (kgperpc/1000);  
             //alert(qty);
             qty = roundToTwo(qty);
             //alert(qty);
            $("#qty2").val(qty);
        }
        var calculatedqty = parseFloat($("#qty2").val());
        if(calculatedqty > availableqty){
            alert("Please enter qty less than available qty"); 
            $("#pieces2").val("");
            $("#qty2").val("");
            return false;
        }else{ 
            return true;
        }
    }
    
    function receiveinKg(){
    var checkBox1 = document.getElementById("check1");
    var div1 = document.getElementById("checkbox1");
        if (checkBox1.checked == true){
            div1.style.display = "block";
        }else{
            div1.style.display = "none";
        }
    }
    
    function receiveinPieces(){
    var checkBox2 = document.getElementById("check2");
    var div2 = document.getElementById("checkbox2");
        if (checkBox2.checked == true){
            div2.style.display = "block";
        }else{
            div2.style.display = "none";
        }
    }
    
    function roundToTwo(num) {    
      return +(Math.round(num + "e+2")  + "e-2"); 
    }
    
    function hideButton(btnId){
                document.getElementById(btnId).style.display = "none";
    }
        </script>
        <link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
    }

    public function pageContent() {
        $menuitem = "stocktransfer"; //pagecode
        include "sidemenu.php";
        $formResult = $this->getFormResult();
        $dbl = new DBLogic();
        $obj_states = $dbl->getStates();

        $obj_categories = $dbl->getAllActiveCategories();
        $obj_products = $dbl->getAllActiveProducts();
        $obj_colors = $dbl->getAllColors();
        $obj_GstPer = $dbl->getAllGSTPer();
        $obj_transports = $dbl->getAllTransports();
        $obj_brands = $dbl->getAllBrands();
        $obj_manufacturers = $dbl->getAllManufacturers();
        $obj_suppliers = $dbl->getAllActiveSuppliers();
        $obj_specifications = $dbl->getAllActiveSpecifications();
        $obj_sttrns = $dbl->getStockTransferDetails($this->transferid);
        //print_r($obj_sttrns);
        $obj_stockitems = $dbl->getStockTransferItems($this->transferid);
        //print_r($obj_stockitems);
        $obj_stockcurrent = $dbl->getStockcurrentDetails($obj_sttrns->from_location_id,$obj_sttrns->from_location_type);
        $obj_products = $dbl->getAllActiveProducts();
        //print_r($obj_stockcurrent);
   
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Purchase Order Details</h2>
                            <div class="common-content-block">
                                <input type="hidden" name="transferid" id="transferid" value="<?php echo $this->transferid; ?>"/>
                                <div class="box box-primary"><br>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label>Purchase Order No : <?php echo $obj_sttrns->transferno; ?></label>
                                        </div>
                                         <div class="col-md-3">
                                            <label>Purchase Order Date : <?php echo $obj_sttrns->transferdate; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <!-- <label>From Location : <?php echo $obj_sttrns->fromloc; ?></label> -->
                                        </div>
                                        <div class="col-md-3">
                                            <label>Stockist : <?php echo $obj_sttrns->toloc; ?></label>
                                        </div>
                                    </div>
                                    
                                </div>   
                            </div>
                        </div>
                    </div>
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Add Items</h2>
                            <div class="common-content-block">
                               
                                    <form role="form" id="grnadditem" name="grnadditem" enctype="multipart/form-data" method="post" action="formpost/stocktransferadditem.php">
                                        <input type="hidden" name="transferid" id="transferid" value="<?php echo $this->transferid; ?>"/>
                                            <div class="col-md-12">
                                                <label>Select Item</label>
                                                <select id="transferitem" name="transferitem" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="selectBatchCode(this.value);">
                                                    <option value="-1">Select Stock Item</option>
                                                    <?php
                                                    foreach ($obj_products as $prod) {
                                                        $desc1 = isset($prod->desc1) && trim($prod->desc1) != "" ? " , " . $prod->desc1." CV" : "";
                                                        $desc2 = isset($prod->desc2) && trim($prod->desc2) != "" ? " x " . $prod->desc2." PAC": "";
                                                        $thickness = isset($prod->thickness) && trim($prod->thickness) != "" ? " , " . $prod->thickness." weight(gm/case)" : "";
                                                        $product = $prod->prod . $desc1 . $desc2 . $thickness;
                                                        ?>
                                                        <option value="<?php echo $prod->id . "::" .$prod->id ."::".$item->kg_per_pc; ?>"><?php echo $product; ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                            </div>
                                        
                                        <div class="col-md-12" id="checkbox1">
                                             <div class="col-md-4">
                                                <label>Enter Required Qty</label><br>
                                                <input type="text" id="qty" name="qty" class="form-control" placeholder="Qty " value="<?php echo $this->getFieldValue("qty"); ?>" onkeyup="checkqty()" autocomplete="off">
                                            </div> 
                                        </div> 
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary" id="add_item_button" style="display:block" onclick="hideButton(this.id);">Add Item</button>
                                            </div>
                                        </div>
                                         <?php if ($formResult->form_id == 'stocktransfer') {  ?>
                                        <div class="alert" style="display:<?php echo $formResult->showhide; ?>;"<?php echo $formResult->status; ?>>
                                            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4> <?php echo $formResult->status; ?>
                                        </div>
                                            <?php }  ?>
                                    </form>                                 
                                 
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>Sl.No.</td>
                                                <td>Product</td>
                                                <td>Qty</td>
                                                <td>Action</td>
                                        
<!--                                                <td>Action</td>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $srno = 1;
                                            $tot_qty = 0;
                                            $itemCount = 0;
                                            setlocale(LC_MONETARY,"en_IN");
                                            $total_value = 0;
                                            if ($obj_stockitems != NULL) {
                                                foreach ($obj_stockitems as $item) {
                                                    $itemCount++;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $srno; ?></td>
                                                        <td><?php
                                                             $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                             $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                             $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                             $itemname = $item->prod.$desc1.$desc2.$thickness;
                                                               echo $itemname;
                                                            ?>
                                                        </td>
                                                        <td><?php echo $item->qty; ?></td>
                                                        <?php $itemtotalval =  $item->qty *1;?>
                                                        
                                                        <td><input class="btn btn-primary" type="button" name="deleteItem" id="deleteItem" value="Delete" onclick="deleteItem(<?php echo $item->id; ?>);" /></td>
                                                    </tr>
                                                            <?php
                                                            $srno = $srno + 1;
                                                            $tot_qty = $tot_qty + round($item->qty,4);
                                                            
                                                }
                                            }
                                            ?>
                                        </tbody> 
                                        <tfoot>
                                            <tr>
                                                <td>Total</td>
                                                <td></td>
                                               
                                                <td><?php echo $tot_qty; ?></td>
                                                <td></td>
                                               
<!--                                                <td></td>-->
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                               <div class="col-md-12">
                                   <form role="form" id="submitpo" name="submitpo" enctype="multipart/form-data" method="post" action="formpost/submitStockTransfer.php">
                                       <input type="hidden" name="itemcount" id="itemcount" value="<?php echo $itemCount; ?>"/>
                                       <input type="hidden" name="transferid" id="transferid" value="<?php echo $this->transferid; ?>"/>
                                <?php //if($obj_po->is_freightapplicable){?>
                                      <!-- <div class="col-md-12">
                                            
                                            <h2 class="title-bar">Add Freight</h2>
                                            <div class="col-md-3">
                                                <label>Freight amount</label><br>
                                                <input type="text" id="freightamt" name="freightamt" class="form-control" placeholder="Freight amount" value="<?php  echo $this->getFieldValue("frightamt"); ?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Select Applicable GST</label><br>
                                                <select id="gstsel" name="gstsel" onchange="changeTest()" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Applicable GST</option>
                                                       <?php foreach ($obj_GstPer as $gstper) {
                                                        ?>
                                                        <option value="<?php echo $gstper->value; ?>"><?php echo $gstper->tax_name; ?></option>
                                                       <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Freight GST</label><br>
                                                <input type="text" id="freightgst" name="freightgst" class="form-control" placeholder="freight GST" value="<?php echo $this->getFieldValue("freightgst")?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Freight + GST</label><br>
                                                <input type="text" id="totalfreight" name="totalfreight" class="form-control" placeholder="freight + GST" value="<?php echo $this->getFieldValue("totalfreight");?>">
                                            </div> 

                                        </div> -->
                                        <?php //if($obj_po->is_transportapplicable) {?>
                                      <!--  <div class="col-md-12">
                                            <br>
                                            <div class="col-md-3">
                                                <label>Select transportation</label><br>
                                                <select id="transportsel" name="transportsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select transportation</option>
                                                        <?php foreach ($obj_transports as $transports) {
                                                        ?>
                                                        <option value="<?php echo $transports->id; ?>"><?php echo $transports->name; ?></option>
                                                        <?php } ?>
                                                </select>
                                            </div>
                                        </div>-->
                                <?php //}?>
                                        <?php //}?>
                                        <div class="col-md-12">
                                            <label>&nbsp;</label><br/>
                                            <button type="submit" class="btn btn-primary" id="save_button" style="display:block" onclick="hideButton(this.id);">Save and Submit</button>
                                        </div>
<!--                                        <input type="hidden" name="pid" id="pid" value="<?php echo $this->poid; ?>">-->

                                    </form>
                                </div> 
    
                            </div>
                        </div>
                    </div> <!--Add Items>   
-->                </div>
            </div> 
        </div><!-- end 
<!--
               <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>      -->


        <?php
        // }else{ print "You are not authorized to access this page";}
    }

}
?>


