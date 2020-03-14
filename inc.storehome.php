<?php
$form_errors = null;
if (isset($_SESSION['form_errors'])) { $form_errors = $_SESSION['form_errors']; }
if ($form_errors && count($form_errors) > 0) {
$form_errors = implode("<br />", $form_errors);
$disp="block";
} else {
$disp="none";
}
$clsLocation = new clsLocation();
$form_storecode = ""; if (isset($_SESSION['form_storecode'])) $form_storecode = $_SESSION['form_storecode'];
?>
<!--<link rel="stylesheet" href="js/chosen/chosen.css" />
<link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
 <section id="page-content-wrapper" class="main-content">
                <div class="main-content-right-side sing-up">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="sign-up-listing-area">
                                    <h1>INTOUCH PARTNER NETWORK</h1>
                                    <p>Keeping Enterprises Connected</p>
                                    <ul class="sign-up-listing">
                                        <li class="supply-tracking">
                                            <a href="#">Supply <br>Tracking</a>
                                        </li>
                                        <li class="data-management">
                                            <a href="#">Channel Data <br>Management</a>
                                        </li>
                                        <li class="retail-solution">
                                            <a href="#">Retail Mall <br>Solutions</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="sing-up-block">
                                    <h2>Login </h2>
                                    <form method="post" name="storeloginform" action="postLogin.php">
                                        <div class="form-group">
                                            <input type="username" class="form-control" id="email" placeholder="Username" name="storecode" value="<?php echo $form_storecode; ?>" />
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                                        </div>
                                        <button type="submit" class="btn btn-default">Login </button> <!--<i class="fa fa-long-arrow-right" aria-hidden="true"></i>-->
                                        <span class="error" id="slf_status" style="display:<?php echo $disp; ?>;"><?php echo $form_errors ?></span>    
                                        <?php unset($_SESSION['form_errors']); ?>
                                        <!--<p>Already have an account? Login <a href="#">here</a></p>-->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </section>
<!--<script src="js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true});</script>        -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           