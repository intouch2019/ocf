<?php

require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/logger/clsLogger.php";
require_once "Classes/html2pdf/html2pdf.class.php";
require_once "lib/core/strutil.php";
require_once "lib/email/EmailHelper.php";

set_time_limit(120);
extract($_POST);
//extract($_GET);
//print_r($_GET); 
//$user = getCurrUser();

$errors = array();
$success = array();

class showPDF {

    function addPageHeader($objpo) {
        $imgurl = "../images/rsz_newsarotam.jpg";
        return '<table align="center" width="100%" border="1" cellspacing="0" cellpadding="0">
                 <tr>   
                 <td><img src=' . $imgurl . ' width="10%"/></td>
                 </tr>
                 </table>
                 <p align="center"><b>Purchase Order</b></p>
                 
                    ';
    }
    
     function getIndianCurrency($number){
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? "And " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise ;
}
    
   function Currency($num){
        $nums = explode(".",$num);
        if(count($nums)>2){
            return "0";
        }else{
        if(count($nums)==1){
            $nums[1]="00";
        }
        $num = $nums[0];
        $explrestunits = "" ;
        if(strlen($num)>3){
            $lastthree = substr($num, strlen($num)-3, strlen($num));
            $restunits = substr($num, 0, strlen($num)-3); 
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++){

                if($i==0)
                {
                    $explrestunits .= (int)$expunit[$i].","; 
                }else{
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $thecash = $explrestunits.$lastthree;
        } else {
            $thecash = $num;
        }
        return $thecash.".".$nums[1]; 
        }
    } 

    function addSupplierInfo($objpo, $objuser,$grandToatl) {
        $suaddress = isset($objpo->address) ? $objpo->address : '';
        $district = isset($objpo->district) ? $objpo->district.'-' : '';
        $pincode = isset($objpo->pincode) ? $objpo->pincode.','  : '';
        $state = isset($objpo->state) ? $objpo->state.',' : '';
        $country = isset($objpo->country) ? $objpo->country.'.' : '';
        $gstin = isset($objpo->gst_no) ? $objpo->gst_no.'.' : '';
        $pan_no = isset($objpo->pan_no) ? $objpo->pan_no.'.' : '';
        $tillAddress = $district.$pincode.$state.$country;
        $arr = explode(".",$grandToatl);
        $conevrtno = $this->getIndianCurrency($arr[0]);
        $convertdec = "";
        if(isset($arr[1])){
            $convertdec = ' And '.$this->getIndianCurrency($arr[1]).' Paise';
        }
        return '<table style="width:100%;" cellspacing="0" cellpadding="0">
                     <tr>
                     <th style="align=center;width:30%;height:10px;font-size:14px;"> <b>Purchase Order No : ' . $objpo->pono . '</b></th>
                     <th style="align=center;width:70%;height:10px;font-size:14px;" colspan="2"> <b>Date : ' . ddmmyy($objpo->createtime) . '</b></th>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;"><b>Bill To :</b>
                     <br/><b>Sarotam Industrial Goods Retail Distribution Private Limited</b>
                     <br/> GSTIN : 27AAYCS5917P1Z5 <br/>CIN : U52609 PN2017 PTC170198
                     <br/> Corporate Office:<br/>401, East Court,<br/> Phoenix Market City,
                     <br/>207, Viman Nagar,<br/> Pune - 411 014,<br/> Maharashtra, India
                     <br/><br/><b>Our Contact Person: </b>
                     <br/>Rajeev Ranjan Prasad
                     <br/>Contact No - 9860198102
                     </td>
                     <td style="align=left;width:40%;height:10px;font-size:14px;">
                     <b>Delivery Address: </b>
                     '.$objpo->dc_master_address.'
                     </td>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">
                     To,<br/><b>' . $objpo->company_name . '</b><br/>GSTIN : '.$gstin.'<br/>PAN : '.$pan_no.'<br/>'
                     . $suaddress . '<br/>'.$tillAddress.'
                     
                     </td> 
                     </tr>
                     <tr>
                     <td style="align=left;width:100%;height:10px;font-size:14px;" colspan="3">'.$objpo->remark_note.'</td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Delivery Schedule
                     </td>
                     <td style="align=left;width:70%;height:10px;font-size:14px;" colspan="2">Please deliver material within '.$objpo->picking_days.' days.
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Delivery
                     </td>
                     <td style="align=left;width:70%;" colspan="2">'.$objpo->dtterm.'
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Payment Terms
                     </td>
                     <td style="align=left;width:70%;height:10px;font-size:14px;" colspan="2">'.$objpo->pmterm.'
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Transit Insurance
                     </td>
                     <td style="align=left;width:70%;height:10px;font-size:14px;" colspan="2">'.$objpo->titerm.'
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Amount in Words :
                     </td>
                     <td style="align=left;width:70%;height:10px;font-size:14px;" colspan="2">'.$conevrtno. $convertdec.' Only
                     </td>
                     </tr>
                     
                     </table>';

        //return $html;
    }

    function addremarks($objpo, $objuser,$grandToatl) {
        $suaddress = isset($objpo->address) ? ', ' . $objpo->address : '';
        $arr = explode(".",$grandToatl);
//        print_r($arr);

        return '<table style="width:100%;" cellspacing="0" cellpadding="0">
                    
                     <tr>
                     <td style="align=left;width:100%;height:10px;font-size:14px;white-space:pre-wrap;word-wrap:break-word" colspan="2">'.$objpo->remark_note.'</td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Delivery
                     </td>
                     <td style="align=left;width:70%;">'.$objpo->dtterm.'
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Payment Terms
                     </td>
                     <td style="align=left;width:70%;height:10px;font-size:14px;">'.$objpo->pmterm.'
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Transit Insurance
                     </td>
                     <td style="align=left;width:70%;height:10px;font-size:14px;">'.$objpo->titerm.'
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Header Notes to supplier :
                     </td>
                     <td style="align=left;width:70%;height:10px;font-size:14px;">'.$objpo->header_note.'
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:100%;height:10px;font-size:14px;" colspan="2">Delivery Of Items - '.$objpo->delivery_note.'
                     </td>
                     </tr>
                     <tr>
                     <td style="align=left;width:30%;height:10px;font-size:14px;">Amount in Words :
                     </td>
                     <td style="align=left;width:70%;height:10px;font-size:14px;">'.$this->convert_number_to_words($arr[0]).' And '.$this->convert_number_to_words($arr[1]).' Paise Only
                     </td>
                     </tr>
                     <tr><td style="align=left;width:100%;height:10px;font-size:14px;" colspan="2">&nbsp;</td></tr>
                     <tr>
                     <td style="align=left;width:100%;height:10px;font-size:14px;" colspan="2">Please always mention our Purchase Order No. in all your delivery challans,bills & other correspondence.
                     </td>
                     </tr>
                     <tr>
                     <th style="align=right;width:100%;height:10px;font-size:14px;" colspan="2"><br/><br/>Signature</th>                   
                     </tr>
                     </table><br/>';


        return $html;
    }

    function addCKTableHeader($totalLD) {
        //echo $totalLD;
        $htmlt = "";
        $htmlt.= '<tr>
                  <th style="align=left;font-size:12px;width:4%;" rowspan="2">Sl.No</th>
                  <th style="align=left;font-size:12px;width:6%;" rowspan="2">Category</th>
                  <th style="align=left;font-size:12px;width:20%;" rowspan="2">Product</th>
                  <th style="align=left;font-size:12px;width:5%;" rowspan="2">Spec</th>
                  
                  <th style="align=left;font-size:12px;width:5%;" rowspan="2">HSN Code</th>
                  <th style="align=left;font-size:12px;width:5%;"rowspan="2">Ordered Length (mm)</th>
                  <th style="align=left;font-size:12px;width:5%;" rowspan="2">UOM</th>
                  <th style="align=left;font-size:12px;width:5%;" rowspan="2">Qty(MT)</th>';
          if($totalLD > 0){        
         $htmlt.='<th style="align=left;font-size:12px;width:8%;" rowspan="2">Base Rate(Rs/MT)</th>
                  <th style="align=left;font-size:12px;width:8%;" rowspan="2">Loading Charges<br/>(Rs/MT)</th>';
          }else{
          $htmlt.='<th style="align=left;font-size:12px;width:8%;" rowspan="2">Base Rate<br/>(Rs/MT)</th>';    
          $htmlt.='<th style="align=left;font-size:12px;width:8%;" rowspan="2">Taxable Value</th>';    
          }
         
         $htmlt.='<th style="align=left;font-size:12px;width:7%;" colspan="2">CGST(9%)</th>
                  <th style="align=left;font-size:12px;width:7%;" colspan="2">SGST(9%)</th>
                  <th style="align=left;font-size:12px;width:7%;" rowspan="2">Rate(Rs/MT)</th>
                  <th style="align=left;font-size:12px;width:8%;" rowspan="2">Value(Rs)</th>
                  </tr>
         <tr>
                    <th style="align=center;font-size:10px;width:4%;">Rs/MT</th>
                    <th style="align=center;font-size:10px;width:3%;">Amt</th>
                    
                    <th style="align=center;font-size:10px;width:4%;">Rs/MT</th>
                    <th style="align=center;font-size:10px;width:3%;">Amt</th>
                </tr>';
        return $htmlt;
    }

    function addPageFooter($pageno) {
        return '<page_footer>
                    <hr><P align="center"><br/><b>Sarotam Industrial Goods Retail Distribution Private Limited</b>
                    <br/>CIN : U52609 PN2017 PTC170198
                    <br/>Registered Office:128, Shiv Hari Complex, Opposite Giga Space, 
                    <br/> 206, Viman Nagar, Pune - 411 014, Maharashtra, India</P>
                    <p align="center">Page ' . $pageno . '
                    </p>
                </page_footer>';
    }

    function breakText($text) {
        $arr_text = array();
        $textToSend = "";
        if (strlen($text) > 15) {
            $arr_text = str_split($text, 15);
            for ($i = 0; $i < sizeof($arr_text); $i++) {
                $textToSend = $textToSend . $arr_text[$i] . '<br/>';
            }
            return $textToSend;
        } else {
            return $text;
        }
    }

}

try {
    $_SESSION['form_post'] = $_POST;
    //$_SESSION['form_post'] = $_GET;
    $db = new DBConn();
    $dbl = new DBLogic();
    $poid = isset($poid) ? intval($poid) : false;
    if ($poid <= 0) {
        $errors['poid'] = "Not able to get PO number";
    }
    //print $poid;
    if (count($errors) == 0) {

        $objpolines = $dbl->getPOItems($poid);
        //print_r($objpolines);
        if ($objpolines == null) {
            $errors['nullPO'] = "PO cannot be publish. Please enter the items.";
        }
    }

    if (count($errors) == 0) {

        $objpo = $dbl->getPODetails($poid);
        //print_r($objpo);
        $objuser = $dbl->getUserInfoById($objpo->createdby_id);
        //print_r($objuser);

        $html2fpdf = new HTML2PDF('L', 'A4', 'en');

        $spdf = new showPDF();
        $pageno = 1;
        $htmlcktable = '<style type="text/css">
                    @page {
                      margin: 1cm;
                      margin-bottom: 2.5cm;
                      width:133%;
                      @frame footer {
                        -pdf-frame-content: footerContent;
                        bottom: 2cm;
                        margin-left: 0.5cm;
                        margin-right: 0.5cm;
                        height: 10cm;
                      }
                    }
                                      
          th { padding: 5px; text-align:center; vertical-align:top; }            
          th, td {
            border: 1px solid black;            
            width: 100px;
          }
          table{
            border-collapse: collapse;            
            table-layout: fixed;
            width: 200px;
        }
</style>
';      $totalValue = 0;
        $totalLD = 0.0;
        foreach ($objpolines as $line) {
             $totalLD = $totalLD + $line->lcrate;
             $totalValue = $totalValue + $line->totalvalue;
             $roundTotalVal= round($totalValue);
        }

        $htmlcktable .= '<page>' . $spdf->addPageHeader($objpo) . $spdf->addSupplierInfo($objpo, $objuser,$roundTotalVal). $spdf->addPageFooter($pageno);
        $htmlcktable .= '</page>'; //
        $pageno = 2;
        //$htmlcktable .= '<page>' . $spdf->addPageHeader($objpo) . $spdf->addSupplierInfo2($objpo, $objuser) . $spdf->addPageFooter($pageno);
        //$htmlcktable .= '</page>';
        $totalQty = 0;
        $totalValue = 0;
        $toatlLoadingChrs = 0.0;
        $totalTax = 0;
        //$pageno = 1;
        $num = 1;
        $count = 5;
        $totalLineCount = 0;
        $grandToatl = 0;
        $totalCgst = 0;
        $totalSgst = 0;
        $tot_taxable = 0;
        $tot_cgst = 0;
        $tot_sgst = 0;
        $taxable_value = 0;
        $cgst_value = 0;
        $sgst_value = 0;
        $total_value = 0;
        
        $htmlcktable .=  '<page>'.'<table style="width:100%;" cellspacing="0" cellpadding="0">';
        $htmlcktable .= $spdf->addCKTableHeader($totalLD);

        foreach ($objpolines as $line) {
            $q = $line->qty;
            $r = $line->rate;
            $desc1 = isset($line->desc_1) && trim($line->desc_1) != "" ? " , ".$line->desc_1." mm" : "";
            $desc2 = isset($line->desc_2) && trim($line->desc_2) != "" ? " x ".$line->desc_2." mm" : "";
            $thickness = isset($line->thickness) && trim($line->thickness) != "" ? " , ".$line->thickness." mm" : "";
            $itemname = $line->prod.$desc1.$desc2.$thickness;
            $color = "";
            $manufacturer = "";
            $brand="";
            $taxable_value = round($line->rate * $line->qty,2);
            $cgst_value = round($taxable_value * $line->cgstpct,2);
            $sgst_value = round($taxable_value * $line->sgstpct,2);
            $total_value = $taxable_value + $cgst_value + $sgst_value; 
            
            if($line->color == "NA"){ $color = "";}else{$color = '<br/>Color- '.$line->color;}
            if($line->manufacturer == "Any"){ $manufacturer = "";}else{$manufacturer = ', Manufacturer- '.$line->manufacturer;}
            if($line->brand == "Any"){ $brand = "";}else{$brand = ', Brand- '.$line->brand;}
            //$value = $q * $r; //desc_2//thickness//speci//hsncode
            $products = $itemname.$color.$manufacturer.$brand;
            $htmlcktable .= '<tr>
                     <td style="align=left;font-size:8px;width:4%;">' . $num . '</td>
                     <td style="align=left;font-size:8px;width:6%;">' . $line->category . '</td>    
                     <td style="align=left;font-size:8px;width:20%;">'. $products.'</td>
                     <td style="align=left;font-size:8px;width:5%;">' . $line->speci . '</td> 
                         
                     <td style="align=left;font-size:8px;width:5%;">' . $line->hsncode . '</td> 
                     <td style="align=left;font-size:8px;width:5%;">' . $line->length . '</td>
                     <td style="align=left;font-size:8px;width:5%;">MT</td>    
                     <td style="align=left;font-size:8px;width:5%;">' . sprintf("%.4f",$line->qty) . '</td>';    
             if($totalLD > 0){            
            $htmlcktable.='<td style="align=left;font-size:8px;width:8%;">' . $spdf->Currency($line->rate) . '</td>    
                     <td style="align=left;font-size:8px;width:8%;">' . $line->lcrate . '</td>';
             }else{
               $htmlcktable.='<td style="align=left;font-size:8px;width:8%;">' . $spdf->Currency($line->rate) . '</td>';  
               $htmlcktable.='<td style="align=left;font-size:8px;width:8%;">' . $spdf->Currency(round($line->rate * $line->qty,2)) . '</td>';  
             }
            
            $htmlcktable.='<td style="align=left;font-size:8px;width:4%;">' . $spdf->Currency($line->cgstval) . '</td>
                     <td style="align=left;font-size:8px;width:3%;">' . $spdf->Currency($cgst_value) . '</td>
                     <td style="align=left;font-size:8px;width:4%;">' . $spdf->Currency($line->sgstval) . '</td>
                     <td style="align=left;font-size:8px;width:3%;">' . $spdf->Currency($sgst_value) . '</td>    
                     <td style="align=left;font-size:8px;width:7%;">' . $spdf->Currency($line->totalrate) . '</td>
                     <td style="align=left;font-size:8px;width:8%;">' . $spdf->Currency($total_value) . '</td>
                     </tr>';

            $num = $num + 1;
            $tot_taxable  = $tot_taxable + round($line->rate * $line->qty,2);
            //$tot_cgst = $tot_cgst + round($line->cgstval * $line->qty,2);
            $tot_cgst = $tot_cgst + $cgst_value;
            //$tot_sgst = $tot_sgst + round($line->sgstval * $line->qty,2);
            $tot_sgst = $tot_sgst + $sgst_value;
            $toatlLoadingChrs = $toatlLoadingChrs + $line->lcrate;
            //$totalTax = $totalTax + ($line->cgstval + $line->sgstval) * $line->qty;
            $totalTax = $tot_cgst + $tot_sgst;
            $totalQty = $totalQty + $q;
            $totalCgst = $totalCgst + $line->cgstval;
            $totalSgst = $totalSgst + $line->sgstval;
            $totalValue = $totalValue + $total_value;
            
            //$grandToatl = $totalValue + $objpo->freightcharges;
            $count = $count + 1;
            if ($count >= 15) {
                $htmlcktable .= '</table>
                        ' . $spdf->addPageFooter($pageno) . '
                        </page>';
                $htmlcktable .= '<page>' . $spdf->addPageHeader($objpo);
                $htmlcktable .= '<br/><table  cellspacing="0" cellpadding="5px" width="100%" border="1" align="center">';
                $htmlcktable .= $spdf->addCKTableHeader($totalLD);

                $count = 5;
                $pageno = $pageno + 1;
            }
        }
        setlocale(LC_MONETARY,"en_IN");
        $roundTotalPOVal= round($totalValue);
        $GrandTotCurrInd = $spdf->Currency( $roundTotalPOVal );
        $totalTaxInd = $spdf->Currency(sprintf ("%.2f",$totalTax));
        $roundoff = $roundTotalPOVal- $totalValue;
        $imgrajiv = "../images/rajiv.jpg";
        //print $htmlcktable;
        $htmlcktable .= '<tr>
                <th style="align=left;font-size:8px;width:4%;">Total :</th>
                <td style="align=left;font-size:10px;width:6%;"></td>
                <td style="align=left;font-size:10px;width:20%;"></td>
                <td style="align=left;font-size:10px;width:5%;"></td>
                
                <td style="align=left;font-size:10px;width:5%;"></td>
                <td style="align=left;font-size:10px;width:5%;"></td>
                <td style="align=left;font-size:10px;width:5%;"></td>
                <th style="align=left;font-size:8px;width:5%;">' . sprintf("%.4f",$totalQty) . '</th>';
        if($totalLD > 0){            
        $htmlcktable.='<th style="align=left;font-size:10px;width:8%;"></th>
                <th style="align=left;font-size:10px;width:8%;">'.$toatlLoadingChrs.'</th>';
        }else{
          $htmlcktable.='<th style="align=left;font-size:8px;width:8%;"></th>';  
          $htmlcktable.='<th style="align=left;font-size:8px;width:8%;">'.$spdf->Currency($tot_taxable).'</th>';  
        }
        
        $htmlcktable.='<th style="align=left;font-size:10px;width:4%;"></th>
                <th style="align=left;font-size:8px;width:3%;">'.$spdf->Currency($tot_cgst).'</th> 
                <th style="align=left;font-size:10px;width:4%;"></th>
                <th style="align=left;font-size:8px;width:3%;">'.$spdf->Currency($tot_cgst).'</th>     
                <td style="align=left;font-size:10px;width:7%;"></td>
                <th style="align=left;font-size:8px;width:8%;">' . $spdf->Currency($totalValue) . '</th>
                </tr>';
        if($totalLD > 0){
        $htmlcktable.='<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="14">GST 18% Value</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $totalTaxInd . '</th>                   
                </tr>';
        }else{
         $htmlcktable.='<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="14">GST 18% Value</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $spdf->Currency($totalTax) . '</th>                   
                </tr>';   
        }
        if($totalLD > 0){
        $htmlcktable.='<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="14">Total Value</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $spdf->Currency($totalValue). '</th>                   
                </tr>';
        }else{
         $htmlcktable.='<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="14">Total Value</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $spdf->Currency($totalValue). '</th>                   
                </tr>';   
        }
        
        if($totalLD > 0){
        $htmlcktable.='<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="14">Round Off</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . sprintf ("%.2f",$roundoff) . '</th>                   
                </tr>
                ';
        }else{
         $htmlcktable.='<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="14">Round Off</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . sprintf ("%.2f",$roundoff) . '</th>                   
                </tr>
                ';   
        }
                 
        if($totalLD > 0){ 
         $htmlcktable.='<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="14">Grand Total Value</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $GrandTotCurrInd . '</th>                   
                </tr>';
        }else{
         $htmlcktable.='<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="14">Grand Total Value</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' .  $GrandTotCurrInd. '</th>                   
                </tr>';   
        }
         
         $htmlcktable.='</table>
                <br/><p align="right"><img src=' . $imgrajiv . ' style="width:150px;"/></p>
                     <p align="right"><b>Authorized Signatory</b></p>';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
        $html = "";

        
        
        $count = $count + 2;

//        if ($count >= 15) {
//            $html .= $spdf->addPageFooter($pageno) . '
//                    </page>';
//            //$html2fpdf->writeHTML($html);
//            $pageno = $pageno + 1;
//            $html .= '<page>' . $spdf->addPageHeader($objpo);
//        }


//        if ($count >= 15) {
//            $html .= $spdf->addPageFooter($pageno) . '
//                    </page>';
//            //$html2fpdf->writeHTML($html);
//            $pageno = $pageno + 1;
//            $html .= '<page>' . $spdf->addPageHeader($objpo);
//            $count = 0;
//        }


        //print $html;                
        //echo $count;
//        if ($count >= 15) {
//            $html .= $spdf->addPageFooter($pageno) . '
//                    </page>';
//            //$html2fpdf->writeHTML($html);
//            $pageno = $pageno + 1;
//            $html .= '<page>' . $spdf->addPageHeader($objpo);
//        }

        if (file_exists("../images/$objuser->image")) {
            $image = $objuser->image;
        } else {
            $image = "signature.gif";
        }

//        if ($count >= 15) {
//            $html .= $spdf->addPageFooter($pageno) . '
//                    </page>';
//            //$html2fpdf->writeHTML($html);
//            $pageno = $pageno + 1;
//            $html .= '<page>' . $spdf->addPageHeader($objpo);
//        }


//        $html.='<hr><table align="right">
//                <tr>
//                <td align="right">For Sarotam</td>
//                </tr>
//                <tr>
//                <td align="right"><img style="width:88px;" src="../images/'.$image.'" /></td>
//                </tr>
//                <tr>
//                <td align="right">Authorised Signatory</td>
//                </tr>
//                </table>';

        $html .= $spdf->addPageFooter($pageno) . '</page>';
        $htmlcktable = $htmlcktable . $html;
        $pageno = $pageno +1;
//        $htmlcktable .= '<page>' . $spdf->addPageHeader($objpo) . $spdf->addremarks($objpo, $objuser,$totalValue) . $spdf->addPageFooter($pageno);
//        $htmlcktable .= '</page>';
        $ckcopy = $objpo->id;
        $html2fpdf = new HTML2PDF('L', 'A4', 'en');
//        $printhtml = $htmlcktable . $html;
        $printhtml = $htmlcktable;        
//        echo $printhtml;
//        return;
        $html2fpdf->writeHTML($printhtml);
        $html2fpdf->Output("../pofiles/$ckcopy.pdf", "F");

        //print $printhtml;

        $num = $num - 1;
        /*if ($objpo->po_status == "5" && $objpo->is_mailsent == 0) {
            if (isset($objpo->email) && trim($objpo->email) != "") {
                $arr_to = explode(",", $objpo->email);

                //$arr_to = $objpo->email;
                foreach ($arr_to as $to) {
                    //echo $to;
                    $subject = "Purchase Order No " . $objpo->pono; //." Date ". ddmmyy_date($objpo->submitdate);
                    $body = '<p>Total Item :' . $num . ' &nbsp;&nbsp;&nbsp;&nbsp; Total Quantity :' . $totalQty . ' &nbsp;&nbsp;&nbsp;&nbsp; Total Amount :' . $totalValue . '</p>
                                <p>Detailed PO is attached with this mail. Please find attachment.</p>
                                <p>Thanks & Regards</p>
                                <p>Sarotam</p>
                                <p><b>Note : This is computer generated email do not reply. Kindly reply to purchase@sarotam.in</b></p>';

                    $emailHelper = new EmailHelper();
                    $success = $emailHelper->send(array($to), $subject, $body, '../pofiles/' . $ckcopy . '.pdf');
                    $dbl->updateEmailStatus($objpo->id);
                }
            }
        }*/
    }
} catch (Exception $xcp) {
    $clsLogger = new clsLogger();
    $clsLogger->logError("Failed to publish PO :" . $xcp->getMessage());
    $errors['status'] = "There was a problem processing your request. Please try again later";
    echo $xcp->getMessage();
    print "\n";
    print $html;
}
if (count($errors) > 0) {
    $_SESSION['form_errors'] = $errors;
    //print_r($errors);
    $redirect = "po/additems/id=$poid/";
} else {
    unset($_SESSION['form_errors']);
//        $_SESSION['form_success'] = $success;
    //$redirect = "po/home/postatus=1";
    $redirect = "pofiles/" . $objpo->id . ".pdf";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
