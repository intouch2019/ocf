<?php
require_once "Classes/html2pdf/html2pdf.class.php";


class showPDFCN {
                      

/*
    <td style="align=left;width:50%;height:100px;">To,<br>'.$dealer_name.'<br>'.$this->breakText($dealer_addr).'</td>
    <td style="align=left;width:50%;height:100px;">From,<br>'.$dist_name.'<br>'.$this->breakText($dist_addr).'</td>                
 *  */    
    
      function addTableHeader($invoice_no,$invoice_dt,$dealer_name,$dealer_addr,$dealer_gstno,$dealer_panno,$state,$dist_name,$dist_addr,
              $dist_gstno,$dist_panno,$type_text,$ref_invno,$ref_invdate) { 
          $invarr = explode("-", $invoice_no);
          $invoicerefno = $invarr[0];
          $invoiceno = $invarr[1];
        $table_heading = '<tr>
                <th style="align=center;width:100%;height:30px;font-size:20px;" colspan="2"><b>'.$type_text.'</b></th>
                </tr>
                <tr>
                <td style="align=left;width:50%;height:30px;font-size:20px;"><b>CREDITNOTE NO: '.$invoice_no.'</b></td>
                <td style="align=left;width:50%;height:30px;font-size:20px;"><b>DATE: '.$invoice_dt.'</b></td>                
                </tr>
                <tr>
                <td style="align=left;width:50%;height:10px;font-size:10px;"><b>INVOICE NO: '.$ref_invno.'</b></td>
                <td style="align=left;width:50%;height:10px;font-size:10px;"><b>INVOICE DATE: '.$ref_invdate.'</b></td>                
                </tr>
                <tr>
                <td style="align=left;width:50%;height:100px;">From and place of supply,<br><br>'.$dealer_name.'<br>'.$dealer_addr.'<br>State : '.$state.'</td>          
                <td style="align=left;width:50%;height:100px;">To,<br>'.$dist_addr.'</td>                    
                </tr>
                <tr>
                <td style="align=left;width:50%;">GSTIN No: '.$dealer_gstno.'</td>
                <td style="align=left;width:50%;">GSTIN No: '.$dist_gstno.'</td>                
                                    
                </tr>
                <tr>
                <td style="align=left;width:50%;">PAN No: '.$dealer_panno.'</td>                    
                <td style="align=left;width:50%;">PAN No: '.$dist_panno.'</td>                
                
                </tr>';
        return $table_heading;
    }
    
    
    function addColHeader(){
        return '<tr>
                <th style="align=center;font-size:10px;width:4%;" rowspan="2"><b>SNo </b></th>
                <th style="align=center;font-size:10px;width:23%;"rowspan="2"><b>Desc</b></th>
                <th style="align=center;font-size:10px;width:8%;" rowspan="2"><b>HSN</b></th>
                <th style="align=center;font-size:10px;width:5%;" rowspan="2"><b>UOM</b></th>
                <th style="align=center;font-size:10px;width:5%;" rowspan="2"><b>Qty</b></th>
                <th style="align=center;font-size:10px;width:5%;" rowspan="2"><b>Base Rate (Rs/MT)</b></th>
                
                <th style="align=center;font-size:10px;width:7%;" rowspan="2"><b>Taxable Value</b></th>
                <th style="align=center;font-size:10px;width:13%;" colspan="3"><b>CGST</b></th>
                <th style="align=center;font-size:10px;width:13%;" colspan="3"><b>SGST</b></th>
                <th style="align=center;font-size:10px;width:7%;" rowspan="2" ><b>Rate<br/>(Rs/MT)</b></th>
                <th style="align=center;font-size:10px;width:10%;" rowspan="2"><b>Total(Rs)</b></th>
                
                </tr>
                <tr>
                    <th style="align=center;font-size:10px;width:2%;"><b>%</b></th>
                    <th style="align=center;font-size:10px;width:5%;"><b>Rs/MT</b></th>
                    <th style="align=center;font-size:10px;width:6%;"><b>Amt</b></th>
                    
                    <th style="align=center;font-size:10px;width:2%;"><b>%</b></th>
                    <th style="align=center;font-size:10px;width:5%;"><b>Rs/MT</b></th>
                    <th style="align=center;font-size:10px;width:6%;"><b>Amt</b></th>
                </tr>
                ';
    }
    
