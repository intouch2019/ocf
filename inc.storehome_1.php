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
<!--<div class="grid_12" style="margin-right:0;">&nbsp;</div>-->
        <br/>
        <br/>
        <div class="grid_4"style="font-size: 26px; width:500px;">
            <h6>INTOUCH PARTNER NETWORK</h6><br/>
<!--            <a href="http://www.mozilla.org/en-US/products/download.html"><img width="32px" src="images/firefox.png"> Download Firefox</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="http://www.google.com/chrome"><img width="32px" src="images/chrome.png"> Download Chrome</a>-->
        </div>
        <div class="grid_4" style="margin-right:0px;">&nbsp;
         </div>
	<div class="box grid_4" style="margin-left:0px;font-size: 16px;">
            <h6><center>Login</center></h6><br/>
		<ul class="bottom" align="right">
                    <form method="post" name="storeloginform" action="postLogin.php">
                                    <div style="height:50px;">
                                        <!--<div style="float:left;width:70px; margin-right:20px; margin-left:15%; font-weight: bolder; font-size: 14px;">Username: </div>-->
                                        <input type="text" size="15" style="float:left;height:30px;width:250px;margin-right:20px; margin-left:15%;" placeholder="UserName" name="storecode" value="<?php echo $form_storecode; ?>" /></div>
                                    <div style="height:20px;">
                                        <!--<div style="float:left;width:70px; margin-right:20px; margin-left:15%; font-weight: bolder; font-size: 14px;">Password: </div>-->
                                        <input type="password" size="15" style="float:left;height:30px;width:250px;margin-right:20px; margin-left:15%;"placeholder="Password" name="password" /></div>
                    
                        <div style="height:100px;"><div style="float:left;  width:145px;">&nbsp;</div ><input type="submit" value="Login" style="float:left; font-size: 20px;color:white; height:40px; width:257px;margin-right:20px; margin-left:15%; background-color: #161616" name="Submit input" /></div>
                  <span class="error" id="slf_status" style="display:<?php echo $disp; ?>;"><?php echo $form_errors ?></span>
                    </form>
		</ul>
	</div>
        <div class="grid_4" style="margin-right:0;">&nbsp;</div>
    <div class="clear"></div>
    <div class="grid_4" style="margin-right:0;">&nbsp;</div>
    <!--<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>-->
