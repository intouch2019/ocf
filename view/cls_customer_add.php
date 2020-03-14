<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_customer_add extends cls_renderer{

        var $currStore;
        var $userid;
        var $dtrange;
        var $params;
       
        function __construct($params=null) {
            //parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
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
        </style>
<script type="text/javaScript">    
    
     $(function() { 
        $('input[type="checkbox"]').bind('click',function() {
          $('input[type="checkbox"]').not(this).prop("checked", false);
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
    
    function downloadSample(){
        window.location.href = "formpost/generateProdPriceSample.php";
    }

    function searchCustomer(){
        var cphone = $("#sphone").val();
        //alert("cphone "+cphone);
        if(cphone == ""){
            alert("Please enter customer phone number to search");
        }else{
            var ajaxURL = "ajax/searchCustomer.php?cphone=" + cphone;
                //alert(ajaxURL);
                $.ajax({ 
                url:ajaxURL,
                    dataType: 'json', 
                    success:function(data){
                        //alert(data.error);
                        if (data.error == "1") {
                            alert(data.msg);
                            $("#phone").val(data.phone);
                            $("#name").focus();
                            $("#custid").val("");
                            $("#name").val("");
                            $("#address").val("");
                            $("#statesel").val("");
                            $("#city").val("");
                            $("#email").val("");
                            $("#gstno").val("");
                            $("#panno").val("");
                            
                            $("#submitbt").show();
                            $("#selectbt").hide();
                        } else {
                            $("#custid").val(data.id);
                            $("#name").val(data.name);
                            $("#address").val(data.address);
                            $("#statesel").val(data.state_id);
                            $("#city").val(data.city);
                            $("#phone").val(data.phone);
                            $("#email").val(data.email);
                            $("#gstno").val(data.gstno);
                            $("#panno").val(data.panno);
                            if(data.isregister == 1){
                              document.getElementById("reg").checked = true;
                              }else if(data.isregister == 0){
                              document.getElementById("nonreg").checked = true;
                             }
                            $("#submitbt").hide();
                            $("#selectbt").show();
                        }
                    }
                });
        }
    }
    
    function selectCustomer(){ 
        var custid = $("#custid").val();
        //update
        var gstin = $("#gstno").val();
        var panno = $("#panno").val();
        if(gstin != "" || panno !=""){
            var ajaxURL = "ajax/updateCustomer.php?custid=" + custid +"&gstin="+ gstin +"&panno" + panno;  
                      alert(ajaxURL);
                      $.ajax({
                      url:ajaxURL, 
                          dataType: 'json',
                          success:function(data){
                              //alert(data.error);
                              if (data.error == "1") {
                                  alert(data.msg);
                              } else {
                                  //alert("PO item deleted successfully.")
                                  //window.location.href = "po/additem/poid="+<?php //echo $this->poid;?>;  
                              }
                          } 
                      });
        }
        
        window.location.href = "sales/create/custid="+custid;
    }
    
    function checkBox(){
    //alert("jjj");
    var cbox = document.forms["custadd"]["reg"];
         if(cbox[0].checked == false && cbox[1].checked == false){
                alert('Please check the Customer Type from Checkbox');
                return false;
         }else{
             //alert("hererrer");
             return true; 
         }
    }
    
    function nonRegCust(){
        //$("#elementId").attr('required', '');
        //$("#elementId").removeAttr('required');
        $("#custid").removeAttr('required');
        $("#name").attr('required', '');
        $("#address").removeAttr('required');
        $("#phone").attr('required', '');
        $("#city").removeAttr('required');
        $("#phone").attr('required', '');
        $("#email").removeAttr('required');
        $("#gstno").removeAttr('required');
        $("#panno").removeAttr('required');
    }
    
    function RegCust(){
        $("#custid").attr('required', '');
        $("#name").attr('required', '');
        $("#address").attr('required', '');
        $("#statesel").attr('required', '');
        $("#city").attr('required', '');
        $("#phone").attr('required', '');
        $("#email").attr('required', '');
       // $("#gstno").attr('required', '');
        $("#panno").attr('required', '');
    }

</script>
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />  
        
        <?php
        }

        public function pageContent() {
            $menuitem = "products";//pagecode
            include "sidemenu.".$this->currStore->usertype.".php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
            $obj_states = $dbl->getStates();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="title-bar">Customer Search</h2>
                            <div class="common-content-block">   
                                 <div class="box box-primary"><br>
                                    <div class="form-group">
                                        <input type="text" id="sphone" name="sphone" class="form-control" placeholder="Phone" value="<?php echo $this->getFieldValue("sphone"); ?>">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" onclick="searchCustomer();" class="btn btn-primary">Search</button>
                                    </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Add Customer</h2>
                        <div class="common-content-block">   
                             <div class="box box-primary"><br>
                                <form role="form" id="custadd" name="custadd" enctype="multipart/form-data" method="post" action="formpost/customerAdd.php" onsubmit="return checkBox();">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <div class="col-md-4" id="nonregister">
                                                <input type="checkbox" id="nonreg" name="reg" value="0" onclick="nonRegCust();"> Non Register
                                            </div>
                                            <div class="col-md-4" id="register" >
                                                 <input type="checkbox" id="reg" name="reg" value="1" onclick="RegCust();"> Register
                                            </div>
                                        </div><br/><br/>
                                        <div class="form-group">
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Name" value="<?php echo $this->getFieldValue("name"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo $this->getFieldValue("address"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <select id="statesel" name="statesel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                                <option value="">Select State</option>
                                                <?php foreach($obj_states as $state){ ?>
                                                    <option value="<?php echo $state->ID;?>"><?php echo $state->STATE." [ ".$state->STATE_CODE." ]";?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="city" name="city" class="form-control" placeholder="City" value="<?php echo $this->getFieldValue("city"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone" value="<?php echo $this->getFieldValue("phone"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="email" name="email" class="form-control" placeholder="Email" value="<?php echo $this->getFieldValue("email"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="gstno" name="gstno" class="form-control" placeholder="GST No" value="<?php echo $this->getFieldValue("gstno"); ?>">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" id="panno" name="panno" class="form-control" placeholder="PAN No" value="<?php echo $this->getFieldValue("panno"); ?>">
                                        </div>
                                    </div>
                                    <div class="box-footer" id="submitbt">
                                        <button type="submit" class="btn btn-primary">Create</button>
                                    </div>
                                    <div class="box-footer" id="selectbt" style="display:none;">
                                        <input type="hidden" id="custid" name="custid" />
<!--                                        <input type="button" class="btn btn-primary" onclick="selectCustomer();" value="Select"/>-->
                                        <button type="submit" class="btn btn-primary">Update & Select</button>
                                    </div>
                                </form><br><br>                                    
                                <?php if ($formResult->form_id == 'custadd') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                             </div>   
                            
                        </div>
                    </div>
                </div>
            </div>
        </div> 
 </div>
            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


