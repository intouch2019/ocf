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
if($currStore->usertype == UserType::HO ){
    $aColumns = array('id','dispname','rfc_name','contact_person','address','emailaddress','phoneno','gstno','panno','state','createtime','action');
}else{
    $aColumns = array('id','dispname','rfc_name','contact_person','address','emailaddress','phoneno','gstno','panno','state','createtime','status');
}
    $sColumns = array('id','dispname','rfc_name','contact_person','address','emailaddress','phoneno','gstno','panno','state','createtime');
    
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
$sOrder = " order by r.createtime desc ";
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
//$sColumns = array('s.id','s.company_name', 's.date_of_entry', 's.contact_person1', 's.phone1', 's.email1' ,'s.active');
$sWhere = "where r.state = s.id";
$sQuery = "
	select SQL_CALC_FOUND_ROWS r.* , s.state
	from it_rfc_master r, states s
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
//$sColumns = array('s.id','s.company_name', 's.date_of_entry', 's.contact_person1', 's.phone1', 's.email1' ,'s.active');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
            if ($aColumns[$i] == 'id') {
                $row[] = $obj->id;
            }else if ($aColumns[$i] == 'dispname') {
                $row[] = $obj->dispname;
            }else if ($aColumns[$i] == 'rfc_name') {
                $row[] = $obj->rfc_name;
            }else if($aColumns[$i] == 'contact_person'){
                $row[] = $obj->contact_person;
            }else if($aColumns[$i] == 'address'){
                $row[] = $obj->address;
            }else if($aColumns[$i] == 'emailaddress'){
                $row[] = $obj->emailaddress;
            }else if($aColumns[$i] == 'phoneno'){
                $row[] = $obj->phoneno;
            }else if($aColumns[$i] == 'gstno'){
                $row[] = $obj->gstno;
            }else if($aColumns[$i] == 'panno'){
                $row[] = $obj->panno;
            }else if($aColumns[$i] == 'state'){
                $row[] = $obj->state;
            }else if($aColumns[$i] == 'createtime'){
                $createDate = date('d-m-Y', strtotime($obj->createtime));
                $row[] = $createDate;
            }else if($aColumns[$i] == 'action' &&  $currStore->usertype ==UserType::HO){
                if($obj->is_approved == 0 ){
                    $row[] = '<input type="button" class="btn btn-primary" name="approve" id="approve" value="Approve" onclick="approve('.$obj->id.','.$obj->created_by.')"/>';
                }else{
                    $row[] = '<input type="button" disabled class="btn btn-primary" name="approve" id="approve" value="Approved"/>';
                }

            }else if($aColumns[$i] == 'status' &&  $currStore->usertype ==UserType::RFCManager){
               if($obj->is_approved == 0 ){
                    $row[] = "Pending";
               }else{
                    $row[] = "Approved";
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
