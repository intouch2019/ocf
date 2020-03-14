<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";

$currStore = getCurrStore();
$usertype = $currStore->usertype;
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}

$aColumns = array('pono','supplier','poqty','povalue','createdby','createdon','action');
$sColumns = array('p.pono', 's.name', 'p.tot_qty', 'p.tot_value','u.name','p.createtime');
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
$sOrder = " order by p.createtime desc";
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

$sWhere = " where s.id = p.supplier_id and u.id = p.createdby_id and p.po_status = $status and p.inactive = 0 ";
$sQuery = "
	select SQL_CALC_FOUND_ROWS p.id as poid,p.pono,s.company_name as supplier, p.tot_qty as qty, p.tot_value as value,p.freightamt as freightamt, u.name as createdby, p.createtime, p.inactive, p.uom_id
	from it_purchaseorder p, it_users u, it_suppliers s
	$sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
//error_log("\nMSL query: ".$sQuery."\n",3,"../tmp.txt");
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
//$aColumns = array('pono','supplier','poqty','povalue','createdby','createdon','action');
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
             if ($aColumns[$i] == 'pono') {
                 $row[] = $obj->pono;
             }else if ($aColumns[$i] == 'supplier') {
                 $row[] = $obj->supplier;
             }else if($aColumns[$i] == 'poqty'){
                 $row[] = sprintf("%.4f",$obj->qty);
             }else if($aColumns[$i] == 'povalue'){
                 $row[] = $obj->value;
             }else if($aColumns[$i] == 'createdby'){
                 $row[] = $obj->createdby;
             }else if($aColumns[$i] == 'createdon'){
                 $row[] = $obj->createtime;
             }else if($aColumns[$i] == 'action'){
                 if($usertype == UserType::Director){
                  if($status == POStatus::AwaitingCancel){
                      $row[] = '<button class="btn btn-primary" type="button" onclick="viewCancelledPO(' . $obj->poid . ')">View PO</button>';
                 }else if($status == POStatus::Created){
                       $row[] = '<button class="btn btn-primary" type="button" onclick="directorApprove(' . $obj->poid . ')">View PO</button>';          
                    }else if($status == POStatus::Submitted){
                            //error_log("\nMSL freight: ".$obj->freightamt."\n",3,"tmp.txt");
                            if($obj->freightamt > 0){
                               $row[] = '<form target="_blank" method="post" action="formpost/popdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Supplier PDF"/>
                                         </form>
                                         <br/><form target="_blank" method="post" action="formpost/transporterpdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Transporter PDF"/>
                                         </form>'.'<br/><button class="btn btn-primary" type="button" onclick="cancelPO(' . $obj->poid . ')">Cancel PO</button>&nbsp;<br>';
           //                    $row[] = '<button class="btn btn-primary" type="button" onclick="viewPDF(' . $obj->poid . ')">View PDF</button>'; 
                            }else{
                                $row[] = '<form target="_blank" method="post" action="formpost/popdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Supplier PDF"/>
                                         </form>
                                         '.'<br/><button class="btn btn-primary" type="button" onclick="cancelPO(' . $obj->poid . ')">Cancel PO</button>&nbsp;<br>';
                            }
                        }else{
                            $row[] = "-";
                        }
                 }else if($usertype == UserType::GRN){
                        if($status == POStatus::Submitted){
                            //error_log("\nMSL freight: ".$obj->freightamt."\n",3,"tmp.txt");
                            if($obj->freightamt > 0){
                               $row[] = '<form target="_blank" method="post" action="formpost/popdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Supplier PDF"/>
                                         </form>
                                         <br/><form target="_blank" method="post" action="formpost/transporterpdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Transporter PDF"/>
                                         </form>';
                            }else{
                                $row[] = '<form target="_blank" method="post" action="formpost/popdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Supplier PDF"/>
                                         </form>';
                                         
                            }
                        }else{
                            $row[] = "-";
                        }
                 }else{
                    if($status == POStatus::Open){
                       $row[] = '<button class="btn btn-primary" type="button" onclick="editPO(' . $obj->poid . ','. $obj->uom_id.')">Edit</button>&nbsp;'
                               . '<button class="btn btn-primary" type="button" onclick="deletePO(' . $obj->poid . ')">Delete</button>';            
                    }else if($status == POStatus::Approved){
                       $row[] = '<button class="btn btn-primary" type="button" onclick="submitPO(' . $obj->poid . ')">View PO</button>&nbsp;<br>';            
                    }else{
                        if($status == POStatus::Submitted){
                            //error_log("\nMSL freight: ".$obj->freightamt."\n",3,"tmp.txt");
                            if($obj->freightamt > 0){
                               $row[] = '<form target="_blank" method="post" action="formpost/popdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Supplier PDF"/>
                                         </form>
                                         <br/><form target="_blank" method="post" action="formpost/transporterpdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Transporter PDF"/>
                                         </form>'.'<br/><button class="btn btn-primary" type="button" onclick="cancelPO(' . $obj->poid . ')">Cancel PO</button>&nbsp;<br>';
           //                    $row[] = '<button class="btn btn-primary" type="button" onclick="viewPDF(' . $obj->poid . ')">View PDF</button>'; 
                            }else{
                                $row[] = '<form target="_blank" method="post" action="formpost/popdf.php">
                                               <input type="hidden" name="poid" id="poid" value='.$obj->poid.'/>
                                               <input class="btn btn-primary" type="submit" name="print" value="View Supplier PDF"/>
                                         </form>
                                         '.'<br/><button class="btn btn-primary" type="button" onclick="cancelPO(' . $obj->poid . ')">Cancel PO</button>&nbsp;<br>';
                            }
                        }else{
                            $row[] = "-";
                        }
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
