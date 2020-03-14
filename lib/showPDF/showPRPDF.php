<?php
require_once "Classes/html2pdf/html2pdf.class.php";
require_once "lib/core/strutil.php";

class showPRPDF {
    
      function addTableHeader($invoice_no,$invoice_dt,$dealer_name,$dealer_addr,$dealer_gstno,$dealer_panno,$dist_name,$dist_addr,$dist_gstno,$dist_panno,$type_text,$ttk_sap_odn_number,$ttk_sap_odn_date) {        
        return '<tr>
                <th style="align=center;width:102%;height:30px;font-size:20px;" colspan="2"><b>'.$type_text.'</b></th>
                </tr>
                <tr>
                <th style="align=left;width:50%;height:30px;font-size:20px;"><b>DC NO: '.$invoice_no.'</b></th>
                <th style="align=left;width:50%;height:30px;font-size:20px;"><b>DATE: '.$invoice_dt.'</b></th>                
                </tr>
                <tr>
                <td style="align=left;width:50%;height:100px;">From,<br>'.$dist_name.''.$dist_addr.'</td>                
                <td style="align=left;width:50%;height:100px;">To,<br>'.$dealer_name.'<br>'.$dealer_addr.'</td>                    
                </tr>
                <tr>
                <td style="align=left;width:50%;">GSTIN No: '.$dist_gstno.'</td>
                <td style="align=left;width:50%;">GSTIN No: '.$dealer_gstno.'</td>                
                </tr>
                <tr>
                <td style="align=left;width:50%;">PAN No: '.$dist_panno.'</td>
                <td style="align=left;width:50%;">PAN No: '.$dealer_panno.'</td>                
                </tr>
                <tr>
                <td style="align=left;width:50%;">Reference TTK Invoice No: '.strtoupper($ttk_sap_odn_number).'</td>
                <td style="align=left;width:50%;">Reference TTK Invoice Date: '.strtoupper(ddmmyy($ttk_sap_odn_date)).'</td>
                </tr>';
    }
    
    function addColHeader(){
        return '<tr>
                <th style="align=center;font-size:10px;width:5%;">SNo</th>                             
                <th style="align=center;font-size:10px;width:10%;">SKU</th>                
                <th style="align=center;font-size:10px;width:17%;">Desc</th>
                <th style="align=center;font-size:10px;width:10%;">HSN</th>
                <th style="align=center;font-size:10px;width:10%;">MRP</th>
                <th style="align=center;font-size:10px;width:5%;">Unit</th>
                <th style="align=center;font-size:10px;width:5%;">Qty</th>
                <th style="align=center;font-size:10px;width:10%;">Rate</th>
                <th style="align=center;font-size:10px;width:10%;">Gross Total</th>
                <th style="align=center;font-size:10px;width:10%;">Disc<br>ount</th>
                <th style="align=center;font-size:10px;width:10%;">Net Total</th>
                </tr>
                ';
    }
    
    function addColFooter($total_qty,$total_tot,$total_disc,$total_taxable_amt,$total_gst_rate,$total_gst_amt){
        return '<tr>
                <th style="align=center;font-size:10px;width:5%;"></th>
                <th style="align=center;font-size:10px;width:10%;"></th>                
                <th style="align=center;font-size:10px;width:17%;"></th>                
                <th style="align=center;font-size:10px;width:10%;"></th>                
                <th style="align=center;font-size:10px;width:10%;">Total</th>
                <th style="align=center;font-size:10px;width:5%;"></th>
                <th style="align=center;font-size:10px;width:5%;">'.trim($total_qty).'</th>
                <th style="align=center;font-size:10px;width:10%;"></th>
                <th style="align=center;font-size:10px;width:10%;"></th>
                <th style="align=center;font-size:10px;width:10%;"></th>
                <th style="align=center;font-size:10px;width:10%;">'.trim(round($total_tot,2)).'</th>
                </tr>
                ';
        
        /*      <th style="align=center;font-size:10px;width:5%;"><b>'.trim($total_qty).'</b></th>
                <th style="align=center;font-size:10px;width:10%;"><b></b></th>
                <th style="align=center;font-size:10px;width:10%;"><b></b></th>
                <th style="align=center;font-size:10px;width:10%;"><b></b></th>
                <th style="align=center;font-size:10px;width:10%;"><b>'.trim($total_tot).'</b></th>
*/
    }
    
    
      function addTransactionDetailsFooter($tot_taxable_value,$net_invoice_value,$tot_sgst_val,$tot_cgst_val,$roundoff,$cashdisc_pct=false,$cashdisc_val=false){          
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
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL OF TAXABLE VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim(round($tot_taxable_value,2)).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL OF CGST</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($tot_cgst_val).'</td>                
                </tr>
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">TOTAL OF SGST</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($tot_sgst_val).'</td>                
                </tr>                
                <tr>
                <td style="align=center;font-size:10px;width:60%;"></td>
                <td style="align=center;font-size:10px;width:20%;">NET VALUE</td>
                <td style="align=center;font-size:10px;width:20%;">'.trim($net_invoice_value).'</td>                
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
    
    function addFooter(){
        return '<tr>
                <td style="align=left;font-size:10px;width:70%;height:80px;">E.&.O.E.</td>
                <td style="align=center;font-size:10px;width:32%;height:20px;">Authorised Signatory</td>                
                </tr>';
    }
    


    
    function addPageFooter($pageno=1) {
        return '<page_footer>
                    <p align="center">Page ' . $pageno . '<br/>
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


