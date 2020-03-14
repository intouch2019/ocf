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

$errors = array();
$success = array();

class showPDF {

    function addPageHeader() {
        $imgurl = "../images/rsz_newsarotam.jpg";
        return '<table align="center" width="100%" border="1" cellspacing="0" cellpadding="0">
                 <tr>   
                 <td><img src=' . $imgurl . ' width="10%"/></td>
                 </tr>
                 </table>
                 <p align="center"><b>STOCK TRANSFER CHALLAN</b></p>
                 
                    ';
    }

   


    function addSupplierInfo($objpo, $grandToatl,$objchallan) {
        $suaddress = isset($objpo->taddress) ? $objpo->taddress . '.' : '';
        $pan_no = isset($objpo->tpanno) ? $objpo->tpanno . '.' : '';
        $gstin = isset($objpo->tgstno) ? $objpo->tgstno. '.' : '';
        $submittedDate = isset($objchallan->submittedDate) ? $objchallan->submittedDate . '.' : '';
        $stcdatearr = explode(" ", $submittedDate);
        $stcdate = $stcdatearr[0];
        $tillAddress = $district . $pincode . $state . $country;
        $invarr = explode("-", $objchallan->challan_no);
        $invoicerefno = $invarr[0]."-".$invarr[1];
        $invoiceno = $invarr[2];
        return '<table style="width:100%;" cellspacing="0" cellpadding="0">
                     <tr>
                     <td style="align=center;width:35%;height:10px;font-size:14px;" colspan ="4">Referance : '.$invoicerefno.' <b><br>Stock Transfer Challan No : ' . $invoiceno . '</b></td>
                     <td style="align=center;width:65%;height:10px;font-size:14px;" colspan="9"> <b>Date : ' . ddmmyy($stcdate) . '</b></td>
                     </tr>
                     <tr>
                     <td style="align=left;width:35%;height:10px;font-size:14px;" colspan ="4"> <b>From :</b>
                     <br/><b>' . $objpo->fromloc . '</b>
                     <br/>' . $objpo->faddress . ' 
                     </td>
                     <td style="align=left;width:36%;height:10px;font-size:14px;" colspan ="5">
                                          To,<br/><b>' . $objpo->toloc . '</b><br/>GSTIN : ' . $gstin . '<br/>PAN : ' . $pan_no . '<br/>'
                . $suaddress . '<br/>' . $tillAddress . '
                     
                   
                     </td>
                     <td style="align=left;width:29%;height:10px;font-size:14px;" colspan ="4">
                        
                        <b>Vehicle Number:</b> '.$objchallan->vehicle_no.'<br/>
                     </td> 
                     </tr>                     
                     </table>';
    }


    function addCKTableHeader($totalLD) {
        $htmlt = "";
        $htmlt .= '<tr>
                  <th style="align=center;font-size:12px;width:5%;">Sl. No.</th>
                  <th style="align=center;font-size:12px;width:5%;">Cat.</th>
                  <th style="align=left;font-size:12px;width:19%;">Product</th>
                  <th style="align=center;font-size:12px;width:6%;">Spec</th>
                  
                  <th style="align=center;font-size:12px;width:6%;">HSN Code</th>
                  <th style="align=center;font-size:12px;width:5%;">UOM</th>
                  <th style="align=center;font-size:12px;width:8%;">Required Qty(MT)</th>
                  <th style="align=center;font-size:12px;width:9%;">Actual Batch No</th>
                  <th style="align=center;font-size:12px;width:8%;">Actual Quantity (MT)</th>
                  
                  
                  <th style="align=center;font-size:12px;width:10%;">Actual No of pieces or Bundles</th>
                  <th style="align=center;font-size:12px;width:9%;">Bundle Number</th>
                  <th style="align=center;font-size:12px;width:10%;">Value</th>
                  </tr>';
        return $htmlt;
    }
    

