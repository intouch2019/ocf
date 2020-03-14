<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";


$currStore = getCurrStore();

$aColumns = array('voucher_no', 'prev_bal', 'amount', 'curr_bal', 'ledger', 'description','reason','action');
$sColumns = array('d.voucher_no', 'd.description','l.ledger');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$dbl = new DBLogic();

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
$sOrder = " order by id desc ";
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

//$DCobj = $dbl->getDCDetailsByUserId($userid);
$dcid = $currStore->dcid;
$sWhere = "where d.dcid = $dcid and  l.id=d.ledger_id";
$sQuery = "
	select SQL_CALC_FOUND_ROWS d.id, d.dcid, d.amount, d.prev_bal, d.curr_bal, d.voucher_no, l.ledger, d.description, d.reason, d.by_user, d.ctime from it_imprest_details d,it_imprest_ledger l
	$sWhere 
	$sOrder
	$sLimit
";//echo $sQuery;
//error_log("\nImprest Details query: ".$sQuery."\n",3,"tmp.txt");
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
            
             if ($aColumns[$i] == 'voucher_no') {
                 $row[] = $obj->voucher_no;
             }else if ($aColumns[$i] == 'prev_bal') {
                 $row[] = $obj->prev_bal;
             }else if ($aColumns[$i] == 'amount') {
                 $row[] = $obj->amount;
             }else if ($aColumns[$i] == 'curr_bal') {
                 $row[] = $obj->curr_bal;
             }else if ($aColumns[$i] == 'ledger') {
                 $row[] = $obj->ledger;
             }else if ($aColumns[$i] == 'description') {
                 $row[] = $obj->description;
             }else if ($aColumns[$i] == 'reason') {
                 if($obj->reason == ImprestReason::Out){
                     $row[] = "Out";
                 }else if($obj->reason == ImprestReason::In){
                     $row[] = "In";
                 }else{
                     $row[] = "-";
                 }
             }else if($aColumns[$i] == 'action'){
                $row[] = '<input type="button" class="btn btn-primary" name="pdf" value="View PDF" onclick="showPDF('.$obj->id.')"/>'; 
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
