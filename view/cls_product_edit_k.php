<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";

class cls_product_edit extends cls_renderer {
    var $params;
    var $currStore;
    var $productid="";
    
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
//        $this->params = $params;
        if($params && isset($params['pid'])){
                 $this->productid = $params['pid']; 
//                 print $this->productid;
             }
    }

    function extraHeaders() {
    ?>
    <script type="text/javascript">
    
</script>  
<!--<link rel="stylesheet" href="js/chosen/chosen.css" />-->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<?php
    } // extraHeaders

    public function pageContent() {
        $formResult = $this->getFormResult();
        $menuitem="product";
        include "sidemenu.php";
        $db = new DBConn();
        //$query = "select p.id as pid, p.name as name, p.current_rate as rate, p.is_active as isactive, u.id as uomid, u.name as uom, c.id as catid, c.name as category, ps.id as pckszid, ps.pack_size as packsize from it_products p, it_uom u, it_category c, it_pack_size ps "
         //       . "where p.id=$this->productid and p.uom_id = u.id and p.pack_size_id = ps.id and p.category_id = c.id";
        $query = "select p.id as pid, p.name as name,p.shopify_name,p.product_handle, p.current_rate as rate, p.product_ref_id as prodrefid, p.variants_id as variantid ,p.is_active as isactive, u.id as uomid, u.name as uom, c.id as catid, c.name as category, ps.id as pckszid, ps.pack_size as packsize,purchase_uom as purchase_uom_id from it_products p, it_uom u, it_category c, it_pack_size ps "
               . "where p.id=$this->productid and p.uom_id = u.id and p.pack_size_id = ps.id and p.category_id = c.id";
        //echo $query;
        $pobj = $db->fetchObject($query);
        if($pobj){
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Edit Products</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form class="form-horizontal" id="editproductform" name="editproductform" enctype="multipart/form-data" method="post" action="formpost/editProduct.php">
                                    <!--<input type="hidden" name="form_id" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="editproductform">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label id="prate" class="col-md-3 control-label">Shopify Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="psname" name="psname"  value = "<?php echo $pobj->shopify_name ;?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="pname" class="col-md-3 control-label">Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="prodname" name="prodname" value = "<?php echo $pobj->name ;?>">
                                                <input type="hidden" name="prod_id" value="<?php echo $pobj->pid ;?>"/> 
                                                <input type="hidden" name="prod_ref_id" value="<?php echo $pobj->prodrefid ;?>"/>
                                                <input type="hidden" name="variant_id" value="<?php echo $pobj->variantid ;?>"/> 
                                                <input type="hidden" name="shopifyname" value="<?php echo $pobj->shopify_name ;?>"/> 
                                                <input type="hidden" name="producthandle" value="<?php echo $pobj->product_handle ;?>"/> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="prate" class="col-md-3 control-label">Rate</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="prodrate" name="prodrate"  value = "<?php echo $pobj->rate ;?>">
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <label id="puom" class="col-md-3 control-label">UOM</label>
                                            <div class="col-md-9">
<!--                                                <select class="form-control" id="uomsel" name="uomsel">
                                                    <option value="">Select UOM</option>
                                                    <option value="<?php // echo $pobj->uomid;?>"> <?php // echo $pobj->uom;?></option>
                                                    <?php
//                                                    $query = "select id,name from it_uom";
////                                                    echo $query;
//                                                    $uobjs = $db->fetchObjectArray($query);
//                                                    if (isset($uobjs)) {
//                                                        foreach ($uobjs as $uobj) {
                                                            ?>
                                                            <option value="<?php //echo $uobj->id; ?>"><?php //echo $uobj->name; ?></option>
                                                            <option value="<?php // echo $uobj->id.":".$uobj->name; ?>"><?php // echo $uobj->name; ?></option>
                                                        <?php
//                                                        }
//                                                    }
                                                    ?>
                                                </select>   -->
                                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="uomsel" name="uomsel">
                                                   <!--<option value="">Select UOM</option>-->
                                                   <!--<option value="<?php //echo $pobj->uomid;?>"> <?php // echo $pobj->uom;?></option>-->
                                                   <?php
                                                   $query = "select id,name from it_uom";
//                                                    echo $query;
                                                   $uobjs = $db->fetchObjectArray($query);
                                                   if (isset($uobjs)) {
                                                       foreach ($uobjs as $uobj) {
                                                           $selected = "";
                                                           if($pobj->uomid == $uobj->id){
                                                              $selected = "selected";
                                                           }
                                                           ?>
                                                           <!--<option value="<?php //echo $uobj->id; ?>"><?php //echo $uobj->name; ?></option>-->
                                                           <option value="<?php echo $uobj->id.":".$uobj->name; ?>" <?php echo $selected; ?>><?php echo $uobj->name; ?></option>
                                                       <?php
                                                       }
                                                   }
                                                   ?>
                                               </select>   
                                                
                                            </div>
                                        </div>
                                       
                                        <div class="form-group" >
                                            <label id="puom" class="col-md-3 control-label" >Category</label>
                                            <div class="col-md-9" >
<!--                                            <select class="form-control" id="catsel" name="catsel">
                                                    <option value="">Select Category</option>
                                                    <option value="<?php // echo $pobj->catid;?>"> <?php // echo $pobj->category;?></option>
                                                    <?php
//                                                    $query = "select id,name from it_category where is_active = 1";
////                                                    echo $query;
//                                                    $cobjs = $db->fetchObjectArray($query);
//                                                    if (isset($cobjs)) {
//                                                        foreach ($cobjs as $cobj) {
                                                            ?>
                                                            <option value="<?php //cho $cobj->id; ?>"><?php //echo $cobj->name; ?></option>
                                                            <option value="<?php // echo $cobj->id.":".$cobj->name; ?>"><?php // echo $cobj->name; ?></option>
                                                        <?php
//                                                        }
//                                                    }
                                                    ?>
                                            </select> -->
                                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="catsel" name="catsel">
                                                   <!--<option value="">Select Category</option>-->
                                                   <!--<option value="<?php // echo $pobj->catid;?>"> <?php // echo $pobj->category;?></option>-->
                                                   <?php
                                                   $query = "select id,name from it_category where is_active = 1";
//                                                    echo $query;
                                                   $cobjs = $db->fetchObjectArray($query);
                                                   if (isset($cobjs)) {
                                                       foreach ($cobjs as $cobj) {
                                                           $selected = "";
                                                           if($pobj->catid == $cobj->id){
                                                              $selected = "selected";
                                                           }
                                                           ?>
                                                           <!--<option value="<?php //cho $cobj->id; ?>"><?php //echo $cobj->name; ?></option>-->
                                                           <option value="<?php echo $cobj->id.":".$cobj->name; ?>" <?php echo $selected; ?>><?php echo $cobj->name; ?></option>
                                                       <?php
                                                       }
                                                   }
                                                   ?>
                                               </select> 
                                            </div>
                                        </div>   
                                        <div class="form-group">
                                            <label id="puom" class="col-md-3 control-label">Pack Size</label>
                                            <div class="col-md-9">
<!--                                                <select class="form-control" id="pckszsel" name="pckszsel">
                                                    <option value="<?php // echo $pobj->pckszid;?>"> <?php // echo $pobj->packsize;?></option>
                                                    <?php
//                                                    $query = "select id,pack_size from it_pack_size";
////                                                    echo $query;
//                                                    $psobjs = $db->fetchObjectArray($query);
//                                                    if (isset($psobjs)) {
//                                                        foreach ($psobjs as $psobj) {
                                                            ?>
                                                            <option value="<?php //echo $psobj->id; ?>"><?php //echo $psobj->pack_size; ?></option>
                                                            <option value="<?php // echo $psobj->id.":".$psobj->pack_size; ?>"><?php // echo $psobj->pack_size; ?></option>
                                                        <?php
//                                                        }
//                                                    }
                                                    ?>
                                                </select>-->
                                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="pckszsel" name="pckszsel">
                                                   <!--<option value="<?php // echo $pobj->pckszid;?>"> <?php // echo $pobj->packsize;?></option>-->
                                                   <?php
                                                   $query = "select id,pack_size from it_pack_size";
//                                                    echo $query;
                                                   $psobjs = $db->fetchObjectArray($query);
                                                   if (isset($psobjs)) {
                                                       foreach ($psobjs as $psobj) {
                                                           $selected="";
                                                           if($pobj->pckszid == $psobj->id){
                                                               $selected= "selected";
                                                           }
                                                           ?>
                                                           <!--<option value="<?php //echo $psobj->id; ?>"><?php //echo $psobj->pack_size; ?></option>-->
                                                           <option value="<?php echo $psobj->id.":".$psobj->pack_size; ?>"><?php echo $psobj->pack_size; ?></option>
                                                       <?php
                                                       }
                                                   }
                                                   ?>
                                               </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="pruom" class="col-md-3 control-label">Purchasing UOM</label>
                                            <div class="col-md-9">
<!--                                                <select class="form-control" id="pckszsel" name="pckszsel">
                                                    <option value="<?php // echo $pobj->pckszid;?>"> <?php // echo $pobj->packsize;?></option>
                                                    <?php
//                                                    $query = "select id,pack_size from it_pack_size";
////                                                    echo $query;
//                                                    $psobjs = $db->fetchObjectArray($query);
//                                                    if (isset($psobjs)) {
//                                                        foreach ($psobjs as $psobj) {
                                                            ?>
                                                            <option value="<?php //echo $psobj->id; ?>"><?php //echo $psobj->pack_size; ?></option>
                                                            <option value="<?php // echo $psobj->id.":".$psobj->pack_size; ?>"><?php // echo $psobj->pack_size; ?></option>
                                                        <?php
//                                                        }
//                                                    }
                                                    ?>
                                                </select>-->
                                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" id="pruomsel" name="pruomsel">
                                                   <!--<option value="<?php // echo $pobj->pckszid;?>"> <?php // echo $pobj->packsize;?></option>-->
                                                   <?php
                                                   $query = "select id,name from it_uom";
//                                                    echo $query;
                                                   $psobjs = $db->fetchObjectArray($query);
                                                   if (isset($psobjs)) {
                                                       foreach ($psobjs as $psobj) {
                                                           $selected="";
                                                           if($pobj->purchase_uom_id == $psobj->id){
                                                               $selected= "selected";
                                                           }
                                                           ?>
                                                           <!--<option value="<?php //echo $psobj->id; ?>"><?php //echo $psobj->pack_size; ?></option>-->
                                                           <option value="<?php echo $psobj->id; ?>" <?php echo $selected; ?>><?php echo $psobj->name; ?></option>
                                                       <?php
                                                       }
                                                   }
                                                   ?>
                                               </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                           <label id="pstatus" class="col-md-3 control-label" >Status</label>
                                           <div class="col-md-9" >
                                               <?php
//                                                 if($pobj->isactive == 1){
//                                                     $str =
//                                                 }
                                               ?>
                                               <input type="radio" class="radio-inline" value="1" id="actvsel" name="actvsel" <?php if($pobj->isactive == 1){ echo "checked"; } ?> > Active
                                                   
                                                   <input type="radio" class="radio-inline" value="0" id="actvsel" name="actvsel" <?php if($pobj->isactive == 0){ echo "checked"; } ?> > In active
                                               <?php //}
                                               //else{ ?>
<!--                                                   <input type="radio" class="radio-inline" value="1" id="actvsel" name="actvsel"> Active
                                                   
                                                   <input type="radio" class="radio-inline" value="0" id="actvsel" name="actvsel" checked> In active-->
                                               <?php //} ?>  
                                           </div>
                                       </div>
                                        
<!--                                        <div class="form-group">
                                            <label id="pstatus" class="col-sm-5 control-label" style="margin-top: 5px;">Status</label>
                                            <div class="col-sm-7" style=" margin-top: 5px; ">
                                                <select  class="form-control" id="actvsel" name="actvsel">
                                                    <?php 
                                                     //if($pobj->isactive == 1){ ?>
                                                        <option value="1"><?php //echo "Active"; ?></option> 
                                                        <option value="0"><?php //echo "Inactive"; ?></option>
                                                    <?php //}else{ ?>
                                                       <option value="0"><?php //echo "Inactive"; ?></option> 
                                                       <option value="1"><?php //echo "Active"; ?></option>
                                                    <?php //} ?>   
                                                </select>
                                            </div>
                                        </div>-->
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <!--<button type="submit" class="btn btn-primary">Submit</button>-->
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                    </div>                   
                                </form>
                                <?php if ($formResult->form_id == 'editproductform') { ?>
                                <div class="alert alert-<?php echo $formResult->cssClass;?> alert-dismissible" style="display:<?php echo $formResult->showhide; ?>;">
                                    <button class="close" type="button" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4> <?php echo $formResult->status; ?>
                                    <?php   
                                    if (isset($formResult->cssClass) && $formResult->cssClass == 'success' && !isset($_SESSION['creditnoteupload'])) {                                                        
                                                    ?>
                                        
                                    <?php } ?>   
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        </div>
                    </div>   
                </div>
            </div>
        </div>   
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>           
    <?php
        }
    } //pageContent
}//class
?>
