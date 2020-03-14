<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/logger/clsLogger.php";
require_once "lib/email/EmailHelper.php";
require_once "lib/db/DBLogic.php";
require_once "lib/showPDF/showPDFCN.php";
require_once "Classes/html2pdf/html2pdf.class.php";
require_once "lib/core/strutil.php";

extract($_GET);
try{
  $tot_sgst_val = 0;
  $tot_cgst_val = 0;
  $tot_igst_val = 0;
  $userid = getCurrStoreId();
  
  $dbLogic = new DBLogic();
    
$html2fpdf = new HTML2PDF('P', 'A4', 'en');
   
        $spdf = new showPDFCN();
        $pageno = 1;
        $htmlstable = '
            <style type="text/css">
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
                                      
          th {  align="center"; }            
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
    
        $htmlstable .= '<page>';      
        $htmlstable .= '<table style="width:100%;">';
        $type_text = "CREDITNOTE";
        
        $obj_inv_header = $dbLogic->getCNDetails($invid);
        $invoice_no = $obj_inv_header->cnno;
        $ref_invno = $obj_inv_header->invoice_no;
        $ref_invdate = ddmmyy($obj_inv_header->invoice_date);
        $invoice_dt = ddmmyy($obj_inv_header->cndate);
        $customer_name = "";
        $customer_address = "";
        $customer_gstno = "";
        $customer_panno = "";
        $customer_state_id = null;
        if($obj_inv_header->customerid != null && $obj_inv_header->customerid > 0){
            $obj_customer = $dbLogic->getCustomerById($obj_inv_header->customerid);
            if($obj_customer != NULL){
                $customer_name = $obj_customer->name;
                $customer_address = $obj_customer->address;
                $customer_gstno = $obj_customer->gstno;
                $customer_panno = $obj_customer->panno;
                $customer_state_id = $obj_customer->state_id;
            }
        }

        $obj_cr = $dbLogic->getCRDetailsByUserId($userid);
          $dist_name = $obj_cr->rfc_name;
          $state = $obj_cr->dealerstate;
          $dist_gstno = $obj_cr->gstno;
          if(isset($obj_cr->panno)){
          $dist_panno = $obj_cr->panno;
          }else{
           $dist_panno = "";   
          }
          
          $dist_addr = $obj_cr->address;
        
        $htmlstable .= $spdf->addTableHeader($invoice_no,$invoice_dt,$customer_name,$customer_address,$customer_gstno,$customer_panno,$state,
                $dist_name,$dist_addr,$dist_gstno,$dist_panno,$type_text,$ref_invno,$ref_invdate);
        $htmlstable .= '</table>';
        $htmlstable .= '<table style="width:100%;">';
        if($obj_cr->state == $customer_state_id || $customer_state_id == null){
            $htmlstable .= $spdf->addColHeader();
        }else if($customer_state_id != null){
            $htmlstable .= $spdf->addColHeaderIGST();
        }
        $invitemsobjs = $dbLogic->getCNItems($invid);
        $cnt=0;$srno=0;
        $items_per_page = 20;
        $total_qty = 0;
        $total_tot = 0;
        $total_disc = 0;
        $total_taxable_amt =0;
        $total_gst_rate =0;
        $total_gst_amt =0;
        $total_line_total = 0;
        $total_sgst_amt = 0;
        $total_cgst_amt = 0;
        $all_total_val = 0;
       
        if(!empty($invitemsobjs)){
           foreach($invitemsobjs as $invitemobj){ 
               if(isset($invitemobj) && !empty($invitemobj) && $invitemobj != null){
                   $cnt++;
                   $srno++;
                   
                   $desc1 = isset($invitemobj->desc_1) && trim($invitemobj->desc_1) != "" ? " , ".$invitemobj->desc_1." mm" : "";
                   $desc2 = isset($invitemobj->desc_2) && trim($invitemobj->desc_2) != "" ? " x ".$invitemobj->desc_2." mm" : "";
                   $thickness = isset($invitemobj->thickness) && trim($invitemobj->thickness) != "" ? " , ".$invitemobj->thickness." mm" : "";
                   $spec  = isset($invitemobj->spec) && trim($invitemobj->spec) !="" ? " ,spec-".$invitemobj->spec."":""; 
                   $itemname = $invitemobj->product.$desc1.$desc2.$thickness.$spec;
                   
                   $qty = round($invitemobj->qty, 4, PHP_ROUND_HALF_UP);
                   $roundedQty = sprintf("%.4f", $qty);
                   
                   $rate = round($invitemobj->rate, 2, PHP_ROUND_HALF_UP);
                   $roundedRate = sprintf("%.2f", $rate);
                   
                   $taxableValue = $roundedQty * $roundedRate;
                   $taxableValue = round($taxableValue, 2, PHP_ROUND_HALF_UP);
                   $roundedTaxableValue = sprintf("%.2f", $taxableValue);
                   
                   $cgstRate = $roundedRate * ($invitemobj->cgstpct / 100);
                   $roundcgstRate = round($cgstRate, 2, PHP_ROUND_HALF_UP);
                   $roundedcgstRate = sprintf("%.2f", $roundcgstRate);
                   
                   $sgstRate = $roundedRate * ($invitemobj->sgstpct / 100);
                   $roundsgstRate = round($sgstRate, 2, PHP_ROUND_HALF_UP);
                   $roundedsgstRate = sprintf("%.2f", $roundsgstRate);
                   
                   $cgstVal = $roundedTaxableValue * ($invitemobj->cgstpct / 100);
                   $roundcgstVal = round($cgstVal, 2, PHP_ROUND_HALF_UP);
                   $roundedcgstVal = sprintf("%.2f", $roundcgstVal);
                   
                   $sgstVal = $roundedTaxableValue * ($invitemobj->sgstpct / 100);
                   $roundsgstVal = round($sgstVal, 2, PHP_ROUND_HALF_UP);
                   $roundedsgstVal = sprintf("%.2f", $roundsgstVal);
                   
                   $itemRate = $roundedRate + $roundedcgstRate + $roundedsgstRate;
                   $roundItemRate = round($itemRate, 2, PHP_ROUND_HALF_UP);
                   $roundedItemRate = sprintf("%.2f", $roundItemRate);
                   
                   $itemValue = $roundedTaxableValue + $roundedcgstVal + $roundedsgstVal;
                   $roundItemValue = round($itemValue, 2, PHP_ROUND_HALF_UP);
                   $roundeditemValue = sprintf("%.2f", $roundItemValue);
                   
                   $total_qty = $total_qty + $roundedQty;
                   $total_tot = $total_tot + $roundedTaxableValue;
                   $total_disc = 0;
                   
                   $total_gst_rate = $total_gst_rate + $invitemobj->sgstpct;
                   $tot_cgst_val = $tot_cgst_val + $roundedcgstVal;
                   $tot_igst_val = $tot_igst_val + $invitemobj->igstval;
                   $total_line_total = $total_line_total + $roundeditemValue ;
                   $total_taxable_amt = $total_taxable_amt + $roundedTaxableValue;
                   $total_sgst_amt = $total_sgst_amt + $roundedsgstVal;
                   $total_cgst_amt = $total_cgst_amt + $roundedcgstVal;
                   $all_total_val = $all_total_val + $roundeditemValue;
                   
                   
                   
                   
//                   $total = $invitemobj->qty * $invitemobj->rate;
//                   $total_qty = $total_qty + $invitemobj->qty;
//                   $total_tot = $total_tot + $total;
//                   $total_disc = 0;
//                   
//                   $total_gst_rate = $total_gst_rate + $invitemobj->sgstpct;
//                   $tot_cgst_val = $tot_cgst_val + $invitemobj->cgstval;
//                   $tot_igst_val = $tot_igst_val + $invitemobj->igstval;
//                   $lineTotal = $invitemobj->cgstval + $invitemobj->cgstval + $invitemobj->rate;
//                   $total_line_total = $total_line_total + $lineTotal ;
//                    
//                   $desc1 = isset($invitemobj->desc_1) && trim($invitemobj->desc_1) != "" ? " , ".$invitemobj->desc_1." mm" : "";
//                   $desc2 = isset($invitemobj->desc_2) && trim($invitemobj->desc_2) != "" ? " x ".$invitemobj->desc_2." mm" : "";
//                   $thickness = isset($invitemobj->thickness) && trim($invitemobj->thickness) != "" ? " , ".$invitemobj->thickness." mm" : "";
//                   $spec  = isset($invitemobj->spec) && trim($invitemobj->spec) !="" ? " ,spec-".$invitemobj->spec."":""; 
//                   $itemname = $invitemobj->product.$desc1.$desc2.$thickness.$spec;
//                   $roundTaxableAmt = round($invitemobj->taxable,2);
//                   $roundCgstAmt = round($invitemobj->cgstval,2);
//                   $roundSgstAmt = round($invitemobj->sgstval,2);
//                   $roundLineTotal = round($lineTotal,2);
//                   $roundQty = sprintf("%.4f",$invitemobj->qty);
//                   $tot_val = round($roundLineTotal * $roundQty,2);
//                   $total_taxable_amt = $total_taxable_amt + ($invitemobj->rate * $roundQty);
//                   $total_sgst_amt = $total_sgst_amt + ($roundSgstAmt * $roundQty);
//                   $total_cgst_amt = $total_cgst_amt + ($roundCgstAmt * $roundQty);
//                   $all_total_val = $all_total_val + $tot_val;
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                   
                    $htmlstable .= '<tr>
                        <td style="align=right;font-size:10px;width:4%;">'.$srno.'</td>';
                        $htmlstable .= '<td style="align=left;font-size:10px;width:23%;">'.$itemname.', <b>'.$invitemobj->batchcode.'</b></td>';                        
                        $htmlstable .= '<td style="align=center;font-size:10px;width:8%;">'.trim($invitemobj->hsncode). '</td>
                        <td style="align=center;font-size:10px;width:5%;">MT</td>    
                        <td style="align=center;font-size:10px;width:5%;">'.$roundedQty.'</td>
                        <td style="align=right;font-size:10px;width:5%;">'.$spdf->Currency($roundedRate).'</td>
                        
                            
                        <td style="align=right;font-size:10px;width:7%;">'.$spdf->Currency($roundedTaxableValue).'</td>';
                        if($obj_cr->state == $customer_state_id || $customer_state_id == null){
                            
                            $htmlstable .='<td style="align=center;font-size:10px;width:2%;">'.trim($invitemobj->cgstpct).'</td>
                            <td style="align=right;font-size:10px;width:5%;">'.$spdf->Currency($roundedcgstRate).'</td>
                            <td style="align=right;font-size:10px;width:6%;">'.$spdf->Currency($roundedcgstVal).'</td>
                            
                            <td style="align=center;font-size:10px;width:2%;">'.trim($invitemobj->sgstpct).'</td>
                            <td style="align=right;font-size:10px;width:5%;">'.$spdf->Currency($roundedsgstRate).'</td>    
                            <td style="align=right;font-size:10px;width:6%;">'.$spdf->Currency($roundedsgstVal).'</td>
                            
                            <td style="align=right;font-size:10px;width:7%;">'.$spdf->Currency($roundedItemRate).'</td>    
                            <td style="align=right;font-size:10px;width:10%;">'.$spdf->Currency($roundeditemValue).'</td>    
                            
                            </tr>
                            '; 
                        }else if($customer_state_id != null){
                            $htmlstable .='<td style="align=left;font-size:10px;width:15%;">'.trim($invitemobj->igstpct).'</td>
                            <td style="align=left;font-size:10px;width:14%;">'.trim($invitemobj->igstval).'</td>
                            <td style="align=left;font-size:10px;width:6%;">'.trim(round($total)).'</td>
                            </tr>
                            '; 
                        }
                        
                    
                    if($cnt>19){
                        $cnt = 0;
                        $htmlstable .= '</table>';
                        $htmlstable.= $spdf->addPageFooter($pageno) . '
                        </page>';
                        $pageno++;
                        $htmlstable .= '<page>';
                        $htmlstable .= '<table style="width:100%;">';
                    }
               }
             
         }
        
        }
        
        if($cnt < $items_per_page){
            $remaining_cnt = $items_per_page - $cnt;
            for($i=1;$i<=$remaining_cnt;$i++){
                 $htmlstable .= '<tr>
                        <td style="align=left;font-size:10px;width:4%;"><br></td>   
                        <td style="align=left;font-size:10px;width:23%;"><br></td>                
                        <td style="align=left;font-size:10px;width:8%;"><br></td>
                        <td style="align=left;font-size:10px;width:5%;"><br></td>
                        <td style="align=left;font-size:10px;width:5%;"><br></td>
                        <td style="align=left;font-size:10px;width:5%;"><br></td>
                        
                       <td style="align=right; font-size:10px; width:7%;"><br></td>';
                        if($obj_cr->state == $customer_state_id || $customer_state_id == null){
                            $htmlstable .='<td style="align=left;font-size:10px;width:2%;"><br></td>
                            <td style="align=left;font-size:10px;width:5%;"><br></td>
                            <td style="align=left;font-size:10px;width:6%;"><br></td>
                            
                            <td style="align=left;font-size:10px;width:2%;"><br></td>
                             <td style="align=left;font-size:10px;width:5%;"><br></td>
                              <td style="align=left;font-size:10px;width:6%;"><br></td>
                              
                             <td style="align=left;font-size:10px;width:7%;"><br></td>
                             <td style="align=left;font-size:10px;width:10%;"><br></td>
                            </tr>
                            '; 
                        }else if($customer_state_id == null){
                            $htmlstable .='<td style="align=left;font-size:10px;width:15%;"><br></td>
                            <td style="align=left;font-size:10px;width:14%;"><br></td>
                             <td style="align=left;font-size:10px;width:6%;"><br></td>
                            </tr>
                            '; 
                        }
            }
        }
        if($obj_cr->state == $customer_state_id || $customer_state_id == null){
            $totqty = $spdf->Currency($total_qty);
            $htmlstable .= $spdf->addColFooter($total_qty,$total_line_total,$all_total_val,$total_taxable_amt,$total_sgst_amt,$total_cgst_amt);
        }else if($customer_state_id == null){
            $htmlstable .= $spdf->addColFooterIGST($total_qty,$total_tot,$total_disc,$total_taxable_amt,$total_gst_rate,$tot_igst_val);
        }
        $htmlstable .= '</table>';
        $htmlstable .= '<table style="width:100%;">';
        $roundoff = 0;
        $net_invoice_value = $all_total_val;
        if($obj_cr->state == $customer_state_id || $customer_state_id == null){
            $htmlstable .= $spdf->addTransactionDetailsFooter($total_taxable_amt,$net_invoice_value,$total_sgst_amt,
                    $total_cgst_amt,$roundoff,0,0);            
        }else if($customer_state_id == null){
            $htmlstable .= $spdf->addTransactionDetailsFooterIGST($total_taxable_value,$net_invoice_value,$tot_igst_val,
                    $roundoff,0,0);                            
        }
        $htmlstable .= '</table>';
        $htmlstable .= '<table style="width:100%;">';
        $htmlstable .= '</table>';
        $htmlstable .= '<table style="width:100%;">';
        $htmlstable .= '</table>';
        $htmlstable .= '<table style="width:100%;">';
        $htmlstable .= $spdf->addFooter($obj_inv_header->discount);
        $count=0;
        $itemcount=0;
        $totalunits=0;

        $htmlstable .= '</table>';
        $htmlstable.= $spdf->addPageFooter($pageno);                        
        $htmlstable.=  '</page>';
        //print $htmlstable;
        $html2fpdf = new HTML2PDF('P', 'A4', 'en');
//        echo $htmlstable;
//        return;
        $html2fpdf->writeHTML($htmlstable);
        $pdfname="$invoice_no.pdf";
        
        $File = $pdfname;
        //header('Content-type: application/pdf');
        $html2fpdf->Output("test.pdf", "O");
        

}catch(Exception $xcp){
    $xcp->getMessage();  
}