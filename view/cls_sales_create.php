<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_sales_create extends cls_renderer {

    var $currStore;
    var $userid;
    var $dtrange;
    var $params;
    var $salesid = 0;
    var $custid = 0;

    function __construct($params = null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
        $this->currStore = getCurrStore();
        $this->params = $params;
        if (isset($this->params["salesid"]) != "") {
            $this->salesid = $this->params["salesid"];
        }
        if (isset($this->params["custid"]) != "") {
            $this->custid = $this->params["custid"];
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
            option{
             font-weight:bold; 
           }
           div.alert *
            {
                color: red;
            }
        </style>
        <script type="text/javaScript">   
            
            $(function () {      
                $('#saledate').datepicker({     
                     format:'dd-mm-yyyy'     
                 }); 
                 
                $('#prodsel').change(function() {  
                    $("#numberofunits").val(""); 
                    $("#rate").val("");  
                    $("#value").val("");  
                    $("#qty").val(""); 
                      
                });   
                
                
                $('#cuttingcharges').change(function() {
                  if($(this).prop('checked')){
                        var rate = parseFloat($("#rate").val());
                        var selectedBatches = $("#selectedbatches").val();
                        var cutcharge = parseFloat($("#cutcharge").val()); 
                        var totrate = rate + (cutcharge * selectedBatches);
                        $("#rate").val(totrate); 
                        //calcValue(0);
                  }else{ 
                        var rate = parseFloat($("#rate").val());
                        var cutcharge = parseFloat($("#cutcharge").val());
                        var totrate = rate - cutcharge; 
                        $("#rate").val(totrate);  
                        //calcValue(0); 
                  }
                });  
                 
                 
                $('#batchcodes').on('change', function(){
                var selected = $(this).find("option:selected");
                var arrSelected = [];
                var count = 0;
                var totQty = 0;
                selected.each(function(){
                    var v = $(this).val().split("::");
                    var qt = parseFloat(v[3]);
                    qt = qt * 1000;
                    count++;
                    if(count > 1){
                        document.getElementById('qty').readOnly = true;
                    }else{  
                        document.getElementById('qty').readOnly = false;
                    }
                    totQty = totQty + qt;
                    $("#availableqty").val(totQty); 
                }); 
                //alert(count);
                $("#selectedbatches").val(count);
                $("#qty").val(totQty);
                $("#mt").val(roundToFour(totQty / 1000));
                var qt = parseFloat($("#qty").val());
                var selectedBatches = $("#selectedbatches").val();
                //alert(selectedBatches);
                //var orgrate = parseFloat($("#orgrate").val());
                var orgrate = parseFloat($("#rate").val());
                var kgperpc = parseFloat($("#kgperpc").val());
                var rate = orgrate * selectedBatches;
                var value = qt * orgrate * selectedBatches;
                var nou = qt / kgperpc;
                nou = nou.toFixed(2);
                //alert(value);
                $("#numberofunits").val(nou);
//                $("#rate").val(rate);
                $("#value").val(roundToTwo(value));
             }); 
             
             $("#qty").click(function(){
                if ( $('#qty').is('[readonly]') ) { 
                   alert("You cannot change qty when multiple batchcode is selected"); 
                }
            });
            
            openSaleCollectionDetails();
           openSaleStockDetails();
 });
            
            
            $(function () {  
            modal = $("#batchselection").dialog 
                ({
                    autoOpen: false,
                    height: 600,            
                    width: 450, //500,  
                    title: 'Enter batchwise sale quantity'
                });
                 });
            
            function selectBatchCode(stockcurrid){
                var arr = stockcurrid.split("::");
                var stockid = arr[0];
                var prodid = arr[1]; 
                //var ajaxURL = "ajax/getCRBatchcodeByProduct.php?prodid=" + prodid;  
                var ajaxURL = "ajax/getCRBatchcodeByProduct.php?prodid=" + prodid; 
                $.ajax({
                url:ajaxURL,
                    //dataType: 'json',
                    cache: false, 
                    success:function(html){
                        //alert(html);
                            $('#batchcodes').html(html); 
                            $('#batchcodes').selectpicker('refresh')
                    }
                });
         
            }

            function getRate(stockcurrid){ 
                var arr = stockcurrid.split("::");
                //var saledate = $("#saledate").val();
                var stockid = arr[0];
                var prodid = arr[1];
                //var ajaxURL = "ajax/getProductRate.php?prodid=" + prodid +"&saledate="+saledate;
                var ajaxURL = "ajax/getProductRate.php?prodid=" + prodid;
//                   alert(ajaxURL); 
                    $.ajax({ 
                    url:ajaxURL,
                        dataType: 'json',
                        success:function(data){
                            //alert(data.error);
                            if (data.error == "1") {
                                alert(data.msg);
                            } else {
                                var base_rate = data.msg;
                                var trate = (18 / 100);
                                var taxable_amt = roundToTwo(base_rate / trate);
                               // alert("taxamt "+taxable_amt);
                                //var tax_amt = roundToTwo(base_rate - taxable_amt);
                                var tax_amt = base_rate * trate;
                                //alert("tax "+tax_amt);
                                //alert(tax_amt / 2);
                                var cgst_amt = roundToTwo(tax_amt / 2);
                                var sgst_amt = roundToTwo(tax_amt / 2);
                                var mrp = roundToTwo(parseFloat(base_rate) +  parseFloat(cgst_amt) + parseFloat(sgst_amt));
                                //alert("mrp "+mrp);
                                //$("#rate").val(data.msg);
                                $("#rate").val(mrp);
                                $("#baserate").val(base_rate);
                                $("#orgrate").val(data.msg);
                                $("#kgperpc").val(data.kgperpc);
                            }
                        }
                    });
            }
            
            function checkmt(mt){
                var enterqtymt = parseFloat($("#mt").val());
                var qtyinkg = roundToTwo(enterqtymt * 1000);
                var availableqty = parseFloat($("#availableqty").val());
                 if(qtyinkg > availableqty){
                     alert("Please enter qty less than available qty");
                    //$("#pieces").val("");
                    $("#qty").val("");
                    $("#mt").val("");
                    return false;
                 }else{
                     var selectedBatches = $("#selectedbatches").val();
                    var MtQty =  roundToFour(qtyinkg / 1000);
                    //$("#mt").val(MtQty);
                    //var orgrate = parseFloat($("#orgrate").val());
                    var orgrate = parseFloat($("#rate").val());
                    var kgperpc = parseFloat($("#kgperpc").val());
                    var rate = orgrate * selectedBatches;
                    //var value = enterqty * orgrate * selectedBatches;
                    var value = MtQty * orgrate * selectedBatches;
                    var nou = qtyinkg / kgperpc; 
                    nou = nou.toFixed(2);
                    //alert(value);
                    $("#numberofunits").val(nou);
                    $("#qty").val(qtyinkg);
                    $("#rate").val(rate);
                    $("#value").val(roundToTwo(value));
                    return true;
                 }
                
            }
            
            function checkqty(value){
                var enterqty = parseFloat($("#qty").val());
                var availableqty = parseFloat($("#availableqty").val());
                
                if(enterqty > availableqty){
                    alert("Please enter qty less than available qty");
                    //$("#pieces").val("");
                    $("#qty").val("");
                    return false;
                }else{ 
                    var selectedBatches = $("#selectedbatches").val();
                    var MtQty =  roundToFour(enterqty / 1000);
                    $("#mt").val(MtQty);
                    //var orgrate = parseFloat($("#orgrate").val());
                    var orgrate = parseFloat($("#rate").val());
                    var kgperpc = parseFloat($("#kgperpc").val());
                    var rate = orgrate * selectedBatches;
                    //var value = enterqty * orgrate * selectedBatches;
                    var value = MtQty * orgrate * selectedBatches;
                    var nou = enterqty / kgperpc; 
                    nou = nou.toFixed(2);
                    //alert(value);
                    $("#numberofunits").val(nou);
                    $("#rate").val(rate);
                    $("#value").val(roundToTwo(value));
                    return true;
                }
                
                
            }
           
            function calcValue(v){ 
                var arr = $("#batchcodes").val()
                alert(batchcodes);
                var arr = arr.split(",");
                var qt = arr[1];
                alert(qt);
                //$("#qty").val(qt);
                var qt = parseFloat($("#qty").val());
                var rate = parseFloat($("#rate").val());
                var kgperpc = parseFloat($("#kgperpc").val());
                var value = qt * rate;
                var nou = qt / kgperpc;
                nou = nou.toFixed(2);
                //alert(value);
                $("#numberofunits").val(nou);
                $("#value").val(roundToTwo(value));
            }
            
            function addCustomer(){
                var custid = $("#custid").val();
                if(custid > 0){
                    alert("Please remove the selected csutomer first");
                }else{
                    window.location.href = "customer/add";
                }
            }
            
            function removeCustomer(salesid){ 
                if(salesid > 0){
                    window.location.href = "sales/create/salesid="+salesid+"/custid=0";
                }else{
                    window.location.href = "sales/create/custid=0";
                }
            }
            
            function cancelSales(salesid){
                //alert(salesid);
                var isCancel = confirm("Are you sure to cancel this transction");
                if(isCancel){
                    if(salesid == 0){
                         window.location.href = "sales/status="+0;
                    }else if(salesid > 0){
                    var ajaxURL = "ajax/cancelSales.php?salesid=" + salesid;
                    //alert(ajaxURL);
                    $.ajax({ 
                        url:ajaxURL,
                        dataType: 'json',
                        success:function(data){
                            //alert(data.error);
                            if (data.error == "1") {
                                alert(data.msg);
                            } else {
                                //alert("Transction Successfully cancelled.");
                                window.location.href = "sales/status="+0;
                            }
                        }
                    });
                }
                 
                }
                
            }
            
            function applyCharges(chargetype){ 
                var salesid = $("#sid").val();
                var custid = $("#custid").val();
                //if(salesid > 0 && chargetype > 0){
                if(salesid > 0){
                    var ajaxURL = "ajax/applyExtraCharges.php?salesid=" + salesid+"&chargetype="+chargetype;
                    //alert(ajaxURL);
                    $.ajax({ 
                        url:ajaxURL,
                        dataType: 'json',
                        success:function(data){
                            if (data.error == "1") {
                                alert(data.msg);
                            } else {
                                window.location.href = "sales/create/salesid="+salesid+"/custid="+custid;
                            }
                        }
                    });
                }
            }
            
            function deleteInvoiceItem(itemid){
                var salesid = $("#sid").val();
                 var custid = $("#custid").val();
                 var isDelete = confirm("Are you sure to delete this item?");
                 //alert(isDelete);
                 if(isDelete){
                    if(itemid > 0){
                        var ajaxURL = "ajax/deleteInvoiceItem.php?itemid=" + itemid;
                        $.ajax({ 
                            url:ajaxURL,
                            dataType: 'json',
                            success:function(data){
                                if (data.error == "1") {
                                    alert(data.msg);
                                } else {
                                    window.location.href = "sales/create/salesid="+salesid+"/custid="+custid;
                                }
                            }
                        });
                      }
                }
            }
            
            function completeSales(salesid, collecRegId){
                
                var paymodeId = $("#paymentMode").val();
 
           //                alert(paymodeId);
           //                return;
           var salesid = $("#sid").val();
                if(salesid > 0 && collecRegId != 0){
                    var paymentMode = document.querySelector('input[name="paymenttype"]:checked').value;
                    var paymentRefNum = 0;
                    if(paymodeId == 5 || paymodeId == 6){
                        var refNum = $("#payrefnum").val();
                        if(refNum == ""){
                            alert("Please enter payment reference number.");
                            return;
                        }else{
                            paymentRefNum = refNum;
                        }
                    }
                    var isConfirmed = confirm("Are you sure do you want to complete the Invoice?\n\nPayment Recived By - "+paymentMode);
//                    alert(isConfirmed);
                    if(isConfirmed){
                        var ajaxURL = "ajax/completeSales.php?salesid=" + salesid+"&collecRegId="+collecRegId+"&paymodeId="+paymodeId+"&paymentRefNum="+paymentRefNum;
                        //alert(ajaxURL);  
                        $.ajax({ 
                            url:ajaxURL,
                            dataType: 'json',  
                            success:function(data){  
                                //alert(data.error);
                                if (data.error == "1") {
                                    alert(data.msg);
                                } else {
                                    //window.location.href = "sales";
                                  //  alert('ajax/printBillPDFTst.php?invid='+salesid);
                                    //window.location.href = "sales/stat"+salesid; 
                                    //var myWindow = window.open('',"_blank");
                                    //myWindow.location.href = 'ajax/printBillPDFTst.php?invid='+salesid;                        
                                    //myWindow.focus(); 
                                    window.location.href = "sales/status="+1; 

                                }
                            } 
                        });
                    }
                }else{
                    alert("Invoice cannot be blank. Add items to Invoice before completing");
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
    
    function openSaleCollectionDetails(){
            var ajaxURL = "ajax/getOpenSaleCollection.php"; 
            //alert(ajaxURL); 
            $.ajax({ 
            url:ajaxURL,
            dataType: 'json',  
            success:function(data){
            if (data.error == "1") {
            alert(data.data);
            } else {
            var data = data.data;
            $("#cash").val(Math.round(data.cash * 100) / 100); 
            }
            } 
            });             
            }

            function openSaleStockDetails(){
            var ajaxURL = "ajax/getStockDetails.php"; 
            //                        alert(ajaxURL); 
            $.ajax({ 
            url:ajaxURL,
            dataType: 'json',  
            success:function(data){
            if (data.error == "1") {
            alert(data.data);
            } else {
            var data = data.data;
            var qty = parseFloat(data.total_qty);
//            alert(qty.toFixed(4));
//            alert(JSON.stringify(data.total_qty));
//            $("#openstock").val(Math.round(data.total_qty * 100) / 100);
            $("#openstock").val(qty.toFixed(4));
            
            }
            } 
            });     
            }


            function startSale(){  
            var openingCash = $("#cash").val();
            var opeingStock = $("#openstock").val();
            var ajaxURL = "ajax/insertIntoCollectionRegister.php?openingCash=" + openingCash+"&opeingStock="+opeingStock; 
//                                    alert(ajaxURL);
            $.ajax({ 
            url:ajaxURL,
            dataType: 'json',  
            success:function(data){
            //                                alert(JSON.stringify(data));
            if (data.error == "1") {
            alert(data.data);
            } else {
            window.location.href = "sales/create";
            }

            } 
            });
            }


            function closeSale(){
            var collecRegID = $('#collecRegID').val();
            var cashAmt = $("#closecash").val();
            var saleCash = $("#saleCash").val();
            var closingStock = $("#closestock").val();
            var debitcardAmt = 0;           //sending 0 to database so that need not to be change further code in ajax and DBLogic. mm_22012019
            var creditcardAmt = 0;

            if(collecRegID != "" && saleCash != ""){
            var ajaxURL = "ajax/closeSaleCollectionRegister.php?collecRegID=" + collecRegID+"&closingStock="+closingStock+"&cashAmt="+cashAmt+"&debitcardAmt="+debitcardAmt+"&creditcardAmt="+creditcardAmt+"&saleCash="+saleCash;
            $.ajax({  
            url:ajaxURL,
            dataType: 'json',  
            success:function(data){
            //                                alert(JSON.stringify(data));
            if (data.error == "1") {
            alert(data.data);
            } else {
            alert(data.data);
            window.location.href = "sales/create";
            }

            } 
            });



            document.getElementById("htmlpage").style.visibility = "hidden";
            hideCloseBtn();
            showOpenBtn();
            }


            }


            function showCloseSaleModel(){
                
                closeSaleStockDetails();
            var collecRegID = $('#collecRegID').val();
            if(collecRegID != ""){

            var ajaxURL = "ajax/getCloseSaleCollection.php?collecRegID=" + collecRegID; 
            //                        alert(ajaxURL); 
            $.ajax({ 
            url:ajaxURL,
            dataType: 'json',  
            success:function(data){
            if (data.error == "1") {
            alert(data.data);
            } else {
            var data = data.data;
            var cashTotal = +data.cash + +$("#cash").val() - +data.deposit_in_bank;
            $("#closecash").val(Math.round(cashTotal * 100) / 100);
            $("#saleCash").val(data.cash);
            } 
            } 
            });

            }
            }
            
            function closeSaleStockDetails(){
                            var ajaxURL = "ajax/getStockDetails.php"; 
            //                        alert(ajaxURL); 
            $.ajax({ 
            url:ajaxURL,
            dataType: 'json',  
            success:function(data){
            if (data.error == "1") {
            alert(data.data);
            } else {
            var data = data.data;
            //                                alert(JSON.stringify(data.total_qty));
            var qty = parseFloat(data.total_qty);
            $("#closestock").val(qty.toFixed(4));
//            $("#closestock").val(Math.round((data.total_qty) * 100) / 100);
            }
            } 
            }); 
            }

            function showCloseBtn(){
            document.getElementById("closeSale").disabled = false; 
            } 

            function hideCloseBtn(){
            document.getElementById("closeSale").disabled = true; 
            }

            function showOpenBtn(){
            document.getElementById("openSale").disabled = false; 
            }

            function hideOpenBtn(){
            document.getElementById("openSale").disabled = true; 
            } 
            
            function showStockDetails(){
                var win = window.open("cr/stock/report", '_blank');
                win.focus();
            }
            
            function hideButton(btnId){
            document.getElementById(btnId).style.display = "none";
            }
        </script>
<!--        <link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />        
        <?php
    }

    public function pageContent() {
        $menuitem = "sales"; //pagecode
        include "sidemenu." . $this->currStore->usertype . ".php";
        $formResult = $this->getFormResult();
        $dbl = new DBLogic();
        $userid = getCurrStoreId();
        $obj_states = $dbl->getStates();

        $obj_categories = $dbl->getAllActiveCategories();
        $obj_products = $dbl->getAllActiveStock($this->currStore->usertype, $userid);
        $obj_suppliers = $dbl->getAllActiveSuppliers();
        $obj_specifications = $dbl->getAllActiveSpecifications();
        $obj_sale = null;
        $paymentmode = 0;
        $obj_sale_items = array();
        $cname = "";
        $cphone = "";
        if ($this->salesid > 0) {
            if ($this->custid == 0) {
                $dbl->removeCustFromInv($userid, $this->custid, $this->salesid);
            }
            $obj_sale = $dbl->getSalesInfo($userid, $this->salesid);
            $obj_sale_items = $dbl->getInvoiceItems($this->salesid, $userid);
            $paymentmode =  $obj_sale->paymentmode;
            //print_r($obj_sale_items);
        }
        if ($this->custid > 0) {
            $obj_customer = $dbl->getCustomerById($this->custid);
            if ($obj_customer != NULL && isset($obj_customer)) {
                $cname = $obj_customer->name != null && trim($obj_customer->name) != "" ? $obj_customer->name : "";
                $cphone = $obj_customer->phone != null && trim($obj_customer->phone) != "" ? $obj_customer->phone : "";
            }
        }
        $cutting_charge = 0;
        $obj_cutting_charges = $dbl->getCuttingCharges();
        if ($obj_cutting_charges != null) {
            $cutting_charge = $obj_cutting_charges->charge;
        }
        $collectionRegObj = $dbl->checkOpenSaleStatus($userid);
        ?>
        <div class="container-section">
             <div class="row">
               <div class="col-md-12">
                   <div  class="panel panel-default">
                       <div class="panel-body">
                           <h1 class="title-bar">Day Open/Close</h1>
                           <div class="common-content-block">
                               <div class="col-md-12">
                                   <?php if ($collectionRegObj->id != 0) { ?>

                                       <div class="col-md-6">
                                           <button type="button" class="btn btn-primary" id="closeSale" data-toggle="modal" data-target="#modal-closesale" onclick="showCloseSaleModel();">Day Close</button>    
                                       </div>
                                   <?php } else { ?>
                                       <div class="col-md-6">
                                           <button type="button" class="btn btn-primary" id="openSale" data-toggle="modal" data-target="#modal-opensale">Day Open</button>                                        
                                       </div>
                                   <?php } ?>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
           <?php
           if ($collectionRegObj->id != 0) {
               echo '<script type="text/javascript">',
               'hideOpenBtn();',
               'showCloseBtn();',
               '</script>'
               ;
               ?>

               <?php if ($paymentmode == 0) { ?>
                   <input  id="paymentMode" hidden name="paymentMode" value="3">
               <?php } ?>
               <?php if ($paymentmode == 1) { ?>
                   <input  id="paymentMode" hidden name="paymentMode" value="1">
               <?php } ?>
               <?php if ($paymentmode == 2) { ?>
                   <input  id="paymentMode" hidden name="paymentMode" value="4">
               <?php } ?>
               <?php if ($paymentmode == 3) { ?>
                   <input  id="paymentMode" hidden name="paymentMode" value="5">
               <?php } ?>
               <?php if ($paymentmode == 4) { ?>
                   <input  id="paymentMode" hidden name="paymentMode" value="6">
               <?php } ?>
               <input  id="collecRegID" hidden name="collecRegID" value="<?php echo $collectionRegObj->id; ?>">
               <input  id="saleCash" hidden name="saleCash" value="">
          <div id="htmlpage">  
            <div class="row">
                <div class="col-md-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Assign Customer (optional)</h2>
                            <div class="common-content-block">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label>Customer Name : <?php echo $cname; ?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Customer Mobile : <?php echo $cphone; ?></label>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-primary" onclick="addCustomer();">
                                            Add Customer
                                        </button>                                        
                                        <button type="button" class="btn btn-primary" onclick="removeCustomer(<?php echo $this->salesid; ?>);">
                                            Remove Customer
                                        </button>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Add Items</h2>
                            <div class="common-content-block">
                                <form role="form" id="salesadditem" name="salesadditem" enctype="multipart/form-data" method="post" action="formpost/salesAddItem.php">
                                    <input type="hidden" name="sid" id="sid" value="<?php echo $this->salesid; ?>"/>
                                    <input type="hidden" name="custid" id="custid" value="<?php echo $this->custid; ?>"/>
                                    <input type="hidden" name="kgperpc" id="kgperpc" value=""/>
                                    <input type="hidden" name="cutcharge" id="cutcharge" value="<?php echo $cutting_charge; ?>"/>
                                    <input type="hidden" name="orgrate" id="orgrate" value=""/>
                                    <input type="hidden" id="batchcode" name="batchcode"  value="<?php echo $this->getFieldValue("batchcode"); ?>"/>
                                    <input type="hidden" id="availableqty" name="availableqty" class="form-control" placeholder="Available Qty (Kg)" value="<?php echo $this->getFieldValue("availableqty"); ?>">
                                    <input type="hidden" id="length" name="length" class="form-control" placeholder="Length (mm)" value="<?php echo $this->getFieldValue("length"); ?>" autocomplete="off" readonly="">
                                    <input type="hidden" id="selectedbatches" name="selectedbatches" value=""/>
                                    <div class="col-md-12">
<!--                                        <div class="col-md-12">
                                           <label> Select Date : </label><br>
                                         <input type="text" id="saledate" name="saledate" class="form-control" placeholder= "Sale Date" value="<?php echo $this->getFieldValue("saledate"); ?>">
                                        </div>-->
                                        <div class="col-md-12">
                                            <br/>
                                            <label>Select Product</label><br/>
                                            <select id="prodsel" name="prodsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="getRate(this.value); selectBatchCode(this.value);">
                                                <option value="-1">Select Product</option>
                                                <?php foreach ($obj_products as $prod) { 
                                                        $desc1 = isset($prod->desc_1) && trim($prod->desc_1) != "" ? " , ".$prod->desc_1." mm" : "";
                                                        $desc2 = isset($prod->desc_2) && trim($prod->desc_2) != "" ? " x ".$prod->desc_2." mm" : "";
                                                        $thickness = isset($prod->thickness) && trim($prod->thickness) != "" ? " , ".$prod->thickness." mm" : "";
                                                        $itemname = $prod->category." - ".$prod->prod.$desc1.$desc2.$thickness;                                                    
                                                    ?>
                                                         
                                                    <option value="<?php echo $prod->id . "::" . $prod->prodid . "::" . $prod->kg_per_pc; ?>"><?php echo $itemname ?></option>
        <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12"><br>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Select Batch Code :</label><br>
                                                <select id="batchcodes" name="batchcodes[]" class="selectpicker form-control" multiple data-show-subtext="true" data-live-search="true" >
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label>UOM (KG)</label><br/>
                                            <input type="text" id="qty" name="qty" class="form-control" placeholder="KG" value="<?php echo $this->getFieldValue("qty"); ?>" onkeyup=checkqty(this.value);>
                                        </div>
                                        <div class="col-md-3">
                                            <label>UOM (MT)</label><br/>
                                            <input type="text" id="mt" name="mt" class="form-control" placeholder="MT" value="<?php echo $this->getFieldValue("mt"); ?>" onkeyup=checkmt(this.value);>
                                        </div>
                                        <div class="col-md-3">
                                            <label>No. Of units</label><br/>
                                            <input type="text" id="numberofunits" name="numberofunits" readonly="true" class="form-control" placeholder="No. Of Units" readonly value="<?php echo $this->getFieldValue("numberofunits"); ?>"/>
                                        </div>



                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label>Base Rate</label><br/>
                                            <input type="text" id="baserate" name="baserate" readonly="true" class="form-control" placeholder="Value" value="<?php echo $this->getFieldValue("baserate"); ?>" onkeyup=calcValue(this.value);>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <label>Rate</label><br/>
                                            <input type="text" id="rate" name="rate" readonly="true" class="form-control" placeholder="Rate" value="<?php echo $this->getFieldValue("rate"); ?>" onkeyup=calcValue(this.value);>
                                        </div>
                                                                               
                                        <div class="col-md-3">
                                            <label>Value</label><br/>
                                            <input type="text" id="value" name="value" readonly="true" class="form-control" placeholder="Value" value="<?php echo $this->getFieldValue("value"); ?>" onkeyup=calcValue(this.value);>
                                        </div>
                                        <div class="col-md-3">
                                            <label></label><br/>
                                            <input type="checkbox" id="cuttingcharges" name="cuttingcharges">Cutting Charges
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-12">
                                        <br/>
                                        <button type="submit" class="btn btn-primary" id="add_item_button" style="display:block" onclick="hideButton(this.id);">Add Item</button>
                                    </div>
                                    <div class="alert" style="display:<?php echo $formResult->showhide; ?>;"<?php // echo $formResult->status; ?>>
                                            <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                            <h4> <?php echo $formResult->status; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Items Added</h2>
                            <div class="common-content-block">
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td>Sl.No.</td>
                                                <td>Product</td>
<!--                                                <td>Batchcode</td>-->
                                                <td>MRP</td>
                                                <td>MT</td>
                                                <td>Base Rate(Rs./MT)</td>
                                                <td>Taxable(Rs)</td>
                                                <td>Tax Value(Rs)</td>
                                                <td>Total(Rs)</td>
                                                <td>Action</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $slno = 0;
                                            $total_qty = 0;
                                            $total_rate = 0;
                                            $total_taxable = 0;
                                            $total_tax = 0;
                                            $total = 0;
                                            if ($obj_sale_items != NULL) {
                                                foreach ($obj_sale_items as $item) {
                                                    $slno++;
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $slno; ?></td>
                                                        <td><?php
                                                             $desc1 = isset($item->desc_1) && trim($item->desc_1) != "" ? " , ".$item->desc_1." mm" : "";
                                                             $desc2 = isset($item->desc_2) && trim($item->desc_2) != "" ? " x ".$item->desc_2." mm" : "";
                                                             $thickness = isset($item->thickness) && trim($item->thickness) != "" ? " , ".$item->thickness." mm" : "";
                                                             $itemname = $item->product.$desc1.$desc2.$thickness;
                                                               //echo $itemname;
                                                        echo $itemname; ?></br><b><?php echo $item->batchcode; ?></b></td>
<!--                                                        <td><?php echo $item->batchcode; ?></td>-->
                                                        <td><?php echo $item->mrp; ?></td>
                                                        <td><?php echo $item->qty; ?></td>
                                                        <td><?php echo $item->rate; ?></td>
                                                        <?php
                                                        
                                                        //$taxable = sprintf("%.2f",$item->rate * $item->qty); 
                                                        $taxable = sprintf("%.2f",$item->taxable); 
                                                        $taxTotal =  sprintf("%.2f",$item->igst_amt * $item->qty);
                                                        
                                                        ?>
                                                        <td><?php echo $taxable; ?></td>
                                                        <td><?php echo $taxTotal; ?></td>
                                                        <td><?php echo $item->total; ?></td>
                                                        <td><button class="btn btn-primary" onclick="deleteInvoiceItem(<?php echo $item->id; ?>);">Delete</button></td>
                                                    </tr>
                                                    <?php
                                                    $total_qty = $total_qty + $item->qty;
                                                    $total_rate = $total_rate + $item->rate;
                                                    $total_taxable = $total_taxable + $taxable;
                                                    $total_tax = $total_tax + $taxTotal;
                                                    $total = $total + $item->total;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                        <tfooter>
                                            <tr>
                                                <td></td>
                                                <td></td>
<!--                                                <td></td>-->
                                                <td></td>
                                                <td><?php echo $total_qty; ?></td>
                                                <td><?php echo $total_rate; ?></td>
                                                <td><?php echo $total_taxable; ?></td>
                                                <td><?php echo $total_tax; ?></td>
                                                <td><?php echo $total; ?></td>
                                                <td></td>
                                            </tr>
                                        </tfooter>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                    <br/>
                                    
                                    <?php 
                                        
                                    if($this->custid > 0){
                                        if ($obj_customer != NULL && isset($obj_customer)) {
                                            
                                            if($obj_customer->isregister == 1){
                                                if($paymentmode == 0){ ?> 
                                                    <input type="radio" name="paymenttype" value="Cash" checked onclick="applyCharges(0);"/>Cash Bill
                                                <?php } else { ?> 
                                                    <input type="radio" name="paymenttype" value="Cash"  onclick="applyCharges(0);"/>Cash Bill
                                                <?php }
                                                if($paymentmode == 1){ ?> 
                                                    <input type="radio" name="paymenttype" value="Dedit Card" checked onclick="applyCharges(1);"/>Debit Card Bill
                                                <?php } else { ?> 
                                                    <input type="radio" name="paymenttype" value="Dedit Card" onclick="applyCharges(1);"/>Debit Card Bill
                                                <?php }
                                                if($paymentmode == 2){ ?> 
                                                    <input type="radio" name="paymenttype" value="Credit Card" checked onclick="applyCharges(2);"/>Credit Card Bill
                                                <?php } else {?> 
                                                    <input type="radio" name="paymenttype" value="Credit Card" onclick="applyCharges(2);"/>Credit Card Bill
                                                <?php }
                                                if($paymentmode == 4){ ?> 
                                                    <input type="radio" name="paymenttype" value="Net Banking" checked onclick="applyCharges(4);"/>Net Banking Bill
                                                <?php } else {?> 
                                                    <input type="radio" name="paymenttype" value="Net Banking" onclick="applyCharges(4);"/>Net Banking Bill
                                                <?php }
                                                
                                            }else{
                                                if($paymentmode == 0){ ?> 
                                                    <input type="radio" name="paymenttype" value="Cash" checked onclick="applyCharges(0);"/>Cash Bill
                                                <?php } else { ?> 
                                                    <input type="radio" name="paymenttype" value="Cash"  onclick="applyCharges(0);"/>Cash Bill 
                                                <?php }
                                            }
                                            
                                        }else{
                                            if($paymentmode == 0){ ?> 
                                                    <input type="radio" name="paymenttype" value="Cash" checked onclick="applyCharges(0);"/>Cash Bill
                                                <?php } else { ?> 
                                                    <input type="radio" name="paymenttype" value="Cash"  onclick="applyCharges(0);"/>Cash Bill 
                                            <?php }
                                        }    
                                        
                                    }else{
                                            if($paymentmode == 0){ ?> 
                                                    <input type="radio" name="paymenttype" value="Cash" checked onclick="applyCharges(0);"/>Cash Bill
                                                <?php } else { ?> 
                                                    <input type="radio" name="paymenttype" value="Cash"  onclick="applyCharges(0);"/>Cash Bill 
                                            <?php }
                                        }
                                            
                                    ?>
                                    
                                    </div>
                                    <?php if($paymentmode == 3 || $paymentmode == 4){  ?>
                                    <div class="col-md-6" id="payrefnumdiv">
                                        <br/>
                                        <label>Payment Reference Number: </label>
                                        <input type="text" name="payrefnum" id="payrefnum" />
                                    </div>
                                    <?php  }  ?>
                                    
                                    
                                </div>
                                <div class="col-md-12">
                                    <br/>
                                    <button class="btn btn-primary" onclick="cancelSales(<?php echo $this->salesid; ?>);">Cancel</button>                                    
                                    <button class="btn btn-primary" onclick="completeSales('<?php echo $this->salesid; ?>', '<?php echo $collectionRegObj->id; ?>');">Complete</button>`                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
<!--          </div> end       -->
            <?php
           } else {
               echo '<script type="text/javascript">',
               'hideCloseBtn();',
               '</script>'
               ;
           }
           ?>      
       
        </div><!-- end -->
        <div class="modal fade" id="modal-opensale" role="dialog" style="padding-top: 50px" data-backdrop="false" tabindex="-1" role="dialog"  > 
            <!--<div>-->
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="title-bar" >Day Open</h4>                       
                    </div>
                    <div class="modal-body">
                        <div  class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="modal-title">Cash in RS.</h2>
                                <div class="common-content-block">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class='col-sm-12'>
                                                <div class='col-sm-12'>
                                                    <input type="number" id="cash" disabled="true" name="cash" class="form-control">
                                                </div>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="modal-title">Stock in MT.</h2>
                                <div class="common-content-block">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class='col-sm-12'>
                                                <div class='col-sm-8'>
                                                    <input type="number" id="openstock" disabled="true" name="openstock" class="form-control">
                                                </div>
                                                <div class='col-sm-4'>
                                                    <button class="btn btn-primary" onclick="showStockDetails();">Show Stock Info</button>
                                                </div>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-group">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <!--<button type="submit" class="btn btn-primary" >Create PDF</button>-->
                            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="startSale();">Start Sale</button>
                        </div>
                    </div>
                </div>
                <!--/.modal-content--> 
            </div>
            <!--/.modal-dialog--> 
        </div>
        <div class="modal fade" id="modal-closesale" role="dialog" style="padding-top: 50px" data-backdrop="false" tabindex="-1" role="dialog"  > 
            <!--<div>-->
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="title-bar">Day Close</h4>                       
                    </div>
                    <div class="modal-body">
                        <div  class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="modal-title">Cash in RS.</h2>
                                <div class="common-content-block">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class='col-sm-12'>
                                                <div class='col-sm-12'>
                                                    <input type="text" id="closecash" disabled="true" name="closecash" class="form-control">
                                                </div>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div  class="panel panel-default">
                            <div class="panel-body">
                                <h2 class="modal-title">Stock in MT.</h2>
                                <div class="common-content-block">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class='col-sm-12'>
                                                <div class='col-sm-12'>
                                                    <input type="number" id="closestock" disabled="true" name="closestock" class="form-control">
                                                </div>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="form-group">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <!--<button type="submit" class="btn btn-primary" >Create PDF</button>-->
                            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="closeSale();">Close Sale</button>
                        </div>
                    </div>
                </div>
                <!--/.modal-content--> 
            </div>
            <!--/.modal-dialog--> 
        </div>

        
        <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
                  
        <?php
        // }else{ print "You are not authorized to access this page";}
    }

}
?>

