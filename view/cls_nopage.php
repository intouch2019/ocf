<?php
require_once "view/cls_renderer.php";

class cls_nopage extends cls_renderer {

	function __construct($params=null) {
	}

	public function pageContent() {
?>
		<div class="container-section">
                    <div class="row">
                        <div class="col-md-12">
                            <br>
			  <div class="common-content-block">The page you requested was not found or your session has timed out.<br>Please go back to the <a href="<?php echo DEF_SITEURL; ?>">Login page</a></div>
                        </div>
                    </div>    
		</div>
<?php
	}

}
?>
