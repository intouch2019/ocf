<?php
error_log("\nMSL daterange queryhiii: \n",3,"tmp.txt");
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();

// $crid = $currStore->crid;
$aColumns = array('crcode','amount','receipt_no','description','transactiontype','byuser','createtime');
$sColumns = array('r.dispname','d.amount', 'd.receipt_no','d.description','e.chargetypedesc','u.name');


$db = new DBConn();
$crid = isset($_GET['crid']) ? $_GET['crid'] : false;

$pid = isset($_GET['pid']) ? $_GET['pid'] : false;
$daterange=isset($_GET['uploaddate']) ? $_GET['uploaddate'] : false;

$dtrng= explode ("-", $daterange);
$frmdt=$dtrng[0];
$tdt=$dtrng[1];

$frmdt = str_replace("/", "-", $frmdt);
$tdt = str_replace("/", "-", $tdt);
$frmdt1 =explode ("-", $frmdt);
$tdt1 =explode ("-", $tdt);
$FromDate=trim($frmdt1[2])."-".trim($frmdt1[1])."-".trim($frmdt1[0])." 00:00:00";
$ToDate= trim($tdt1[2])."-".trim($tdt1[1])."-".trim($tdt1[0])." 23:59:59";
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
$sOrder = " order by d.ctime desc ";
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
 * SQL queriespageLength: 10
 * Get data to display
 */

if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}



//$queryy = "select * from it_rfc_master where id = $crid";
//
//$crobj = $db->fetchObject($queryy);
//$crcode = $crobj->crcode;
$sWhere .= " d.crid = r.id and d.by_user = u.id and d.payment_type = e.id and d.ctime > '$FromDate' and d.ctime < '$ToDate' and d.crid = $crid";
$sQuery = "SELECT SQL_CALC_FOUND_ROWS r.dispname , d.amount, d.receipt_no,d.description, e.chargetypedesc,u.name,d.ctime from it_deposit_diary d, it_extra_charges e, it_users u, it_rfc_master r 
        $sWhere 
	$sOrder
	$sLimit
";
//print_r($sQuery);
//return;
$objs = $db->fetchObjectArray($sQuery);


/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{      
    
    $aColumns = array('crcode','amount','receipt_no','description','transactiontype','byuser','createtime');
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'crcode') {
                 $row[] = $obj->dispname;
             }else if ($aColumns[$i] == 'amount') {
                 $row[] = $obj->amount;
             }else if ($aColumns[$i] == 'receipt_no') {
                 $row[] = $obj->receipt_no;
             }else if ($aColumns[$i] == 'description') {
                 $row[] = $obj->description;
             }else if ($aColumns[$i] == 'transactiontype') {
                 $transactionType = strtolower($obj->chargetypedesc);
                 $transactionMode = str_replace("charges", "", $transactionType);
                 $row[] = $transactionMode;
             }else if ($aColumns[$i] == 'byuser') {
                 $row[] = $obj->name;
             }else if ($aColumns[$i] == 'createtime') {
                 $row[] = $obj->ctime;
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
	
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $rows
);

echo json_encode( $output );
?>
