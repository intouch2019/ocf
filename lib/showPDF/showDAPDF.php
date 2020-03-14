<?php
require_once "Classes/html2pdf/html2pdf.class.php";


class showDAPDF {
    
      function addTableHeader($invoice_no,$invoice_dt,$dealer_name,$dealer_addr,$dealer_gstno,$dealer_panno,$dist_name,$dist_addr,$dist_gstno,$dist_panno,$type_text) {        
        return '<tr>
                <th style="align=center;width:102%;height:30px;font-size:20px;" colspan="2"><b>Debit Note</b></th>
                </tr>
                <tr>
                <th style="align=left;width:50%;height:30px;font-size:20px;"><b>DA NO: '.$invoice_no.'</b></th>
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
                </tr>';
    }
    
    
    function addColHeader(){
        return '<tr>
                <th style="align=left;font-size:10px;width:70%;" rowspan="1"><b>Particulars</b></th>
                <th style="align=center;font-size:10px;width:32%;" rowspan="1"><b>Amount</b></th>                
                </tr>
                ';
    }
    
    function addColFooter($amount){
        return '<tr>
                <th style="align=left;font-size:10px;width:70%;" rowspan="1"><b>Net Amount :</b></th>
                <th style="align=center;font-size:10px;width:32%;" rowspan="1"><b>'.$amount.'</b></th>                
                </tr>
                ';
    }
    
    
      function addTransactionDetailsFooter($amount){          
        return '<tr>
                <td style="align=left;font-size:10px;width:60%;">Total Amount(In Words): &nbsp;Rupees &nbsp;'.$this->convert_number_to_words($amount).'</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>
                <td style="align=center;font-size:10px;width:20%;">&nbsp;</td>                
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


