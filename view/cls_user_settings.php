<?php
require_once ("view/cls_renderer.php");
require_once ("lib/db/DBConn.php");
require_once ("lib/core/Constants.php");
require_once ("session_check.php");

class cls_user_settings extends cls_renderer {
    var $currUser;
    var $userid;
    function __construct($params=null) {
//        parent::__construct(array(UserType::WKAdmin,UserType::Admin));
        $this->currUser = getCurrStore();
        if (!$this->currUser) { return; }
        $this->userid = $this->currUser->id;
    }

    function extraHeaders() { ?>

<script language="JavaScript" src="js/tigra/validator.js"></script>
<script type="text/javascript">
    function changeInfo(){
        $("#storeInfo").hide();
        $("#storeAdd").show();
    }

    function validate1(theForm){
//alert("Called");
  var elements = document.getElementsByClassName("required");
  var i;
  var validated = true;
for (i = 0; i < elements.length; i++) {
  if (elements[i].value == "") {
    if (validated == true) {
      validated = false;
    }
    elements[i].style.borderColor = "#f67575"; 
  }else{
    elements[i].style.borderColor = "#00b0da";
  }
    // elements[i].style.backgroundColor = "red";
}

if(validated == true){
  //alert("validated");
        var formName = theForm.name;
        var arr = formName.split("_");
        var form_id = arr[1];
        var params = $(theForm).serialize();
      //  alert(params);
        var ajaxUrl = "formpost/changePassword.php?"+params;
    //    alert(ajaxUrl);
  $.getJSON(ajaxUrl, function(data){
  //  alert(data['msg']);
    var error= data['error'];
    var msg = data['msg'];
    var id= data['spanid'];
//    alert(msg);
   if(error==0){
      alert(msg);
      $("#password").val("");
      $("#password2").val("");
   }else{
       alert(msg);
       $("#password").val("");
       $("#password2").val("");
   }
  });
}
}    
</script>
    <?php
    } //end of extra headers

    public function pageContent() {
        $formResult = $this->getFormResult();
        $menuitem="settings";
        //$_SESSION["form_type"]="";
//        include "sidemenu.".$this->currUser->usertype.".php";
        include "sidemenu.php";


        ?>
<div class="main-content-right-side sing-up">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="sing-up-block">
                                    <form id="form1" name="form1" action="" onsubmit="validate1(this); return false;" method="post">
                                        <input type = "hidden" name="form_id" id="form_id" value="form1">

                                            <div class="form-group">
                                                <input class="required logintextbox"  placeholder="Username" type="text" name="username" value="<?php echo  $this->currUser->username ?>" readonly="" />
                                                <label name="usernameerror"  style="margin-left: 120px;" ><span style="font-size: 12px; color:#f67575; " id="usernameerror"></span></label>
                                                <label name="usererror"  style="float:left;margin-left: 120px;" ><span style="font-size: 12px; color:#f67575; " id="usererror"></span></label>
                                            </div>

                                            <div class="form-group">
                                                <input class="required logintextbox" placeholder="Password" type="password" id="password" name="password" value="" />
                                                <label name="password1error"  style="margin-left: 120px;" ><span style="font-size: 12px; color:#f67575; " id="password1error"></span></label>
                                                <label name="pass1error"  style="float:left;margin-left: 120px;" ><span style="font-size: 12px; color:#f67575; " id="pass1error"></span></label>
                                            </div>

                                            <div class="form-group">
                                                <input class="required logintextbox"  placeholder="Re-type Password" type="password" id="password2" name="password2" value=""/>
                                                <label name="passworderror"  style="margin-left: 120px;" ><span style="font-size: 12px; color:#f67575; " id="passworderror"></span></label>
                                                <label name="passerror"  style="float:left;margin-left: 120px;" ><span style="font-size: 12px; color:#f67575; " id="passerror"></span></label>
                                            </div>

                                                    <?php if ($formResult->form_id == 'form1') { ?>
                                            <p>
                                                  <span id="statusMsg" class="<?php echo $formResult->cssClass; ?>" style="display:<?php echo $formResult->showhide; ?>;"><?php echo $formResult->status; ?>   </span>
                                            </p>
                                                    <?php } ?>
                                           <div class="form-group">
                                               <button type="submit" class="btn btn-default">Change Password </button> <!--<i class="fa fa-long-arrow-right" aria-hidden="true"></i>-->
                                            <!--<input class="btn btn-default" type="submit" value="Change Password" name="Submit input" />-->
                                             <label name="success"  ><span style="font-size: 12px; color:green; " id="success"></span></label> <!-- style="margin-left: 120px;"-->
                                           </div>
                                        </form>
   
                                </div>
                            </div>
                        </div>
                    </div>
</div>    
<!--<div class="grid_10">
    <div class="grid_3"><h5 style="margin-top:0;"></h5></div>
    <div class="box grid_4" style=" height: 450px; width:520px; font-size: 16px; opacity:0.9;margin-top: 1%"> 
    <div class="login">User Details&nbsp;</div>
    <ul class="bottom" align="right">
      <form id="form1" action="formpost/changePassword.php" method="post"> 
     <form id="form1" name="form1" action="" onsubmit="validate1(this); return false;" method="post">
            <input type = "hidden" name="form_id" id="form_id" value="form1">

                <div style="height:70px;">
                <input class="required logintextbox"  size="15" placeholder="Username" type="text" name="username" value="<?php echo  $this->currUser->username ?>" readonly="" /></div>

                <div style="height:70px;">
                <input class="required logintextbox"  size="15" placeholder="Password" type="password" name="password" value="" /></div>

                <div style="height:50px;">
                <input class="required logintextbox" size="15" placeholder="Re-type Password" type="password" name="password2" value=""/>
                <label name="passworderror"  style="margin-left: 120px;" ><span style="font-size: 12px; color:#f67575; " id="passworderror"></span></label>
                <label name="passerror"  style="float:left;margin-left: 120px;" ><span style="font-size: 12px; color:#f67575; " id="passerror"></span></label>
                </div>
      
                        <?php if ($formResult->form_id == 'form1') { ?>
                <p>
                      <span id="statusMsg" class="<?php echo $formResult->cssClass; ?>" style="display:<?php echo $formResult->showhide; ?>;"><?php echo $formResult->status; ?>   </span>
                </p>
                        <?php } ?>
   <div style="height:100px;"><div style="float:left;  width:120px;">&nbsp;</div>
                <input class="loginbutton" type="submit" value="Change Password" name="Submit input" />
                 <label name="success"  style="margin-left: 120px;" ><span style="font-size: 12px; color:green; " id="success"></span></label></div>
            </form>
            </ul>
    </div>
</div>  end class=grid_10 -->
<div class="clear"></div>
    <?php
                unset ($_SESSION["form_type"]);
    }
}
?>
