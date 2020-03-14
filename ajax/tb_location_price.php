<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();


$ctgid = isset($_GET['cid']) ? $_GET['cid'] : false;
$uomid = isset($_GET['uid']) ? $_GET['uid'] : false;
$pszid = isset($_GET['pid']) ? $_GET['pid'] : false;
$status = isset($_GET['sid']) ? $_GET['sid'] : false;



$aColumns = array('location','shopify_name','product','applicable_date','price','is_active','action');
$sColumns = array('l.name','p.shopify_name','p.name','lp.applicable_date','lp.price','p.is_active');
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
$sOrder = " order by lp.updatetime desc ";
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
if(trim($ctgid)!=""){
    $addClause .= " and c.id = $ctgid ";
}
if(trim($uomid)!=""){
    $addClause .= " and u.id = $uomid ";
}
if(trim($pszid)!=""){
    $addClause .= " and pz.id = $pszid ";
}
if(trim($status)!="" && trim($status)!="-1"){
    $addClause .= " and p.is_active = $status ";
}

if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}
$sWhere .= " lp.location_type_id = l.id and lp.product_id = p.id ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS lp.id ,l.name as location ,p.shopify_name,  p.name as product,lp.applicable_date, lp.price , lp.is_active
	from it_location_types l ,it_products p , it_location_prices lp 
	$sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
//error_log("\nMSL query: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS        
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array(); $iTotal=0;
$currdt = date('Y-m-d');
foreach ($objs as $obj)
{       $tot_stk = 0;
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
             if ($aColumns[$i] == 'location') {
                 $row[] = $obj->location;
             }else if($aColumns[$i] == 'shopify_name'){
                 $row[] = $obj->shopify_name;
             }else if($aColumns[$i] == 'product'){
                 $row[] = $obj->product;
             }else if($aColumns[$i] == 'applicable_date'){
                 $row[] = ddmmyy($obj->applicable_date);
             }else if($aColumns[$i] == 'price'){
                $row[] = $obj->price;                
             }else if($aColumns[$i] == 'is_active'){
                 if(trim($obj->is_active)==1){
                     $status = "Active";
                 }else{
                     $status = "Deactive";
                 }
                $row[] = $status;                
             }else if($aColumns[$i] == 'action'){
                 //$objserial= serialize($obj);
                 $ap_dt = yymmdd($obj->applicable_date);
                 if($currdt <= $ap_dt){
                  $row[] = '<button type="button" onclick="editLocationPrice(' . $obj->id . ')">Edit</button>';            
                 }else{
                   $row[] = 'Cannot edit as applicable date has expired';  
                 }
             }
//             else{
//                 $row[] = "-";
//             }   
       
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
