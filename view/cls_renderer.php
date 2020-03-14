<?php
abstract class cls_renderer {
var $currStore;

        function __construct($allowedUserTypes) {
		$this->currStore = getCurrStore();
//		if ($this->currStore && !in_array($this->currStore->usertype, $allowedUserTypes)) {
                if (! $this->currStore) {
			header("Location: ".DEF_SITEURL."unauthorized");
		}
	}
        
	function baseUrl() {
		return DEF_SITEURL;
	}

	function pageTitle() {
		return DEF_PAGE_TITLE;
	}

	function pageKeywords() {
		return DEF_PAGE_KEYWORDS;
	}

	function pageDescription() {
		return DEF_PAGE_DESCRIPTION;
	}

	function extraHeaders() {
		return "";
	}

	function getSessionVal($var,$retain_value=false) {
		$val=null;
		if (isset($_SESSION[$var])) {
			$val = $_SESSION[$var];
		}
		if (!$retain_value) {
			unset($_SESSION[$var]);
		}
		return $val;
	}

	function getFormResult() {
		$result=array();
		$form_id = $this->getSessionVal('form_id');
		$form_errors = $this->getSessionVal('form_errors');
		$form_success = $this->getSessionVal('form_success');
		$result['form_id'] = $form_id;
		if ($form_errors && count($form_errors) > 0) {
			$result['status']=implode("<br />", $form_errors);
			$result['showhide']="block";
			$result['cssClass']="danger";
		} else if ($form_success != null) {
			$success_disp="block";
			$result['status']=$form_success;
			$result['showhide']="block";
			$result['cssClass']="success";
		} else {
			$result['status']="";
			$result['showhide']="none";
		}
		return (object)$result;
	}

	function successResult($msg) {
		$result = array();
		$result['status']=$msg;
		$result['showhide']="block";
		$result['cssClass']="success";
		return (object)$result;
	}

	function errorResult($msg) {
		$result = array();
		$result['status']=$msg;
		$result['showhide']="block";
		$result['cssClass']="error";
		return (object)$result;
	}

        function getFieldValue($fieldname,$default=false) {
		$val = "";
		if (isset($_SESSION['form_post'])) {
			$post = $_SESSION['form_post'];
			if (isset($post[$fieldname])) { $val = $post[$fieldname]; }
		}
		if ($default && $val == "") { return $default; }
		return $val;
	}
}
