<?php
require_once "view/cls_renderer.php";
require_once "lib/core/Constants.php";
require_once "session_check.php";
require_once 'lib/db/DBLogic.php';

class cls_bin_edit extends cls_renderer {
    var $params;
    var $currStore;
    function __construct($params=null) {
        // $this->currStore = getCurrUser();
	//parent::__construct(array(UserType::Admin, UserType::CKAdmin)); 
     parent::__construct(array());
        $this->currStore = getCurrStore();       
//        $this->params = $params;
        if($params && isset($params['bid'])){
                 $this->binid = $params['bid']; 
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
        $menuitem="bins";
        include "sidemenu.php";
        $dbl = new DBLogic();
//        $query = "select * from it_bins where id= $this->binid ";
//        $query = "select b.*, l.name as locname from it_bins b, it_locations l where b.id= $this->binid and l.id= b.location_id";
//        $bobj = $db->fetchObject($query);
        $bobj = $dbl->getBinById($this->binid);
        if($bobj){
        ?>
        <div class="container-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                        <h2 class="title-bar">Edit Bin</h2>
                        <div class="common-content-block"> 
                            <div class="box box-primary">                                            
                                <form class="form-horizontal" id="editbinform" name="editbinform" enctype="multipart/form-data" method="post" action="formpost/editBin.php">
                                    <input type = "hidden" name="form_id" id="form_id" value="editbinform">
                                    <div class="box-body">
                                       <div class="form-group">
                                            <label id=name" class="col-md-3 control-label" >Location</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="locname" name="locname" value = "<?php echo $bobj->locname ;?>" disabled="">
                                                <input type="hidden" name="loc_id" value="<?php echo $bobj->location_id ;?>"/> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id=name" class="col-md-3 control-label" >Name</label>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control" id="binname" name="binname"  value = "<?php echo $bobj->bin ;?>">
                                                <input type="hidden" name="bin_id" value="<?php echo $bobj->id ;?>"/> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label id="pstatus" class="col-md-3 control-label">Status</label>
                                            <div class="col-md-9" >
                                                <input type="radio" class="radio-inline" value="1" id="actvsel" name="actvsel" <?php if($bobj->is_active == 1){ echo "checked"; } ?> > Active
                                                <input type="radio" class="radio-inline" value="0" id="actvsel" name="actvsel" <?php if($bobj->is_active == 0){ echo "checked"; } ?> > In active
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <input type="submit" class="btn btn-primary" value="Submit">
                                    </div>                   
                                </form>
                                <?php if ($formResult->form_id == 'editbinform') { ?>
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
    <?php
        }
    } //pageContent
}//class
?>