    function addColHeaderIGST(){
        return '<tr>
                <th style="align=center;font-size:10px;width:4%;" rowspan="2"><b>SNo </b></th>
                <th style="align=center;font-size:10px;width:23%;"rowspan="2"><b>Desc</b></th>
                <th style="align=center;font-size:10px;width:8%;" rowspan="2"><b>HSN</b></th>
                <th style="align=center;font-size:10px;width:5%;" rowspan="2"><b>Unit Rate (Rs/kg)</b></th>
                <th style="align=center;font-size:10px;width:5%;" rowspan="2"><b>Unit</b></th>
                <th style="align=center;font-size:10px;width:5%;" rowspan="2"><b>Qty</b></th>
                
                <th style="align=center;font-size:10px;width:5%;" rowspan="2"><b>Base Rate (Rs/kg)</b></th>
                <th style="align=center;font-size:10px;width:10%;" rowspan="2" ><b>Taxable Value</b></th>
                <th style="align=center;font-size:10px;width:29%;" colspan="2"><b>IGST</b></th>
                <th style="align=center;font-size:10px;width:9%;" rowspan="2"><b>Total</b></th>
                </tr>
                <tr>
                    <th style="align=center;font-size:10px;width:15%;"><b>Rate%</b></th>
                    <th style="align=center;font-size:10px;width:14%;"><b>Amt</b></th>
                </tr>
                ';
    }    
    
    
     function addColFooter($total_qty,$total_tot,$all_total_val,$total_taxable_amt,$total_sgst_amt,$total_cgst_amt){
         $roundtotal_tot = round($total_tot,2);
         $roundtotal_taxable_amt = number_format((float)$total_taxable_amt, 2, '.', '');
        return '<tr>
                <th style="align=center;font-size:10px;width:4%;"></th>
                <th style="align=center;font-size:10px;width:23%;"><b>Total</b></th>                
                <th style="align=center;font-size:10px;width:8%;"><b></b></th>
                <th style="align=center;font-size:10px;width:5%;"><b></b></th>
                <th style="align=center;font-size:10px;width:5%;"><b></b></th>
                <th style="align=center;font-size:10px;width:5%;"><b></b></th>
                    
                <th style="align=center;font-size:10px;width:7%;"><b>'.$this->Currency($roundtotal_taxable_amt).'</b></th>
                
                <th style="align=center;font-size:10px;width:2%;"><b></b></th>
                <th style="align=center;font-size:10px;width:5%;"><b></b></th>
                <th style="align=right;font-size:10px;width:6%;"><b>'.$this->Currency(number_format((float)$total_cgst_amt, 2, '.', '')).'</b></th>
                    
                <th style="align=center;font-size:10px;width:2%;"><b></b></th>
                <th style="align=center;font-size:10px;width:5%;"><b></b></th>
                <th style="align=right;font-size:10px;width:6%;"><b>'.$this->Currency(number_format((float)$total_sgst_amt, 2, '.', '')).'</b></th>
                    
                <th style="align=right;font-size:10px;width:7%;"><b></b></th>    
                <th style="align=right;font-size:10px;width:10%;"><b>'.$this->Currency(number_format((float)$all_total_val, 2, '.', '')).'</b></th>    
                </tr>
                ';
    }
    /*<th style="align=center;font-size:10px;width:6%;"><b>'.trim(round($total_tot,2)).'</b></th>*/
    function addColFooterIGST($total_qty,$total_tot,$total_disc,$total_taxable_amt,$total_gst_rate,$total_igst_amt){
        return '<tr>
                <th style="align=center;font-size:10px;width:4%;"></th>
                <th style="align=center;font-size:10px;width:23%;"><b>Total</b></th>                
                <th style="align=center;font-size:10px;width:8%;"><b></b></th>
                <th style="align=center;font-size:10px;width:5%;"><b></b></th>
                <th style="align=center;font-size:10px;width:5%;"><b></b></th>
                <th style="align=center;font-size:10px;width:5%;"><b>'.trim($total_qty).'</b></th>
                <th style="align=center;font-size:10px;width:5%;"><b></b></th>
                
                <th style="align=center;font-size:10px;width:10%;"><b>'.trim(round($total_taxable_amt,2)).'</b></th>
                <th style="align=center;font-size:10px;width:15%;"><b></b></th>
                <th style="align=center;font-size:10px;width:14%;"><b>'.trim(round($total_igst_amt,2)).'</b></th>
                <th style="align=center;font-size:10px;width:6%;"><b>'.trim(round($total_tot,2)).'</b></th>
                </tr>
                ';
    }
    
