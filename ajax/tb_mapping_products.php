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

$aColumns = array('id','catname','prodname','action');
$sColumns = array('p.id','c.name','p.name');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$lid = isset($_GET['locid']) ? $_GET['locid'] : false;
$status = isset($_GET['status']) ? $_GET['status'] : false;
//print"$lid**********$status<br>";
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
// select l.name,b.bin,p.name,s.qty from it_stock_current s ,it_locations l ,it_bins b ,it_products p  where l.id= s.createdat_locationid and b.id =s.bin_id and p.id= s.product_id;
if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}
$onClause = "";
if(trim($lid)!="" && trim($lid)!="0" && $lid != null ){//&& trim($status)!="" && trim($status)!="0" && $status != null
    $onClause .= " and lp.location_id = $lid ";
}
$sClause= "";
if(trim($status)!=""  && $status != null && trim($status)!="0"){
//    $sClause .= "  where lp.is_mapped = $status ";
    $sClause .= "  and lp.is_mapped = $status ";
}

    
//select p.name from it_products p left join  it_location_products lp on p.id= lp.product_id;

$sWhere .= " p.category_id = c.id $sClause";
$sQuery = "select SQL_CALC_FOUND_ROWS p.id, p.name as prodname, c.name as catname ,lp.is_mapped from it_category c , it_products p left join it_location_products lp on p.id = lp.product_id $onClause 
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

static $rowcount=0;
$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{       $tot_stk = 0;
	$row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'id') {
                $row[] = $obj->id;
            }else if ($aColumns[$i] == 'catname') {
                $row[] = $obj->catname;
            }else if ($aColumns[$i] == 'prodname') {
                $row[] = $obj->prodname;
            }else if($aColumns[$i] == "action"){
//                $row[] = "<input type = 'radio' class='case1' name=$obj->id id=$obj->id value='1' onClick='remSel($obj->id);'>Map  <input type = 'radio' class='case2' name=$obj->id  id=$obj->id value='2' onClick='remSel($obj->id);'>Unmap";
                $str="";  $str2="";   
                if(trim($obj->is_mapped) == LocationProductStatus::mapped){ // = 1
                    $str = "checked";                   
                }else if (trim($obj->is_mapped) == LocationProductStatus::unmapped) { //= 2
                    $str2 = "checked";                    
                }   
                                              
                $row[] = "<input type = 'radio' class='case1' name=$obj->id id=$obj->id value='1' onClick='remSel($obj->id);' ".$str.">Map  <input type = 'radio' class='case2' name=$obj->id  id=$obj->id value='2' onClick='remSel($obj->id);'".$str2.">Unmap";
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
