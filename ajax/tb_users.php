<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('id','name','usertype','username','inactive','createdby','createdon','action');
$sColumns = array('u.id','u.name', 'u.usertype', 'u.username', 'u.inactive','u.created_by','u.createtime');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();

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
$sOrder = " order by u.createtime desc ";
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
//$aColumns = array('id','name','state','address','graddress','contact_person','phone','email','gstno','panno','status','action');        
//echo "Status : ".$status."<br>";

//$sWhere = " where s.id = p.supplier_id and u.id = p.createdby_id and p.po_status = $status and p.inactive = 0 ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS u.id,u.name,u.usertype,u.username,u.created_by,u.inactive,u.createtime from it_users u
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
foreach ($objs as $obj){
        $tot_stk = 0;
	$row = array();
//$aColumns = array('name','usertype','username','inactive','createdby','createdon','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'id') {
                 $row[] = $obj->id;
             }else if ($aColumns[$i] == 'name') {
                 $row[] = $obj->name;
             }else if ($aColumns[$i] == 'usertype') {
                 $row[] = UserType::getName($obj->usertype);
             }else if($aColumns[$i] == 'username'){
                 $row[] = $obj->username;
             }else if($aColumns[$i] == 'inactive'){
                 $row[] = $obj->inactive;
             }else if($aColumns[$i] == 'createdby'){
                 $query = "select name from it_users where id = $obj->created_by";
                 $ob = $db->fetchObject($query);
                 if(isset($ob) && $ob != NULL){
                     $row[] = $ob->name;
                 }else{
                    $row[] = "-";
                 }
             }else if($aColumns[$i] == 'createdon'){
                 $row[] = ddmmyy($obj->createtime);
             }else if($aColumns[$i] == 'action'){
                 if($obj->inactive == 0){
                      $row[] = '<button class="btn btn-primary" type="button" onclick="editUser(' . $obj->id . ')">Edit</button>&nbsp;'
                            . '<button class="btn btn-primary" type="button" onclick="deleteUser(' . $obj->id . ')">Inactive</button>';
                 } else {
                      $row[] = '<button class="btn btn-primary" type="button" onclick="editUser(' . $obj->id . ')">Edit</button>&nbsp;'
                            . '<button class="btn btn-primary" type="button" disabled onclick="deleteUser(' . $obj->id . ')">Inactive</button>';
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
