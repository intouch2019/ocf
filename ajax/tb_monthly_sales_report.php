<?php
include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
//print_r($currStore);
//sale date | retail outlet no.(CR) | Cash | net banking | debit card | credit card | cheque | Total | 
$aColumns = array('createtime','crcode','cash','netbanking','debitcard','creditcard','cheque','total');
$sColumns = array('createtime','crcode');


/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : false;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : false;

print_r($startDate);
return;
//error_log("\nMSL grnquery: ".$dcid."\n",3,"tmp.txt");
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
$sOrder = " order by p.createtime desc ";
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
if(trim($daterange)!="" && trim($daterange)!="-1"){
	$daterange = str_replace("'"," ",$daterange);
	$dates = explode("-",$daterange);
	// print_r($dates);
	$startDate = $dates[0];
	$startDate = str_replace('/', '-', $startDate);	
	$endDate = $dates[1];
	$endDate = str_replace('/', '-', $endDate);
	$startDate = explode("-", $startDate);
	$nstartdate = trim($startDate[2])."-".trim($startDate[1])."-".trim($startDate[0]);
	$endDate = explode("-", $endDate);
	$nenddate = trim($endDate[2])."-".trim($endDate[1])."-".trim($endDate[0]);
	$startDate = date("Y-m-d 00:00:00", strtotime($nstartdate));
	$endDate = date("Y-m-d 23:59:59", strtotime($nenddate));
}

/*
 * SQL queries
 * Get data to display
 */
if($sWhere==""){
    $sWhere .= " where ";
}else{
    $sWhere .= " and ";
}
$sGroup = " group by date(p.createtime)";
$sWhere .= " c.crid = $crid and c.status=1 and c.id=p.invoice_id and c.crid= rfc.id and p.createtime >='$startDate' and p.createtime <= '$endDate'";
$sQuery = " select rfc.crcode,date(p.createtime)as createtime from it_cr270001 c, it_payments_diary p , it_rfc_master rfc
        $sWhere  
        $sGroup
	$sOrder
	$sLimit
";
//echo $sQuery;
//error_log("\nMSL poquery: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;
$rows = array(); $iTotal=0;
foreach ($objs as $obj)
{     
    $total = 0;
    $row = array();
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
            if ($aColumns[$i] == 'createtime') {
                //show in ddmmyy
                $row[] = $obj->createtime;
            }elseif($aColumns[$i] == 'crcode'){
                $row[] = $obj->crcode;
            }if($aColumns[$i] == 'cash'){
                $q= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 3";
//                print "CASH:$q\n";
                $cashobj = $db->fetchObject($q);
                if(isset($cashobj) && $cashobj->amt !=null){ 
                    $row[] = round($cashobj->amt,2);
                }else{
                    $row[] = "-";
                }
            } if($aColumns[$i] == 'netbanking'){
               $q1= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 6";
//               print "NB:$q1\n";
                $cashobj1 = $db->fetchObject($q1);                
                if(isset($cashobj1) && $cashobj1->amt !=null){ 
                    $row[] = round($cashobj1->amt,2);
                }else{
                    $row[] = "-";
                }
            }if($aColumns[$i] == 'debitcard'){
              $q2= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 1";
            //print "DC:$q2\n";
                $cashobj2 = $db->fetchObject($q2);
                if(isset($cashobj2) && $cashobj2->amt !=null){ 
                    $row[] = round($cashobj2->amt,2);
                }else{
                    $row[] = "-";
                }
            }if($aColumns[$i] == 'creditcard'){
               $q3= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 4";
            //    print "CD:$q3\n";
                $cashobj3 = $db->fetchObject($q3);
                if(isset($cashobj3) && $cashobj3->amt !=null){ 
                    $row[] = round($cashobj3->amt,2);
                }else{
                    $row[] = "-";
                }
            } if($aColumns[$i] == 'cheque'){
               $q4= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime' and paymenttype = 5";
//               print "CH:$q4\n"; 
                $cashobj4 = $db->fetchObject($q4);
                if(isset($cashobj4) && $cashobj4->amt !=null ){ 
                    $row[] = round($cashobj4->amt,2);
                }else{
                    $row[] = "-";
                }
            } if($aColumns[$i] == 'total'){
             $qt= "select sum(p.amount) as amt from it_cr270001 c, it_payments_diary p where  c.crid = $crid and c.status=1 and c.id=p.invoice_id and date(p.createtime) =  '$obj->createtime'";
//               print "TOT:$qt\n";
                $objt = $db->fetchObject($qt);
                if(isset($objt) && $objt->amt !=null ){ 
                    $row[] = round($objt->amt,2);
                }else{
                    $row[] = "-";
                }
            }
//            else{
//               $row[] = "-";
//            }
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
