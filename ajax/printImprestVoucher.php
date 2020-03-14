<?php
require_once("../../it_config.php");
require_once("session_check.php");
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/logger/clsLogger.php";
require_once "lib/email/EmailHelper.php";
require_once "lib/db/DBLogic.php";
require_once "lib/showPDF/showPDF.php";
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
  
  $width_in_inches = 6;
  $height_in_inches = 16;
  $width_in_mm = $width_in_inches * 25.4; 
$height_in_mm = $height_in_inches * 25.4;

//    $html2pdf = new HTML2PDF('P', array($width_in_mm, $height_in_mm), 'en', true, 'UTF-8', array(0, 0, 0, 0));
$html2pdf = new HTML2PDF('L', 'A5', 'en');
   
        $spdf = new showPDF();
      //print "here2";
        
        $impObj = $dbLogic->getImpDetaildById($impDetailsId);
        
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
        
        
        $htmlstable = '
            <table cellspacing="0" cellpadding="1" border="1" >
            
            <tr>
                <td>
                <table border="0">
                    <tr>
                        <td  align="center" colspan="4"><font size="50"><b>Imprest Voucher</b></font></td>
                    </tr>
                    <tr>
                        <td style="width:80px" style="height:50px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date & Time:</td>
                            <td style="width:300px" >'.$impObj->ctime .'</td>
                        <td style="width:80px" > Voucher No.:</td>
                            <td style="width:200px">'.$impObj->voucher_no .'</td>
                    </tr>
                    <tr>
                        <td style="width:90px" style="height:50px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Description: </td>
                            <td style="width:300px">'.$impObj->description .'</td>
                        <td style="width:90px">Amount (Rs.): </td>
                            <td style="width:200px">'.$impObj->amount . '/-</td>
                    </tr>
                    
                 </table>
                 </td>
            </tr>     
            </table>    
';
        $htmlstable = "<br><br>".$htmlstable;
        
//        return;
//        $html2fpdf = new HTML2PDF('P', 'A4', 'en');
//        echo $htmlstable;
//        return;
        $html2pdf->writeHTML($htmlstable);
        $pdfname="$impObj->voucher_no.pdf";
        $pdfname = str_replace("/","-",$pdfname);
        $File = $pdfname;
        //header('Content-type: application/pdf');
        $html2pdf->Output("$pdfname","0");
        

}catch(Exception $xcp){
    $xcp->getMessage();  
}