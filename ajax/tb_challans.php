<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
$usertype = $currStore->usertype;
$userid = $currStore->id;
$crid = $currStore->crid;
$dcid = $currStore->dcid;
// $crid = $currStore->crid;
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('challanno','po_alloc_num','st_num','fromloc','toloc','po_qty','qty','fullfilled_qty','createdby','createdon','action');
$sColumns = array('s.transferno', 'd.dc_name', 's.to_location_id', 's.tot_qty', 's.tot_value','u.name','s.createtime');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();

$status = isset($_GET['status']) ? $_GET['status'] : false;
//error_log("\nMSL Status query: ".$status."\n",3,"tmp.txt");
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
$sOrder = " order by s.createtime desc ";
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

// $sWhere = " where case when s.from_location_type = 1 then s.from_location_id = d.id else s.from_location_id = c.id end and case when s.to_location_type = 1 "
//         . "then s.to_location_id = d.id else s.to_location_id = c.id end and u.id = s.createdby and stc.status = $status and stc.inactive = 0 and c.id = $crid and stc.st_id = s.id";

        $sWhere = " where u.id = stc.user and stc.status = $status and stc.inactive = 0 and stc.st_id = s.id  and pa.id = stc.po_alloc_id";
if($usertype == UserType::RFC){
	$sWhere = $sWhere . " and s.to_location_id = $crid and pa.to_location_id = $crid"; 
}else if($usertype == UserType::PurchaseOfficer){
    $sWhere = $sWhere . " and s.from_location_id = $dcid and pa.from_location_id = $dcid"; 
}
$sQuery = "
	select SQL_CALC_FOUND_ROWS stc.id, stc.st_id ,stc.challan_no,s.from_location_type,s.to_location_type, (select dc_name from it_dc_master where id = s.from_location_id)as fromloc, (select upper(dispname) from it_rfc_master where id = s.to_location_id) as toloc,pa. order_qty as total_qty, stc.total_qty as fullfilled_qty,u.name,stc.submittedDate,s.createtime, pa.allocation_num, s.transferno,s.tot_qty from it_stock_transfer s,
        it_users u, st_challan stc, it_rfc_master c, it_po_allocation pa
	$sWhere group by stc.id
	$sOrder
	$sLimit
";
 // echo $sQuery;
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
//$aColumns = array('transferno','fromloc','toloc','qty','value','createdby','createdon','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'challanno') {
                 $row[] = $obj->challan_no;
             }else if ($aColumns[$i] == 'po_alloc_num') {
                 $row[] = $obj->allocation_num;
             }else if ($aColumns[$i] == 'st_num') {
                 $row[] = $obj->transferno;
             }else if ($aColumns[$i] == 'fromloc') {
                 $row[] = $obj->fromloc;
             }else if ($aColumns[$i] == 'toloc') {
                 $row[] = $obj->toloc;
             }else if($aColumns[$i] == 'po_qty'){
                 $row[] = $obj->tot_qty;
             }else if($aColumns[$i] == 'qty'){
                 $row[] = $obj->total_qty;
             }else if($aColumns[$i] == 'fullfilled_qty'){
                 $row[] = $obj->fullfilled_qty;
             }else if($aColumns[$i] == 'createdby'){
                 $row[] = $obj->name;
             }else if($aColumns[$i] == 'createdon'){
                if(!isset($obj->submittedDate) || $obj->submittedDate == null || $obj->submittedDate == ""){
                    $row[] = "Pending";
                }else{
                    $row[] = $obj->submittedDate;
                }
             }else if($aColumns[$i] == 'action'){
             	if(($usertype == UserType::RFC && $obj->to_location_type == LocationType::CR)  || $usertype == UserType::PurchaseOfficer && $obj->to_location_type == LocationType::DC){
             		if($status == StockTransferChallanStatus::AwaitingIn){
             			$row[] = '<button class="btn btn-primary" type="button" onclick="pullChallan(' . $obj->id . ')">Pull</button>'
                                        . '<br/><br/><form target="_blank" method="post" action="formpost/stocktranschallanpdf.php">
                                <input type="hidden" name="challanid" id="challanid" value='.$obj->id.' />
                                <input class="btn btn-primary" type="submit" name="print" value="View PDF" />
                             </form><br/>';	
             		}else if($status == StockTransferChallanStatus::Completed){
             			$row[] = '<button class="btn btn-primary" type="button" onclick="viewChallan(' . $obj->id . ')">View Delivery Note</button>'
                                        . '<br/><br/><form target="_blank" method="post" action="formpost/stocktranschallanpdf.php">
                                <input type="hidden" name="challanid" id="challanid" value='.$obj->id.' />
                                <input class="btn btn-primary" type="submit" name="print" value="View PDF" />
                             </form><br/>';
                        }else{
                            $row[] = "-";
                        }	
             	}else{
             		if($status == StockTransferChallanStatus::AwaitingIn){
             			$row[] = '<button class="btn btn-primary" type="button" onclick="viewChallan(' . $obj->id . ')">View Delivery Note</button>'
                                        . '<br/><br/><form target="_blank" method="post" action="formpost/stocktranschallanpdf.php">
                                <input type="hidden" name="challanid" id="challanid" value='.$obj->id.' />
                                <input class="btn btn-primary" type="submit" name="print" value="View PDF" />
                             </form><br/>';	
             		}else if($status == StockTransferChallanStatus::Completed){
             			$row[] = '<button class="btn btn-primary" type="button" onclick="viewChallan(' . $obj->id . ')">View Delivery Note</button>'
                                        . '<br/><br/><form target="_blank" method="post" action="formpost/stocktranschallanpdf.php">
                                <input type="hidden" name="challanid" id="challanid" value='.$obj->id.' />
                                <input class="btn btn-primary" type="submit" name="print" value="View PDF" />
                             </form><br/>';
             		}else if($status == StockTransferChallanStatus::BeingCreated && $usertype == UserType::Director){
                        $row[] = '<button class="btn btn-primary" type="button" onclick="viewChallan(' . $obj->id . ')">View Delivery Note</button>';
                    }else{
             			$row[] = '<button class="btn btn-primary" type="button" onclick="editChallan(' . $obj->st_id . ',' . $obj->id .')">Edit Delivery Note</button>';
             		}
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
