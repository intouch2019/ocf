<?php
error_log("\nMSL daterange queryhiii: \n",3,"tmp.txt");
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();

// $crid = $currStore->crid;
$aColumns = array('dispname','voucher_no', 'prev_bal', 'amount', 'curr_bal' ,'description','reason','byuser','ctime','action');
$sColumns = array('r.dispname','i.voucher_no', 'i.description','i.ctime','i.prev_bal','i.amount','i.curr_bal');

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
$sOrder = " order by i.ctime desc ";
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
$sWhere .= " u.id= i.by_user and r.id = i.crid and i.ctime > '$FromDate' and i.ctime < '$ToDate' and i.crid = $crid";
$sQuery = "SELECT SQL_CALC_FOUND_ROWS i.id,r.dispname,i.prev_bal,i.amount,i.curr_bal,i.voucher_no,i.description,i.reason,u.name as byuser,i.ctime FROM it_imprest_details i, it_rfc_master r,it_users u 
        $sWhere 
	$sOrder
	$sLimit
";
//print_r($sQuery);
//return;
//console.log($sQuery);
//error_log("\n imprest_report_query : $sQuery \n",3,"../ajax/imprest_tmp.txt");
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
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'dispname') {
                 $row[] = $obj->dispname;
             }else if ($aColumns[$i] == 'voucher_no') {
                 $row[] = $obj->voucher_no;
             }else if ($aColumns[$i] == 'prev_bal') {
                 $row[] = $obj->prev_bal;
             }else if ($aColumns[$i] == 'amount') {
                 $row[] = $obj->amount;
             }else if ($aColumns[$i] == 'curr_bal') {
                 $row[] = $obj->curr_bal;
             }else if ($aColumns[$i] == 'description') {
                 $row[] = $obj->description;
             }else if ($aColumns[$i] == 'reason') {
                 if($obj->reason == ImprestReason::Out){
                     $row[] = "Out";
                 }else if($obj->reason == ImprestReason::In){
                     $row[] = "In";
                 }else if($obj->reason == 0){
                     $row[] = "Out";
                 }else{
                     $row[] = "-";
                 }
             }else if ($aColumns[$i] == 'byuser') {
                 $row[] = $obj->byuser;
             }else if ($aColumns[$i] == 'ctime') {
                 $row[] = $obj->ctime;
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
	
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $rows
);

echo json_encode( $output );
?>
