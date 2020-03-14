<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/strutil.php";
require_once "lib/supplier/clsSupplier.php";
require_once "lib/db/DBLogic.php";

class cls_purchase_in_entry_edit extends cls_renderer {
    var $params;
    var $currStore;
    var $pid="";
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
//        $this->params = $params;
        if($params && isset($params['pid'])){
                 $this->pid = $params['pid']; 
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
        $menuitem="purin";
        include "sidemenu.php";
        $dbLogic = new DBLogic();
        $clsSupplier = new clsSupplier();            
        
        $prinobj = $dbLogic->fetchPurInEnById($this->pid);
        //print_r($prinobj);
        
        if($prinobj){
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Edit Purchase In Entry</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form class="form-horizontal" id="editpurinenform" name="editpurinenform"  method="post" action="formpost/editPurchaseInEntry.php">
                                    <!--<input type="hidden" name="form_id" value="1"/>-->
                                    <input type = "hidden" name="form_id" id="form_id" value="editpurinenform">
                                    <div class="box-body">
                                        <div class="form-group">                                            
                                            <label id="pname" class="col-md-3 control-label">Product Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="lname" name="lname" value = "<?php echo $prinobj->product ;?>" readonly>
                                                <input type="hidden" name="prenid" id="prenid" value="<?php echo $prinobj->id ;?>"/>                                                
                                                <input type="hidden" name="pur_in_id" id="pur_in_id" value="<?php echo $prinobj->pur_in_id ;?>"/>                                                 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="prate" class="col-md-3 control-label">Supplier</label>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <select class="form-control chzn-select" id="selsupp" name="selsupp" style="width:75%;" >                                                    
                                                        <option value="" disabled selected>Select Supplier</option>
                                                        <?php
                                                        $sobjs = $clsSupplier->getAllActiveSuppliers();
                                                        if(!empty($sobjs)){
                                                           foreach($sobjs as $sobj){
                                                              if(isset($sobj) && !empty($sobj) && $sobj != null){
                                                                  $selected = "";
                                                                  if($sobj->id == $prinobj->supplier_id ){
                                                                      $selected = "selected";
                                                                  }
                                                            ?>
                                                        <option value="<?php echo $sobj->id; ?>" <?php echo $selected; ?>><?php echo $sobj->name; ?></option>
                                                            <?php      
                                                              } 
                                                           }
                                                        }
                                                        ?>                                                            
                                                    </select> 
                                                </div> 
                                            </div>
                                                                                        
                                        </div>
                                        <div class="form-group">
                                            <label id="puom" class="col-md-3 control-label">Quantity</label>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <input type="text" id="qty" name="qty" placeholder="Enter Quantity" value="<?php echo $prinobj->qty; ?>" style="width:75%;">
                                                </div> 
                                            </div>                                                                                                                                                                                                                                                                        
                                        </div>
                                        <div class="form-group">
                                            <label id="prate" class="col-md-3 control-label">Select UOM &nbsp;&nbsp;</label>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                   <select class="form-control chzn-select" id="seluom" name="seluom" style="width:75%;" >                                                    
                                                    <option value="" disabled selected>Select UOM</option>
                                                    <?php
                                                    $uobjs = $dbLogic->getAllUOM();
                                                    if(!empty($uobjs)){
                                                       foreach($uobjs as $uobj){
                                                          if(isset($uobj) && !empty($uobj) && $uobj != null){
                                                              $selected = "";
                                                                  if($uobj->id == $prinobj->uom_id ){
                                                                      $selected = "selected";
                                                                  }
                                                        ?>
                                                    <option value="<?php echo $uobj->id; ?>" <?php echo $selected; ?>><?php echo $uobj->name; ?></option>
                                                        <?php      
                                                          } 
                                                       }
                                                    }
                                                    ?>                                                            
                                                </select> 
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="form-group" >
                                            <label id="puom" class="col-md-3 control-label">Rate</label>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <input type="text" id="rate" name="rate" placeholder="Enter Amount" value="<?php echo $prinobj->rate; ?>" style="width:75%;">
                                                </div> 
                                            </div>
                                        </div>                                       
                                       
                                                                           
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <!--<button type="submit" class="btn btn-primary">Submit</button>-->
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                    </div>                   
                                </form>
                                <?php if ($formResult->form_id == 'editpurinenform') { ?>
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