      function addTransactionDetailsFooter($tot_taxable_value,$net_invoice_value,$tot_sgst_val,$tot_cgst_val,$roundoff,$cashdisc_pct=false,$cashdisc_val=false){
        
          $roundtot_taxable_value  = number_format((float)$tot_taxable_value, 2, '.', '');
          $roundnet_invoice_value  = number_format((float)$net_invoice_value, 2, '.', '');
          $roundtot_sgst_val = number_format((float)$tot_sgst_val, 2, '.', '');
          $roundtot_cgst_val = number_format((float)$tot_cgst_val, 2, '.', '');
          if(trim($cashdisc_pct)== "" || trim($cashdisc_pct) < 0){
              $cashdisc_pct = 0 ;              
          }
          
          if(trim($cashdisc_val)== "" || trim($cashdisc_val) < 0){
              $cashdisc_val = 0 ;              
          }
          $invoice_value = $net_invoice_value - $cashdisc_val;
          $round_invoice_val = round($invoice_value);
          $roundoff = round($round_invoice_val - $invoice_value ,2,PHP_ROUND_HALF_DOWN);
          
          $tot_tax = $tot_cgst_val + $tot_sgst_val;
        return '<tr>
                <td style="align=center;font-size:10px;width:50%;"></td>
                <td style="align=center;font-size:10px;width:25%;">TOTAL OF TAXABLE VALUE</td>
                <td style="align=right;font-size:10px;width:25%;">'.$this->Currency($roundtot_taxable_value).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:50%;"></td>
                <td style="align=center;font-size:10px;width:25%;">TOTAL OF CGST</td>
                <td style="align=right;font-size:10px;width:25%;">'.$this->Currency($roundtot_cgst_val).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:50%;"></td>
                <td style="align=center;font-size:10px;width:25%;">TOTAL OF SGST</td>
                <td style="align=right;font-size:10px;width:25%;">'.$this->Currency($roundtot_sgst_val).'</td>                
                </tr>                
                <tr>
                <td style="align=center;font-size:10px;width:50%;"></td>
                <td style="align=center;font-size:10px;width:25%;">NET VALUE</td>
                <td style="align=right;font-size:10px;width:25%;">'.$this->Currency($roundnet_invoice_value).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:50%;"></td>
                <td style="align=center;font-size:10px;width:25%;">ROUND OFF</td>
                <td style="align=right;font-size:10px;width:25%;">'.trim($roundoff).'</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:50%;"></td>
                <td style="align=center;font-size:10px;width:25%;"><b>CREDIT NOTE VALUE</b></td>
                <td style="align=right;font-size:10px;width:25%;"><b>'.$this->Currency($round_invoice_val).'</b></td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:50%;">Total Invoice Value(In Words): &nbsp;Rupees &nbsp;'.$this->convert_number_to_words($round_invoice_val).'</td>
                <td style="align=center;font-size:10px;width:25%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:25%;">&nbsp;</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:50%;">Total TAX Value(In Figure):&nbsp;'.trim(round($tot_tax,2)).'</td>
                <td style="align=center;font-size:10px;width:25%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:25%;">&nbsp;</td>                
                </tr>';
    }

      function addTransactionDetailsFooterIGST($tot_taxable_value,$net_invoice_value,$tot_igst_val,
              $roundoff,$cashdisc_pct=false,$cashdisc_val=false){          
          if(trim($cashdisc_pct)== "" || trim($cashdisc_pct) < 0){
              $cashdisc_pct = 0 ;              
          }
          
          if(trim($cashdisc_val)== "" || trim($cashdisc_val) < 0){
              $cashdisc_val = 0 ;              
          }
          $invoice_value = $net_invoice_value - $cashdisc_val;
          $round_invoice_val = round($invoice_value);
          $roundoff = round($round_invoice_val - $invoice_value ,2,PHP_ROUND_HALF_DOWN);
          
          $tot_tax = $tot_igst_val;
        return '<tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL OF TAXABLE VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($tot_taxable_value,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL OF IGST</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($tot_igst_val,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">NET VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($net_invoice_value,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">ROUND OFF</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($roundoff).'</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">INVOICE VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($round_invoice_val).'</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;">Total Invoice Value(In Words): &nbsp;Rupees &nbsp;'.$this->convert_number_to_words($round_invoice_val).'</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;">Total TAX Value(In Figure):&nbsp;'.trim(round($tot_tax,2)).'</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:22%;">&nbsp;</td>                
                </tr>
                ';
    }
    