    function addPageFooter($pageno) {
        
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

    $challanid = isset($challanid) ? intval($challanid) : false;
    if ($challanid <= 0) {
        $errors['transferid'] = "Not able to get PO number";
    }
    if (count($errors) == 0) {

//        $objpolines = $dbl->getStockTransferChallanItems($challanid);
        $objpolines = $dbl->getChallanItems($challanid);
//        print_r($objpolines);
//        return;
        if ($objpolines == null) {
            $errors['nullPO'] = "PO cannot be publish. Please enter the items.";
        }
    }


    if (count($errors) == 0) {

        $objchallan = $dbl->getChallanInfoByChallanid($challanid);
        //$objpo = $dbl->getStockTransferDetails($objchallan->st_id);
        $objpo = $dbl->getStockTransferInfo($objchallan->st_id);
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
        $totalLD = 0.0;
        $roundTotalVal = 0.0;


        $htmlcktable .= '<page>' . $spdf->addPageHeader() . $spdf->addSupplierInfo($objpo, $roundTotalVal,$objchallan); //. $spdf->addPageFooter($pageno);


        $pageno = 1;
        $totalQty = 0;
        $totalReqQty = 0;
        $totalActualQty = 0;
        $totalValue = 0;
        $toatlLoadingChrs = 0.0;
        $totalTax = 0;
        $num = 1;
        $count = 5;
        $totalLineCount = 0;

        $htmlcktable .= '<table style="width:100%;" cellspacing="0" cellpadding="0">';
        $htmlcktable .= $spdf->addCKTableHeader($totalLD);

        foreach ($objpolines as $line) {
            $totalReqQty = $totalReqQty + $line->req_qty;
            $totalActualQty = $totalActualQty + $line->qty;
            $desc1 = isset($line->desc_1) && trim($line->desc_1) != "" ? " , " . $line->desc_1 . " mm" : "";
            $desc2 = isset($line->desc_2) && trim($line->desc_2) != "" ? " x " . $line->desc_2 . " mm" : "";
            $thickness = isset($line->thickness) && trim($line->thickness) != "" ? " , " . $line->thickness . " mm" : "";
            $itemname = $line->prod . $desc1 . $desc2 . $thickness;
            $rounded_req_qty = sprintf("%.4f", $line->req_qty);
            $rounded_qty = sprintf("%.4f", $line->qty);
            $value = $rounded_qty * $line->rate;
            $roundValue = round($value,2,PHP_ROUND_HALF_UP);
            $roundValue = sprintf("%.2f",$roundValue);
            $totalValue = $totalValue + $value;
            $color = "";
            $manufacturer = "";
            $brand = "";
            $products = $itemname . $color . $manufacturer . $brand;
            $htmlcktable .= '<tr>
                
                  <td style="align=center;font-size:11px;width:5%;">' . $num . '</td>
                  <td style="align=center;font-size:11px;width:5%;">Mild Steel</td>
                  <td style="align=left;font-size:11px;width:19%;">' . $products . '</td>
                  <td style="align=center;font-size:11px;width:6%;">'.$line->spec.'</td>
                  
                  <td style="align=center;font-size:11px;width:6%;">' . $line->hsncode . '</td>
                  <td style="align=center;font-size:11px;width:5%;">MT</td>
                  <td style="align=center;font-size:11px;width:8%;">' . $rounded_req_qty . '</td>
                  <td style="align=center;font-size:11px;width:9%;">' . $line->batchcode . '</td>
                  <td style="align=center;font-size:11px;width:8%;">' . $rounded_qty . '</td>
                  
                  
                  <td style="align=center;font-size:11px;width:10%;">' . $line->numberpcs . '</td>
                  <td style="align=center;font-size:11px;width:9%;">' . $line->length . '</td>
                  <td style="align=center;font-size:11px;width:10%;">' . $roundValue . '</td>

                </tr>';
            
  

            $num = $num + 1;

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
        
        $roundTotalValue = round($totalValue,2,PHP_ROUND_HALF_UP);
        $roundTotalValue = sprintf("%.2f",$roundTotalValue);
        
        $roundtotalReqQty = round($totalReqQty,4,PHP_ROUND_HALF_UP);
        $roundtotalReqQty = sprintf("%.4f",$roundtotalReqQty);
        
        $roundtotalActualQty = round($totalActualQty,4,PHP_ROUND_HALF_UP);
        $roundtotalActualQty = sprintf("%.4f",$roundtotalActualQty);

        setlocale(LC_MONETARY, "en_IN");
        $imgrajiv = "../images/rajiv.jpg";
        $htmlcktable .= '<tr>
                  <th style="align=left;font-size:11px;width:5%;">Total</th>
                  <td style="align=center;font-size:11px;width:5%;"></td>
                  <td style="align=center;font-size:11px;width:19%;"></td>
                  <td style="align=center;font-size:11px;width:6%;"></td>
                  
                  <td style="align=center;font-size:11px;width:6%;"></td>
                  <td style="align=center;font-size:11px;width:5%;"></td>
                  <td style="align=center;font-size:11px;width:8%;">'.$roundtotalReqQty.'</td>
                  <td style="align=center;font-size:11px;width:9%;"></td>
                  <th style="align=center;font-size:11px;width:8%;">'.$roundtotalActualQty.'</th>
                  
                  <td style="align=center;font-size:11px;width:10%;"></td>
                  <td style="align=center;font-size:11px;width:9%;"></td>
                  <th style="align=center;font-size:11px;width:10%;">'.$roundTotalValue.'</th>
                </tr>';
//      
//
        $htmlcktable .= '</table>
                <br/><p align="right"><img src=' . $imgrajiv . ' style="width:150px;"/></p>
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
        $ckcopy = $objchallan->id;
        $html2fpdf = new HTML2PDF('P', 'A4', 'en');
        $printhtml = $htmlcktable;
        $html2fpdf->writeHTML($printhtml);
        $html2fpdf->Output("../stcfiles/$ckcopy.pdf", "F");

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
    $redirect = "po/additems/id=$challanid";
} else { 
    unset($_SESSION['form_errors']);
    $redirect = "stcfiles/" . $objchallan->id . ".pdf";
}
session_write_close();
header("Location: " . DEF_SITEURL . $redirect);
exit;
