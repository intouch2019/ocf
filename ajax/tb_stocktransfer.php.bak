<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
$usertype = $currStore->usertype;
$storeDcId = $currStore->dcid;
$crid = $currStore->crid;
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('transferno','po_alloc_no','toloc','qty','po_alloc_fullfilled_qty','createdby','createdon','action');
$sColumns = array('s.transferno', 'd.dc_name', 's.to_location_id', 's.tot_qty','u.name','s.transferdate');
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
//$aColumns = array('id','name','state','address','graddress','contact_person','phone','email','gstno','panno','status','action');        
//echo "Status : ".$status."<br>";

//$sWhere = " where s.from_location_id = d.id and u.id = s.createdby and s.status = $status and s.inactive = 0 ";

$addWhere = "";
if($usertype == UserType::RFC && isset($crid)){
    $addWhere .= "and s.to_location_id = $crid";
}else if($usertype == UserType::PurchaseOfficer){
    $addWhere .= "and pa.from_location_id = $storeDcId";
}
$sWhere = " where u.id = s.createdby and pa.status = $status and s.inactive = 0 and pa.transferid = s.id $addWhere";



$sQuery = "select SQL_CALC_FOUND_ROWS s.id,s.transferno, pa.allocation_num, pa.order_qty,pa.fullfilled_qty, (select upper(dispname) from it_rfc_master where id = s.to_location_id) as toloc,s.tot_qty,u.name,s.transferdate,s.createtime,pa.id as po_alloc_id from it_stock_transfer s,it_users u, it_po_allocation pa
	$sWhere 
	$sOrder
	$sLimit
";



// echo $sQuery;
//return;
// error_log("\nMSLmainnn query: ".$sQuery."\n",3,"tmp.txt");
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
        // $transferId = $obj->id;
	$row = array();
    // $qry = "select s.id from it_stock_transfer s, it_stock_transfer_items si, it_products p where s.id = si.transferid and s.id = $transferId and si.prodid = p.id and p.supplier_dc = $storeDcId group by s.id";
    // $result = $db->fetchObject($qry);
    // // print_r($currStore);
    // $flag = false;
    // if(isset($result) && $result != null && $result != "" && $usertype == UserType::PurchaseOfficer){
    //     $flag = true;
    // }else if($usertype == UserType::RFC){
    //     $flag = true;
    // }
    // if($flag){

	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'transferno') {
                 $row[] = $obj->transferno;
             }else if ($aColumns[$i] == 'po_alloc_no') {
                 $row[] = $obj->allocation_num;
             }else if ($aColumns[$i] == 'toloc') {
                 $row[] = $obj->toloc;
             }else if($aColumns[$i] == 'qty'){
                 $row[] = $obj->order_qty;
             }else if($aColumns[$i] == 'po_alloc_fullfilled_qty'){
                 $row[] = $obj->fullfilled_qty;
             }else if($aColumns[$i] == 'value'){
                 $row[] = $obj->tot_value;
             }else if($aColumns[$i] == 'createdby'){
                 $row[] = $obj->name;
             }else if($aColumns[$i] == 'createdon'){
                 $row[] = $obj->transferdate;
             }else if($aColumns[$i] == 'action'){
                if($status == StockTransferStatus::BeingCreated){
                    if($usertype == UserType::RFC){
                        $row[] = '<button class="btn btn-primary" type="button" onclick="editStockTransfer(' . $obj->id . ')">Edit</button>';
                    }else{
                        $row[] = '<button class="btn btn-primary" type="button" onclick="pullStockTransfer(' . $obj->id . ')">View</button>';
                    }
//                           . '<button class="btn btn-primary" type="button" onclick="deleteGRN(' . $obj->id . ')">Delete</button>';            
                }else if($status == StockTransferStatus::AwaitingIn){

                    if($usertype == UserType::PurchaseOfficer){
                        $challanquery = "select id, status from st_challan where st_id = $obj->id";
                        $challan = $db->fetchObject($challanquery);


                        $row[] = '<button class="btn btn-primary" type="button" onclick="pullStockTransfer(' . $obj->po_alloc_id . ')">View</button>
                             <br/><br/><button class="btn btn-primary" type="button" onclick="createChallan(' . $obj->id . ',' . $obj->po_alloc_id .')">Create Delivery Note</button><br/>';
                    }else{
                        // print_r("here");
                        $row[] = '<button class="btn btn-primary" type="button" onclick="pullStockTransfer(' . $obj->po_alloc_id . ')">View</button>';
                    }
                   
                }else{
                    // $row[] = '<button class="btn btn-primary" type="button" onclick="pullStockTransfer(' . $obj->id . ')">View</button>';
                    $row[] = '<button class="btn btn-primary" type="button" onclick="pullStockTransfer(' . $obj->po_alloc_id . ')">View</button>';
                    // $row[] = "-";
                }
             }
             // else if ($aColumns[$i] == 'challan'){
             // 	$challanquery = "select id, status from st_challan where st_id = $obj->id";
             // 	$challan = $db->fetchObject($challanquery);
             // 	if(($usertype == UserType::PurchaseOfficer && $obj->from_location_type == LocationType::DC)||($usertype == UserType::HO && $obj->from_location_type == LocationType::CR)){
             // 		if($challan && isset($challan) && $challan != NULL ){
             // 			if($challan->status == StockTransferChallanStatus::BeingCreated){
             // 				$row[]='<button class="btn btn-primary" type="button" onclick="editChallan(' . $obj->id . ')">Edit Challan</button>';	
             // 			}else{
             // 				$row[]='<button class="btn btn-primary" type="button" onclick="viewChallan(' . $challan->id . ')">View Challan</button>';	
             // 			}
						
             // 		}else{
             // 			$row[]='<button class="btn btn-primary" type="button" onclick="createChallan(' . $obj->id . ')">Create Challan</button>';
             // 		}
             // 	}else{
             // 		$row[]='-';
             // 	}
             // }
             else {
                 $row[] = "-";
             }   

       
	}

	$rows[] = $row;
	$iTotal++;
    // }
    // break;
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
