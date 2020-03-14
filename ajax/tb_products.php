<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();

$aColumns = array('id','category','prodsname','desc1','desc2','thickness','is_active');
$sColumns = array('p.id','c.name','p.shortname','p.desc1','p.desc2','p.thickness','p.active');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();

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
$sOrder = " order by p.id asc ";
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
//$aColumns = array('id','category','prodsname','desc1','desc2','thickness','is_active');

$sWhere .= " p.ctg_id = c.id $addClause ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS p.id , c.name as ctg, p.shortname as itemname, p.desc1, p.desc2, p.thickness, p.active  
	from it_products p , it_categories c 
	$sWhere 
	$sOrder
	$sLimit
";
//error_log("\nMSL query: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS        
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;
//$aColumns = array('id','category','prodsname','desc1','desc2','thickness','is_active');
$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{       $tot_stk = 0;
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
            if ($aColumns[$i] == 'id') {
                 $row[] = $obj->id;
             }else if ($aColumns[$i] == 'category') {
                 $row[] = $obj->ctg;
             }else if($aColumns[$i] == 'prodsname'){
                 $row[] = $obj->itemname;
             }else if($aColumns[$i] == 'desc1'){
                 $row[] = $obj->desc1;
             }else if($aColumns[$i] == 'desc2'){
                 $row[] = $obj->desc2;
             }else if($aColumns[$i] == 'thickness'){
                 $row[] = $obj->thickness;
             }else if($aColumns[$i] == 'is_active'){
                 if(trim($obj->active)==1){
                     $status = "Active";
                 }else{
                     $status = "Inactive";
                 }
                $row[] = $status;                
             }/*else if($aColumns[$i] == 'action'){
                 //$objserial= serialize($obj);
                 $row[] = '<button type="button" class="btn btn-primary" onclick="editProduct(' . $obj->id . ')">Edit</button>';            
             }*/else{
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
