<?php

include "../../it_config.php";
require_once "session_check.php";
require_once "lib/db/DBConn.php";
require_once "lib/db/DBLogic.php";
require_once "lib/core/Constants.php";
require_once "lib/core/strutil.php";

$currStore = getCurrStore();
$userid = $currStore->id;
//if (!$currStore || !($currStore->usertype == UserType::Admin || $currStore->usertype == UserType::WKAdmin || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::CRM_Manager || $currStore->usertype == UserType::City_Head)) {
//    print "Unauthorized Access !!! CurrStore=" . print_r($currStore, true);
//    return;
//}
//i.id,i.customer_id,i.cnno,i.cndate,i.createtime,i.cname,i.cphone,i.total_qty,i.total_value from it_creditnote i
$aColumns = array('invoiceno', 'createtime', 'cname', 'cphone', 'totqty', 'totvalue', 'action');
$sColumns = array('i.cnno', 'i.cndate', 'i.cname', 'i.cphone', 'i.total_qty', 'i.total_value');
/* Indexed column (used for fast and accurate table cardinality) */
$db = new DBConn();
$dbl = new DBLogic();
$status = isset($_GET['status']) ? $_GET['status'] : false;

/*
 * Paging
 */
$sLimit = "";
if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
    $sLimit = " LIMIT " . $db->getConnection()->real_escape_string($_GET['iDisplayStart']) . ", " .
            $db->getConnection()->real_escape_string($_GET['iDisplayLength']);
}


/*
 * Ordering
 */
$sOrder = " order by i.id desc ";
if (isset($_GET['iSortCol_0'])) {
    $sOrder = " ORDER BY  ";
    for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
        if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
            $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . "
			 	" . $db->getConnection()->real_escape_string($_GET['sSortDir_' . $i]) . ", ";
        }
    }

    $sOrder = substr_replace($sOrder, "", -2);
    if ($sOrder == " ORDER BY ") {
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
if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
    $sWhere = "WHERE (";
    for ($i = 0; $i < count($sColumns); $i++) {
        $sWhere .= $sColumns[$i] . " LIKE '%" . $db->getConnection()->real_escape_string($_GET['sSearch']) . "%' OR ";
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ')';
}

/* Individual column filtering */
for ($i = 0; $i < count($sColumns); $i++) {
    if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && isset($_GET['sSearch_' . $i]) && $_GET['sSearch_' . $i] != '') {
        if ($sWhere == "") {
            $sWhere = "WHERE ";
        } else {
            $sWhere .= " AND ";
        }
        $sWhere .= $sColumns[$i] . " LIKE '%" . $db->getConnection()->real_escape_string($_GET['sSearch_' . $i]) . "%' ";
    }
}

/*
 * SQL queries
 * Get data to display
 */

//$aColumns = array('invoiceno','cname','cphone','totqty','tottax','totvalue','action');
//$status = InvoiceStatus::Created;
//$tablename = $dbl->getSalesTableName($userid);
$sWhere = " where i.status = $status";
$sQuery = "
	select SQL_CALC_FOUND_ROWS i.id,i.customerid,i.cnno,i.cndate,i.createtime,i.cname,i.cphone,i.tot_qty,i.tot_value from it_creditnote i
	$sWhere 
	$sOrder
	$sLimit
";
//echo $sQuery;
error_log("\nCNQry query: ".$sQuery."\n",3,"tmp.txt");
$objs = $db->fetchObjectArray($sQuery);

/* Data set length after filtering */
$sQuery = "
	SELECT FOUND_ROWS() AS TOTAL_ROWS
";
$obj = $db->fetchObject($sQuery);
$iFilteredTotal = $obj->TOTAL_ROWS;

$rows = array();
$iTotal = 0;
foreach ($objs as $obj) {
    $tot_stk = 0;
    $row = array();

//$aColumns = array('invoiceno','cname','cphone','totqty','tottax','totvalue','action');
    for ($i = 0; $i < count($aColumns); $i++) {
        $custid = 0;
        if (isset($obj->customerid)) {
            $custid = $obj->customerid;
        }
        if ($aColumns[$i] == 'invoiceno') {
            $row[] = $obj->cnno;
        } else if ($aColumns[$i] == 'createtime') {
            $row[] = ddmmyy($obj->cndate);
        } else if ($aColumns[$i] == 'cname') {
            $row[] = $obj->cname;
        } else if ($aColumns[$i] == 'cphone') {
            $row[] = $obj->cphone;
        } else if ($aColumns[$i] == 'totqty') {
            $row[] = round($obj->tot_qty, 2);
        } else if ($aColumns[$i] == 'totvalue') {
            $round_invoice_val = round($obj->tot_value);
            $row[] = $round_invoice_val;
        } else if ($aColumns[$i] == 'action') {
            if ($status == CreditNoteStatus::Created) {
                $row[] = '<input type="button" class="btn btn-primary" name="pdf" value="View PDF" onclick="showPDF(' . $obj->id . ')"/>';
            } else {
                $row[] = '<input type="button" class="btn btn-primary" name="pdf" value="Edit" onclick="editCN(' . $obj->id . ')"/>';
            }
        } else {
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

echo json_encode($output);
?>
