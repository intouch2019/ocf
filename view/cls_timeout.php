<?php
require_once "view/cls_renderer.php";

class cls_timeout extends cls_renderer {

	function __construct($params=null) {
	}

	public function pageContent() {
?>
<!--		<div class="grid_9">
			<div class="error" style="font-size:1.5em;">Your session has timed out. Please <a href="<?php // echo DEF_SITEURL; ?>home/login">Login</a> again.</div>
		</div>-->
                <div class="container-section">
                    <div class="row">
                        <div class="col-md-12">
                            <br>
			  <div class="common-content-block">Your session has timed out. Please <a href="<?php echo DEF_SITEURL; ?>">Login</a> again.</div>
                        </div>
                    </div>    
		</div>
<?php
	}

}
?>