      function addTransactionDetailsFooterForCDisc($tot_taxable_value,$net_invoice_value,$tot_sgst_val,$tot_cgst_val,$roundoff,$cashdisc_pct=false,$cashdisc_val=false,$tot_tax){          
          if(trim($cashdisc_pct)== "" || trim($cashdisc_pct) < 0){
              $cashdisc_pct = 0 ;              
          }
          
          if(trim($cashdisc_val)== "" || trim($cashdisc_val) < 0){
              $cashdisc_val = 0 ;              
          }
          //$invoice_value = $net_invoice_value - $cashdisc_val;
          $invoice_value = $net_invoice_value;
          $round_invoice_val = round($invoice_value);
          $roundoff = round($round_invoice_val - $invoice_value ,2,PHP_ROUND_HALF_DOWN);
          $taxable_after_cash_disc = $tot_taxable_value - $cashdisc_val;
          
          //$tot_tax = $tot_cgst_val + $tot_sgst_val;
        return '<tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL OF TAXABLE VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($tot_taxable_value,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">CASH DISC %</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($cashdisc_pct).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">CASH DISC VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($cashdisc_val).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">SUB TOTAL</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($taxable_after_cash_disc,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL TAX</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($tot_tax,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">NET VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($net_invoice_value,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">ROUND OFF</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($roundoff).'</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">INVOICE VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($round_invoice_val).'</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;">Total Invoice Value(In Words): &nbsp;Rupees &nbsp;'.$this->convert_number_to_words($round_invoice_val).'</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;">Total TAX Value(In Figure):&nbsp;'.trim(round($tot_tax,2)).'</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:22%;">&nbsp;</td>                
                </tr>
                ';
    }

    
      function addTransactionDetailsFooterForCDiscIGST($tot_taxable_value,$net_invoice_value,$tot_igst_val,
              $roundoff,$cashdisc_pct=false,$cashdisc_val=false,$tot_tax){          
          if(trim($cashdisc_pct)== "" || trim($cashdisc_pct) < 0){
              $cashdisc_pct = 0 ;              
          }
          
          if(trim($cashdisc_val)== "" || trim($cashdisc_val) < 0){
              $cashdisc_val = 0 ;              
          }
          //$invoice_value = $net_invoice_value - $cashdisc_val;
          $invoice_value = $net_invoice_value;
          $round_invoice_val = round($invoice_value);
          $roundoff = round($round_invoice_val - $invoice_value ,2,PHP_ROUND_HALF_DOWN);
          $taxable_after_cash_disc = $tot_taxable_value - $cashdisc_val;
          
          //$tot_tax = $tot_cgst_val + $tot_sgst_val;
        return '<tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL OF TAXABLE VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($tot_taxable_value,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">CASH DISC %</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($cashdisc_pct).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">CASH DISC VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($cashdisc_val).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">SUB TOTAL</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($taxable_after_cash_disc,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL TAX</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($tot_tax,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">NET VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($net_invoice_value,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">ROUND OFF</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($roundoff).'</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">INVOICE VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($round_invoice_val).'</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;">Total Invoice Value(In Words): &nbsp;Rupees &nbsp;'.$this->convert_number_to_words($round_invoice_val).'</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>                
                </tr>
                <tr>
                <td style="align=left;font-size:10px;width:60%;">Total TAX Value(In Figure):&nbsp;'.trim(round($tot_tax,2)).'</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:22%;">&nbsp;</td>                
                </tr>
                ';
    }
    
    
    function addModeOfPaymentTitle(){
        return '
               <tr>
                 <td style="align=left;font-size:10px;width:102%;"><b>Mode(s) of Payment</b></td>
               </tr>                             
                ';
    }
    
    function addModesOfPayment($payment_mode,$amt){
        return '              
               <tr>
                <td style="align=left;font-size:10px;width:80%;">'.trim($payment_mode).'</td>
                <td style="align=center;font-size:10px;width:22%;">'.trim($amt).'</td>                
                </tr>                
                ';
    }
    
    function addFooter($disc){
        $imgurl = "../images/rajesh.jpg";
        return '<tr>
                <td style="align=left;font-size:10px;width:75%;height:80px;">'.$disc.'% rate reduction for returned material.<br/><br/>E.&.O.E.</td>
                <td style="align=center;font-size:10px;width:25%;height:20px;"><img src=' . $imgurl . ' width="1%"/><br/><br/>Authorised Signatory</td>                
                </tr>';
    }
    


    
    function addPageFooter($pageno=1) {
        return '<page_footer>
                    <p style="font-size:12px;" align="center">Page ' . $pageno . '<br/>
                    Corporate Office : Sarotam Industrial Goods Retail Distribution Private Limited <br/>CIN : U52609 PN2017 PTC170198<br/>401, East Court, Phoenix Market City,<br/> 207, Viman Nagar, Pune 400194, Maharashtra, India    
                    </p>
                </page_footer>';
        
        
       
    }

    function breakText($text){
        $arr_text = array();
        $textToSend = "";
        if(strlen($text) > 251){
            $arr_text = str_split($text,251);
            for($i=0; $i<sizeof($arr_text); $i++){
                $textToSend = $textToSend . $arr_text[$i] . '<br/>';
            }
            return $textToSend;
        }else{
            return $text;
        }
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
    
    function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
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
    
}


