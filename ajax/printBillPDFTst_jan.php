<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/logger/clsLogger.php";
require_once "lib/email/EmailHelper.php";
require_once "lib/db/DBLogic.php";
require_once "lib/showPDF/showPDFJAN.php";
require_once "Classes/html2pdf/html2pdf.class.php";
require_once "lib/core/strutil.php";
//print_r($_GET);
//$_SESSION['form_get'] = $_GET;
extract($_GET);

try{
  $tot_sgst_val = 0;
  $tot_cgst_val = 0;
  $tot_igst_val = 0;
  $userid = getCurrStoreId();
  
  $dbLogic = new DBLogic();
    
$html2fpdf = new HTML2PDF('P', 'A4', 'en');
   
        $spdf = new showPDFJAN();
      //print "here2";
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
        $type_text = "TAX INVOICE";
        
        $obj_inv_header = $dbLogic->getINVHeaderPDetails($invid,$userid);
        $invoice_no = $obj_inv_header->invoice_no;
        //$invoice_dt = ddmmyy($obj_inv_header->createtime);
        $invoice_dt = ddmmyy($obj_inv_header->saledate);
        $customer_name = "";
        $customer_address = "";
        $customer_gstno = "";
        $customer_panno = "";
        $customer_state_id = null;
        if($obj_inv_header->customer_id != null && $obj_inv_header->customer_id > 0){
            $obj_customer = $dbLogic->getCustomerById($obj_inv_header->customer_id);
            if($obj_customer != NULL){
                $customer_name = $obj_customer->name;
                $customer_address = $obj_customer->address;
                $customer_gstno = $obj_customer->gstno;
                $customer_panno = $obj_customer->panno;
                $customer_state_id = $obj_customer->state_id;
            }
        }

        $obj_cr = $dbLogic->getCRInfoById($crid);
        //print_r($obj_cr);
//        $dist_name = "Sarotam Industrial Goods Retail <br> Distribution Private Limited";
//        $dist_gstno = "27AAYCS5917P1Z5";
//        $dist_panno = "";
//        $dist_addr = "128, Shiv Hari Complex, Opposite Giga Space,<br>206, Viman Nagar, Pune - 411 014<br>CIN: U52609 PN2017 PTC170198";
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
                $dist_name,$dist_addr,$dist_gstno,$dist_panno,$type_text);
        $htmlstable .= '</table>';
        $htmlstable .= '<table style="width:100%;">';
        if($obj_cr->state == $customer_state_id || $customer_state_id == null){
            $htmlstable .= $spdf->addColHeader();
        }else if($customer_state_id != null){
            $htmlstable .= $spdf->addColHeaderIGST();
        }
        $invitemsobjs = $dbLogic->getInvoiceItems($invid,$userid);
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
                   $cnt++;$srno++;
                   $total = $invitemobj->qty * $invitemobj->rate;
                   $total_qty = $total_qty + $invitemobj->qty;
                   $total_tot = $total_tot + $total;
                   $total_disc = 0;
                   //$lineTotal = 0;
                   
                   $total_gst_rate = $total_gst_rate + $invitemobj->sgst_percent;
                   $tot_cgst_val = $tot_cgst_val + $invitemobj->cgst_amt;
                   $tot_igst_val = $tot_igst_val + $invitemobj->igst_amt;
                   //$lineTotal = $invitemobj->cgst_amt + $invitemobj->cgst_amt + $invitemobj->taxable;
                   $lineTotal = $invitemobj->cgst_amt + $invitemobj->cgst_amt + $invitemobj->rate;
                   $total_line_total = $total_line_total + $lineTotal ;
                    
                   $desc1 = isset($invitemobj->desc_1) && trim($invitemobj->desc_1) != "" ? " , ".$invitemobj->desc_1." mm" : "";
                   $desc2 = isset($invitemobj->desc_2) && trim($invitemobj->desc_2) != "" ? " x ".$invitemobj->desc_2." mm" : "";
                   $thickness = isset($invitemobj->thickness) && trim($invitemobj->thickness) != "" ? " , ".$invitemobj->thickness." mm" : "";
                   $spec  = isset($invitemobj->spec) && trim($invitemobj->spec) !="" ? " ,spec-".$invitemobj->spec."":""; 
                   $itemname = $invitemobj->product.$desc1.$desc2.$thickness.$spec;
                   $roundTaxableAmt = round($invitemobj->taxable,2);
                   $roundCgstAmt = round($invitemobj->cgst_amt,2);
                   $roundSgstAmt = round($invitemobj->sgst_amt,2);
                   $roundLineTotal = round($lineTotal,2);
                   $roundQty = round($invitemobj->qty,2);
                   $tot_val = round($roundLineTotal * $roundQty,2);
                   $total_taxable_amt = $total_taxable_amt + ($invitemobj->rate * $roundQty);
                   $total_sgst_amt = $total_sgst_amt + ($roundSgstAmt * $roundQty);
                   $total_cgst_amt = $total_cgst_amt + ($roundCgstAmt * $roundQty);
                   $all_total_val = $all_total_val + $tot_val;
                   //echo $roundLineTotal;
                                                               //echo $itemname;
                                                             //</br><b><?php echo $item->batchcode;;
                   
                    $htmlstable .= '<tr>
                        <td style="align=right;font-size:10px;width:4%;">'.$srno.'</td>
                        ';
                        //$htmlstable .= '<td style="align=left;font-size:10px;width:23%;">'.trim($invitemobj->product).'</td>';
                        $htmlstable .= '<td style="align=left;font-size:10px;width:23%;">'.$itemname.', <b>'.$invitemobj->batchcode.'</b></td>';                        
                        $htmlstable .= '<td style="align=center;font-size:10px;width:8%;">'.trim($invitemobj->hsncode). '</td>
                        <td style="align=center;font-size:10px;width:5%;">KG</td>    
                        <td style="align=center;font-size:10px;width:5%;">'.$spdf->Currency($roundQty).'</td>
                        <td style="align=right;font-size:10px;width:5%;">'.$spdf->Currency(number_format((float)trim($invitemobj->rate), 2, '.', '')).'</td>
                        
                            
                        <td style="align=right;font-size:10px;width:7%;">'.$spdf->Currency(number_format((float)trim($invitemobj->rate * $roundQty), 2, '.', '')).'</td>';
                        if($obj_cr->state == $customer_state_id || $customer_state_id == null){
                            
                            $htmlstable .='<td style="align=center;font-size:10px;width:2%;">'.trim($invitemobj->cgst_percent).'</td>
                            <td style="align=right;font-size:10px;width:5%;">'.$spdf->Currency(number_format((float)$roundCgstAmt, 2, '.', '')).'</td>
                            <td style="align=right;font-size:10px;width:6%;">'.$spdf->Currency(number_format((float)$invitemobj->cgst_amt * $roundQty, 2, '.', '')).'</td>
                            
                            <td style="align=center;font-size:10px;width:2%;">'.trim($invitemobj->sgst_percent).'</td>
                            <td style="align=right;font-size:10px;width:5%;">'.$spdf->Currency(number_format((float)$roundSgstAmt, 2, '.', '')).'</td>    
                            <td style="align=right;font-size:10px;width:6%;">'.$spdf->Currency(number_format((float)$invitemobj->sgst_amt * $roundQty, 2, '.', '')).'</td>
                            
                            <td style="align=right;font-size:10px;width:7%;">'.$spdf->Currency(number_format((float)$roundLineTotal, 2, '.', '')).'</td>    
                            <td style="align=right;font-size:10px;width:10%;">'.$spdf->Currency(number_format((float)$tot_val, 2, '.', '')).'</td>    
                            
                            </tr>
                            '; 
                        }else if($customer_state_id != null){
                            $htmlstable .='<td style="align=left;font-size:10px;width:15%;">'.trim($invitemobj->igst_percent).'</td>
                            <td style="align=left;font-size:10px;width:14%;">'.trim($invitemobj->igst_amt).'</td>
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
//                            echo "hhhhhhhhhhh";
                            
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
        $net_invoice_value = $obj_inv_header->total_amount;
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
//        $htmlstable .= $spdf->addModeOfPaymentTitle();
        $htmlstable .= '</table>';
        $htmlstable .= '<table style="width:100%;">';
        $htmlstable .= $spdf->addFooter();
        $count=0;
        $itemcount=0;
        $totalunits=0;

        $htmlstable .= '</table>';
        $htmlstable.= $spdf->addPageFooter($pageno);                        
        $htmlstable.=  '</page>';
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