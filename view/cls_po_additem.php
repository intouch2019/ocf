<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_po_additem extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $poid = "";

    function __construct($params = null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
        $this->params = $params;
        if (isset($this->params["poid"]) != "") {
            $this->poid = $this->params["poid"];
        }
        if(isset($this->params["uom"]) != ""){
            $this->uom = $this->params["uom"];
        }
    }

    function extraHeaders() {
        ?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
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
            
            #lchide{
                visibility: hidden;
            }
        </style>
        <script type="text/javaScript">   
 
    $(function () { 
        
            var uom = <?php echo $this->uom; ?>;
            if(uom == 2){
                $('#qty').attr('readonly', 'readonly');
            }else if(uom==1){
                $('#mtqty').attr('readonly', 'readonly');
            }
    
            $('#exdate').datepicker({   
            format:'dd-mm-yyyy'  
            });  
 
  });
            function setCtg(ctgValue){   
            if(ctgValue == -1){
            $("#addctg").show();
            }else{
            $("#addctg").hide();
            }
            }  

            function setSpec(specValue){ 
            if(specValue == -1){
            $("#addspec").show(); 
            }else{
            $("#addctg").hide();
            } 
            }

            function changeTest(){
            var freightamt = parseFloat($("#freightamt").val());
            var gstpercent = parseFloat($("#gstsel").val());
            //freightgst
            var freightGST = (freightamt * gstpercent) / 100;
            freightGST = roundToTwo(freightGST);
            //totalfreight
            var totalFreight = freightamt + freightGST;
            $("#freightgst").val(freightGST);
            $("#totalfreight").val(totalFreight);

            }
            
            function calcDays(date){
                var dt = $("#datepicker").val(); 
                var parts =dt.split('/');
                var mydate = new Date(parts[2],parts[0]-1, parts[1]);
                var dateinstr = mydate.getDate()+'/'+(mydate.getMonth()+1)+'/'+mydate.getFullYear();
                var c = 24*60*60*1000;
                var today = new Date();
                var timeDiff = Math.abs(mydate.getTime() - today.getTime());
//                diffDays = Math.round((mydate-today)/(1000*60*60*24));
                diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
                $("#nodays").val(diffDays);
                
/*                var supoffers = $("#supoffers").val(); 
                var offerref = $("#offerref").val();
                $("#refval").val(offerref);
                
                $("#enddate").val(dateinstr); */ 
            }
            
            function calcValueinMT(v){ 
                var prodsel = $("#prodsel").val();
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
//                    alert(value);
                    $("#value").val(roundToTwo(value));
                }

                if(kgperpc > 0 && qt > 0 && length > 0){
                    var piece = qt / ((length / 1000) * kgperpc);
                    piece = Math.round(piece);
                    $("#pieces").val(piece);
                }
            }

            function calcValue(v){ 
                var prodsel = $("#prodsel").val();
                var length = parseFloat($("#length").val());
                var arr = prodsel.split("::");
                var prodid = arr[0];
                var kgperpc = parseFloat(arr[1]);
                var qt = parseFloat($("#qty").val());
                var mtqty = roundToFour(qt / 1000);
                if(mtqty > 0){
                    $("#mtqty").val(mtqty);
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

            function deletePOItem(itemid){ 
              var r = confirm("Are you sure you want to delete this item");
                 if(r){
                  var remarks = $('#remarks').val();  
                  var ajaxURL = "ajax/deletePOitem.php?itemid=" + itemid +"&poid="+<?php echo $this->poid;?>;  
                      //alert(ajaxURL);
                      $.ajax({
                      url:ajaxURL,
                          dataType: 'json',
                          success:function(data){
                              //alert(data.error);
                              if (data.error == "1") {
                                  alert(data.msg);
                              } else {
                                  alert("PO item deleted successfully.")
                                  var uom = <?php echo $this->uom; ?>;
                                  window.location.href = "po/additem/poid="+<?php echo $this->poid;?>+"/uom="+uom;  
                              }
                          }
                      });
       }
            }

            function submitPO(poid){

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
            
            function myFunction() {
                var checkBox = document.getElementById("remark2");
                var li1 = document.getElementById("r2inv");
                var li2 = document.getElementById("r2dc"); 
                var li3 = document.getElementById("r2pl");
                var li4 = document.getElementById("r2ws");
                var li5 = document.getElementById("r2tc");
                if (checkBox.checked == true){
                    li1.style.display = "block";
                    li2.style.display = "block";
                    li3.style.display = "block";
                    li4.style.display = "block";
                    li5.style.display = "block";
                    document.getElementById("li1").checked = true;
                    document.getElementById("li2").checked = true;
                    document.getElementById("li3").checked = true;
                    document.getElementById("li4").checked = true;
                    document.getElementById("li5").checked = true;
                } else {
                   li1.style.display = "none"; 
                   li2.style.display = "none";
                   li3.style.display = "none";
                   li4.style.display = "none";
                   li5.style.display = "none";
                   document.getElementById("li1").checked = false;
                   document.getElementById("li2").checked = false;
                   document.getElementById("li3").checked = false;
                   document.getElementById("li4").checked = false;
                   document.getElementById("li5").checked = false;
                }
            }
 
            function r1clicked() {
                var checkBox = document.getElementById("remark1"); 
                if (checkBox.checked == true){
                    var dt = $("#datepicker").val();
                    if(dt == ""){
                        alert("Please select the required scheduled delivery date");
                        $("#refval").val("");
                        $("#enddate").val(""); 
                        document.getElementById("remark1").checked = false;
                        return;
                    }else{
                        var parts =dt.split('/');
                        var mydate = new Date(parts[2],parts[0]-1, parts[1]);
                        var dateinstr = mydate.getDate()+'/'+(mydate.getMonth()+1)+'/'+mydate.getFullYear();
                        $("#enddate").val(dateinstr); 
                    }
                    var supoffers = $("#supoffers").val();
                    var offerref = $("#offerref").val();
                    if(offerref == ""){
                        alert("Please enter supplier offer reference");
                        $("#refval").val("");
                        $("#enddate").val(""); 
                        document.getElementById("remark1").checked = false;
                        return;
                    }else{
                        $("#refval").val(offerref);
                    }
                } else {
                        $("#refval").val("");
                        $("#enddate").val(""); 
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
        $menuitem = "po"; //pagecode
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
        $obj_po = $dbl->getPODetails($this->poid);
        $obj_poitems = $dbl->getPOItems($this->poid);
        $obj_freight = $dbl->getFreightdetails($this->poid);
        $obj_suppoffers = $dbl->getSuppliersOffers();

        //print_r($obj_po);
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">PO Details</h2>
                            <div class="common-content-block">
                                <input type="hidden" name="poid" id="poid" value="<?php echo $this->poid; ?>"/>
                                <div class="box box-primary"><br>
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <label>Supplier Code : <?php echo $obj_po->supplier_code; ?></label>

                                        </div>
                                        <div class="col-md-4">
                                            <label>Payment Terms : <?php echo $obj_po->pmterm; ?></label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>PO No : <?php echo $obj_po->pono; ?></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <label>Supplier Name : <?php echo $obj_po->company_name; ?></label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Delivery Terms : <?php echo $obj_po->dtterm; ?></label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>PO Date : <?php echo ddmmyy($obj_po->createtime); ?></label>
                                        </div>
                                    </div>
<!--                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <label>Buyer Code : <?php echo $obj_po->buyer_code; ?></label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>DC Code : <?php echo $obj_po->dccode; ?></label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Contract No : <?php echo $obj_po->supp_contract_no; ?></label>
                                        </div>
                                    </div>-->
                                    <div class="col-md-12">
<!--                                        <div class="col-md-4">
                                            <label>Buyer Name : <?php echo $obj_po->buyer_name; ?></label>
                                        </div>
-->                                     <div class="col-md-4">
                                            <label>DC Code : <?php echo $obj_po->dcname; ?></label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Transit Insurance : <?php echo $obj_po->titerm; ?></label>
                                        </div>
                                         <div class="col-md-4">
                                            <label>Unit Of Measurement : <?php echo $obj_po->uom; ?></label>
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
                                    <form role="form" id="poadditem" name="poadditem" enctype="multipart/form-data" method="post" action="formpost/poAddItem.php">
                                        <input type="hidden" name="poid" id="poid" value="<?php echo $this->poid; ?>"/>
                                        <input type="hidden" name="uom" id="uom" value="<?php echo $this->uom; ?>"/>
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-12">
                                                <label>Select Category</label><br>  
                                                <select id="catsel" name="catsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Category</option>
                                                    <?php
                                                    foreach ($obj_categories as $cat) {
                                                        $desc1 = isset($cat->desc1) && trim($cat->desc1) != "" ? " , " . $cat->desc1 : "";
                                                        $desc2 = isset($cat->desc2) && trim($cat->desc2) != "" ? " x " . $cat->desc2 : "";
                                                        $thickness = isset($cat->thickness) && trim($cat->thickness) != "" ? " , " . $cat->thickness : "";
                                                        $category = $cat->name . $desc1 . $desc2 . $thickness;
                                                        ?>
                                                        <option selected="selected" value="<?php echo $cat->id . "::" . $cat->name; ?>"><?php echo $category; ?></option>
        <?php } ?>
                                                </select>
                                            </div>
                                        </div>  
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-12">
                                                <label>Select Product</label><br>  
                                                <select id="prodsel" name="prodsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Product</option>
                                                    <?php
                                                    foreach ($obj_products as $prod) {
                                                        $desc1 = isset($prod->desc1) && trim($prod->desc1) != "" ? " , " . $prod->desc1." mm" : "";
                                                        $desc2 = isset($prod->desc2) && trim($prod->desc2) != "" ? " x " . $prod->desc2." mm": "";
                                                        $thickness = isset($prod->thickness) && trim($prod->thickness) != "" ? " , " . $prod->thickness." mm" : "";
                                                        $product = $prod->prod . $desc1 . $desc2 . $thickness;
                                                        ?>
                                                        <option value="<?php echo $prod->id . "::" . $prod->kg_per_pc; ?>"><?php echo $product; ?></option>
        <?php } ?>
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="col-md-12">
                                            <br> 
                                            <div class="col-md-3">
                                                <label>Length (mm)</label><br>
                                                <input type="text" id="length" name="length" class="form-control" placeholder="Length (mm)" value="<?php echo $this->getFieldValue("length"); ?>" onkeyup="calcValue(this.value);" autocomplete="off">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Select Color</label><br>
                                                <select id="colorsel" name="colorsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Color</option>
                                                    <?php foreach ($obj_colors as $color) {
                                                        ?>
                                                        <option selected="selected" value="<?php echo $color->id; ?>"><?php echo $color->color; ?></option>
        <?php } ?>
                                                </select>
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Select Brand</label><br>
                                                <select id="brandsel" name="brandsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Brand</option>
                                                    <?php foreach ($obj_brands as $brand) {
                                                        ?>
                                                        <option selected="selected" value="<?php echo $brand->id; ?>"><?php echo $brand->brand; ?></option>
        <?php } ?>
                                                </select>
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Select Manufacturer</label><br>
                                                <select id="manfsel" name="manfsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Select Manufacturer</option>
                                                    <?php foreach ($obj_manufacturers as $manf) {
                                                        ?>
                                                        <option selected="selected" value="<?php echo $manf->id; ?>"><?php echo $manf->manufacturer; ?></option>
        <?php } ?>
                                                </select>
                                            </div> 
                                        </div>
                                        <div class="col-md-12">
                                            <br> 
                                            <div class="col-md-3">
                                                <label>Enter Qty (MT)</label><br>
                                                <input type="text" id="mtqty" name="mtqty" class="form-control" placeholder="MT Qty" value="<?php echo $this->getFieldValue("mtqty"); ?>" onkeyup="calcValueinMT(this.value);" autocomplete="off">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Enter Qty (kg)</label><br>
                                                <input type="text" id="qty" name="qty" class="form-control" placeholder="Qty (kilograms)" value="<?php echo $this->getFieldValue("qty"); ?>" onkeyup="calcValue(this.value);" autocomplete="off">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Calculated number of pieces</label><br>
                                                <input type="text" id="pieces" name="pieces" class="form-control" placeholder="Calculated number of pieces" value="<?php echo $this->getFieldValue("pieces"); ?>" onkeyup="calcValue(this.value);">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Enter Base Rate</label><br>
                                                <input type="text" id="rate" name="rate" class="form-control" placeholder="Base Rate" value="<?php echo $this->getFieldValue("rate"); ?>" onkeyup=calcValue(this.value); autocomplete="off">
                                            </div> 
                                            <!--                                    <div class="col-md-3">
                                                                                    <label>Base Rate including LC (15%)</label><br>
                                                                                    <input type="text" id="lcrate" name="lcrate" class="form-control" placeholder="Base Rate including LC (15%)" value="<?php echo $this->getFieldValue("lcrate"); ?>" onkeyup=calcValue(this.value);>
                                                 
                                            </div> -->
<!--                                            <div class="col-md-3">
                                                <label>Total Tax Rate</label><br>
                                                <input type="text" id="taxrate" name="taxrate" class="form-control" placeholder="Loading Charges(Rs./Kg)" value="18" onkeyup="calcValue(this.value);" autocomplete="off" readonly="">
                                            </div>-->
<!--                                            <div class="col-md-3" id="lchide">
                                                <label>Loading Charges(Rs./Kg)</label><br>
                                                <input type="text" id="lcrate" name="lcrate" class="form-control" placeholder="Loading Charges(Rs./Kg)" value="<?php echo $this->getFieldValue("lcrate"); ?>" onkeyup="calcValue(this.value);" autocomplete="off">
                                            </div> -->
                                        </div>
                                        <div class="col-md-12">
                                            <br> 
                                            <div class="col-md-3">
                                                <label>CGST 9%</label><br>
                                                <input type="text" id="cgst" name="cgst" class="form-control" placeholder="CGST" value="<?php echo $this->getFieldValue("cgst"); ?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>SGST 9%</label><br>
                                                <input type="text" id="sgst" name="sgst" class="form-control" placeholder="SGST" value="<?php echo $this->getFieldValue("sgst"); ?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Rate (Rs./MT)</label><br>
                                                <input type="text" id="totalrate" name="totalrate" class="form-control" placeholder="Rate (Rs./Kg)" value="<?php echo $this->getFieldValue("totalrate"); ?>">
                                            </div> 
                                            <div class="col-md-3">
                                                <label>Total Value (Rs.)</label><br>
                                                <input type="text" id="value" name="value" class="form-control" placeholder="Value" value="<?php echo $this->getFieldValue("value"); ?>">
                                            </div> 
                                        </div>
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary" id="add_item_button" style="display:block" onclick="hideButton(this.id);">Add Item</button>
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
                                                <td>Qty (MT)</td>
                                                <td>Rate (Rs./MT)</td>
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
                                            if ($obj_poitems != NULL) {
                                                foreach ($obj_poitems as $item) {
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
                                                        <td><?php echo $item->totalrate; ?></td>
                                                        <td><?php echo $item->totalvalue;
                                                            ?></td>
                                                        <td><input class="btn btn-primary" type="button" name="deleteItem" id="deleteItem" value="Delete" onclick="deletePOItem(<?php echo $item->id; ?>);" /></td>
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
                                   <form role="form" id="submitpo" name="submitpo" enctype="multipart/form-data" method="post" action="formpost/submitpo.php">
                                       <input type="hidden" name="itemcount" id="itemcount" value="<?php echo $itemCount; ?>"/>
                                <?php if($obj_po->is_freightapplicable){?>
                                        <div class="col-md-12">
                                            
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

                                        </div>
                                        <?php if($obj_po->is_transportapplicable) {?>
                                        <div class="col-md-12">
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
                                        </div>
                                <?php }?>
                                        <?php }?>
                                        <div class="col-md-12">
                                            <br>
                                            <div class="col-md-4">
                                                <label>What is Supplier's Offers (select one)</label><br>
                                                <select id="supoffers" name="supoffers" class="selectpicker form-control" data-show-subtext="true" data-live-search="true">
                                                    <option value="-1">Supplier's Offers (select any one)</option>
                                                        <?php foreach ($obj_suppoffers as $offers) {
                                                        ?>
                                                        <option value="<?php echo $offers->id; ?>"><?php echo $offers->offers; ?></option>
                                                        <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Enter Supplier Offer Reference</label><br>
                                                <input type="text" id="offerref" name="offerref" class="form-control" placeholder="Offer Reference" value="<?php echo $this->getFieldValue("offerref"); ?>">
                                            </div> 
                                            <div class="col-md-4">
                                                <label>Provide Required Delivery Schedule </label><br>
                                                <input type="text" id="datepicker" value="<?php echo $this->getFieldValue("offerref"); ?>" name="datepicker" onchange="calcDays(this)" width="300"/>
                                                    <script>
                                                        $('#datepicker').datepicker({
                                                            uiLibrary: 'bootstrap'
//                                                            format: 'dd/mm/yyyy',
                                                        });
                                                    </script>
                                            </div>
                                            
                                        </div>
                                       <div class="col-md-12">
                                            <div class="col-md-4">
                                                <label>number Of Days</label><br>
                                                <input type="text" id="nodays"  name="nodays" class="form-control" placeholder="Number Of Days" value="<?php echo $this->getFieldValue("nodays"); ?>">
                                            </div>
                                       </div>
                                        <br/>
                                        <div class="col-md-12">
                                            <br/>
                                            <input type="checkbox" id="remark1"  name="remark1" onclick="r1clicked();" value='With ref to your Offer Reference <input type="text" id="refval" value="refval"> Dated <input type="text" id="enddate" value="enddate">, we are pleased to issue this Purchase Order for the supply of following Items'> With ref to your Offer Reference <input type="text" id="refval" readonly value="<?php echo $this->getFieldValue("refval"); ?>"> Dated <input type="text" id="enddate" readonly value="<?php echo $this->getFieldValue("enddate"); ?>">, we are pleased to issue this Purchase Order for the supply of following Items<br>
                                        </div> 
                                        <div class="col-md-12">
                                            <br/>
                                            <input type="checkbox" id="remark2" name="remark2" value="Kindly ensure that you send the following documents to the Delivery Address:" onclick="myFunction();"> Kindly ensure that you send the following documents to the Delivery Address:<br>
                                            <div class="col-md-12" id="r2inv" style="display:none;">
                                                 <input type="checkbox" id="li1" name="li1" value="Invoice"> Invoice
                                            </div>
                                            <div class="col-md-12" id="r2dc" style="display:none">
                                                 <input type="checkbox" id="li2" name="li2" value="Delivery Challan"> Delivery Challan
                                            </div>
                                            <div class="col-md-12" id="r2pl" style="display:none;">
                                                 <input type="checkbox" id="li3" name="li3" value="Packing List with quantity in kg and in number of pieces"> Packing List with quantity in kg and in number of pieces
                                            </div> 
                                            <div class="col-md-12" id="r2ws" style="display:none;">
                                                 <input type="checkbox" id="li4" name="li4" value="Weighment Slip"> Weighment Slip
                                            </div>    
                                            <div class="col-md-12" id="r2tc" style="display:none;">
                                                  <input type="checkbox" id="li5" name="li5" value="Test Certificate"> Test Certificate 
                                            </div>      
                                        </div>
                                            
<!--                                              </ul>-->
                                        <div class="col-md-12">
                                         <input type="checkbox" id="remark3" name="remark3" value="Ensure that you always mention our Purchase Order Number in all documents mentioned above."> Ensure that you always mention our Purchase Order Number in all documents mentioned above.<br>
                                        </div>
                                        <div class="col-md-12">
                                         <input type="checkbox" id="remark4" name="remark4" value="Please ensure proper packing of all items with marking on each bundle for identification and traceability"> Please ensure proper packing of all items with marking on each bundle for identification and traceability<br>
                                            
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <label>&nbsp;</label><br/>
                                            <button type="submit" class="btn btn-primary" id="save_button" style="display:block" onclick="hideButton(this.id);">Save and Send for Approval</button>
                                        </div>
                                        <input type="hidden" name="pid" id="pid" value="<?php echo $this->poid; ?>">

                                    </form>
                                </div> 
    
                            </div>
                        </div>
                    </div> <!--Add Items>   
                </div>
            </div> 
        </div><!-- end -->

<!--                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
                    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
                    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           -->

        <?php
        // }else{ print "You are not authorized to access this page";}
    }

}
?>


