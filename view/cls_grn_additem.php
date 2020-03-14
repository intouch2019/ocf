<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_grn_additem extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $poid = "";

    function __construct($params = null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
        $this->params = $params;
        if (isset($this->params["grnid"]) != "") {
            $this->grnid = $this->params["grnid"];
        }
        
          if(isset($this->params["uom"]) != ""){
                $this->uom = $this->params["uom"];
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

$(function(){
    var uom = <?php echo $this->uom; ?>;
    if(uom == 2){
        $('#qty').attr('readonly', 'readonly');
    }else if(uom==1){
        $('#mtqty').attr('readonly', 'readonly');
    }
    
    
}); 
  function calcValue(v){ 
                var prodsel = $("#poitem").val();   
                //alert(prodsel);
                var length = parseFloat($("#length").val());  
                var arr = prodsel.split("::"); 
                var prodid = arr[0]; 
                var kgperpc = parseFloat(arr[1]);  
                var qt = parseFloat($("#qty").val()); 
                var rate = parseFloat($("#rate").val()); 
                //var lccharge = parseFloat($("#lcrate").val()); 
                var lccharge = 0;
                var mtqty = roundToFour(qt / 1000);
                if(mtqty > 0){
                    $("#mtqty").val(mtqty);
                }
                if(isNaN(lccharge)){  
                     lccharge = 0; 
                } 
                
                if(rate > 0){ 
                    //var lcrate = rate + 0.15;
                    var lcrate = rate + lccharge; 
                    var totRate = roundToTwo(lcrate * mtqty);
                    var sgst = lcrate * 0.09; 
                    var totSgst = roundToTwo(sgst * mtqty);
                    sgst = roundToTwo(sgst);
                    $("#sgst").val(sgst);
                    var cgst = lcrate * 0.09;
                    cgst = roundToTwo(cgst);
                    var totCgst = roundToTwo(cgst * mtqty);
                    $("#cgst").val(cgst); 
                    var totalrate = parseFloat(lcrate) + parseFloat(sgst) + parseFloat(cgst);
                    //var totalrate = lcrate + sgst + cgst;
                    totalrate = roundToTwo(totalrate); 
                    $("#totalrate").val(totalrate);
                    //var value = qt * totalrate;
                    var value = roundToTwo(totRate + totSgst + totCgst);
                    //alert(value);
                    $("#value").val(value); 
                }

                if(kgperpc > 0 && qt > 0 && length > 0){
                    var piece = qt / ((length / 1000) * kgperpc); 
                    piece = Math.round(piece);
                    $("#pieces").val(piece);
                }
            }
            
      function calcValuecheck2(v){ 
        var prodsel = $("#poitem").val();
        //alert(prodsel);
        var length = parseFloat($("#length").val());
        var arr = prodsel.split("::");
        var prodid = arr[0];
        var kgperpc = parseFloat(arr[1]);
        //alert(kgperpc);
        var pieces = parseFloat($("#pieces2").val()); 
        var rate = parseFloat($("#rate2").val());
        var lccharge = parseFloat($("#lcrate2").val());
        if(isNaN(lccharge)){
            lccharge = 0;  
        } 
        //alert(lccharge);
        
        if(kgperpc > 0 && pieces > 0 && length > 0){
            var qty = pieces * length * (kgperpc/1000);
            qty = roundToTwo(qty);
            $("#qty2").val(qty);
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
            var value = qty * totalrate;
            //alert(value);
            $("#value").val(roundToTwo(value));  
        }

//        if(kgperpc > 0 && pieces > 0 && length > 0){
//            var qty = pieces * length * (kgperpc/1000);
//            qty = Math.round(qty);
//            $("#qty").val(qty);
//        }
    }
//

function calcValueinMT(v){ 
                var prodsel = $("#poitem").val();
                var length = parseFloat($("#length").val());
                var arr = prodsel.split("::");
                var prodid = arr[0];
                var kgperpc = parseFloat(arr[1]);
                var mtqty = parseFloat($("#mtqty").val());
                var qt = roundToTwo(mtqty * 1000);
                if(qt > 0){
                    $("#qty").val(qt);
                }
                var rate = parseFloat($("#rate").val());
                //var lccharge = parseFloat($("#lcrate").val());
                var lccharge = 0;
                if(rate > 0){
                    //var lcrate = rate + 0.15;
                    var lcrate = rate + lccharge; 
                    var totRate = roundToTwo(lcrate * mtqty);
                    var sgst = lcrate * 0.09;
                    sgst = roundToTwo(sgst);
                    var totSgst = roundToTwo(sgst * mtqty);
                    $("#sgst").val(sgst);
                    var cgst = lcrate * 0.09;
                    cgst = roundToTwo(cgst);
                    var totCgst = roundToTwo(cgst * mtqty);
                    $("#cgst").val(cgst);
                    var totalrate = parseFloat(lcrate) + parseFloat(sgst) + parseFloat(cgst);
                    //var totalrate = lcrate + sgst + cgst;
                    totalrate = roundToTwo(totalrate);   
                    $("#totalrate").val(totalrate);
                    //var value = qt * totalrate;
                    var value = totRate + totSgst + totCgst;
                    alert(value);
                    $("#value").val(roundToTwo(value));
                }

                if(kgperpc > 0 && qt > 0 && length > 0){
                    var piece = qt / ((length / 1000) * kgperpc);
                    piece = Math.round(piece);
                    $("#pieces").val(piece);
                }
            }

function deleteGRNItem(itemid){  
              var r = confirm("Are you sure you want to delete this item");
                 if(r){
                  //var remarks = $('#remarks').val();  
                  var ajaxURL = "ajax/deleteGRNitem.php?itemid=" + itemid;  
                      //alert(ajaxURL);
                      $.ajax({
                      url:ajaxURL,
                          dataType: 'json',
                          success:function(data){
                              //alert(data.error);
                              if (data.error == "1") {
                                  alert(data.msg);
                              } else {
                                  alert("GRN item deleted successfully.")
                                  window.location.href = "grn/additem/grnid="+<?php echo $this->grnid;?>+"/uom="+<?php echo $this->uom; ?>;  
                              }
                          }
                      });
       }
            }

function roundToThree(num) {    
      return +(Math.round(num + "e+3")  + "e-3");   
    }
    
    function roundToTwo(num) {    
      return +(Math.round(num + "e+2")  + "e-2");
    }

    function roundToFour(num) {    
      return +(Math.round(num + "e+4")  + "e-4");
    } 
            
    function getsomeGRNitems(polineid){
        arr = polineid.split("::");
        var kgperpc = parseFloat(arr[1]);
        var prodid = arr[2];  
        var grnid = <?php echo $this->grnid; ?>; 
        var ajaxURL = "ajax/getGRNItemValue.php?prodid=" + prodid +"&grnid="+grnid;  
        //alert(ajaxURL);
        $.ajax({
        url:ajaxURL,
            dataType: 'json',
            success:function(data){
                if (data.error == "1") { 
                    alert(data.msg);
                } else {
                   // alert(data.qty);
                  if(!$.trim(data)){
                   //alert("Nothing Found");
                 }else{
                     $("#grnqty").html(data.qty);
                     $("#grnqty2").html(data.qty);
                 }
                }
            }
        });
    }
    
    function selectPOItem(polineid){
        //alert(polineid);
        var arr = polineid.split("::");
        var prodid = arr[0];
        var kgperpc = parseFloat(arr[1]);
        var ajaxURL = "ajax/getPOItemValue.php?poitemid=" + prodid;  
        $.ajax({
        url:ajaxURL,
            dataType: 'json',
            success:function(data){
                if (data.error == "1") { 
                    alert(data.msg);
                } else {
                    //alert(data.length);
                    $("#polength").html(data.length);
                    $("#pocolor").html(data.color);
                    $("#pobrand").html(data.brand);
                    $("#pomanf").html(data.manufacturer);
                    $("#pomtqty").html(data.mtqty);
                    $("#poqty").html(data.qty);
                    $("#ponoofpc").html(data.pono_of_pieces);
                    $("#pobaserate").html(data.baserate);
                    $("#polcrate").html(data.lcharge);
                    $("#poqty2").html(data.qty);
                    $("#ponoofpc2").html(data.pono_of_pieces);
                    $("#pobaserate2").html(data.baserate);
                    $("#polcrate2").html(data.lcharge);
                    $("#pocgstval").html(data.cgstvalue); 
                    $("#posgstval").html(data.sgstvalue);
                    $("#pototrate").html(data.totrate);
                    $("#pototvalue").html(data.totvalue);                     
                    
                }
            }
        });
    }

    $(document).ready(function(){
      $('[type="checkbox"]').change(function(){ 
       if(this.checked){
       $('[type="checkbox"]').not(this).prop('checked', false);
      }    
    }); 
    });
    
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
    
    function hideButton(btnId){
                document.getElementById(btnId).style.display = "none";
    }
        </script>
        <link rel="stylesheet" href="css/bigbox.css" type="text/css" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
    }

    public function pageContent() {
        $menuitem = "grn"; //pagecode
        include "sidemenu." . $this->currStore->usertype . ".php";
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
        $obj_grn = $dbl->getGRNDetails($this->grnid);
        //echo $obj_grn;
        $obj_grnitems = $dbl->getGRNItems($this->grnid);
//        print_r($obj_grnitems);
        $obj_poitems = null;
        if($obj_grn != null){
            $obj_poitems = $dbl->getPOItems($obj_grn->poid);
        }

        //print_r($obj_po);
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">GRN Details</h2>
                            <div class="common-content-block">
                                <input type="hidden" name="grnid" id="grnid" value="<?php echo $this->grnid; ?>"/>
                                <div class="box box-primary"><br>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label>GRN No : <?php echo $obj_grn->grnno; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>GRN Date : <?php echo $obj_grn->grndate; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>PO No : <?php echo $obj_grn->pono; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Supplier : <?php echo $obj_grn->supplier; ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label>DC : <?php echo $obj_grn->dc_name; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Invoice No : <?php echo $obj_grn->invoice_no; ?></label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Invoice Date : <?php echo $obj_grn->invoice_date; ?></label>
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
                                <div class="box box-primary"><br>
                                    <form role="form" id="grnadditem" name="grnadditem" enctype="multipart/form-data" method="post" action="formpost/grnadditem.php">
                                        <input type="hidden" name="poid" id="poid" value="<?php echo $obj_grn->poid; ?>"/>
                                        <input type="hidden" name="grnid" id="grnid" value="<?php echo $this->grnid; ?>"/>
                                        <input type="hidden" name="uom" id="uom" value="<?php echo $this->uom; ?>"/>
                                        <div class="col-md-12">
                                            <div class="col-md-6" id="receiveinkg">
                                                <input type="checkbox" id="check1" name="check1" value="recinkg" onclick="receiveinKg();"> Receive in KG
                                            </div>
                                            <div class="col-md-6" id="receiveinpieces" style="display:none">
                                                 <input type="checkbox" id="check2" name="check2" value="recinpieces" onclick="receiveinPieces();"> Receive in Pieces
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-12">
                                                <label>Select PO Item</label><br>  
                                                <select id="poitem" name="poitem" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="selectPOItem(this.value);getsomeGRNitems(this.value);" >
                                                    <option value="-1">Select PO Item</option>
                                                    <?php
                                                    foreach ($obj_poitems as $item) {
                                                        $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                        $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                        $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                        $itemname = $item->category." ".$item->prod.$desc1.$desc2.$thickness;
                                                        ?>
                                                        <option value="<?php echo $item->id . "::" . $item->kg_per_pc . "::" .$item->product_id; ?>"><?php echo $itemname; ?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                            </div>
                                        </div> 
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-12">
                                                <label>Enter Alias Name :</label><br>
                                                <input type="text" id="alias" name="alias" class="form-control" placeholder="Alias" value="<?php echo $this->getFieldValue("alias"); ?>">
                                            </div>
                                        </div> 
                                        <div class="col-md-12">
                                            <br> 
                                            <div class="col-md-3">
                                                <label>Length (mm)<div id="polength"></div></label><br>
                                                <input type="text" id="length" name="length" class="form-control" placeholder="Length (mm)" value="<?php echo $this->getFieldValue("length"); ?>" onkeyup="calcValue(this.value);" autocomplete="off">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Select Color<div id="pocolor"></div></label><br>
                                                <select id="colorsel" name="colorsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Color</option>
                                                    <?php foreach ($obj_colors as $color) {
                                                        ?>
                                                        <option selected="selected" value="<?php echo $color->id; ?>"><?php echo $color->color; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Select Brand<div id="pobrand"></div></label><br>
                                                <select id="brandsel" name="brandsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Brand</option>
                                                    <?php foreach ($obj_brands as $brand) {
                                                        ?>
                                                        <option selected="selected" value="<?php echo $brand->id; ?>"><?php echo $brand->brand; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Select Manufacturer<div id="pomanf"></div></label><br>
                                                <select id="manfsel" name="manfsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Manufacturer</option>
                                                    <?php foreach ($obj_manufacturers as $manf) {
                                                        ?>
                                                        <option selected="selected" value="<?php echo $manf->id; ?>"><?php echo $manf->manufacturer; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div> 
                                        </div>
                                        <div class="col-md-12" id="checkbox1" style="display:none">
                                            <br>
                                             <div class="col-md-3">
                                                <label>Enter Qty (MT)<div id="pomtqty"></div></label><br>
                                                <input type="text" id="mtqty" name="mtqty" class="form-control" placeholder="MT Qty" value="<?php echo $this->getFieldValue("mtqty"); ?>" onkeyup="calcValueinMT(this.value);" autocomplete="off">
                                            </div>
                                            <div class="col-md-3">
                                                <!--<label>Enter Qty (kg) <div id="poqty"></div><div id="grnqty"></div></label><br>-->
                                                <label>Enter Qty (kg) <div id="poqty"></div></label><br>
                                                <input type="text" id="qty" name="qty" class="form-control" placeholder="Qty (kilograms)" value="<?php echo $this->getFieldValue("qty"); ?>" onkeyup="calcValue(this.value);" autocomplete="off">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Calculated number of pieces<div id="ponoofpc"></div></label><br>
                                                <input type="text" id="pieces" name="pieces" class="form-control" placeholder="Calculated number of pieces" value="<?php echo $this->getFieldValue("pieces"); ?>" onkeyup="calcValue(this.value);">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Enter Base Rate<div id="pobaserate"></div></label><br>
                                                <input type="text" id="rate" name="rate" class="form-control" placeholder="Base Rate" value="<?php echo $this->getFieldValue("rate"); ?>" onkeyup=calcValue(this.value); autocomplete="off">
                                            </div> 
<!--                                            <div class="col-md-3">
                                                <label>Loading Charges(Rs./Kg)<div id="polcrate"></div></label><br>
                                                <input type="text" id="lcrate" name="lcrate" class="form-control" placeholder="Loading Charges(Rs./Kg)" value="<?php echo $this->getFieldValue("lcrate"); ?>" onkeyup="calcValue(this.value);" autocomplete="off">
                                            </div> -->
                                        </div>
                                        <div class="col-md-12" id="checkbox2" style="display:none">
                                            <br> 
                                            <div class="col-md-3">
                                                <label>Enter number of pieces<div id="ponoofpc2"></div></label><br>
                                                <input type="text" id="pieces2" name="pieces2" class="form-control" placeholder="no of pieces" value="<?php echo $this->getFieldValue("pieces2"); ?>" onkeyup="calcValuecheck2(this.value);" autocomplete="off">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Calculated Received Qty (Kg)<div id="poqty2"></div><div id="grnqty2"></div></label><br>
                                                <input type="text" id="qty2" name="qty2" class="form-control" placeholder="Qty (kilograms)" value="<?php echo $this->getFieldValue("qty2"); ?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Enter Base Rate<div id="pobaserate2"></div></label><br>
                                                <input type="text" id="rate2" name="rate2" class="form-control" placeholder="Base Rate" value="<?php echo $this->getFieldValue("rate2"); ?>" onkeyup=calcValuecheck2(this.value); autocomplete="off">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Loading Charges(Rs./Kg)<div id="polcrate2"></div></label><br>
                                                <input type="text" id="lcrate2" name="lcrate2" class="form-control" placeholder="Loading Charges(Rs./Kg)" value="<?php echo $this->getFieldValue("lcrate2"); ?>" onkeyup="calcValuecheck2(this.value);" autocomplete="off">
                                            </div> 
                                        </div>
                                        <div class="col-md-12">
                                            <br> 
                                            <div class="col-md-3">
                                                <label>CGST 9%<div id="pocgstval"></div></label><br>
                                                <input type="text" id="cgst" name="cgst" class="form-control" placeholder="CGST" value="<?php echo $this->getFieldValue("cgst"); ?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>SGST 9%<div id="posgstval"></div></label><br>
                                                <input type="text" id="sgst" name="sgst" class="form-control" placeholder="SGST" value="<?php echo $this->getFieldValue("sgst"); ?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Rate (Rs./Kg)<div id="pototrate"></div></label><br>
                                                <input type="text" id="totalrate" name="totalrate" class="form-control" placeholder="Rate (Rs./Kg)" value="<?php echo $this->getFieldValue("totalrate"); ?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Total Value (Rs.)<div id="pototvalue"></div></label><br>
                                                <input type="text" id="value" name="value" class="form-control" placeholder="Value" value="<?php echo $this->getFieldValue("value"); ?>">
                                            </div> 
                                        </div>
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary" id="add_item_btn" style="display:block" onclick="hideButton(this.id);">Add Item</button>
                                            </div>
                                        </div>
                                         <?php //if ($formResult->form_id == 'createdc') {  ?>
                                        <div class="alert" style="display:<?php echo $formResult->showhide; ?>;"<?php // echo $formResult->status; ?>>
                                            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4> <?php echo $formResult->status; ?>
                                        </div>
                                            <?php //}  ?>
                                    </form>                                 
                                </div>  
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>Sl.No.</td>
                                                <td>Product</td>
                                                <td>Length (mm)</td>
                                                <td>Qty (Kg)</td>
                                                <td>Rate (Rs./Kg)</td>
                                                <td>Value (Rs.)</td>
                                                <td>Action</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $srno = 1;
                                            $tot_qty = 0;
                                            $itemCount = 0;
                                            setlocale(LC_MONETARY,"en_IN");
                                            $total_value = 0;
                                            if ($obj_grnitems != NULL) {
                                                foreach ($obj_grnitems as $item) {
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
                                                               ?></br><b><?php echo $item->batchcode; ?></b>
                                                        </td>
                                                        <td><?php echo $item->length; ?></td>
                                                        <td><?php echo $item->qty; ?></td>
                                                        <td><?php echo $item->totalrate; ?></td>
                                                        <td><?php echo $item->totalvalue;
                                                            ?></td>
                                                        <td><input class="btn btn-primary" type="button" name="deleteItem" id="deleteItem" value="Delete" onclick="deleteGRNItem(<?php echo $item->id; ?>);" /></td>
                                                    </tr>
                                                            <?php
                                                            $srno = $srno + 1;
                                                            $tot_qty = $tot_qty + $item->qty;
                                                            $total_value = $total_value + $item->totalvalue;
                                                }
                                            }
                                            ?>
                                        </tbody> 
                                        <tfoot>
                                            <tr>
                                                <td>Total</td>
                                                <td></td>
                                                <td></td>
                                                <td><?php echo $tot_qty; ?></td>
                                                <td></td>
                                                <td><?php echo money_format('%!i',$total_value); ?></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                               <div class="col-md-12">
                                   <form role="form" id="submitpo" name="submitpo" enctype="multipart/form-data" method="post" action="formpost/submitgrn.php">
                                       <input type="hidden" name="itemcount" id="itemcount" value="<?php echo $itemCount; ?>"/>
                                       <input type="hidden" name="grnid" id="grnid" value="<?php echo $this->grnid; ?>"/>
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
                </div>
            </div> 
        </div><!-- end -->
          

        <?php
        // }else{ print "You are not authorized to access this page";}
    }

}
?>


