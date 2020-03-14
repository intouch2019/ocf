<?php
require_once "view/cls_renderer.php";
require_once ("lib/db/DBConn.php");
require_once ("lib/db/DBLogic.php");
require_once ("lib/core/Constants.php");
require_once "lib/core/strutil.php";
require_once "session_check.php";

class cls_user_create extends cls_renderer{

        var $currStore;
        var $userid;
        var $params;
       
        function __construct($params=null) {
// parent::__construct(array(UserType::Admin,UserType::WKAdmin,UserType::CRM_Manager,UserType::City_Head));
            $this->currStore = getCurrStore();
            $this->params = $params;
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
/*            option{
                font-weight:bold; 
            }
            div.alert *
            {
                color: red;
            }*/
        </style>
        <script type="text/javaScript">   
            
             $(function () {
              $("#crlist").hide();
              
        });
            
    
 function chkutype(usertype_id) {
     
                if(usertype_id == <?php echo UserType::RFC; ?> ){
                    $("#crlist").show();
                    $("#crlist").focus();
                } else {
                    $("#crlist").hide();
                } 
        }
    
    
        </script>
        <!--        <link rel="stylesheet" href="css/bigbox.css" type="text/css" />-->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />      
        <?php
        }

        public function pageContent() {
            $menuitem = "users";//pagecode
            include "sidemenu.php";
            $formResult = $this->getFormResult();
            $dbl = new DBLogic();
?>
 <div class="container-section">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Create User</h2>
                        <div class="common-content-block">
                            <form  role="form" id="createuser" name="createuser" enctype="multipart/form-data" method="post" action="formpost/create_user.php">
                             <div class="box box-primary"><br>
                                 <div class="col-md-12">
                                     <div class="col-md-12">
                                        <select id="utypesel" name="utypesel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="chkutype(this.value);">
                                            <option value="" disabled="" selected="">Select User Type</option>
                                            <?php
                                            $allUserTypes = UserType::getAll();
                                            $display = "block";
                                            foreach ($allUserTypes as $usertype => $typename) { ?>
                                                    <option value="<?php echo $usertype; ?>" <?php echo $selected; ?>><?php echo $typename; ?></option>                                                                
                                            <?php } ?>
                                        </select>
                                     </div>
                                 </div> 
                                 <div class="col-md-12" id="crlist">
                                     <br>
                                     <div class="col-md-12">
                                        <select id="crsel" name="crsel" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" onchange="">
                                            <option value="" disabled="" selected="">Select CR</option>
                                            <?php
                                            $crObjs = $dbl->getCRList();
                                            $display = "block";
                                            foreach ($crObjs as $crObj) { ?>
                                                    <option value="<?php echo $crObj->id; ?>" <?php echo $selected; ?>><?php echo $crObj->dispname; ?></option>                                                                
                                            <?php } ?>
                                        </select>
                                     </div>
                                 </div>
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-6">
                                         <input type="text" id="name" name="name" class="form-control" placeholder="Name" value="<?php echo $this->getFieldValue("name"); ?>" required>
                                     </div>
                                     <div class="col-md-6">
                                         <input type="text" id="username" name="username" class="form-control" placeholder="User Name" value="<?php echo $this->getFieldValue("username"); ?>" required>
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-6">
                                         <input type="text" id="email" name="email" class="form-control" placeholder="Email Id" value="<?php echo $this->getFieldValue("email"); ?>" required>
                                     </div>
                                     <div class="col-md-6">
                                         <input type="password" id="password" name="password" class="form-control" placeholder="Password" value="<?php echo $this->getFieldValue("password"); ?>" required>
                                     </div>
                                 </div> 
                                 <div class="col-md-12">
                                    <br>                                     
                                     <div class="col-md-6">
                                         <input type="number" id="phoneno" name="phoneno" class="form-control" placeholder="Phone No." value="<?php echo $this->getFieldValue("phoneno"); ?>" required>
                                     </div>
                                     <div class="col-md-6">
                                         <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" placeholder="Confirm Password" value="<?php echo $this->getFieldValue("confirmpassword"); ?>" required>
                                     </div>
                                 </div> 
                                 
                                 <div class="col-md-12">
                                    <br>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Create User</button>
                                    </div>
                                 </div>
                                
                             </div>   
                            </form>

                        </div>
                    </div>
                </div>
            </div>
                <div class="col-md-6">
                                                <?php if ($formResult->form_id == 'createUserErrors') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                </div>
                                <?php } ?>
                </div>
        </div> 
 </div>
<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           -->

            <?php // }else{ print "You are not authorized to access this page";}
	}
}
?>


