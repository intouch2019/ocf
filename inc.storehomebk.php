<?php
$form_errors = null;
if (isset($_SESSION['form_errors'])) { $form_errors = $_SESSION['form_errors']; }
if ($form_errors && count($form_errors) > 0) {
$form_errors = implode("<br />", $form_errors);
$disp="block";
} else {
$disp="none";
}
$form_storecode = ""; if (isset($_SESSION['form_storecode'])) $form_storecode = $_SESSION['form_storecode'];
?>
<br/>
<br/>
<div class="grid_4"style="font-size: 28px; width:450px;margin-left:5%;margin-top: 1% "> 
    <h6>INTOUCH PARTNER NETWORK</h6> <hr style="background:#686868; border:0; height:1px; margin-top:0px;margin-bottom:5px;" />
    <p style="font-size:25px;">Keeping Enterprises Connected</p>
    <ul class="sign-up-listing">
        <li class ="supply-tracking"><img height="50" src="images/icon-suppy-tracking.png"><a href="#">Inventory<br>Tracking</a></li>
    </ul>
   
</div>

<div class="box grid_4" style="width:520px; font-size: 16px; opacity:0.9;margin-top: 1%">    
    <div class="login">Login</div>
    <ul class="bottom" align="right">
        <form method="post" name="storeloginform" action="postLogin.php">
            <div style="height:70px;">
                <!--<div style="float:left;width:70px; margin-right:20px; margin-left:15%; font-weight: bolder; font-size: 14px;">Username: style="float:left;height:40px;width:255px;margin-right:20px; margin-left:15%; background-color: #e7e7e7 " </div>-->
                <input  class="logintextbox" type="text" size="15" placeholder="Username" name="storecode" value="<?php echo $form_storecode; ?>" /></div>
            <div style="height:40px;">
                <!--<div style="float:left;width:70px; margin-right:20px; margin-left:15%; font-weight: bolder; font-size: 14px;">Password: </div>-->
                <input  class="logintextbox"  type="password" size="15" placeholder="Password" name="password" />
                <span class="error " id="slf_status" style="float:left; margin-left: 25%; color:#f67575; font-size:12px; display:<?php echo $disp; ?>;"><?php echo $form_errors ?></span>
            </div>
            <div style="height:100px;"><div style="float:left;  width:120px;">&nbsp;</div>
                <input class="loginbutton"type="submit" value="Login" name="Submit input" /></div>
            <!--<span class="error " id="slf_status" style="float:left; color:#f67575; font-size:12px; display:<?php //echo $disp; ?>;"><?php //echo $form_errors ?></span>-->
        </form>
    </ul>
</div>
<div class="grid_6" id="footer" style=" text-align:left; width:40%; float:right;">
   <a target="_blank" href="<?php echo DEF_SITEURL ?>">Intouch Consumer Care Solutions Pvt. Ltd</a>. All rights reserved.
</div>
<div class="grid_4" style="margin-right:0;">&nbsp;</div>
<div class="clear"></div>
<div class="grid_4" style="margin-right:0;">&nbsp;</div>