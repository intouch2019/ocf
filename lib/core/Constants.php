<?php
class UserType {

    const Admin = 0;
    const HO = 1;
    const DC = 2;
    const RFC = 3;
    const PurchaseOfficer = 4;
    const NoLogin = 5;
    const Director = 6;
    const GRN = 7;
    const RFCManager = 8;
    const RFCLogin = 9;
    const DCReportUser = 10;
    const CRImprestUser = 11;
    const IntouchAdmin = 12;
    

    public static function getAll() {
        return array(
            UserType::Admin => "Administrator",
            UserType::HO => "Sarotam HO",
            UserType::DC => "Distribution Center",
            UserType::RFC => "Consignee Retailer",
            UserType::PurchaseOfficer => "Purchase Officer",
            UserType::NoLogin => "No Login",
            UserType::Director => "Director",
            UserType::GRN => "Goods Inward",
            UserType::RFCManager => "RFC Manager",
            UserType::RFCLogin => "RFC Login",
            UserType::DCReportUser => "DC Report User",
            UserType::IntouchAdmin => "Intouch Admin"
        );
    }

    public static function getName($usertype) {
        $all = UserType::getAll();
        if (isset($all[$usertype])) {
            return $all[$usertype];
        } else {
            return "Not Found";
        }
    }

}

class POStatus {

    const Open = 0;
    const Created = 1;
    const Approved = 2;
    const Rejected = 3;
    const Deleted = 4;
    const Submitted = 5;
    const AwaitingCancel = 6;
    const Cancelled = 7;
    const CancelReject = 8;
    //const Closed = 7;
    //const Inprocess = 8;
    //const Shortclosed = 9;
    
    public static function getAll() {
        return array(
            POStatus::Open => "Being Created",
            POStatus::Created => "PO created. Awaiting Approval",
            POStatus::Approved => "Approved",
            POStatus::Rejected => "Not Approved",
            POStatus::Deleted => "Deleted",
            POStatus::Submitted => "Submitted",
            POStatus::AwaitingCancel => "PO cancelled. Awaiting Approval",
            POStatus::Cancelled => "PO Cancellation Approved",
            POStatus::CancelReject => "PO Cancellation Rejected",            
            //POStatus::Closed => "PO Closed. All items are delivered",
            //POStatus::Inprocess => "In process. Partial deliveries are done",
            //POStatus::Shortclosed => "Manually closed",
            //POStatus::Deleted => "Deleted PO",

        );
    }

    public static function getName($postatus) {
        $all = POStatus::getAll();
        if (isset($all[$postatus])) {
            return $all[$postatus];
        } else {
            return "Not Found";
        }
    }
}

class SupplierBillStatus {

    const Open = 0;
    const Submit = 1;
    const Deleted = 2;
    
    public static function getAll() {
        return array(
            SupplierBillStatus::Open => "Being Created",
            SupplierBillStatus::Submit => "Submitted",
            SupplierBillStatus::Deleted => "Deleted"
        );
    }

    public static function getName($status) {
        $all = SupplierBillStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}

class InvoiceType {

    const Cash = 0;
    const CashDiscount = 1;
    const Credit = 2;
    const CreditDebitFee = 3;
    
    public static function getAll() {
        return array(
            InvoiceType::Cash => "Cash",
            InvoiceType::CashDiscount => "Cash Discount",
            InvoiceType::Credit => "Credit Against PDC",
            InvoiceType::CreditDebitFee => "Credit/Debit Card Fee"
        );
    }

    public static function getName($status) {
        $all = InvoiceType::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}

class InvoiceStatus {

    const Open = 0;
    const Created = 1;
    //const Deleted = 2;
    
    public static function getAll() {
        return array(
            InvoiceStatus::Open => "Open",
            InvoiceStatus::Created => "Created",
           // InvoiceStatus::Deleted => "Deleted",
        );
    }

    public static function getName($status) {
        $all = InvoiceStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}

class ProductPriceStatus {

    const Pending = 0;
    const Approved = 1;
    const Disapproved = 2;
    //const Deleted = 2;
    
    public static function getAll() {
        return array(
            ProductPriceStatus::Pending => "Pending",
            ProductPriceStatus::Approved => "Approved",
            ProductPriceStatus::Disapproved => "Disapproved"
           // InvoiceStatus::Deleted => "Deleted",
        );
    }

    public static function getName($status) {
        $all = ProductPriceStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}

/*class ProductPriceStatus {

    const Pending = 0;
    const AwaingForApproval = 1;
    const Approved = 2;
    const Disapproved = 3;
    //const Deleted = 2;
    
    public static function getAll() {
        return array(
            ProductPriceStatus::Pending => "Pending",
            ProductPriceStatus::AwaingForApproval=>"Awaiting For Approval",
            ProductPriceStatus::Approved => "Approved",
            ProductPriceStatus::Disapproved => "Disapproved"
            
           // InvoiceStatus::Deleted => "Deleted",
        );
    }

    public static function getName($status) {
        $all = ProductPriceStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}*/

class GRNStatus {

    const Open = 0;
    const Created = 1;
    const Deleted = 4;
    
    public static function getAll() {
        return array(
            GRNStatus::Open => "Being Created",
            GRNStatus::Created => "GRN Created",
            GRNStatus::Deleted => "Deleted",
        );
    }

    public static function getName($grnstatus) {
        $all = GRNStatus::getAll();
        if (isset($all[$grnstatus])) {
            return $all[$grnstatus];
        } else {
            return "Not Found";
        }
    }
}

class GRNItemStatus {

    const Open = 0;
    const Submitted = 1;
    const ApprovalRequired = 2;
    
