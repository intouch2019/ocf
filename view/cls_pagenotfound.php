<?php
require_once "view/cls_renderer.php";

class cls_pagenotfound extends cls_renderer {

	function __construct($params=null) {
	}

	public function pageContent() {		
?>
<!--		<div class="grid_9">
			<div class="error" style="font-size:1.5em;">Page Not Found. Please <a href="<?php //echo DEF_SITEURL; ?>home/login">TRY AGAIN</a> later. Thank you for your patience.</div>
		</div>-->
                <div class="container-section">
                    <div class="row">
                        <div class="col-md-12">
                            <br>
			  <div class="common-content-block">Page Not Found. Please <a href="<?php echo DEF_SITEURL; ?>">TRY AGAIN</a> later. Thank you for your patience.</div>
                        </div>
                    </div>    
		</div>
<?php
	}

}
