<?php

define("BILL_NUMBER", "billnumber");
define("BILL_DATE", "billdate");
define("BILL_TIME", "billtime");
define("BILL_DATETIME", "billdatetime");
define("BILL_AMOUNT", "billamount");
define("BILL_QUANTITY", "billquantity");
define("BILL_DISCOUNT_VAL", "discountval");
define("BILL_DISCOUNT_PCT", "discountpct");

class ReceiptFormat {

	var $patterns;
	var $parsedValues;

	public function __construct($billPatterns) {
		$this->patterns = $billPatterns;
		$this->initValues();
	}

	private function initValues() {
		$this->parsedValues = array(
			BILL_NUMBER => null,
			BILL_DATE => null,
			BILL_TIME => null,
			BILL_DATETIME => null,
			BILL_AMOUNT => null,
			BILL_QUANTITY => null,
			BILL_DISCOUNT_VAL => null,
			BILL_DISCOUNT_PCT => null
		);
	}

	public function parseOrder($orderinfo, $createtime) {
		$orderinfo = preg_replace('/[\n\r]/',' ',$orderinfo);
		$this->initValues();
		foreach ($this->patterns as $pattern) {
			if (isset($this->parsedValues[$pattern->fieldname])) { continue; }
			$matches=array();
			preg_match($pattern->regex, $orderinfo, $matches);
			if (isset($matches['value'])) {
				$this->parsedValues[$pattern->fieldname] = $this->parseValue($matches['value'], $pattern->fieldtype, $pattern->fieldformat);
			}
		}
		// combine date & time if present
		if (isset($this->parsedValues[BILL_DATE]) && isset($this->parsedValues[BILL_TIME])) {
			$this->parsedValues[BILL_DATETIME] = $this->parsedValues[BILL_DATE]." ".$this->parsedValues[BILL_TIME];
		} else if (isset($this->parsedValues[BILL_DATE])) {
			$this->parsedValues[BILL_DATETIME] = $this->parsedValues[BILL_DATE]." 00:00:00";
		} else { // default to order row insert time
			$this->parsedValues[BILL_DATETIME] = $createtime;
		}
		return $this->parsedValues;
	}

	private function parseValue($strvalue, $fieldtype, $fieldformat) {
		switch ($fieldtype) {
			case 1: // int
				return intval(str_replace(",","",$strvalue));
			case 2: // double
				return doubleval(str_replace(",","",$strvalue));
			case 3: // date
				$dformat = str_replace("/", '\/', $fieldformat);
				$dformat = str_replace("d", '(?P<DAY>\d+)', $dformat);
				$dformat = str_replace("m", '(?P<MONTH>\d+)', $dformat);
				$dformat = str_replace("y", '(?P<YEAR>\d+)', $dformat);
				$dformat = '/'.$dformat.'/';
				preg_match($dformat,$strvalue,$dmatches);
				return $dmatches['YEAR'].'-'.$dmatches['MONTH'].'-'.$dmatches['DAY'];
			case 4: // time
				$tformat = str_replace("/", '\/', $fieldformat);
				$tformat = str_replace("h", '(?P<HOURS>\d+)', $tformat);
				$tformat = str_replace("m", '(?P<MINUTES>\d+)', $tformat);
				$tformat = str_replace("s", '(?P<SECONDS>\d+)', $tformat);
				$tformat = '/'.$tformat.'/';
				preg_match($tformat,$strvalue,$tmatches);
				if (!isset($tmatches['HOURS'])) { $tmatches['HOURS'] = '00'; }
				if (!isset($tmatches['MINUTES'])) { $tmatches['MINUTES'] = '00'; }
				if (!isset($tmatches['SECONDS'])) { $tmatches['SECONDS'] = '00'; }
				return $tmatches['HOURS'].':'.$tmatches['MINUTES'].':'.$tmatches['SECONDS'];
			case 5: // string
				return $strvalue;
			default:
				return $strvalue;
		}
	}
}

?>
