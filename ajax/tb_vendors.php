<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('name','address','phone','city','pincode','comission_per','active','createtime','action');
$sColumns = array('s.name','s.address','s.phone','s.city','s.pincode','s.comission_per','s.active','s.createtime','s.action');
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
$sOrder = " order by s.updatetime desc ";
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

//if($sWhere==""){
//    $sWhere .= " where ";
//}else{
//    $sWhere .= " and ";
//}
//$sWhere .= " b.location_id = l.id ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS s.id,s.name,s.address,s.phone,s.city,s.pincode,s.comission_per,s.is_active,s.createtime
	from it_vendors s
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
foreach ($objs as $obj)
{       $tot_stk = 0;
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
             if ($aColumns[$i] == 'name') {
                 $row[] = $obj->name;
             }else if($aColumns[$i] == 'address'){
                 $row[] = $obj->address;
             }else if($aColumns[$i] == 'phone'){
                 if(trim($obj->phone)!=""){
                   $row[] = $obj->phone;  
                 }else{
                   $row[] = "-";  
                 }                 
             }else if($aColumns[$i] == 'city'){
                 if(trim($obj->city)!=""){
                   $row[] = $obj->city;  
                 }else{
                   $row[] = "-";  
                 }
                 
             }else if($aColumns[$i] == 'pincode'){
                 if(trim($obj->pincode)!=""){
                   $row[] = $obj->pincode;  
                 }else{
                   $row[] = "-";
                 }
             }else if($aColumns[$i] == 'comission_per'){
                 if(trim($obj->comission_per)!=""){
                   $row[] = $obj->comission_per;  
                 }else{
                   $row[] = "-";
                 }
             }else if($aColumns[$i] == 'active'){
                if(trim($obj->is_active) == 1)
                  $row[] = "Active";
                else
                  $row[] = "Inactive";
             }else if($aColumns[$i] == 'createtime'){
                 $row[] = $obj->createtime;
             }
             else if($aColumns[$i] == 'action'){
                 //$objserial= serialize($obj);
                 $row[] = '<button type="button" onclick="editVendor(' . $obj->id . ')">Edit</button>';            
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
