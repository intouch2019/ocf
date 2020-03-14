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
    
    function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . $this->convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . $this->convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= $this->convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

    function addSupplierInfo($objpo, $objuser) {
        $suaddress = isset($objpo->address) ? ', ' . $objpo->address : '';

        return '<table style="width:100%;" cellspacing="0" cellpadding="0">
                     <tr>
                     <th style="align=center;width:55%;height:10px;font-size:14px;" colspan="2"> <b>Purchase Order No : ' . $objpo->pono . '</b></th>
                     <th style="align=center;width:45%;height:10px;font-size:14px;" colspan="2"> <b>Date : ' . ddmmyy($objpo->submitdate) . '</b></th>
                     </tr>
                     <tr>
                     <td style="align=left;width:35%;height:10px;font-size:14px;">&nbsp;
                     <br/><b>Sarotam Industrial Goods Retail Distribution Private Limited</b>
                     <br/> CIN : U52609 PN2017 PTC170198
                     <br/> Corporate Office:401, East Court, Phoenix Market City,
                     <br/>207, Viman Nagar, Pune - 411 014, Maharashtra, India
                     <br/><br/><b>Our Contact Person: </b>
                     <br/>Rajeev Ranjan Prasad
                     <br/>Contact No - 9860198102
                     </td>
                     <td style="align=left;width:20%;height:10px;font-size:14px;">
                     <b>Delivery Address: </b>
                     '.$objpo->dc_master_address.'
                     </td>
                     <td style="align=left;width:25%;height:10px;font-size:14px;">
                     Pickup From,<br/><b>' . $objpo->company_name . '</b><br/>'
                . $suaddress . '<br/>
                     
                     </td> 
                     <td style="align=left;width:20%;height:10px;font-size:14px;">
                     Transporter Details,<br/><b>' . $objpo->transname . '</b><br/>'
                . $objpo->transemail . '<br/>
                     
                     </td> 
                     </tr>
                     <tr>
                     <td style="align=left;width:100%;height:10px;font-size:14px;" colspan="4">&nbsp;
                     text to added in this td<br/><br/><br/><br/>
                     </td>
                     </tr>
                     <tr>
                     <td style="align=right;width:80%;height:10px;font-size:14px;" colspan="3">&nbsp;
                     <br/><br/><br/><br/>
                     </td>
                     <th style="align=right;width:20%;height:10px;font-size:14px;">
                     Freight Amount :  '.$objpo->freightamt.'<br/><br/>
                     Freight GST    :  '.$objpo->freight_gst.'   <br/><br/>
                     Freight Total  :  '.$objpo->freight_total.'   <br/><br/>    
                     </th>
                     </tr>
                     
                     
                     </table>
                     <br/><br/><br/>
                     <p align="right"><b>Authorized Signatory</b></p>';

        //return $html;
    }

    function addremarks($objpo, $objuser,$grandToatl) {
        $suaddress = isset($objpo->address) ? ', ' . $objpo->address : '';
        $arr = explode(".",$grandToatl);

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

    function addCKTableHeader() {
        return '<tr>
                  <th style="align=left;font-size:12px;width:4%;">Sl.No</th>
                  <th style="align=left;font-size:12px;width:6%;">Category</th>
                  <th style="align=left;font-size:12px;width:20%;">Product</th>
                  <th style="align=left;font-size:12px;width:5%;">Spec</th>
                  
                  <th style="align=left;font-size:12px;width:5%;">HSN Code</th>
                  <th style="align=left;font-size:12px;width:5%;">Ordered Length</th>
                  <th style="align=left;font-size:12px;width:5%;">UOM</th>
                  <th style="align=left;font-size:12px;width:5%;">Qty(Kg)</th>
                  
                  <th style="align=left;font-size:12px;width:8%;">Base Rate(Rs/Kg)</th>
                  <th style="align=left;font-size:12px;width:8%;">Loading Charges<br/>(Rs/Kg)</th>
                  <th style="align=left;font-size:12px;width:7%;">CGST(9%)<br/>(Rs/Kg)</th>
                  <th style="align=left;font-size:12px;width:7%;">SGST(9%)<br/>(Rs/Kg)</th>
                  <th style="align=left;font-size:12px;width:7%;">Rate(Rs/Kg)</th>
                  <th style="align=left;font-size:12px;width:8%;">Value(Rs)</th>
                  </tr>';
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
';

        $htmlcktable .= '<page>' . $spdf->addPageHeader($objpo) . $spdf->addSupplierInfo($objpo, $objuser). $spdf->addPageFooter($pageno);
        $htmlcktable .= '</page>'; //
        //$pageno = 2;
        //$htmlcktable .= '<page>' . $spdf->addPageHeader($objpo) . $spdf->addSupplierInfo2($objpo, $objuser) . $spdf->addPageFooter($pageno);
        //$htmlcktable .= '</page>';
        $totalQty = 0;
        $totalValue = 0;
        $toatlLoadingChrs = 0;
        $totalTax = 0;
        $pageno = 1;
        $num = 1;
        $count = 8;
        $totalLineCount = 0;
        $grandToatl = 0;

        //$htmlcktable .=  '<table style="width:100%;" cellspacing="0" cellpadding="0">';
        //$htmlcktable .= $spdf->addCKTableHeader();

//        foreach ($objpolines as $line) {
//            $q = $line->qty;
//            $r = $line->rate;
//            $desc1 = isset($line->desc_1) && trim($line->desc_1) != "" ? " , ".$line->desc_1." mm" : "";
//            $desc2 = isset($line->desc_2) && trim($line->desc_2) != "" ? " x ".$line->desc_2." mm" : "";
//            $thickness = isset($line->thickness) && trim($line->thickness) != "" ? " , ".$line->thickness." mm" : "";
//            $itemname = $line->prod.$desc1.$desc2.$thickness;
//            //$value = $q * $r; //desc_2//thickness//speci//hsncode
//            $htmlcktable .= '<tr>
//                     <td style="align=left;font-size:10px;width:4%;">' . $num . '</td>
//                     <td style="align=left;font-size:10px;width:6%;">' . $line->category . '</td>    
//                     <td style="align=left;font-size:10px;width:20%;">'.$itemname.'<br/>Color- '.$line->color.', Manufacturer- '.$line->manufacturer.', Brand- '.$line->brand.'</td>
//                     <td style="align=left;font-size:10px;width:5%;">' . $line->speci . '</td> 
//                         
//                     <td style="align=left;font-size:10px;width:5%;">' . $line->hsncode . '</td> 
//                     <td style="align=left;font-size:10px;width:5%;">' . $line->length . '</td>
//                     <td style="align=left;font-size:10px;width:5%;">Kg</td>    
//                     <td style="align=left;font-size:10px;width:5%;">' . $line->qty . '</td>    
//                         
//                     <td style="align=left;font-size:10px;width:8%;">' . $line->rate . '</td>    
//                     <td style="align=left;font-size:10px;width:8%;">' . $line->lcrate . '</td>                             
//                     <td style="align=left;font-size:10px;width:7%;">' . $line->cgstval . '</td>
//                     <td style="align=left;font-size:10px;width:7%;">' . $line->sgstval . '</td>
//                     <td style="align=left;font-size:10px;width:7%;">' . $line->totalrate . '</td>
//                     <td style="align=left;font-size:10px;width:8%;">' . $line->totalvalue . '</td>
//                     </tr>';
//
//            $num = $num + 1;
//            $toatlLoadingChrs = $toatlLoadingChrs + $line->lcrate;
//            $totalTax = $totalTax + $line->cgstval + $line->sgstval;
//            $totalQty = $totalQty + $q;
//            $totalCgst = $totalCgst + $line->cgstval;
//            $totalSgst = $totalSgst + $line->sgstval;
//            $totalValue = $totalValue + $line->totalvalue;
//            
//            //$grandToatl = $totalValue + $objpo->freightcharges;
//            $count = $count + 1;
//            if ($count >= 15) {
//                $htmlcktable .= '</table>
//                        ' . $spdf->addPageFooter($pageno) . '
//                        </page>';
//                $htmlcktable .= '<page>' . $spdf->addPageHeader($objpo);
//                $htmlcktable .= '<br/><table  cellspacing="0" cellpadding="5px" width="100%" border="1" align="center">';
//                $htmlcktable .= $spdf->addCKTableHeader();
//
//                $count = 2;
//                $pageno = $pageno + 1;
//            }
//        }
        
        //$roundTotalPOVal= round($totalValue);
        //$roundoff = $roundTotalPOVal- $totalValue;

        //print $htmlcktable;
//        $htmlcktable .= '<tr>
//                <th style="align=left;font-size:10px;width:4%;">Totals:</th>
//                <td style="align=left;font-size:10px;width:6%;"></td>
//                <td style="align=left;font-size:10px;width:20%;"></td>
//                <td style="align=left;font-size:10px;width:5%;"></td>
//                
//                <td style="align=left;font-size:10px;width:5%;"></td>
//                <td style="align=left;font-size:10px;width:5%;"></td>
//                <td style="align=left;font-size:10px;width:5%;"></td>
//                <th style="align=left;font-size:10px;width:5%;">' . $totalQty . '</th>
//                    
//                <th style="align=left;font-size:10px;width:8%;"></th>
//                <th style="align=left;font-size:10px;width:8%;">'.$toatlLoadingChrs.'</th>
//                <th style="align=left;font-size:10px;width:7%;">'.$totalCgst.'</th>
//                <th style="align=left;font-size:10px;width:7%;">'.$totalSgst.'</th> 
//                <td style="align=left;font-size:10px;width:7%;"></td>
//                <th style="align=left;font-size:10px;width:8%;">' . $totalValue . '</th>
//                </tr>
//                <tr>
//                <th style="align=right;font-size:10px;width:85%;" colspan="12">GST 18% Value</th>
//                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . sprintf ("%.2f",$totalTax) . '</th>                   
//                </tr>
//                <tr>
//                <th style="align=right;font-size:10px;width:85%;" colspan="12">Total Value</th>
//                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $roundTotalPOVal . '</th>                   
//                </tr>';
                
                
        
//        if($objpo->freightamt > 0){
//          $htmlcktable.='<tr><th style="align=right;font-size:10px;width:85%;" colspan="12">Freight Amount</th>
//                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $objpo->freightamt . '</th>                   
//                </tr>
//                <tr>
//                <th style="align=right;font-size:10px;width:85%;" colspan="12">Freight GST</th>
//                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $objpo->freight_gst . '</th>                   
//                </tr>
//                <tr>
//                <th style="align=right;font-size:10px;width:85%;" colspan="12">Freight Charges</th>
//                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $objpo->freightcharges . '</th>                   
//                </tr>';
//        }   
        
//         $htmlcktable.='<tr>
//                <th style="align=right;font-size:10px;width:85%;" colspan="12">Round Off</th>
//                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . sprintf ("%.2f",$roundoff) . '</th>                   
//                </tr>
//                </table>';


        //$html = "";

        
        
//        $count = $count + 2;
//
//        if ($count >= 15) {
//            $html .= $spdf->addPageFooter($pageno) . '
//                    </page>';
//            //$html2fpdf->writeHTML($html);
//            $pageno = $pageno + 1;
//            $html .= '<page>' . $spdf->addPageHeader($objpo);
//        }
//
//
//        if ($count >= 15) {
//            $html .= $spdf->addPageFooter($pageno) . '
//                    </page>';
//            //$html2fpdf->writeHTML($html);
//            $pageno = $pageno + 1;
//            $html .= '<page>' . $spdf->addPageHeader($objpo);
//            $count = 0;
//        }
//
//
//        //print $html;                
//        //echo $count;
//        if ($count >= 15) {
//            $html .= $spdf->addPageFooter($pageno) . '
//                    </page>';
//            //$html2fpdf->writeHTML($html);
//            $pageno = $pageno + 1;
//            $html .= '<page>' . $spdf->addPageHeader($objpo);
//        }
//
//        if (file_exists("../images/$objuser->image")) {
//            $image = $objuser->image;
//        } else {
//            $image = "signature.gif";
//        }
//
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

        //$html .= $spdf->addPageFooter($pageno) . '</page>';
        //$htmlcktable = $htmlcktable . $html;
        $pageno = $pageno +1;
        //$htmlcktable .= '<page>' . $spdf->addPageHeader($objpo) . $spdf->addremarks($objpo, $objuser,$totalValue) . $spdf->addPageFooter($pageno);
        //$htmlcktable .= '</page>';
        $ckcopy = $objpo->id;
        $html2fpdf = new HTML2PDF('L', 'A4', 'en');
//        $printhtml = $htmlcktable . $html;
        $printhtml = $htmlcktable;        
       //echo $printhtml;
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