    public static function getAll() {
        return array(
            GRNItemStatus::Open => "Being Created",
            GRNItemStatus::Submitted => "Submitted",
            GRNItemStatus::ApprovalRequired => "Approval Required",
        );
    }

    public static function getName($grnstatus) {
        $all = GRNItemStatus::getAll();
        if (isset($all[$grnstatus])) {
            return $all[$grnstatus];
        } else {
            return "Not Found";
        }
    }
}

/*class StockDiaryReason {

    const PurchaseIn = 1;
    const StockTransfer = 2;
    const Sale = 3;
    const CRStockpulled = 4;
    const CreditNote = 5;
   
    public static function getAll() {
        return array(
            StockDiaryReason::PurchaseIn => "Purchase In",
            StockDiaryReason::StockTransfer => "Stock Transfer",
            StockDiaryReason::Sale => "Sale",
            StockDiaryReason::CRStockpulled => "CR Stock Pulled",
            StockDiaryReason::CreditNote => "Credit Note",
        );
    }

    public static function getName($status) {
        $all = StockDiaryReason::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}*/

class StockDiaryReason {

    const PurchaseIn = 1;
    const StockTransfer = 2;
    const Sale = 3;
    const CRStockpulled = 4;
    const CreditNote = 5;
    const ChallanOut = 6;
    const ChallanIn = 7;
    const StockAdjustment = 8;
   
    public static function getAll() {
        return array(
            StockDiaryReason::PurchaseIn => "Purchase In",
            StockDiaryReason::StockTransfer => "Stock Transfer",
            StockDiaryReason::Sale => "Sale",
            StockDiaryReason::CRStockpulled => "CR Stock Pulled",
            StockDiaryReason::CreditNote => "Credit Note",
            StockDiaryReason::ChallanOut => "Challan Out",
            StockDiaryReason::CreditIn => "Challan In",
            StockDiaryReason::StockAdjustment => "StockAdjustment",
            
        );
    }

    public static function getName($status) {
        $all = StockDiaryReason::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else { 
            return "Not Found";
        }
    }
}



class LocationType {

    const DC = 1;
    const CR = 2;
   
    public static function getAll() {
        return array(
            LocationType::DC => "Distribution Center",
            LocationType::CR => "Consignment Retailer"
        );
    }

    public static function getName($status) {
        $all = LocationType::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}

class StockTransferStatus {

    const BeingCreated = 1;
    const AwaitingIn = 2;
    const Completed = 3;
    const Deleted = 4;
   
    public static function getAll() {
        return array(
            StockTransferStatus::BeingCreated => "Draft",
            StockTransferStatus::AwaitingIn => "Pending",
            StockTransferStatus::Completed => "Completed",
            StockTransferStatus::Deleted => "Deleted"
        );
    }

    public static function getName($status) {
        $all = StockTransferStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}

class CreditNoteStatus {

    const Open = 0;
    const Created = 1;
  
    
    public static function getAll() {
        return array(
            CreditNoteStatus::Open => "Open CN",
            CreditNoteStatus::Created => "Created CN",
           // InvoiceStatus::Deleted => "Deleted",
        );
    }

    public static function getName($status) {
        $all = CreditNoteStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}

class StockTransferChallanStatus {

    const BeingCreated = 1;
    const AwaitingIn = 2;
    const Completed = 3;
    const Deleted = 4;
   
    public static function getAll() {
        return array(
            StockTransferChallanStatus::BeingCreated => "Being Created",
            StockTransferChallanStatus::AwaitingIn => "Awaiting For Stock Pull",
            StockTransferChallanStatus::Completed => "Completed",
            StockTransferChallanStatus::Deleted => "Deleted"
        );
    }

    public static function getName($status) {
        $all = StockTransferChallanStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}
         

class ImprestReason {

    const In = 1;
    const Out = 2;
    
    public static function getAll() {
        return array(
            ImprestReason::In => "In",
            ImprestReason::Out => "Out",
        );
    }

    public static function getName($status) {
        $all = DepositePaymentMode::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }

}

    class Months {

    const January = "01";
    const February = "02";
    const March = "03";
    const April = "04";
    const May = "05";
    const June = "06";
    const July = "07";
    const August = "08";
    const September = "09";
    const October = "10";
    const November = "11";
    const December = "12";

    public static function getAll() {

        return array(
            Months::January => "January",
            Months::February => "February",
            Months::March => "March",
            Months::April => "April",
            Months::May => "May",
            Months::June => "June",
            Months::July => "July",
            Months::August => "August",
            Months::September => "September",
            Months::October => "October",
            Months::November => "November",
            Months::December => "December",
        );
    }


    public static function getName($status) {
        $all = ProductPriceStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
           return "Not Found";
        }
    }
}


class Years {
    const LastYear = "2018";
    const CurrentYear = "2019";
    public static function getAll() {
        return array(
            Years::LastYear => "2018",
            Years::CurrentYear => "2019",
        );
    }

    public static function getName($status) {
        $all = ProductPriceStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}

class POAllocationStatus {

    const BeingCreated = 1;
    const AwaitingIn = 2;
    const Completed = 3;
    const Deleted = 4;
   
    public static function getAll() {
        return array(
            POAllocationStatus::BeingCreated => "Draft",
            POAllocationStatus::AwaitingIn => "Pending",
            POAllocationStatus::Completed => "Completed",
            POAllocationStatus::Deleted => "Deleted"
        );
    }

    public static function getName($status) {
        $all = StockTransferChallanStatus::getAll();
        if (isset($all[$status])) {
            return $all[$status];
        } else {
            return "Not Found";
        }
    }
}
   
 

