<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$userid = $currStore->id;
$crid = $currStore->crid;
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('invoiceno','createtime','cname','cphone','totqty','totvalue','action');
$sColumns = array('i.invoice_no','i.createtime', 'i.cname', 'i.cphone', 'i.total_qty','i.total_amount');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$dbl = new DBLogic();
$status = isset($_GET['status']) ? $_GET['status'] : false;

/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
	$sLimit = " LIMIT ".$db->getConnection()->real_escape_string( $_GET['iDisplayStart'] ).", ".
		$db->getConnection()->real_escape_string( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = " order by i.id desc ";
if ( isset( $_GET['iSortCol_0'] ) )
{
	$sOrder = " ORDER BY  ";
	for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
	{
		if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
		{
			$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
			 	".$db->getConnection()->real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
		}
	}
	
	$sOrder = substr_replace( $sOrder, "", -2 );
	if ( $sOrder == " ORDER BY " )
	{
		$sOrder = "";
	}
}


/* 
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */

$sWhere = "";
if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
{
	$sWhere = "WHERE (";
	for ( $i=0 ; $i<count($sColumns) ; $i++ )
	{
		$sWhere .= $sColumns[$i]." LIKE '%".$db->getConnection()->real_escape_string( $_GET['sSearch'] )."%' OR ";
	}
	$sWhere = substr_replace( $sWhere, "", -3 );
	$sWhere .= ')';
}

/* Individual column filtering */
for ( $i=0 ; $i<count($sColumns) ; $i++ )
{
	if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && isset($_GET['sSearch_'.$i]) && $_GET['sSearch_'.$i] != '' )
	{
		if ( $sWhere == "" )
		{
			$sWhere = "WHERE ";
		}
		else
		{
			$sWhere .= " AND ";
		}
		$sWhere .= $sColumns[$i]." LIKE '%".$db->getConnection()->real_escape_string($_GET['sSearch_'.$i])."%' ";
	}
}

/*
 * SQL queries
 * Get data to display
 */

//$aColumns = array('invoiceno','cname','cphone','totqty','tottax','totvalue','action');
//$status = InvoiceStatus::Created;
$tablename = $dbl->getSalesTableName($userid);
$sWhere = " where i.status = $status and i.crid = $crid";
$sQuery = "
	select SQL_CALC_FOUND_ROWS i.id,i.customer_id,i.invoice_no,i.saledate,i.createtime,i.cname,i.cphone,i.total_qty,i.total_tax,i.total_amount,i.uom_id from $tablename i
	$sWhere 
	$sOrder
	$sLimit
";
// echo $sQuery;
//error_log("\nSalesQry query: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); $iTotal=0;
foreach ($objs as $obj){
        $tot_stk = 0;
	$row = array();
        
//$aColumns = array('invoiceno','cname','cphone','totqty','tottax','totvalue','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
            $custid = 0;
            if(isset($obj->customer_id)){
                $custid = $obj->customer_id;
            }
             if ($aColumns[$i] == 'invoiceno') {
                 $row[] = $obj->invoice_no;
             }else if ($aColumns[$i] == 'createtime') {
                 $row[] = ddmmyy($obj->saledate);
             }else if ($aColumns[$i] == 'cname') {
                 $row[] = $obj->cname;
             }else if($aColumns[$i] == 'cphone'){
                 $row[] = $obj->cphone;
             }else if($aColumns[$i] == 'totqty'){
                 $row[] = round($obj->total_qty,4);
             }else if($aColumns[$i] == 'totvalue'){
                 $round_invoice_val = round($obj->total_amount);
                 $row[] = $round_invoice_val;
             }else if($aColumns[$i] == 'action'){

                if($status == InvoiceStatus::Created){ 
                    if(strtotime($obj->createtime) < strtotime("2019-01-10 00:00:00")){
                        $row[] = '<input type="button" class="btn btn-primary" name="pdf" value="View PDF" onclick="showPDFOLD('.$obj->id.','.$crid.')"/>';
                    }else if(strtotime($obj->createtime) > strtotime("2019-01-10 00:00:00") && strtotime($obj->createtime) < strtotime("2019-02-28 23:59:59") ){
                        $row[] = '<input type="button" class="btn btn-primary" name="pdf" value="View PDF" onclick="showPDFOld('.$obj->id.','.$crid.')"/>';
                   }
                    else if($obj->uom_id == "2"){
                        $row[] = '<input type="button" class="btn btn-primary" name="pdf" value="View PDF" onclick="showPDF('.$obj->id.','.$crid.')"/>'; 
                    }else{
                        $row[] = '<input type="button" class="btn btn-primary" name="pdf" value="View PDF" onclick="showPDFJAN('.$obj->id.','.$crid.')"/>'; 
                    }
                }else{
                 $row[] = '<input type="button" class="btn btn-primary" name="pdf" value="Edit" onclick="editSales('.$obj->id.','.$custid.')"/>';    
                }
             }else{
                 $row[] = "-";
             }   
       
	}
	$rows[] = $row;
	$iTotal++;

}

$db->closeConnection();
/*
 * Output
 */
$output = array(
	//"sEcho" => intval($_GET['sEcho']),
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $rows
);

echo json_encode( $output );
?>
