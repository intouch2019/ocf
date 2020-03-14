<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();

$aColumns = array('id','prod_desc','desc1','desc2','thickness','length','currentStock','addedStock','total','date','approved');
$sColumns = array('p.id','p.name','p.desc1','p.desc2','p.thickness','p.length','p.addedstock', 'p.oldstock','ph.isApproved');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$crid = $_GET['crid'];
$status = $_GET['status'];
//$uploaddate = $_GET['uploaddate'];

/* 
 * Paging
 */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
	$sLimit = " LIMIT ".$db->getConnection()->real_escape_string( $_GET['iDisplayStart'] ).", ".
		$db->getConnection()->real_escape_string( $_GET['iDisplayLength'] );
}


/*
 * Ordering
 */
$sOrder = " order by p.id ";
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
$addClause = "";

if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}

$sWhere .= " ph.id = p.id and p.crid = $crid and ph.isApproved = $status $addClause ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS p.id ,p.prodid, p.name , p.desc1, p.desc2, p.thickness, p.length, p.addedstock, p.oldstock, round(p.addedstock + p.oldstock,4) as total, ph.requestDate,ph.isApproved from stockadjustmentItemDetails p , stockadjustmentHeader ph  
	$sWhere 
	$sOrder
	$sLimit
";

$objs = $db->fetchObjectArray($sQuery);
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS        
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); $iTotal=0;
$name = "";
foreach ($objs as $obj)
{       $tot_stk = 0;
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'id') {
                 $row[] = $obj->id;
             }
             else if($aColumns[$i] == 'prod_desc'){
                 $row[] = $obj->name;
                // $name = "'".$obj->name."'";
             }else if($aColumns[$i] == 'desc1'){
                 $row[] = $obj->desc1;
             }else if($aColumns[$i] == 'desc2'){
                 $row[] = $obj->desc2;
             }else if($aColumns[$i] == 'thickness'){
                 $row[] = $obj->thickness;
             }else if($aColumns[$i] == 'length'){
                 $row[] = $obj->length;
             }else if($aColumns[$i] == 'currentStock'){
                $row[] = $obj->oldstock; 
             }else if($aColumns[$i] == 'addedStock'){
                 $row[] = $obj->addedstock;
                          
             }else if($aColumns[$i] == 'total'){
                $row[] = $obj->total;                
             }else if($aColumns[$i] == 'date'){
                $row[] = ($obj->requestDate);  
             }
             else if($aColumns[$i] == 'approved'){
                 if($obj->isApproved == ProductPriceStatus::Pending){
                    $approvestatus = ProductPriceStatus::Approved;
                    $disapprovestatus = ProductPriceStatus::Disapproved;
                    $row[] = '<input type="radio" class="btn btn-primary" name="rdbapprove" id="rdbapprove" value="approve" onclick="approve('.$obj->prodid.','.$obj->id.','.$obj->addedstock.','.$approvestatus.')"/>Approve&nbsp;&nbsp;&nbsp;'
                            . '<input type="radio" class="btn btn-primary" name="rdbapprove" id="rdbapprove" value="disapprove" onclick="disapprove('.$obj->prodid.','.$obj->id.','.$disapprovestatus.')"/>DisApprove';
                 }else{
                     $status = ProductPriceStatus::getName($obj->isApproved);
                     $row[] = $status;
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
	"iTotalRecords" => $iTotal,
	"iTotalDisplayRecords" => $iFilteredTotal,
	"aaData" => $rows
);

echo json_encode( $output );
?>
