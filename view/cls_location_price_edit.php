<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/strutil.php";

class cls_location_price_edit extends cls_renderer {
    var $params;
    var $currStore;
    var $lprid="";
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
//        $this->params = $params;
        if($params && isset($params['lprid'])){
                 $this->lprid = $params['lprid']; 
//                 print $this->productid;
             }
    }

    function extraHeaders() {
    ?>
    <script type="text/javascript">
    
</script>  
<?php
    } // extraHeaders

    public function pageContent() {
        $formResult = $this->getFormResult();
        $menuitem="locpr";
        include "sidemenu.php";
        $dbLogic = new DBLogic();
        
        $lprobj = $dbLogic->fetchLocationTypePriceById($this->lprid);
        
        if($lprobj){
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Edit Products</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form class="form-horizontal" id="editlocprform" name="editlocprform"  method="post" action="formpost/editLocationPrice.php">
                                    <!--<input type="hidden" name="form_id" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="editlocprform">
                                    <div class="box-body">
                                        <div class="form-group">                                            
                                            <label id="pname" class="col-md-3 control-label">Location Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="lname" name="lname" value = "<?php echo $lprobj->location_type ;?>" readonly>
                                                <input type="hidden" name="lprid" id="lprid" value="<?php echo $lprobj->id ;?>"/>                                                
                                                <input type="hidden" name="location_id" id="location_id" value="<?php echo $lprobj->location_id ;?>"/>  
                                                <input type="hidden" name="product_id" id="product_id" value="<?php echo $lprobj->product_id ;?>"/>  
                                                <input type="hidden" name="location_type_id" id="location_type_id" value="<?php echo $lprobj->location_type_id ;?>"/>  
                                                <input type="hidden" name="product_ref_id" id="product_ref_id" value="<?php echo $lprobj->product_ref_id ;?>"/>  
                                                <input type="hidden" name="variants_id" id="variants_id" value="<?php echo $lprobj->variants_id ;?>"/>  
                                                
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="prate" class="col-md-3 control-label">Shopify Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="sname" name="sname"  value = "<?php echo $lprobj->shopify_name ;?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="prate" class="col-md-3 control-label">Product Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="pname" name="pname"  value = "<?php echo $lprobj->product ;?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="prate" class="col-md-3 control-label">Applicable Date&nbsp;&nbsp;</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="appldt" name="appldt"  value = "<?php echo ddmmyy($lprobj->applicable_date) ;?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <label id="puom" class="col-md-3 control-label">Rate</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="price" name="price"  value = "<?php echo $lprobj->price ;?>">                                                
                                            </div>
                                        </div>                                       
                                       
                                        <div class="form-group">
                                           <label id="pstatus" class="col-md-3 control-label" >Status</label>
                                           <div class="col-md-9" >                                              
                                               <input type="radio" class="radio-inline" value="1" id="actvsel" name="actvsel" <?php if($lprobj->is_active == 1){ echo "checked"; } ?> > Active                                                   
                                               <input type="radio" class="radio-inline" value="0" id="actvsel" name="actvsel" <?php if($lprobj->is_active == 0){ echo "checked"; } ?> > In active                                               
                                           </div>
                                       </div>                                        
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <!--<button type="submit" class="btn btn-primary">Submit</button>-->
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                    </div>                   
                                </form>
                                <?php if ($formResult->form_id == 'editlocprform') { ?>
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
    <?php
        }
    } //pageContent
}//class
?>
