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
                 <p align="center"><b>STOCK TRANSFER</b></p>
                 
                    ';
    }

    function getIndianCurrency($number) {
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
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
            } else
                $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal) ? "And " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
    }

    function Currency($num) {
        $nums = explode(".", $num);
        if (count($nums) > 2) {
            return "0";
        } else {
            if (count($nums) == 1) {
                $nums[1] = "00";
            }
            $num = $nums[0];
            $explrestunits = "";
            if (strlen($num) > 3) {
                $lastthree = substr($num, strlen($num) - 3, strlen($num));
                $restunits = substr($num, 0, strlen($num) - 3);
                $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
                $expunit = str_split($restunits, 2);
                for ($i = 0; $i < sizeof($expunit); $i++) {

                    if ($i == 0) {
                        $explrestunits .= (int) $expunit[$i] . ",";
                    } else {
                        $explrestunits .= $expunit[$i] . ",";
                    }
                }
                $thecash = $explrestunits . $lastthree;
            } else {
                $thecash = $num;
            }
            return $thecash . "." . $nums[1];
        }
    }

    function addSupplierInfo($objpo, $objuser, $grandToatl) {
        $suaddress = isset($objpo->taddress) ? $objpo->taddress : '';
	$gstin = isset($objpo->tgstno) ? $objpo->tgstno. '.' : '';
	$pan_no = isset($objpo->tpanno) ? $objpo->tpanno . '.' : '';
        $arr = explode(".", $grandToatl);
        $conevrtno = $this->getIndianCurrency($arr[0]);
        $invarr = explode("-", $objpo->transferno);
        $invoicerefno = $invarr[0]."-".$invarr[1];
        $invoiceno = $invarr[2];
        $convertdec = "";
        if (isset($arr[1])) {
            $convertdec = ' And ' . $this->getIndianCurrency($arr[1]) . ' Paise';
        }
        return '<table style="width:100%;" cellspacing="0" cellpadding="0">
                     <tr>
                     <td style="align=center;width:35%;height:10px;font-size:14px;" colspan ="4">Referance : '.$invoicerefno.' <b><br>Stock Transfer No : ' . $invoiceno . '</b></td>
                     <td style="align=center;width:65%;height:10px;font-size:14px;" colspan="9"> <b>Date : ' . ddmmyy($objpo->transferdate) . '</b></td>
                     </tr>
                     <tr>
                     <td style="align=left;width:35%;height:10px;font-size:14px;" colspan ="4"><b>From :</b>
                     <br/><b>' . $objpo->fromloc . '</b>
                     <br/>' . $objpo->faddress . ' 
                     </td>
                     <td style="align=left;width:36%;height:10px;font-size:14px;" colspan ="5">
                     <b>Delivery Address: </b>
                     ' . $objpo->taddress . '
                     </td>
                     <td style="align=left;width:29%;height:10px;font-size:14px;" colspan ="4">
                     To,<br/><b>' . $objpo->toloc . '</b><br/>GSTIN : ' . $gstin . '<br/>PAN : ' . $pan_no . '<br/>'
                . $suaddress . '<br/>                     
                     </td> 
                     </tr>                     
                     </table>';

        //return $html;
    }


    function addCKTableHeader($totalLD) {
        $htmlt = "";
        $htmlt .= '<tr>
                  <th style="align=center;font-size:12px;width:5%;">Sl.<br/>No</th>
                  <th style="align=center;font-size:12px;width:10%;">Cat.</th>
                  <th style="align=left;font-size:12px;width:20%;">Product</th>
                  
                  <th style="align=center;font-size:12px;width:15%;">Spec</th>
                  <th style="align=center;font-size:12px;width:15%;">HSN Code</th>
                  <th style="align=center;font-size:12px;width:6%;">UOM</th>
                  
                  <th style="align=center;font-size:12px;width:29%;">Required Qty(MT)</th>
        
                  </tr>';
        return $htmlt;
    }
    

    function addPageFooter($pageno) {
        /*  <hr><P align="center"><br/><b>Sarotam Industrial Goods Retail Distribution Private Limited</b>
          <br/>CIN : U52609 PN2017 PTC170198
          <br/>Registered Office:128, Shiv Hari Complex, Opposite Giga Space,
          <br/> 206, Viman Nagar, Pune - 411 014, Maharashtra, India</P>
          <p align="center">Page ' . $pageno . ' */
        return '<page_footer>
                    <p align="center">Page ' . $pageno . '</p>
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
    $db = new DBConn();
    $dbl = new DBLogic();
    $transferid = isset($transferid) ? intval($transferid) : false;
    if ($transferid <= 0) {
        $errors['transferid'] = "Not able to get PO number";
    }
    if (count($errors) == 0) {

        $objpolines = $dbl->getStockTransferItems($transferid);
        //print_r($objpolines);
        if ($objpolines == null) {
            $errors['nullPO'] = "PO cannot be publish. Please enter the items.";
        }
    }

    if (count($errors) == 0) {

//        $objpo = $dbl->getStockTransferDetails($transferid);
        $objpo = $dbl->getStockTransferInfo($transferid);
//        print_r($objpo);
        $objuser = $dbl->getUserInfoById($objpo->createdby);
        $html2fpdf = new HTML2PDF('P', 'A4', 'en');

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
        $totalValue = 0;
        $totalLD = 0.0;
        $roundTotalVal = 0.0;


        $htmlcktable .= '<page>' . $spdf->addPageHeader($objpo) . $spdf->addSupplierInfo($objpo, $objuser, $roundTotalVal); //. $spdf->addPageFooter($pageno);
        //$htmlcktable .= '</page>'; //
        $pageno = 1;
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

        $htmlcktable .= '<table style="width:100%;" cellspacing="0" cellpadding="0">';
        $htmlcktable .= $spdf->addCKTableHeader($totalLD);

        foreach ($objpolines as $line) {
            $q = $line->qty;
            //$r = $line->rate + $line->lcrate;
            //$totval = $line->totalrate * $q;
            $desc1 = isset($line->desc_1) && trim($line->desc_1) != "" ? " , " . $line->desc_1 . " mm" : "";
            $desc2 = isset($line->desc_2) && trim($line->desc_2) != "" ? " x " . $line->desc_2 . " mm" : "";
            $thickness = isset($line->thickness) && trim($line->thickness) != "" ? " , " . $line->thickness . " mm" : "";
            $itemname = $line->prod . $desc1 . $desc2 . $thickness;
            $color = "";
            $manufacturer = "";
            $brand = "";
            //if($line->color == "NA"){ $color = "";}else{$color = '<br/>Color- '.$line->color;}
            //if($line->manufacturer == "Any"){ $manufacturer = "";}else{$manufacturer = ', Manufacturer- '.$line->manufacturer;}
            //if($line->brand == "Any"){ $brand = "";}else{$brand = ', Brand- '.$line->brand;}
            //$value = $q * $r; //desc_2//thickness//speci//hsncode
            $products = $itemname . $color . $manufacturer . $brand;
            $htmlcktable .= '<tr>
                     <td style="align=center;font-size:10px;width:5%;">' . $num . '</td>
                     <td style="align=center;font-size:10px;width:10%;"> Mild Steel</td>    
                     <td style="align=left;font-size:10px;width:20%;">' . $products . '</td>
                         
                     <td style="align=center;font-size:10px;width:15%;">'.$line->spec.'</td> 
                     <td style="align=center;font-size:10px;width:15%;">' . $line->hsncode . '</td> 
                     <td style="align=center;font-size:10px;width:6%;">MT</td> 
                     
                     <td style="align=center;font-size:10px;width:29%;">' . sprintf("%.4f",$line->qty) . '</td>'
            
                     .'</tr>';

            $num = $num + 1;
            //$toatlLoadingChrs = $toatlLoadingChrs + $line->lcrate;
           // $totalTax = $totalTax + ($line->cgstval + $line->sgstval) * $line->qty;
            $totalQty = $totalQty + $q;
            //$totalCgst = $totalCgst + $line->cgstval;
            //$totalSgst = $totalSgst + $line->sgstval;
            //$totalValue = $totalValue + $totval;

            $count = $count + 1;
            if ($count >= 19) {
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
        setlocale(LC_MONETARY, "en_IN");
       // $roundTotalPOVal = round($totalValue);
        //$GrandTotCurrInd = $spdf->Currency($roundTotalPOVal);
       // $totalTaxInd = $spdf->Currency(sprintf("%.2f", $totalTax));
        //$roundoff = $roundTotalPOVal - $totalValue;
        $signimg = "../images/".$objuser->username.".jpg";
        $htmlcktable .= '<tr>
                <th style="align=left;font-size:10px;width:5%;">Total </th>
                <td style="align=left;font-size:10px;width:10%;"></td>
                <td style="align=left;font-size:10px;width:20%;"></td>
                
                <td style="align=left;font-size:10px;width:15%;"></td>
                <td style="align=left;font-size:10px;width:15%;"></td>
                <td style="align=left;font-size:10px;width:6%;"></td>
                
                <th style="align=center;font-size:10px;width:29%;">' . sprintf("%.4f",$totalQty) . '</th>'
       
                .'</tr>';
       /* if ($totalLD > 0) {
            $htmlcktable .= '<tr>
                <th style="align=right;font-size:10px;width:71%;" colspan="7">GST 18% Value</th>
                <th style="align=left;font-size:10px;width:29%;" colspan="2">' . $totalTaxInd . '</th>                   
                </tr>';
        } else {
            $htmlcktable .= '<tr>
                <th style="align=right;font-size:10px;width:71%;" colspan="7">GST 18% Value</th>
                <th style="align=left;font-size:10px;width:29%;" >' . $spdf->Currency(sprintf ("%.2f",$totalTax )). '</th>                   
                </tr>';
        }
        if ($totalLD > 0) {
            $htmlcktable .= '<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="12">Total Value</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $spdf->Currency(sprintf ("%.2f",$totalValue)) . '</th>                   
                </tr>';
        } else {
            $htmlcktable .= '<tr>
                <th style="align=right;font-size:10px;width:71%;" colspan="7">Total Value</th>
                <th style="align=left;font-size:10px;width:29%;">' . $spdf->Currency(sprintf ("%.2f",$totalValue)) . '</th>                   
                </tr>';
        }

        if ($totalLD > 0) {
            $htmlcktable .= '<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="12">Round Off</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . sprintf("%.2f", $roundoff) . '</th>                   
                </tr>
                ';
        } else {
            $htmlcktable .= '<tr>
                <th style="align=right;font-size:10px;width:71%;" colspan="7">Round Off</th>
                <th style="align=left;font-size:10px;width:29%;">' . sprintf("%.2f", $roundoff) . '</th>                   
                </tr>
                ';
        }

        if ($totalLD > 0) {
            $htmlcktable .= '<tr>
                <th style="align=right;font-size:10px;width:85%;" colspan="12">Grand Total Value</th>
                <th style="align=left;font-size:10px;width:15%;" colspan="2">' . $GrandTotCurrInd . '</th>                   
                </tr>';
        } else {
            $htmlcktable .= '<tr>
                <th style="align=right;font-size:10px;width:71%;" colspan="7">Grand Total Value</th>
                <th style="align=left;font-size:10px;width:29%;">' . $GrandTotCurrInd . '</th>                   
                </tr>';
        }*/

        $htmlcktable .= '</table>
                <br/><p align="right"><img src=' . $signimg . ' style="width:150px;"/></p>
                     <p align="right"><b>Authorized Signatory</b></p>';

        $html = "";
        $count = $count + 2;

        if (file_exists("../images/$objuser->image")) {
            $image = $objuser->image;
        } else {
            $image = "signature.gif";
        }


        $html .= $spdf->addPageFooter($pageno) . '</page>';
        $htmlcktable = $htmlcktable . $html;
        $pageno = $pageno + 1;
//        $htmlcktable .= '<page>' . $spdf->addPageHeader($objpo) . $spdf->addremarks($objpo, $objuser,$totalValue) . $spdf->addPageFooter($pageno);
//        $htmlcktable .= '</page>';
        $ckcopy = $objpo->id;
        $html2fpdf = new HTML2PDF('P', 'A4', 'en');
//        $printhtml = $htmlcktable . $html;
        $printhtml = $htmlcktable;
        //echo $printhtml;
        $html2fpdf->writeHTML($printhtml);
        $html2fpdf->Output("../pofiles/$ckcopy.pdf", "F");

        $num = $num - 1;
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
    $redirect = "po/additems/id=$transferid";
} else {
    unset($_SESSION['form_errors']);
    $redirect = "pofiles/" . $objpo->id . ".pdf";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
