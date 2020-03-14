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
                                            <input type="text" class="form-control" id="name" placeholder="Name">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" id="email" placeholder="Email">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" id="password" placeholder="Set a Password">
                                        </div>
                                        <button type="submit" class="btn btn-default">Continue <i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>

                                        <p>Already have an account? Login <a href="#">here</a></p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </section>