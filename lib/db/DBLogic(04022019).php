<?php

require_once "lib/db/DBConn.php";
require_once "lib/core/strutil.php";
require_once "lib/core/Constants.php";

class DBLogic {

    var $db;

    function getCategories() {
        $this->db = new DBConn();
        $query = "select * from it_categories";
        $obj_ctg = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_ctg)) {
            return $obj_ctg;
        } else {
            return NULL;
        }
    }

    function insertCategory($ctg, $userid) {
        $this->db = new DBConn();
        $ctg_db = $this->db->safe($ctg);
        $query = "insert into it_categories set name = $ctg_db, active = 1, createtime = now(), created_by = $userid";
        $ctg_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($ctg_id) && $ctg_id > 0) {
            return $ctg_id;
        } else {
            return NULL;
        }
    }

    function getCategoryByName($ctgname) {
        $this->db = new DBConn();
        $ctgname_db = $this->db->safe($ctgname);
        $query = "select * from it_categories where name = $ctgname_db";
        $obj_ctg = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_ctg)) {
            return $obj_ctg;
        } else {
            return NULL;
        }
    }

    function getSpecifications() {
        $this->db = new DBConn();
        $query = "select * from it_specifications";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function insertSpecification($spec, $userid) {
        $this->db = new DBConn();
        $spec_db = $this->db->safe($spec);
        $query = "insert into it_specifications set name = $spec_db, active = 1, createtime = now(), created_by = $userid";
        $spec_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($spec_id) && $spec_id > 0) {
            return $spec_id;
        } else {
            return NULL;
        }
    }

    function getSpecificationByName($specification) {
        $this->db = new DBConn();
        $spec_db = $this->db->safe($specification);
        $query = "select * from it_specifications where name = $spec_db";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getSpecificationById($id) {
        $this->db = new DBConn();
        $query = "select * from it_specifications where id = $id";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    //function insertProduct($ctgid, $name, $specid, $hsncode, $std_kg_per_pc, $userid){
    function insertProduct($ctg_id, $proddesc, $shortname, $desc1, $desc2, $thickness, $stdlength, $spec_id, $hsncode, $std_kg_per_pc, $userid) {
        $this->db = new DBConn();
        $name_db = $this->db->safe($proddesc);
        $shname_db = $this->db->safe($shortname);
        $desc1_query = "";
        $desc2_query = "";
        $thickness_query = "";
        if ($desc1 != null && trim($desc1) != "") {
            $desc1_db = $this->db->safe($desc1);
            $desc1_query = " ,desc1 = $desc1_db";
        }
        if ($desc2 != null && trim($desc2) != "") {
            $desc2_db = $this->db->safe($desc2);
            $desc2_query = " ,desc2 = $desc2_db";
        }
        if ($thickness != null && trim($thickness) != "") {
            $thickness_db = $this->db->safe($thickness);
            $thickness_query = " ,thickness = $thickness_db";
        }
        $stdlength_db = $this->db->safe($stdlength);
        $hsncode_db = $this->db->safe($hsncode);
        $query = "insert into it_products set ctg_id = $ctg_id, name = $name_db, shortname = $shname_db, stdlength = $stdlength_db, "
                . " spec_id = $spec_id, hsncode = $hsncode_db, kg_per_pc = $std_kg_per_pc, active = 1, createtime = now(), created_by = $userid $desc1_query $desc2_query $thickness_query";
//        echo $query."<br>";
        $prod_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($prod_id) && $prod_id > 0) {
            return $prod_id;
        } else {
            return NULL;
        }
    }

    function updateProduct($ctg_id, $proddesc, $shortname, $desc1, $desc2, $thickness, $stdlength, $spec_id, $hsncode, $std_kg_per_pc, $userid, $prod_id) {
        $this->db = new DBConn();
        $name_db = $this->db->safe($proddesc);
        $shname_db = $this->db->safe($shortname);
        $desc1_query = "";
        $desc2_query = "";
        $thickness_query = "";
        if ($desc1 != null && trim($desc1) != "") {
            $desc1_db = $this->db->safe($desc1);
            $desc1_query = " ,desc1 = $desc1_db";
        }
        if ($desc2 != null && trim($desc2) != "") {
            $desc2_db = $this->db->safe($desc2);
            $desc2_query = " ,desc2 = $desc2_db";
        }
        if ($thickness != null && trim($thickness) != "") {
            $thickness_db = $this->db->safe($thickness);
            $thickness_query = " ,thickness = $thickness_db";
        }
        $stdlength_db = $this->db->safe($stdlength);
        $hsncode_db = $this->db->safe($hsncode);

        $query = "update it_products set ctg_id = $ctg_id, name = $name_db, shortname = $shname_db, stdlength = $stdlength_db, spec_id = $spec_id, "
                . "hsncode = $hsncode_db, kg_per_pc = $std_kg_per_pc, active = 1, updatetime = now(), updated_by = $userid $desc1_query $desc2_query $thickness_query where id = $prod_id";
        //echo $query . "<br>";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getProductById($prodid) {
        $this->db = new DBConn();
        $query = "select * from it_products where id = $prodid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getProducts($prodid) {
        $this->db = new DBConn();
        $query = "select * from it_products where id = $prodid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getProductByName($prodname) {
        $this->db = new DBConn();
        $prodname_db = $this->db->safe($prodname);
        $query = "select * from it_products where name = $prodname_db";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getProduct($prodname, $shname, $desc1, $desc2, $thickness) {
        $this->db = new DBConn();
        $prodname_db = $this->db->safe($prodname);
        $shname_db = $this->db->safe($shname);
        $desc1_query = "";
        $desc2_query = "";
        $thickness_query = "";
        if ($desc1 != null && trim($desc1) != "") {
            $desc1_db = $this->db->safe($desc1);
            $desc1_query = " and desc1 = $desc1_db";
        }
        if ($desc2 != null && trim($desc2) != "") {
            $desc2_db = $this->db->safe($desc2);
            $desc2_query = " and desc2 = $desc2_db";
        }
        if ($thickness != null && trim($thickness) != "") {
            $thickness_db = $this->db->safe($thickness);
            $thickness_query = " and thickness = $thickness_db";
        }

        $query = "select * from it_products where name = $prodname_db and shortname = $shname_db $desc1_query $desc2_query $thickness_query";
        //echo $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getStates() {
        $this->db = new DBConn();
        $query = "select * from states";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function insertSupplier($dateofentry, $kycnumber, $companyname, $suppliercode, $bankname, $bankaccno, $bankbranchname, $firmtype, $currency, $state, $country, $district, $address, $graddress, $pincode, $panno, $cinno, $gstapp, $gstno, $contactperson1, $contactperson2, $contactperson3, $contactperson4, $phone1, $phone2, $phone3, $phone4, $email1, $email2, $email3, $email4, $msmedno, $userid) {
        $this->db = new DBConn();
        $addquery = "";

        if ($dateofentry) {
            $dateofentry_db = $this->db->safe($dateofentry);
            $addquery .= " ,date_of_entry = " . $dateofentry_db;
        }

        if ($kycnumber) {
            $kycnumber_db = $this->db->safe($kycnumber);
            $addquery .= " ,kyc_number = " . $kycnumber_db;
        }

        if ($companyname) {
            $companyname_db = $this->db->safe($companyname);
            $addquery .= " ,company_name = " . $companyname_db;

            $firstCharacter = substr($companyname, 0, 1);
            $suppliercode_db = isset($suppliercode) && $suppliercode != "" ? $this->db->safe($firstCharacter . $suppliercode) : false;
            if (isset($suppliercode_db)) {
                $addquery .= " ,supplier_code = " . $suppliercode_db;
            }
        }

        if ($bankname) {
            $bankname_db = $this->db->safe($bankname);
            $addquery .= " ,bank_name = " . $bankname_db;
        }

        if ($bankaccno) {
            $bankaccno_db = $this->db->safe($bankaccno);
            $addquery .= " ,bank_ac_no = " . $bankaccno_db;
        }

        if ($bankbranchname) {
            $bankbranchname_db = $this->db->safe($bankbranchname);
            $addquery .= " ,bank_branch = " . $bankbranchname_db;
        }

        if ($firmtype) {
            $firmtype_db = $this->db->safe($firmtype);
            $addquery .= " ,firm_type = " . $firmtype_db;
        }

        if ($currency) {
            $currency_db = $this->db->safe($currency);
            $addquery .= " ,currency = " . $currency_db;
        }

        if ($state) {
            $state = $state;
            $addquery .= " ,state = " . $state;
        }

        if ($country) {
            $country_db = $this->db->safe($country);
            $addquery .= " ,country = " . $country_db;
        }

        if ($district) {
            $district_db = $this->db->safe($district);
            $addquery .= " ,district = " . $district_db;
        }

        if ($address) {
            $address_db = $this->db->safe($address);
            $addquery .= " ,address = " . $address_db;
        }

        if ($graddress) {
            $graddress_db = $this->db->safe($graddress);
            $addquery .= " ,graddress = " . $graddress_db;
        }

        if ($pincode) {
            $pincode_db = $this->db->safe($pincode);
            $addquery .= " ,pincode = " . $pincode_db;
        }

        if ($panno) {
            $panno_db = $this->db->safe($panno);
            $addquery .= " ,pan_no = " . $panno_db;
        }

        if ($cinno) {
            $cinno_db = $this->db->safe($cinno);
            $addquery .= " ,cin_no = " . $cinno_db;
        }

        if ($gstapp) {
            $gstapp_db = intval($gstapp);
            $addquery .= " ,is_gst_applicable = " . $gstapp_db;
        }

        if ($gstno) {
            $gstno_db = $this->db->safe($gstno);
            $addquery .= " ,gst_no = " . $gstno_db;
        }

        if ($contactperson1) {
            $contactperson1_db = $this->db->safe($contactperson1);
            $addquery .= " ,contact_person1 = " . $contactperson1_db;
        }

        if ($contactperson2) {
            $contactperson2_db = $this->db->safe($contactperson2);
            $addquery .= " ,contact_person2 = " . $contactperson2_db;
        }

        if ($contactperson3) {
            $contactperson3_db = $this->db->safe($contactperson3);
            $addquery .= " ,contact_person3 = " . $contactperson3_db;
        }

        if ($contactperson4) {
            $contactperson4_db = $this->db->safe($contactperson4);
            $addquery .= " ,contact_person4 = " . $contactperson4_db;
        }

        if ($phone1) {
            $phone1_db = $this->db->safe($phone1);
            $addquery .= " ,phone1 = " . $phone1_db;
        }

        if ($phone2) {
            $phone2_db = $this->db->safe($phone2);
            $addquery .= " ,phone2 = " . $phone2_db;
        }

        if ($phone3) {
            $phone3_db = $this->db->safe($phone3);
            $addquery .= " ,phone3 = " . $phone3_db;
        }

        if ($phone4) {
            $phone4_db = $this->db->safe($phone4);
            $addquery .= " ,phone4 = " . $phone4_db;
        }

        if ($email1) {
            $email1_db = $this->db->safe($email1);
            $addquery .= " ,email1 = " . $email1_db;
        }

        if ($email2) {
            $email2_db = $this->db->safe($email2);
            $addquery .= " ,email2 = " . $email2_db;
        }

        if ($email3) {
            $email3_db = $this->db->safe($email3);
            $addquery .= " ,email3 = " . $email3_db;
        }

        if ($email4) {
            $email4_db = $this->db->safe($email4);
            $addquery .= " ,email4 = " . $email4_db;
        }

        if ($msmedno) {
            $msmedno_db = $this->db->safe($msmedno);
            $addquery .= " ,msmed_reg_no = " . $msmedno_db;
        }

        $query = "insert into it_suppliers set created_by = $userid $addquery";
        //echo $query;
        $supp_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($supp_id) && $supp_id > 0) {
            return $supp_id;
        } else {
            return NULL;
        }
    }

    function updateSupplier($dateofentry, $kycnumber, $companyname, $bankname, $bankaccno, $bankbranchname, $firmtype, $currency, $state, $country, $district, $address, $graddress, $pincode, $panno, $cinno, $gstapp, $gstno, $contactperson1, $contactperson2, $contactperson3, $contactperson4, $phone1, $phone2, $phone3, $phone4, $email1, $email2, $email3, $email4, $msmedno, $userid, $suppid) {
        $this->db = new DBConn();
        $addquery = "";

        if ($dateofentry) {
            $dateofentry_db = $this->db->safe($dateofentry);
            $addquery .= " ,date_of_entry = " . $dateofentry_db;
        }

        if ($kycnumber) {
            $kycnumber_db = $this->db->safe($kycnumber);
            $addquery .= " ,kyc_number = " . $kycnumber_db;
        }

        if ($companyname) {
            $companyname_db = $this->db->safe($companyname);
            $addquery .= " ,company_name = " . $companyname_db;

            /* $firstCharacter = substr($companyname, 0, 1);
              $suppliercode_db = isset($suppliercode) && $suppliercode != "" ? $this->db->safe($firstCharacter.$suppliercode) : false;
              if(isset($suppliercode_db)){ $addquery .= " ,supplier_code = ".$suppliercode_db; } */
        }

        if ($bankname) {
            $bankname_db = $this->db->safe($bankname);
            $addquery .= " ,bank_name = " . $bankname_db;
        }

        if ($bankaccno) {
            $bankaccno_db = $this->db->safe($bankaccno);
            $addquery .= " ,bank_ac_no = " . $bankaccno_db;
        }

        if ($bankbranchname) {
            $bankbranchname_db = $this->db->safe($bankbranchname);
            $addquery .= " ,bank_branch = " . $bankbranchname_db;
        }

        if ($firmtype) {
            $firmtype_db = $this->db->safe($firmtype);
            $addquery .= " ,firm_type = " . $firmtype_db;
        }

        if ($currency) {
            $currency_db = $this->db->safe($currency);
            $addquery .= " ,currency = " . $currency_db;
        }

        if ($state) {
            $state = $state;
            $addquery .= " ,state = " . $state;
        }

        if ($country) {
            $country_db = $this->db->safe($country);
            $addquery .= " ,country = " . $country_db;
        }

        if ($district) {
            $district_db = $this->db->safe($district);
            $addquery .= " ,district = " . $district_db;
        }

        if ($address) {
            $address_db = $this->db->safe($address);
            $addquery .= " ,address = " . $address_db;
        }

        if ($graddress) {
            $graddress_db = $this->db->safe($graddress);
            $addquery .= " ,graddress = " . $graddress_db;
        }

        if ($pincode) {
            $pincode_db = $this->db->safe($pincode);
            $addquery .= " ,pincode = " . $pincode_db;
        }

        if ($panno) {
            $panno_db = $this->db->safe($panno);
            $addquery .= " ,pan_no = " . $panno_db;
        }

        if ($cinno) {
            $cinno_db = $this->db->safe($cinno);
            $addquery .= " ,cin_no = " . $cinno_db;
        }

        if ($gstapp) {
            $gstapp_db = intval($gstapp);
            $addquery .= " ,is_gst_applicable = " . $gstapp_db;
        }

        if ($gstno) {
            $gstno_db = $this->db->safe($gstno);
            $addquery .= " ,gst_no = " . $gstno_db;
        }

        if ($contactperson1) {
            $contactperson1_db = $this->db->safe($contactperson1);
            $addquery .= " ,contact_person1 = " . $contactperson1_db;
        }

        if ($contactperson2) {
            $contactperson2_db = $this->db->safe($contactperson2);
            $addquery .= " ,contact_person2 = " . $contactperson2_db;
        }

        if ($contactperson3) {
            $contactperson3_db = $this->db->safe($contactperson3);
            $addquery .= " ,contact_person3 = " . $contactperson3_db;
        }

        if ($contactperson4) {
            $contactperson4_db = $this->db->safe($contactperson4);
            $addquery .= " ,contact_person4 = " . $contactperson4_db;
        }

        if ($phone1) {
            $phone1_db = $this->db->safe($phone1);
            $addquery .= " ,phone1 = " . $phone1_db;
        }

        if ($phone2) {
            $phone2_db = $this->db->safe($phone2);
            $addquery .= " ,phone2 = " . $phone2_db;
        }

        if ($phone3) {
            $phone3_db = $this->db->safe($phone3);
            $addquery .= " ,phone3 = " . $phone3_db;
        }

        if ($phone4) {
            $phone4_db = $this->db->safe($phone4);
            $addquery .= " ,phone4 = " . $phone4_db;
        }

        if ($email1) {
            $email1_db = $this->db->safe($email1);
            $addquery .= " ,email1 = " . $email1_db;
        }

        if ($email2) {
            $email2_db = $this->db->safe($email2);
            $addquery .= " ,email2 = " . $email2_db;
        }

        if ($email3) {
            $email3_db = $this->db->safe($email3);
            $addquery .= " ,email3 = " . $email3_db;
        }

        if ($email4) {
            $email4_db = $this->db->safe($email4);
            $addquery .= " ,email4 = " . $email4_db;
        }

        if ($msmedno) {
            $msmedno_db = $this->db->safe($msmedno);
            $addquery .= " ,msmed_reg_no = " . $msmedno_db;
        }

        $query = "update it_suppliers set updated_by = $userid, updatetime = now() $addquery where id = $suppid";
        //echo $query;
        $supp_id = $this->db->execUpdate($query);
        $this->db->closeConnection();
        if (isset($supp_id) && $supp_id > 0) {
            return $supp_id;
        } else {
            return NULL;
        }
    }

    function updateSupplierCode($prefix, $code) {
        $this->db = new DBConn();
        $prefix_db = $this->db->safe($prefix);
        $code_db = $this->db->safe($code);
        $query = "update it_supplier_codes set snumber = $code_db, updatetime=now() where prefix = $prefix_db";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function insertDC($name, $contact_person, $address, $state, $phoneno, $email, $gstno, $panno, $userid, $baseName) {
        $this->db = new DBConn();
        $addquery = "";
        $name_db = $this->db->safe($name);
        $contact_person_db = $this->db->safe($contact_person);
        $address_db = $this->db->safe($address);
        $state_db = $state;
        $phoneno_db = $this->db->safe($phoneno);
        $email_db = $this->db->safe($email);
        $gstno_db = $this->db->safe($gstno);
        if (isset($panno)) {
            $panno_db = $this->db->safe($panno);
            $addquery .= " , panno = $panno_db";
        }

        if (isset($baseName)) {
            $baseName_db = $this->db->safe($baseName);
            $addquery .= " , panimg = $baseName_db";
        }

        $query = "insert into it_dc_master set dc_name = $name_db, contact_person = $contact_person_db, address = $address_db, emailaddress = $email_db,"
                . "phoneno = $phoneno_db, gstno = $gstno_db, state = $state_db, created_by = $userid $addquery";
        //echo $query;
        $dc_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($dc_id) && $dc_id > 0) {
            return $dc_id;
        } else {
            return NULL;
        }
    }

    function insertRFC($name, $contact_person, $address, $state, $phoneno, $email, $gstno, $panno, $userid) {
        $this->db = new DBConn();
        $addquery = "";
        $name_db = $this->db->safe($name);
        $contact_person_db = $this->db->safe($contact_person);
        $address_db = $this->db->safe($address);
        $state_db = $state;
        $phoneno_db = $this->db->safe($phoneno);
        $email_db = $this->db->safe($email);
        $gstno_db = $this->db->safe($gstno);
        if (isset($panno)) {
            $panno_db = $this->db->safe($panno);
            $addquery .= " , panno = $panno_db";
        }

        $query = "insert into it_rfc_master set rfc_name = $name_db, contact_person = $contact_person_db, address = $address_db, emailaddress = $email_db,"
                . "phoneno = $phoneno_db, gstno = $gstno_db, state = $state_db, created_by = $userid $addquery";
        //echo $query;
        $dc_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($dc_id) && $dc_id > 0) {
            return $dc_id;
        } else {
            return NULL;
        }
    }

    function getAllActiveProducts() {
        $this->db = new DBConn();
        $query = "select p.id,p.name as prod,c.name as ctg,p.spec_id,p.created_by,p.updated_by,p.createtime,p.updatetime,p.hsncode,"
                . "p.kg_per_pc,p.shortname,p.desc1,p.desc2,p.thickness,p.stdlength from it_products p, it_categories c where c.id = p.ctg_id "
                . "and p.active = 1 order by p.id";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getActiveProductsCount() {
        $this->db = new DBConn();
        $query = "select count(*) as cnt from it_products where active = 1";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllActiveCategories() {
        $this->db = new DBConn();
        $query = "select * from it_categories where active = 1";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllActiveSpecifications() {
        $this->db = new DBConn();
        $query = "select * from it_specifications where active = 1";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllActiveSuppliers() {
        $this->db = new DBConn();
        $query = "select s.*,f.firm_type,c.iso_code as currency from it_suppliers s , it_firm_type f, it_currencies c "
                . "where f.id = s.firm_type and c.id = s.currency and s.inactive = 0 order by s.id";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getUserInfoById($userid) {
        $this->db = new DBConn();
        $query = "select * from it_users where id = $userid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getUserInfoByType($usertype) {
        $this->db = new DBConn();
        $query = "select * from it_users where usertype = $usertype";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getFirmTypes() {
        $this->db = new DBConn();
        $query = "select * from it_firm_type";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllCurrencies() {
        $this->db = new DBConn();
        $query = "select * from it_currencies";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getSupplierById($suppid) {
        $this->db = new DBConn();
        $query = "select * from it_suppliers where id = $suppid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllPaymentTerms() {
        $this->db = new DBConn();
        $query = "select * from it_payment_terms";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllDeliveryTerms() {
        $this->db = new DBConn();
        $query = "select * from it_delivery_terms";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllTransitInsurance() {
        $this->db = new DBConn();
        $query = "select * from it_transit_insurance";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function insertPO($suppsel, $paymentterms, $deliveryterms, $transitinsurance, $dccode, $postatus, $ponum, $userid) {
        $this->db = new DBConn();
        $addquery = "";
        //$suppsel,$paymentterms,$deliveryterms,$transitinsurance,$dccode,$postatus,$ponum,$userid
        if ($suppsel) {
            $suppsel_id = $suppsel;
            $addquery .= " ,supplier_id = " . $suppsel_id;
        }

//        if($suppcontractno){
//            $suppcontractno_db = $this->db->safe($suppcontractno);
//            $addquery .= " ,supp_contract_no = ".$suppcontractno_db;
//        }

        if ($paymentterms) {
            $paymentterms_db = $paymentterms;
            $addquery .= " ,payment_id = " . $paymentterms_db;
        }

        if ($deliveryterms) {
            $deliveryterms_db = $deliveryterms;
            $addquery .= " ,delivery_id = " . $deliveryterms_db;
        }

        if ($transitinsurance) {
            $transitinsurance_db = $transitinsurance;
            $addquery .= " ,transit_id = " . $transitinsurance_db;
        }

//        if($buyercode){
//            $buyercode_db = $this->db->safe($buyercode);
//            $addquery .= " ,buyer_code = ".$buyercode_db;
//        }
//        
//        if($buyername){
//            $buyername_db = $this->db->safe($buyername);
//            $addquery .= " ,buyer_name = ".$buyername_db;
//        }
//        
//        if($referance1){
//            $referance1_db = $this->db->safe($referance1);
//            $addquery .= " ,referance1 = ".$referance1_db;
//        }
//        
//        if($referance2){
//            $referance2_db = $this->db->safe($referance2);
//            $addquery .= " ,referance2 = ".$referance2_db;
//        }

        if ($dccode) {
            $dccode_db = $this->db->safe($dccode);
            $addquery .= " ,dccode = " . $dccode_db;
        }

//        if($deliveryname){
//            $deliveryname_db = $this->db->safe($deliveryname);
//            $addquery .= " ,delivery_name = ".$deliveryname_db;
//        }

        if ($postatus) {
            $postatus_db = $postatus;
            $addquery .= " ,po_status = $postatus_db";
        }

        if ($ponum) {
            $ponum_db = $this->db->safe($ponum);
            $addquery .= " ,pono = $ponum_db";
        }

        $query = "insert into it_purchaseorder set createdby_id = $userid $addquery";
        //return $query;
        $po_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($po_id) && $po_id > 0) {
            $this->updatePONumber();
            return $po_id;
        } else {
            return NULL;
        }
    }

    function fetchNextPONumber($stateid) {
        $this->db = new DBConn();
        $query = "select * from it_ponum where stateid = $stateid";
        //return $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj->num + 1;
        } else {
            return NULL;
        }
    }

    function updatePONumber() {
        $this->db = new DBConn();
        $query = "update it_ponum set num = num + 1";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getPODetails($poid) {
        $this->db = new DBConn();
        $query = "select p.id,p.pono,p.createtime,p.submittedby,p.createdby_id,p.submittedtime as submitdate,p.po_status as po_status,p.tot_qty as totalqty,p.tot_value as totalvalue,"
                . "p.freight_total as freightcharges,p.freightamt as freightamt,p.freight_gst as freight_gst,p.freight_total as freight_total,p.is_mailsent as is_mailsent,p.remark_note as remark_note,"
                . "p.delivery_note as delivery_note,p.picking_days as picking_days,p.header_note as header_note,p.cancelreason,s.supplier_code,s.email1 as email,s.company_name,s.address,s.pan_no,s.gst_no,s.pincode,states.state,s.cin_no,s.country,s.district,"
                . "p.buyer_code,p.buyer_name,p.referance1,p.referance2,p.dccode,p.delivery_name,pm.code as pmcode,pm.term as pmterm,dt.code as dtcode,"
                . "dt.term as dtterm,dt.is_freightapplicable,dt.is_transportapplicable,ti.code as ticode,ti.term as titerm,p.supp_contract_no,dc.dc_name as dcname,dc.address as dc_master_address from it_purchaseorder p, it_payment_terms pm, it_delivery_terms dt,"
                . "it_transit_insurance ti, it_suppliers s ,it_dc_master dc,it_transportation trans,states where s.id = p.supplier_id and pm.id = p.payment_id and dt.id = p.delivery_id and ti.id = p.transit_id and s.state = states.id"
                . " and p.id = $poid";
        //echo $query;
        $obj_po = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_po) && $obj_po != NULL) {
            return $obj_po;
        } else {
            return NULL;
        }
    }

    function insertPOItem($poid, $prodid, $ctgid, $qty, $rate, $exdate, $length, $colorsel, $brandsel, $manfsel, $pieces, $lcrate, $cgstpct, $cgstval, $sgstpct, $sgstval, $totalrate, $totalvalue) {
        $this->db = new DBConn();
        $exdate_db = $this->db->safe($exdate);
        $addquery = "";
        if ($colorsel > 0) {
            $addquery .= " ,color_id = $colorsel";
        }
        if ($brandsel > 0) {
            $addquery .= " ,brand_id = $brandsel";
        }
        if ($manfsel > 0) {
            $addquery .= " ,manufacturer_id = $manfsel";
        }

        $query = "insert into it_polines set po_id = $poid, product_id = $prodid, ctg_id=$ctgid, qty = $qty, rate = $rate, expected_date = now(),"
                . " length = $length, no_of_pieces = $pieces, lcrate = $lcrate, cgstpct = $cgstpct, cgstval = $cgstval, sgstpct = $sgstpct, sgstval = $sgstval,"
                . " totalrate = $totalrate, totalvalue = $totalvalue $addquery";
        //echo $query;
        $poitem_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($poitem_id) && $poitem_id > 0) {
            $this->updatePOTotals($poid);
            return $poitem_id;
        } else {
            return NULL;
        }
    }

    function getPOItems($poid) {
        $this->db = new DBConn();
        $query = "select p.name as prod,p.desc1 as desc_1,p.desc2 as desc_2,p.thickness as thickness,p.hsncode as hsncode,p.kg_per_pc as kg_per_pc,"
                . "spec.name as speci,ctg.name as category,cls.color as color, mf.manufacturer as manufacturer, b.brand as brand,"
                . "pl.* from it_polines pl, it_products p,it_specifications spec,it_categories ctg,it_colors cls,it_manufacturer mf,"
                . "it_brands b where p.id = pl.product_id and p.spec_id = spec.id and pl.ctg_id=ctg.id and cls.id = pl.color_id and "
                . "mf.id = pl.manufacturer_id and b.id = brand_id and pl.po_id = $poid order by pl.id";
        //echo $query;
        $obj_polines = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_polines) && $obj_polines != NULL) {
            return $obj_polines;
        } else {
            return NULL;
        }
    }

    function updatePOTotals($poid) {
        $this->db = new DBConn();
        $query = "select sum(qty) as tot_qty, sum(totalvalue) as tot_value from it_polines where po_id = $poid";
        //echo $query."<br>";
        $obj_pototal = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_pototal) && $obj_pototal != NULL) {
            $query = "update it_purchaseorder set tot_qty = $obj_pototal->tot_qty, tot_value = round($obj_pototal->tot_value,3) where id = $poid";
            //echo $query."<br>";
            $this->db->execUpdate($query);
            return $obj_pototal;
        } else {
            return NULL;
        }
    }

    //($pid, $postatus, $userid, $freightamt, $freightgst, $totalfreight, $gstsel, $transportsel,$supoffers,$offerref,$datepicker,$nodays);
    function savePO($pid, $postatus, $userid, $freightamt, $freightgst, $totalfreight, $gstsel, $transportsel, $supoffers, $offerref, $datepicker, $nodays, $po_remarks) {
        $this->db = new DBConn();
        $addquery = "";
        if ($freightamt > 0) {
            $freightamt_db = $freightamt;
            $addquery .= " ,freightamt = " . $freightamt_db;
        }

        if ($freightgst > 0) {
            $freightgst_db = $freightgst;
            $addquery .= " ,freight_gst = " . $freightgst_db;
        }

        if ($totalfreight > 0) {
            $totalfreight_db = $totalfreight;
            $addquery .= " ,freight_total = " . $totalfreight_db;
        }

//        if($gstsel  > 0){
//            $gstsel_db = $gstsel;
//            $addquery .= " ,email = ".$gstsel_db;
//        }

        if ($transportsel > 0) {
            $transportsel_db = $transportsel;
            $addquery .= " ,transport_id = " . $transportsel_db;
        }

        if (isset($supoffers)) {
            $supoffers_db = $supoffers;
            $addquery .= " ,supplyeroffer_id = " . $this->db->safe($supoffers_db);
        }

        if (isset($offerref)) {
            $offerref_db = $offerref;
            $addquery .= " ,offer_ref = " . $this->db->safe($offerref_db);
        }

        if (isset($datepicker)) {
            $datepicker_db = $datepicker;
            $addquery .= " ,picking_date = " . $this->db->safe($datepicker_db);
        }

        if (isset($po_remarks)) {
            $po_remarks_db = $po_remarks;
            $addquery .= " ,remark_note = " . $this->db->safe($po_remarks_db);
        }

        if (isset($nodays) && $nodays > 0) {
            $nodays_db = $nodays;
            $addquery .= " ,picking_days = " . $nodays_db;
        }

        $query = "update it_purchaseorder set po_status = $postatus $addquery where id = $pid";
        //echo $query."<br>";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function approvePO($poid, $postatus, $userid, $remarks) {
        $this->db = new DBConn();
        $remarks_db = "";
        if (isset($remarks)) {
            $remarks = $this->db->safe($remarks);
            $remarks_db = " ,remarks = $remarks";
        }
        $query = "update it_purchaseorder set po_status = $postatus, approvedtime = now(), approvedby = $userid $remarks_db where id = $poid";
        //echo $query."<br>";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function rejectPO($poid, $postatus, $userid, $remarks) {
        $this->db = new DBConn();
        $remarks_db = "";
        if (isset($remarks)) {
            $remarks = $this->db->safe($remarks);
            $remarks_db = " ,remarks = $remarks";
        }
        $query = "update it_purchaseorder set po_status = $postatus, rejectedtime = now(), rejectedby = $userid $remarks_db where id = $poid";
        //echo $query."<br>";        
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function deletePO($poid, $postatus, $userid) {
        $this->db = new DBConn();
        $query = "update it_purchaseorder set po_status = $postatus, deletedtime = now(), deletedby = $userid where id = $poid";
        //echo $query."<br>";        
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function submitPO($poid, $postatus, $userid) {
        $this->db = new DBConn();
        $query = "update it_purchaseorder set po_status = $postatus, submittedtime = now(), submittedby = $userid where id = $poid";
        //echo $query."<br>";        
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function insertUser($utypesel, $uname, $username, $email, $password, $phoneno, $userid) {
        $this->db = new DBConn();
        $addquery = "";

        if ($utypesel) {
            $utypesel_id = $utypesel;
            $addquery .= " ,usertype = " . $utypesel_id;
        }

        if ($uname) {
            $uname_db = $this->db->safe($uname);
            $addquery .= " ,name = " . $uname_db;
        }

        if ($username) {
            $username_db = $this->db->safe($username);
            $addquery .= " ,username = " . $username_db;
        }

        if ($email) {
            $email_db = $this->db->safe($email);
            $addquery .= " ,email = " . $email_db;
        }

        if ($password) {
            $password_db = $password;
            $addquery .= " ,password = md5('" . $password_db . "')";
        }

        if ($phoneno) {
            $phoneno_db = $this->db->safe($phoneno);
            $addquery .= " ,phoneno = " . $phoneno_db;
        }

        $query = "insert into it_users set created_by = $userid $addquery";
        //echo $query;
        $po_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($po_id) && $po_id > 0) {
            $this->updatePONumber();
            return $po_id;
        } else {
            return NULL;
        }
    }

    function getNoLoginUsers($usertype) {
        $this->db = new DBConn();
        $query = "select * from it_users where usertype = $usertype";
        $obj_users = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_users) && $obj_users != NULL) {
            return $obj_users;
        } else {
            return NULL;
        }
    }

    function insertNoLoginUser($name) {
        $this->db = new DBConn();
        $name_db = $this->db->safe($name);
        $username = $this->db->safe("na" . $name);
        $password = $this->db->safe("na" . $name);
        $usertype = UserType::NoLogin;
        $query = "insert into it_users set username = $username, password = md5($password), name = $name_db, usertype = $usertype";
        //echo $query;
        $user_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($user_id) && $user_id != NULL) {
            return $user_id;
        } else {
            return NULL;
        }
    }

    function getTransporters() {
        $this->db = new DBConn();
        $query = "select * from it_transporters";
        $obj_transporter = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_transporter) && $obj_transporter != NULL) {
            return $obj_transporter;
        } else {
            return NULL;
        }
    }

    function insertGateEntry($suppsel, $transsel, $lrno, $details, $qty, $receiver_id, $userid) {
        $this->db = new DBConn();
        $addquery = "";
        if ($details) {
            $details = $this->db->safe($details);
            $addquery = " ,transport_dtls = $details";
        }
        $lrno = $this->db->safe($lrno);
        $query = "insert into it_gateentry set supplier_id = $suppsel, transport_id = $transsel, lrno = $lrno, qty_received = $qty, received_by = $receiver_id,"
                . "entered_by = $userid $addquery";
        //return $query;
        $id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($id) && $id != NULL) {
            return $id;
        } else {
            return NULL;
        }
    }

    function getGateEntryDetails($gateentryid) {
        $this->db = new DBConn();
        $query = "select * from it_gateentry where id = $gateentryid";
        $obj_gateentry = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_gateentry) && $obj_gateentry != NULL) {
            return $obj_gateentry;
        } else {
            return NULL;
        }
    }

    function editGateEntry($suppsel, $transsel, $lrno, $details, $qty, $receiver_id, $userid, $gateentryid) {
        $this->db = new DBConn();
        $addquery = "";
        if ($details) {
            $details = $this->db->safe($details);
            $addquery = " ,transport_dtls = $details";
        } else {
            $addquery = " ,transport_dtls = ''";
        }
        $lrno = $this->db->safe($lrno);
        $query = "update it_gateentry set supplier_id = $suppsel, transport_id = $transsel, lrno = $lrno, qty_received = $qty, received_by = $receiver_id,"
                . "updated_by = $userid, updatetime = now() $addquery where id = $gateentryid";
        //
        //echo $query;
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getPODetailsByPONo($pono) {
        $this->db = new DBConn();
        $pono_db = $this->db->safe($pono);
        $query = "select * from it_purchaseorder where pono = $pono_db";
        $obj_po = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_po) && $obj_po != NULL) {
            return $obj_po;
        } else {
            return NULL;
        }
    }

    function insertSupplierBill($gateentryid, $supplierid, $poid, $billno, $billdate, $status, $userid) {
        $this->db = new DBConn();
        $billno_db = $this->db->safe($billno);
        $billdate_db = $this->db->safe($billdate);
        $query = "insert into it_supplier_bill set po_id = $poid, gateentry_id = $gateentryid, supplier_id = $supplierid,"
                . " billno = $billno_db, bill_date = $billdate_db,status = $status, createdby = $userid";
        //echo $query;
        $id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($id) && $id != NULL) {
            return $id;
        } else {
            return NULL;
        }
    }

    function getSupplierBillDetails($billid) {
        $this->db = new DBConn();
        $query = "select sb.id,sb.po_id,p.pono,p.submittedtime,sb.gateentry_id as gatentryno,sb.supplier_id,s.company_name,"
                . "sb.status,sb.billno,sb.bill_date,g.createtime as gateentry_date from it_supplier_bill sb, it_suppliers s,it_purchaseorder p,"
                . "it_gateentry g where p.id = sb.po_id and g.id = sb.gateentry_id and "
                . "s.id = sb.supplier_id and sb.id = $billid";
        $obj_supp_bill = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_supp_bill) && $obj_supp_bill != NULL) {
            return $obj_supp_bill;
        } else {
            return NULL;
        }
    }

    function getPOProductsBySupplierBill($billid) {
        $this->db = new DBConn();
        $query = "select pr.name,pl.id as polineid, pl.product_id,pl.qty,pl.rate,pl.expected_date from it_products pr,it_polines pl, "
                . "it_purchaseorder p, it_supplier_bill sb where pr.id = pl.product_id and pl.po_id = p.id and "
                . "p.id = sb.po_id and sb.id = $billid order by pl.id";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getSupplierBillByGE($gateentryid) {
        $this->db = new DBConn();
        $query = "select * from it_supplier_bill where gateentry_id = $gateentryid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getSupplierBillItems($billid) {
        $this->db = new DBConn();
        $query = "select si.id,p.name as product, si.qty, si.rate, (si.qty * si.rate) as value, si.receiveddate "
                . "from it_supplier_bill_items si , it_products p where p.id = si.product_id and si.bill_id = $billid order by si.id";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getPOLineItemById($polineid) {
        $this->db = new DBConn();
        $query = "select pr.name,pl.id as polineid, pl.product_id,pl.qty,pl.rate,pl.expected_date "
                . "from it_products pr,it_polines pl, it_purchaseorder p where pr.id = pl.product_id "
                . "and pl.po_id = p.id and pl.id = $polineid order by pl.id";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function insertSupplierBillItem($billid, $polineid, $prodid, $poqty, $porate, $poexdate, $receivedqty, $receivedrate, $receiveddate, $poid, $userid) {
        $this->db = new DBConn();
        $poexdate_db = $this->db->safe($poexdate);
        $receiveddate_db = $this->db->safe($receiveddate);
        $query = "insert into it_supplier_bill_items set bill_id = $billid, poid = $poid, poline_id = $polineid, product_id = $prodid, qty = $receivedqty, poqty = $poqty, "
                . "rate = $receivedrate, porate = $porate, poexdate = $poexdate_db, receiveddate = $receiveddate_db, createdby = $userid";
        //echo $query;
        $id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($id) && $id != NULL) {
            return $id;
        } else {
            return NULL;
        }
    }

    function checkSuppBillItemInserted($polineid, $billid) {
        $this->db = new DBConn();
        $query = "select * from it_supplier_bill_items where bill_id = $billid and poline_id = $polineid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function suppBillSubmit($billid, $status, $userid) {
        $this->db = new DBConn();
        $query = "update it_supplier_bill set status = $status, submittedby = $userid, submittedtime = now(), updatetime = now() where id = $billid";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getProductsForInspection() {
        $this->db = new DBConn();
        $status = SupplierBillStatus::Submit;
        $query = "select p.name as product,sb.billno,pr.pono,sbi.qty,sbi.rate,sbi.rate*sbi.qty as value,sbi.receiveddate "
                . "from it_products p, it_purchaseorder pr, it_supplier_bill sb, it_supplier_bill_items sbi where sb.id = sbi.bill_id "
                . "and p.id = sbi.product_id and sb.status = 1 and pr.id = sbi.poid and sbi.inspected = 0";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function uploadProductPrice($prodid, $price, $userid, $cr, $last_price, $uploaddate) {
        $this->db = new DBConn();
        $uploaddate_db = $this->db->safe($uploaddate);
//        $query = "insert into it_product_price set product_id = $prodid, price = $price, lastprice = $last_price, applicable_date = date(now()), createdby = $userid, crid = $cr";
        $query = "insert into it_product_price set product_id = $prodid, price = $price, lastprice = $last_price, applicable_date = $uploaddate_db, createdby = $userid, crid = $cr";
        $id = $this->db->execInsert($query);
        $this->db->closeConnection();
        return $id;
    }

    function fetchLastProductPrice($prodid, $price, $cr) {
        $this->db = new DBConn();
        $query = "select * from it_product_price where product_id = $prodid and crid = $cr order by id desc limit 1";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function fetchProductPrice($prodid, $price) {
        $this->db = new DBConn();
        $query = "select * from it_product_price where product_id = $prodid and price = $price and applicable_date = date(now())";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getPriceApprovalDates() {
        $this->db = new DBConn();
        $query = "select distinct date(applicable_date) as applicable_date from it_product_price order by applicable_date desc";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function updateProductPrice($prodid, $price, $userid, $cr) {
        $this->db = new DBConn();
        $query = "update it_product_price set product_id = $prodid, price = $price, applicable_date = date(now()),"
                . " updatedby = $userid, updatetime = now(), rfc_id = $cr where id = $userid";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

	function fetchProductPriceByProdId($prodid) {
        	$date= date('Y-m-d');
        	$startDate=$date." 00:00:00";
        	$endDate=$date." 23:59:59";
        	$this->db = new DBConn();
        	$query = "select p.kg_per_pc,pr.* from it_product_price pr, it_products p where p.id = pr.product_id and pr.product_id = $prodid and pr.is_approved = true and pr.applicable_date >='".$startDate."' and pr.applicable_date <='".$endDate."' ";
        	error_log("\nSalesQry query: " . $query . "\n", 3, "tmp.txt");
        	$obj = $this->db->fetchObject($query);
        	$this->db->closeConnection();
        	if (isset($obj) && $obj != NULL) {
            		return $obj;
        	} else {
            		return NULL;
        	}
    	}
    function getSalesInfo($userid, $saleid) {
        $this->db = new DBConn();
        $tablename = $this->getSalesTableName($userid);
        $query = "select * from $tablename where id = $saleid";
        //echo $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getCustomerByPhone($phone) {
        $this->db = new DBConn();
        $phonedb = $this->db->safe($phone);
        $query = "select * from it_customers where phone = $phonedb";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function insertCustomer($name, $address, $statesel, $city, $phone, $email, $gstno, $panno, $userid) {
        $this->db = new DBConn();

        $namedb = $this->db->safe($name);
        $address = isset($address) && $address != "" ? " ,address = " . $this->db->safe($address) : "";
        $statesel = ",state_id = " . $statesel;
        $city = isset($city) && $city != "" ? " ,city = " . $this->db->safe($city) : "";
        $phone = isset($phone) && $phone != "" ? " ,phone = " . $this->db->safe($phone) : "";
        $email = isset($email) && $email != "" ? " ,email = " . $this->db->safe($email) : "";
        $gstno = isset($gstno) && $gstno != "" ? " ,gstno = " . $this->db->safe($gstno) : "";
        $panno = isset($panno) && $panno != "" ? " ,panno = " . $this->db->safe($panno) : "";

        $query = "insert into it_customers set name = $namedb , createdby = $userid $address $statesel $city $phone $email $gstno $panno";
        $customer_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        return $customer_id;
    }

    function getCustomerById($custid) {
        $this->db = new DBConn();
        $query = "select * from it_customers where id = $custid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function removeCustFromInv($userid, $custid, $salesid) {
        $this->db = new DBConn();
        $tablename = $this->getSalesTableName($userid);
        $query = "update " . $tablename . " set cname = null, cphone = null, customer_id = null, updatetime = now() where id =" . $salesid;
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getSalesTableName($userid) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        //$query = "select crcode from it_rfc_master where userid = $userid";
        $query = "select crcode from it_rfc_master where id = $crid";
        $obj_cr = $this->db->fetchObject($query);
        $crcode = $obj_cr->crcode;
        $tablename = "it_" . $crcode;
        $this->db->closeConnection();
        return $tablename;
    }

    function getSalesItemsTableName($userid) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        //$query = "select crcode from it_rfc_master where userid = $userid";
        $query = "select crcode from it_rfc_master where id = $crid";
        //error_log("\nSalesQry query: ".$query."\n",3,"tmp.txt");
        $obj_cr = $this->db->fetchObject($query);
        $crcode = $obj_cr->crcode;
        $tablename = "it_" . $crcode . "_items";
        $this->db->closeConnection();
        return $tablename;
    }

    function saveInvoice($userid, $custid, $invoicetype, $status) {
        $this->db = new DBConn();
        //$saledate_db = $this->db->safe($saledate);
        $custquery = "";
        if ($custid > 0) {
            $obj_cust = $this->getCustomerById($custid);
            $name = $this->db->safe($obj_cust->name);
            $phone = $this->db->safe($obj_cust->phone);
            $custquery = " ,customer_id = $custid, cname = $name, cphone = $phone";
        }
        $tablename = $this->getSalesTableName($userid);
        $query = "insert into $tablename set invoice_type = $invoicetype, status = $status,saledate = date(now()), createdby = $userid, createtime = now() $custquery";
        //$query = "insert into $tablename set invoice_type = $invoicetype, status = $status, createdby = $userid, createtime = now() $custquery";
        //$query = "insert into $tablename set invoice_type = $invoicetype, status = $status, saledate = $saledate_db, createdby = $userid, createtime = now() $custquery";
        //echo $query;
        $saleid = $this->db->execInsert($query);
        $this->db->closeConnection();
        return $saleid;
    }

    function getTaxRate($prodid) {
        $this->db = new DBConn();
        $query = "select ctg_id from it_products where id = $prodid";
        $obj_product = $this->db->fetchObject($query);
        $ctg_id = 0;
        if ($obj_product != NULL) {
            $ctg_id = $obj_product->ctg_id;
        }
        $query = "select * from it_taxes where category_id = $ctg_id and validfrom_date <= date(now()) order by id desc limit 1";
        $obj_tax = $this->db->fetchObject($query);
        $tax_rate = 0;
        if ($obj_tax != null) {
            $tax_rate = $obj_tax->rate;
        }
        $this->db->closeConnection();
        return $tax_rate;
    }

    /* function insertInvoiceItem($userid, $saleid, $prodid,$stockcurrid,$batchcodes, $batchqty,$qty, $mrp, $cutting_charge, $actual_rate,$batcharray) {
      $this->db = new DBConn();
      $inv_item_table = $this->getSalesItemsTableName($userid);
      $tax_rate = $this->getTaxRate($prodid);
      $trate = 1 + ($tax_rate / 100);
      $total = round($qty * $mrp, 2);
      $rate = round($mrp / $trate, 2);
      $taxable_amt = round($total / $trate, 2);
      $tax_amt = round($total - $taxable_amt, 2);
      $cgst_per = round($tax_rate / 2, 2);
      $sgst_per = round($tax_rate / 2, 2);
      $igst_per = round($tax_rate, 2);
      $cgst_amt = round($tax_amt / 2, 2);
      $sgst_amt = round($tax_amt / 2, 2);
      $igst_amt = round($tax_amt, 2);
      $batchcode_db = $this->db->safe($batchcodes);

      $query = "insert into $inv_item_table set invoice_id = $saleid, product_id = $prodid,batchcode = $batchcode_db, qty = $qty, mrp = $mrp,"
      . " rate = $rate, taxable = $taxable_amt, cgst_percent = $cgst_per, cgst_amt = $cgst_amt, sgst_percent = $sgst_per, sgst_amt = $sgst_amt,"
      . "igst_percent = $igst_per, igst_amt = $igst_amt, total = $total, cuttingcharges = $cutting_charge, actualrate = $actual_rate ";
      //echo $query;
      $item_id = $this->db->execInsert($query);
      if($item_id > 0){
      if(isset($batcharray)){
      $len = sizeof($batcharray);
      for ($i = 0; $i<$len; $i++){
      $batchcodearr = explode("::",$batcharray[$i]);
      $stockid = $batchcodearr[0];
      $batchcode =  $batchcodearr[1];
      $batchqty =  $batchcodearr[3];
      if($len == 1){
      $batchqty =  $qty;
      }
      $stockqry = "update it_stockcurr set qty = qty - $batchqty where batchcode = $batchcode and id = $stockid";
      //echo $stockqry;
      $updated_id = $this->db->execUpdate($stockqry);

      }
      }
      }
      $this->db->closeConnection();
      return $item_id;
      } */

    /* function insertInvoiceItem($userid, $saleid, $prodid, $stockcurrid, $qty, $rate, $cutting_charge, $actual_rate, $batcharray) {
      $this->db = new DBConn();
      $inv_item_table = $this->getSalesItemsTableName($userid);
      $tax_rate = $this->getTaxRate($prodid);
      if (isset($batcharray)) {
      $len = sizeof($batcharray);
      for ($i = 0; $i < $len; $i++) {
      $batchcodearr = explode("::", $batcharray[$i]);
      $stockid = $batchcodearr[0];
      $trate = 1 + ($tax_rate / 100);
      if ($len == 1) {
      $qtys = $qty;
      } else {
      $qtys = $batchcodearr[3];
      }
      $baserate = $actual_rate + $cutting_charge;

      //$mrp = $actual_rate + $cutting_charge;
      //$total = round($qtys * $mrp, 2);
      //$rate = round($mrp / $trate, 2);
      $rate = $baserate;
      //$taxable_amt = round($total / $trate, 2);
      $taxable_amt = round($baserate / $trate, 2);
      //$tax_amt = round($total - $taxable_amt, 2);
      $tax_amt = round($baserate - $taxable_amt, 2);
      $cgst_per = round($tax_rate / 2, 2);
      $sgst_per = round($tax_rate / 2, 2);
      $igst_per = round($tax_rate, 2);
      $cgst_amt = round($tax_amt / 2, 2);
      $sgst_amt = round($tax_amt / 2, 2);
      $igst_amt = round($tax_amt, 2);

      $mrp = round($baserate +  $cgst_amt + $sgst_amt, 2);

      $total = round($qtys * $mrp, 2);

      $batchcode_db = $this->db->safe($batchcodearr[1]);

      $query = "insert into $inv_item_table set invoice_id = $saleid, product_id = $prodid,stockcurrid = $stockid,batchcode = $batchcode_db, qty = $qtys, mrp = $mrp,"
      . " rate = $rate, taxable = $taxable_amt, cgst_percent = $cgst_per, cgst_amt = $cgst_amt, sgst_percent = $sgst_per, sgst_amt = $sgst_amt,"
      . "igst_percent = $igst_per, igst_amt = $igst_amt, total = $total, cuttingcharges = $cutting_charge, actualrate = $actual_rate ";
      //                echo $query;
      //                return;
      $item_id = $this->db->execInsert($query);
      if ($item_id > 0) {
      $stockqry = "update it_stockcurr set qty = qty - $qtys where batchcode = $batchcode_db and id = $stockid";
      //echo $stockqry;
      $updated_id = $this->db->execUpdate($stockqry);
      }
      }
      $this->db->closeConnection();
      return $item_id;
      }
      } */

    function insertInvoiceItem($userid, $saleid, $prodid, $stockcurrid, $qty, $rate, $cutting_charge, $actual_rate, $batcharray) {
        $this->db = new DBConn();
        $inv_item_table = $this->getSalesItemsTableName($userid);
        $tax_rate = $this->getTaxRate($prodid);
        if (isset($batcharray)) {
            $len = sizeof($batcharray);
            for ($i = 0; $i < $len; $i++) {
                $batchcodearr = explode("::", $batcharray[$i]);
                $stockid = $batchcodearr[0];
                $trate = ($tax_rate / 100);
                if ($len == 1) {
                    $qtys = $qty;
                } else {
                    $qtys = $batchcodearr[3];
                }
                $baserate = $actual_rate + $cutting_charge;

                //$mrp = $actual_rate + $cutting_charge;
                //$total = round($qtys * $mrp, 2);
                //$rate = round($mrp / $trate, 2);
                $rate = $baserate;
                //$taxable_amt = round($total / $trate, 2);
                $taxable_amt = round($baserate / $trate, 2);
                //$tax_amt = round($total - $taxable_amt, 2);
                $tax_amt = $baserate * $trate;
                $cgst_per = round($tax_rate / 2, 2);
                $sgst_per = round($tax_rate / 2, 2);
                $igst_per = round($tax_rate, 2);
                $cgst_amt = round($tax_amt / 2, 2);
                $sgst_amt = round($tax_amt / 2, 2);
                $igst_amt = round($tax_amt, 2);

                $mrp = round($baserate + $cgst_amt + $sgst_amt, 2);

                $total = round($qtys * $mrp, 2);

                $batchcode_db = $this->db->safe($batchcodearr[1]);

                $query = "insert into $inv_item_table set invoice_id = $saleid, product_id = $prodid,stockcurrid = $stockid,batchcode = $batchcode_db, qty = $qtys, mrp = $mrp,"
                        . " rate = $rate, taxable = $taxable_amt, cgst_percent = $cgst_per, cgst_amt = $cgst_amt, sgst_percent = $sgst_per, sgst_amt = $sgst_amt,"
                        . "igst_percent = $igst_per, igst_amt = $igst_amt, total = $total, cuttingcharges = $cutting_charge, actualrate = $actual_rate ";
//                echo $query;
//                return;
                $item_id = $this->db->execInsert($query);
                if ($item_id > 0) {
                    $stockqry = "update it_stockcurr set qty = qty - $qtys where batchcode = $batchcode_db and id = $stockid";
                    //echo $stockqry;
                    $updated_id = $this->db->execUpdate($stockqry);
                }
            }
            $this->db->closeConnection();
            return $item_id;
        }
    }

    function updatetInvoiceItem($userid, $prodid, $qty, $mrp, $paymentchargepct, $itemid) {
        $this->db = new DBConn();
        $inv_item_table = $this->getSalesItemsTableName($userid);
        $tax_rate = $this->getTaxRate($prodid);
        $trate = ($tax_rate / 100);
        //$total = round($qty * $mrp, 2);
        $baserate = $mrp;
        //$rate = round($mrp / $trate, 2);
        $rate = $baserate;
        //$taxable_amt = round($total / $trate, 2);
        $taxable_amt = round($baserate / $trate, 2);

        //$tax_amt = round($total - $taxable_amt, 2);
        $tax_amt = $baserate * $trate;
        $cgst_per = round($tax_rate / 2, 2);
        $sgst_per = round($tax_rate / 2, 2);
        $igst_per = round($tax_rate, 2);
        $cgst_amt = round($tax_amt / 2, 2);
        $sgst_amt = round($tax_amt / 2, 2);
        $igst_amt = round($tax_amt, 2);

        $mrp = round($baserate + $cgst_amt + $sgst_amt, 2);
        $total = round($qty * $mrp, 2);

        $query = "update $inv_item_table set mrp = $mrp,"
                . " rate = $rate, taxable = $taxable_amt, cgst_percent = $cgst_per, cgst_amt = $cgst_amt, sgst_percent = $sgst_per, sgst_amt = $sgst_amt,"
                . "igst_percent = $igst_per, igst_amt = $igst_amt, total = $total, paymentcharges = $paymentchargepct, updatetime = now() where id = $itemid";
        //echo $query."<br>";
        $item_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        return $item_id;
    }

    function getInvoiceItems($saleid, $userid) {
        $this->db = new DBConn();
        $inv_item_table = $this->getSalesItemsTableName($userid);
        $query = "select p.hsncode, p.name as product,sp.name as spec,p.desc1 as desc_1,p.desc2 as desc_2,p.thickness as thickness,p.hsncode as hsncode, it.* from $inv_item_table as it, it_products p,it_specifications sp where it.product_id = p.id and p.spec_id = sp.id and it.invoice_id = $saleid";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function completeSales($saleid, $userid, $status, $stockdiarystatus,$collecRegId, $paymodeId) {
        $this->db = new DBConn();
        $inv_item_table = $this->getSalesItemsTableName($userid);
        $inv_table = $this->getSalesTableName($userid);
        $inv_item_details = $this->getInvoiceItems($saleid, $userid);
        $crdetails = $this->getCRDetailsByUserId($userid);
        $query = "select sum(qty) as qty, sum(total) as total from $inv_item_table where invoice_id= $saleid";
        $obj_sum = $this->db->fetchObject($query);
        $tot_qty = $obj_sum->qty;
        $tot_amt = $obj_sum->total;
        $invoice_num = $this->getInvoiceNum($userid);
        $stateobj = $this->getStateInfo($crdetails->state);
        $stateTin = $stateobj->TIN;
        //error_log("\n Invoice no: ".$stateTin."\n",3,"../ajax/tmp.txt");
        $checkqry = "select * from $inv_table where id = $saleid";
        $checkifexist = $this->db->fetchObject($checkqry);
        if (isset($checkifexist->invoice_no) && trim($checkifexist->invoice_no) != NULL) {
            $invoice_no_db = $this->db->safe($checkifexist->invoice_no);
        } else {
            $invoice_no = strtoupper($this->getCRCode($userid) . "/" . $this->getActiveFinancialYear() . "-" . $stateTin . "/" . $invoice_num);
            $invoice_no_db = $this->db->safe($invoice_no);
        }
        $query = "update $inv_table set invoice_no = $invoice_no_db, total_qty = $tot_qty , total_amount = $tot_amt, status = $status,"
                . " updatetime = now() where id = $saleid";
        $rows_affected = $this->db->execUpdate($query);
        //echo $query;
        
        $this->insert_into_payments_diary($saleid,$userid, $collecRegId, $paymodeId, 'sale', $tot_amt);
        if ($rows_affected > 0) {
            if (!isset($checkifexist->invoice_no)) {
                $this->updateInvoiceNum($userid);
                if (isset($inv_item_details)) {
                    foreach ($inv_item_details as $items) {
                        $stockdiaryquery = "insert into it_stockdiary set prodid = $items->product_id,"
                                . " batchcode = $items->batchcode, reason = $stockdiarystatus, qty = $items->qty ,crid = $crdetails->id";
                        //echo $stockdiaryquery;
                        $this->db->execInsert($stockdiaryquery);
                    }
                    //$this->insert_into_it_cr_salesreport($saleid,$userid);
                }
            }
        }
        $this->db->closeConnection();
        return $rows_affected;
    }

    function getInvoiceNum($userid) {
        $this->db = new DBConn();
        $crcode = $this->getCRCode($userid);
        $query = "select num from " . $crcode . "_invoice_num";
        //error_log("\n Invoice no query: ".$query."\n",3,"../ajax/tmp.txt");
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj->num;
    }

    function updateInvoiceNum($userid) {
        $this->db = new DBConn();
        $crcode = $this->getCRCode($userid);
        $query = "update " . $crcode . "_invoice_num set num = num + 1";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getCRCode($userid) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        //$query = "select crcode from it_rfc_master where userid = $userid";
        $query = "select crcode from it_rfc_master where id = $crid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj->crcode;
    }

    function getActiveFinancialYear() {
        $this->db = new DBConn();
        $query = "select financial_year from financial_year where is_active = 1";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj->financial_year;
    }

    function getINVHeaderPDetails($salesid, $userid) {
        $this->db = new DBConn();
        $inv_item_table = $this->getSalesItemsTableName($userid);
        $inv_table = $this->getSalesTableName($userid);
        $query = "select * from " . $inv_table . " where id = $salesid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getCRDetailsByUserId($userid) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        //$query = "select s.state as dealerstate, rf.* from it_rfc_master rf,states s where rf.state = s.id and  rf.userid = $userid";
        $query = "select s.state as dealerstate, rf.* from it_rfc_master rf,states s where rf.state = s.id and  rf.id = $crid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getCurrentProductPriceList() {
        $this->db = new DBConn();
        $query = "select p.id , c.name as ctg, p.name as itemname,s.name as spec, pr.price, pr.applicable_date, p.active, pr.is_approved   
	from it_products p , it_categories c , it_specifications s, it_product_price pr where p.ctg_id = c.id and p.spec_id = s.id 
        and pr.product_id = p.id and pr.applicable_date = date(now()) and p.active = 1";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getCRList() {
        $this->db = new DBConn();
        $query = "select * from it_rfc_master where inactive = 0";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function approveAllProductPrice($status, $date, $crid, $userid) {
        $this->db = new DBConn();
        $date = $this->db->safe($date);
        $pendingstatus = ProductPriceStatus::Pending;
        //$pendingstatus = ProductPriceStatus::AwaingForApproval;
        $query = "update it_product_price set is_approved = 1, approvedby = $userid, status = $status,"
                . " approveddate = now() where applicable_date = $date and crid = $crid and status = $pendingstatus";
        //error_log("\napprove query: ".$query."\n",3,"../ajax/tmp.txt");
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function disapproveAllProductPrice($status, $date, $crid, $userid) {
        $this->db = new DBConn();
        $date = $this->db->safe($date);
        $pendingstatus = ProductPriceStatus::Pending;
        $query = "update it_product_price set is_approved = 0, disapprovedby = $userid, status = $status,"
                . " disapproveddate = now() where applicable_date = $date and crid = $crid and status = $pendingstatus";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function approveProductPrice($status, $date, $prodid, $crid, $userid) {
        $this->db = new DBConn();
        $date = $this->db->safe($date);
        $pendingstatus = ProductPriceStatus::Pending;
        $query = "update it_product_price set is_approved = 1, approvedby = $userid, status = $status,"
                . " approveddate = now() where applicable_date = $date and product_id = $prodid and crid = $crid and status = $pendingstatus";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function disapproveProductPrice($status, $date, $prodid, $crid, $userid) {
        $this->db = new DBConn();
        $date = $this->db->safe($date);
        $pendingstatus = ProductPriceStatus::Pending;
        $query = "update it_product_price set is_approved = 0, approvedby = $userid, status = $status,"
                . " approveddate = now() where applicable_date = $date and product_id = $prodid and crid = $crid and status = $pendingstatus";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getCRInfoById($crid) {
        $this->db = new DBConn();
        $query = "select * from it_rfc_master where id = $crid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getCuttingCharges() {
        $this->db = new DBConn();
        $query = "select * from it_cutting_charges where isactive = 1";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getChargeTypeInfo($chargetype) {
        $this->db = new DBConn();
        $query = "select * from it_extra_charges where chargetype = $chargetype and isactive = 1";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getAllColors() {
        $this->db = new DBConn();
        $query = "select * from it_colors";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllBrands() {
        $this->db = new DBConn();
        $query = "select * from it_brands";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllManufacturers() {
        $this->db = new DBConn();
        $query = "select * from it_manufacturer";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllDCMasters() {
        $this->db = new DBConn();
        $query = "select * from it_dc_master";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllGSTPer() {
        $this->db = new DBConn();
        $query = "select * from it_gst_percentage";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getAllTransports() {
        $this->db = new DBConn();
        $query = "select * from it_transportation";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    //getFreightdetails
    //getGSTbyid

    function getGSTbyid($gstperid) {
        $this->db = new DBConn();
        $query = "select * from it_gst_percentage where id = $gstperid";
        //echo $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function insertPOFright($poid, $freightamt, $transportsel, $gstsel, $fright_taxableAmt, $fright_GST) {
        $this->db = new DBConn();
        $query = "update it_purchaseorder set freightamt = $freightamt, freight_rate = $fright_taxableAmt, transport_id = $transportsel,"
                . " freight_gst = $fright_GST,is_freight_applicable = 1 where id = $poid";
        //echo $query;
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

//getFreightdetails

    function updateEmailStatus($poid) {
        $this->db = new DBConn();
        $query = "update it_purchaseorder set is_mailsent = 1 where id = $poid";
        //echo $query;
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getFreightdetails($poid) {
        $this->db = new DBConn();
        $query = "select freightamt,freight_rate,transport_id,freight_gst,is_freight_applicable from it_purchaseorder where id = $poid and is_freight_applicable = 1";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function insertTransportation($name, $email, $phoneno, $userid) {
        $this->db = new DBConn();
        $addquery = "";



        if ($name) {
            $name_db = $this->db->safe($name);
            $addquery .= " ,name = " . $name_db;
        }

        if ($email) {
            $email_db = $this->db->safe($email);
            $addquery .= " ,email = " . $email_db;
        }

        if ($phoneno) {
            $phoneno_db = $this->db->safe($phoneno);
            $addquery .= " ,phoneno = " . $phoneno_db;
        }

        $query = "insert into it_transportation set created_by = $userid $addquery";
        //echo $query;
        $trans_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($trans_id) && $trans_id > 0) {
            return $trans_id;
        } else {
            return NULL;
        }
    }

    function cancelAwaitingPO($poid, $postatus, $userid, $remarks) {
        $this->db = new DBConn();
        $remarks_db = "";
        if (isset($remarks)) {
            $remarks = $this->db->safe($remarks);
            $remarks_db = " ,cancelreason = $remarks";
        }
        $query = "update it_purchaseorder set po_status = $postatus, approvedtime = now(), approvedby = $userid $remarks_db where id = $poid";
        //echo $query."<br>";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    //approvecancelPO
    function approvecancelPO($poid, $postatus, $userid, $remarks) {
        $this->db = new DBConn();
        $remarks_db = "";
        if (isset($remarks)) {
            $remarks = $this->db->safe($remarks);
            $remarks_db = " ,remarks = $remarks";
        }
        $query = "update it_purchaseorder set po_status = $postatus, approvedtime = now(), approvedby = $userid $remarks_db where id = $poid";
        //echo $query."<br>";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

//getSuppliersOffers

    function getSuppliersOffers() {
        $this->db = new DBConn();
        $query = "select * from it_suppliers_offers where inactive=false";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function insertTransporter($dateofentry, $kycnumber, $companyname, $suppliercode, $bankname, $bankaccno, $bankbranchname, $firmtype, $currency, $state, $country, $district, $address, $graddress, $pincode, $panno, $cinno, $gstapp, $gstno, $contactperson1, $contactperson2, $contactperson3, $contactperson4, $phone1, $phone2, $phone3, $phone4, $email1, $email2, $email3, $email4, $msmedno, $userid) {
        $this->db = new DBConn();
        $addquery = "";

        if ($dateofentry) {
            $dateofentry_db = $this->db->safe($dateofentry);
            $addquery .= " ,date_of_entry = " . $dateofentry_db;
        }

        if ($kycnumber) {
            $kycnumber_db = $this->db->safe($kycnumber);
            $addquery .= " ,kyc_number = " . $kycnumber_db;
        }

        if ($companyname) {
            $companyname_db = $this->db->safe($companyname);
            $addquery .= " ,company_name = " . $companyname_db;

            $firstCharacter = substr($companyname, 0, 1);
            $suppliercode_db = isset($suppliercode) && $suppliercode != "" ? $this->db->safe("T" . $suppliercode) : false;
            if (isset($suppliercode_db)) {
                $addquery .= " ,supplier_code = " . $suppliercode_db;
            }
        }

        if ($bankname) {
            $bankname_db = $this->db->safe($bankname);
            $addquery .= " ,bank_name = " . $bankname_db;
        }

        if ($bankaccno) {
            $bankaccno_db = $this->db->safe($bankaccno);
            $addquery .= " ,bank_ac_no = " . $bankaccno_db;
        }

        if ($bankbranchname) {
            $bankbranchname_db = $this->db->safe($bankbranchname);
            $addquery .= " ,bank_branch = " . $bankbranchname_db;
        }

        if ($firmtype) {
            $firmtype_db = $this->db->safe($firmtype);
            $addquery .= " ,firm_type = " . $firmtype_db;
        }

        if ($currency) {
            $currency_db = $this->db->safe($currency);
            $addquery .= " ,currency = " . $currency_db;
        }

        if ($state) {
            $state = $state;
            $addquery .= " ,state = " . $state;
        }

        if ($country) {
            $country_db = $this->db->safe($country);
            $addquery .= " ,country = " . $country_db;
        }

        if ($district) {
            $district_db = $this->db->safe($district);
            $addquery .= " ,district = " . $district_db;
        }

        if ($address) {
            $address_db = $this->db->safe($address);
            $addquery .= " ,address = " . $address_db;
        }

        if ($graddress) {
            $graddress_db = $this->db->safe($graddress);
            $addquery .= " ,graddress = " . $graddress_db;
        }

        if ($pincode) {
            $pincode_db = $this->db->safe($pincode);
            $addquery .= " ,pincode = " . $pincode_db;
        }

        if ($panno) {
            $panno_db = $this->db->safe($panno);
            $addquery .= " ,pan_no = " . $panno_db;
        }

        if ($cinno) {
            $cinno_db = $this->db->safe($cinno);
            $addquery .= " ,cin_no = " . $cinno_db;
        }

        if ($gstapp) {
            $gstapp_db = intval($gstapp);
            $addquery .= " ,is_gst_applicable = " . $gstapp_db;
        }

        if ($gstno) {
            $gstno_db = $this->db->safe($gstno);
            $addquery .= " ,gst_no = " . $gstno_db;
        }

        if ($contactperson1) {
            $contactperson1_db = $this->db->safe($contactperson1);
            $addquery .= " ,contact_person1 = " . $contactperson1_db;
        }

        if ($contactperson2) {
            $contactperson2_db = $this->db->safe($contactperson2);
            $addquery .= " ,contact_person2 = " . $contactperson2_db;
        }

        if ($contactperson3) {
            $contactperson3_db = $this->db->safe($contactperson3);
            $addquery .= " ,contact_person3 = " . $contactperson3_db;
        }

        if ($contactperson4) {
            $contactperson4_db = $this->db->safe($contactperson4);
            $addquery .= " ,contact_person4 = " . $contactperson4_db;
        }

        if ($phone1) {
            $phone1_db = $this->db->safe($phone1);
            $addquery .= " ,phone1 = " . $phone1_db;
        }

        if ($phone2) {
            $phone2_db = $this->db->safe($phone2);
            $addquery .= " ,phone2 = " . $phone2_db;
        }

        if ($phone3) {
            $phone3_db = $this->db->safe($phone3);
            $addquery .= " ,phone3 = " . $phone3_db;
        }

        if ($phone4) {
            $phone4_db = $this->db->safe($phone4);
            $addquery .= " ,phone4 = " . $phone4_db;
        }

        if ($email1) {
            $email1_db = $this->db->safe($email1);
            $addquery .= " ,email1 = " . $email1_db;
        }

        if ($email2) {
            $email2_db = $this->db->safe($email2);
            $addquery .= " ,email2 = " . $email2_db;
        }

        if ($email3) {
            $email3_db = $this->db->safe($email3);
            $addquery .= " ,email3 = " . $email3_db;
        }

        if ($email4) {
            $email4_db = $this->db->safe($email4);
            $addquery .= " ,email4 = " . $email4_db;
        }

        if ($msmedno) {
            $msmedno_db = $this->db->safe($msmedno);
            $addquery .= " ,msmed_reg_no = " . $msmedno_db;
        }

        $query = "insert into it_transporters set created_by = $userid $addquery";
        //echo $query;
        $supp_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        if (isset($supp_id) && $supp_id > 0) {
            return $supp_id;
        } else {
            return NULL;
        }
    }

    function updateTransportersCode($prefix, $code) {
        $this->db = new DBConn();
        $prefix_db = $this->db->safe($prefix);
        $code_db = $this->db->safe($code);
        $query = "update it_transporter_codes set snumber = $code_db, updatetime=now()";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

//added by yuvaraj    
    function deletePOItem($itemid) {
        $this->db = new DBConn();
        $query = "delete from it_polines where id = $itemid";
        $this->db->execQuery($query);
        $this->db->closeConnection();
    }

    function getDCInfo($id) {
        $this->db = new DBConn();
        $query = "select * from it_dc_master where id = $id";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getStateInfo($id) {
        $this->db = new DBConn();
        $query = "select * from states where id = $id";
        //error_log("\n State QUery: ".$query."\n",3,"../ajax/tmp.txt");
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function AddSku($poitem_id, $barcode) {
        $this->db = new DBConn();
        $prefix_db = $this->db->safe($prefix);
        $code_db = $this->db->safe($code);
        $query = "update it_polines set sku = $barcode, updatetime=now() where id = $poitem_id";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function resetPassword($username, $pwd1) {
        $this->db = new DBConn();
        $username_db = $this->db->safe($username);
        $pwd1_db = $this->db->safe($pwd1);
        $query = "update it_users set password = md5($pwd1_db) , updatetime = now() where username = $username_db";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    /* by pratiraj */

    function rejectcancelledPO($poid, $postatus, $userid, $remarks) {
        $this->db = new DBConn();
        $remarks_db = "";
        if (isset($remarks)) {
            $remarks = $this->db->safe($remarks);
            $remarks_db = " ,remarks = $remarks";
        }
        $query = "update it_purchaseorder set po_status = $postatus, approvedtime = now(), approvedby = $userid $remarks_db where id = $poid";
        //echo $query."<br>";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getTransporterCode() {
        $this->db = new DBConn();
        $query = "select snumber from it_transporter_codes";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    /* end */

    function fetchNextGRNNumber($stateid) {
        $this->db = new DBConn();
        $query = "select * from it_grnnum where stateid = $stateid";
        //return $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj->num + 1;
        } else {
            return NULL;
        }
    }

    function insertGRN($dccode, $poid, $suppid, $sinvno, $sinvdate, $grnnum, $status, $userid, $grndate) {
        $this->db = new DBConn();
        $sinvno_db = $this->db->safe($sinvno);
        $sinvdate_db = $this->db->safe($sinvdate);
        $grnnum_db = $this->db->safe($grnnum);
        $grndate = $this->db->safe($grndate);
        $query = "insert into it_grn set grnno = $grnnum_db, dcid = $dccode, poid = $poid, suppid = $suppid, invoice_no = $sinvno_db,"
                . " invoice_date = $sinvdate_db, status = $status, createdby = $userid,grndate = $grndate";
        $id = $this->db->execInsert($query);
        if ($id > 0) {
            $query = "update it_grnnum set num = num + 1";
            $this->db->execUpdate($query);
        }
        $this->db->closeConnection();
        return $id;
    }

    function getGRNDetails($grnid) {
        $this->db = new DBConn();
        $query = "select g.*,s.company_name as supplier, p.pono as pono, d.dc_name from it_grn g, it_suppliers s, it_purchaseorder p,"
                . " it_dc_master d where p.id = g.poid and s.id = g.suppid and d.id = g.dcid and g.id = $grnid";
        //return $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getGRNItems($grnid) {
        $this->db = new DBConn();
        //$query = "select * from it_grnitems where grnid = $grnid";
        $query = "select p.id as prodid,p.name as prod,p.desc1 as desc_1,p.desc2 as desc_2,p.thickness as thickness,p.hsncode as hsncode,cls.color as color,"
                . "mf.manufacturer as manufacturer, b.brand as brand,gl.* from it_grnitems gl,it_products p,it_colors cls,"
                . "it_manufacturer mf,it_brands b where p.id = gl.product_id  and cls.id = gl.color_id and mf.id = gl.manufacturer_id and b.id = brand_id "
                . "and gl.grnid = $grnid order by gl.id";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getPOItemInfo($poitemid) {
        $this->db = new DBConn();
        $query = "select p.name as prod,p.desc1 as desc_1,p.desc2 as desc_2,p.thickness as thickness,p.hsncode as hsncode,"
                . "spec.name as speci,ctg.name as category,ctg.id as ctg_id,cls.color as color, mf.manufacturer as manufacturer, b.brand as brand,"
                . "pl.* from it_polines pl, it_products p,it_specifications spec,it_categories ctg,it_colors cls,it_manufacturer mf,"
                . "it_brands b where p.id = pl.product_id and p.spec_id = spec.id and pl.ctg_id=ctg.id and cls.id = pl.color_id and "
                . "mf.id = pl.manufacturer_id and b.id = brand_id and pl.id = $poitemid";
        $obj_poline = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_poline) && $obj_poline != NULL) {
            return $obj_poline;
        } else {
            return NULL;
        }
    }

    function insertGRNItem($poid, $prodid, $grnid, $polineid, $qty, $rate, $length, $colorsel, $brandsel, $manfsel, $pieces, $lcrate, $cgstpct, $cgstval, $sgstpct, $sgstval, $totalrate, $totalvalue, $status, $batchcode, $alias, $receiveIn) {
        $objpoline = $this->getPOItemInfo($polineid);
        $polength = 0;
        $poqty = 0;
        $po_no_of_pieces = 0;
        $porate = 0;
        $category_id = 0;
        $polcrate = 0;
        $pocgstpct = 0;
        $posgstpct = 0;
        $pocgstval = 0;
        $posgstval = 0;
        $pototalrate = 0;
        $pototalvalue = 0;
        if ($objpoline != NULL) {
            $polength = $objpoline->length;
            $poqty = $objpoline->qty;
            $po_no_of_pieces = $objpoline->no_of_pieces;
            $porate = $objpoline->rate;
            $category_id = $objpoline->ctg_id;
            $polcrate = $objpoline->lcrate;
            $pocgstpct = $objpoline->cgstpct;
            $posgstpct = $objpoline->sgstpct;
            $pocgstval = $objpoline->cgstval;
            $posgstval = $objpoline->sgstval;
            $pototalrate = $objpoline->totalrate;
            $pototalvalue = $objpoline->totalvalue;
        }
        $addquery = "";
        if ($grnid) {
            $grnid_db = $grnid;
            $addquery .= " ,grnid = " . $grnid_db;
        }

        if ($prodid) {
            $prodid_db = $prodid;
            $addquery .= " ,product_id = " . $prodid_db;
        }

        if ($polineid) {
            $polineid_db = $polineid;
            $addquery .= " ,polineid = " . $polineid_db;
        }

        if ($qty) {
            $qty_db = $qty;
            $addquery .= " ,qty = " . $qty_db;
        }

        if ($rate) {
            $rate_db = $rate;
            $addquery .= " ,rate = " . $rate_db;
        }

        if ($length) {
            $length_db = $length;
            $addquery .= " ,length = " . $length_db;
        }

        if ($colorsel) {
            $colorsel_db = $colorsel;
            $addquery .= " ,color_id = " . $colorsel_db;
        }

        if ($brandsel) {
            $brandsel_db = $brandsel;
            $addquery .= " ,brand_id = " . $brandsel_db;
        }

        if ($manfsel) {
            $manfsel_db = $manfsel;
            $addquery .= " ,manufacturer_id = " . $manfsel_db;
        }

        if ($pieces) {
            $pieces_db = $pieces;
            $addquery .= " ,no_of_pieces = " . $pieces_db;
        }

        if ($lcrate) {
            $lcrate_db = $lcrate;
            $addquery .= " ,lcrate = " . $lcrate_db;
        }

        if ($cgstpct) {
            $cgstpct_db = $cgstpct;
            $addquery .= " ,cgstpct = " . $cgstpct_db;
        }

        if ($cgstval) {
            $cgstval_db = $cgstval;
            $addquery .= " ,cgstval = " . $cgstval_db;
        }

        if ($sgstpct) {
            $sgstpct_db = $sgstpct;
            $addquery .= " ,sgstpct = " . $sgstpct_db;
        }

        if ($sgstval) {
            $sgstval_db = $sgstval;
            $addquery .= " ,sgstval = " . $sgstval_db;
        }

        if ($totalrate) {
            $totalrate_db = $totalrate;
            $addquery .= " ,totalrate = " . $totalrate_db;
        }

        if ($totalvalue) {
            $totalvalue_db = $totalvalue;
            $addquery .= " ,totalvalue = " . $totalvalue_db;
        }

        if (isset($status)) {
            $status_db = $status;
            $addquery .= " ,status = " . $status_db;
        }

        if ($batchcode) {
            $batchcode_db = $batchcode;
            $addquery .= " ,batchcode = " . $batchcode_db;
        }

        if ($alias) {
            $alias_db = $this->db->safe($alias);
            $addquery .= " ,alias = " . $alias_db;
        }

        if ($receiveIn) {
            $receiveIn_db = $this->db->safe($receiveIn);
            $addquery .= " ,receivedin = " . $receiveIn_db;
        }

        $query = "insert into it_grnitems set porate = $porate, polength = $polength, poqty = $poqty, ctg_id = $category_id, po_no_of_pieces  = $po_no_of_pieces, polcrate = $polcrate, pocgstpct = $pocgstpct,"
                . "pocgstval = $pocgstval, posgstpct =  $posgstpct, posgstval = $posgstval, pototalrate = $pototalrate, pototalvalue = $pototalvalue $addquery";
        //echo  $query;      
        $this->db = new DBConn();
        $grnItem_id = $this->db->execInsert($query);
        if ($grnItem_id > 0) {
            $query = "update it_batchnum set num = num + 1";
            $this->db->execUpdate($query);
        }
        $this->db->closeConnection();
    }

    function saveGRN($grnid, $grnStatus, $totalQty, $totalValue) {
        $this->db = new DBConn();
        $query = "update it_grn set status = $grnStatus, tot_qty = $totalQty, tot_value = $totalValue  where id = $grnid";
        //echo $query."<br>";
        $id = $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function updateGRNItemStatus($grnid, $grnitemstatus, $reason, $dcid) {
        $this->db = new DBConn();
        $query = "update it_grnitems set status = $grnitemstatus where grnid = $grnid";
        //echo $query."<br>";
        $id = $this->db->execUpdate($query);
        if ($id > 0) {
            $obj_grnitems = $this->getGRNItems($grnid);
            if ($obj_grnitems != NULL) {
                foreach ($obj_grnitems as $grnitems) {
                    $stockdiaryquery = "insert into it_stockdiary set dcid = $dcid, prodid = $grnitems->prodid, batchcode = $grnitems->batchcode, reason = $reason, qty = $grnitems->qty";
                    $this->db->execInsert($stockdiaryquery);
                    $stockcurrentquery = "insert into it_stockcurr set dcid = $dcid, prodid = $grnitems->prodid, batchcode = $grnitems->batchcode, qty = $grnitems->qty";
                    $this->db->execInsert($stockcurrentquery);
                }
            }
        }
        $this->db->closeConnection();
    }

    function fetchNextBatchNumber($stateid) {
        $this->db = new DBConn();
        $query = "select * from it_batchnum where stateid = $stateid";
        //return $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj->num + 1;
        } else {
            return NULL;
        }
    }

    function fetchNextstockTransferNumber($stateid) {
        $this->db = new DBConn();
        $query = "select * from it_stock_transfer_num where stateid = $stateid";
        //return $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj->num + 1;
        } else {
            return NULL;
        }
    }

    //insertStockTransfer

    function insertStockTransfer($fromLoctype, $toLoctype, $dccode, $crcode, $stocknum, $stocktransferStatus, $userid, $transferdate) {
        $this->db = new DBConn();
        $stocknum_db = $this->db->safe($stocknum);
        $transferdate_db = $this->db->safe($transferdate);
        $query = "insert into it_stock_transfer set transferno = $stocknum_db, from_location_type = $fromLoctype, from_location_id = $dccode, to_location_type = $toLoctype,"
                . " to_location_id = $crcode, status = $stocktransferStatus, createdby = $userid, transferdate = $transferdate_db";
        //echo $query;
        $id = $this->db->execInsert($query);
        if ($id > 0) {
            $query = "update it_stock_transfer_num set num = num + 1";
            $this->db->execUpdate($query);
        }
        $this->db->closeConnection();
        return $id;
    }

    //getStockTransferDetails
    function getStockTransferDetails($transferid) {
        $this->db = new DBConn();
        $checkqry = "select * from it_stock_transfer where id = $transferid";
        $objchk = $this->db->fetchObject($checkqry);
        if ($objchk->from_location_type == LocationType::DC && $objchk->to_location_type == LocationType::CR) {
            $query = "select s.*,d.dc_name as fromloc,d.address as address,upper(c.crcode) as toloc,c.rfc_name,c.address as rfcaddr,c.gstno,c.panno,c.phoneno from it_stock_transfer s,it_dc_master d,it_rfc_master c"
                    . " where s.from_location_id = d.id and s.to_location_id = c.id and s.id = $transferid";
        } else if ($objchk->from_location_type == LocationType::CR && $objchk->to_location_type == LocationType::DC) {
            $query = "select s.*,d.dc_name as toloc,upper(c.crcode) as fromloc from it_stock_transfer s,it_dc_master d,it_rfc_master c"
                    . " where s.from_location_id = c.id and s.to_location_id = d.id and s.id = $transferid";
        } else if ($objchk->from_location_type == LocationType::DC && $objchk->to_location_type == LocationType::DC) {
            $query = "select s.*,d1.dc_name as fromloc,d2.dc_name  as toloc from it_stock_transfer s,it_dc_master d1,it_dc_master d2 where "
                    . "s.from_location_id = d1.id and s.to_location_id = d2.id and s.id = $transferid";
        } else if ($objchk->from_location_type == LocationType::CR && $objchk->to_location_type == LocationType::CR) {
            $query = "select s.*,upper(c1.crcode) as fromloc,upper(c2.crcode) as toloc from it_stock_transfer s,it_rfc_master c1,it_rfc_master c2 "
                    . "where s.from_location_id = c1.id and s.to_location_id = c2.id and s.id = $transferid";
        }
        //echo $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    //getStockTransferItems
    function getStockTransferItems($transferid) {
        $this->db = new DBConn();
        //$query = "select * from it_stock_transfer_items where transferid = $transferid";
        $query = "select p.id as prodid,p.name as prod,sp.name as spec,p.desc1 as desc_1,p.desc2 as desc_2,p.thickness as thickness,p.hsncode as hsncode,gl.totalrate,gl.length,gl.rate,gl.cgstval,gl.sgstval,gl.lcrate,gl.totalvalue as gltotval,"
                . "st.* from it_stock_transfer_items st,it_products p,it_specifications sp,it_grnitems gl where p.id = st.prodid and p.spec_id = sp.id and st.batchcode = gl.batchcode and st.transferid = $transferid";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getStockcurrentDetails($fromlocid, $fromloctype) {
        $this->db = new DBConn();
        if ($fromloctype == LocationType::CR) {
            $addqry = " and sc.crid = $fromlocid";
        } else {
            $addqry = " and sc.dcid = $fromlocid";
        }
        $query = "select sc.id,sc.dcid,sc.crid,sc.prodid,sc.batchcode,sum(sc.qty),sc.createtime,p.name as prod,p.desc1 as desc_1,p.desc2 as desc_2,"
                . "p.thickness as thickness,p.hsncode as hsncode,p.kg_per_pc  from it_stockcurr sc, it_products p where p.id = sc.prodid and sc.qty > 0 $addqry "
                . "group by prodid order by sc.id";
        //echo $query;
        $obj_stockcurrent = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockcurrent) && $obj_stockcurrent != NULL) {
            return $obj_stockcurrent;
        } else {
            return NULL;
        }
    }

    function getStockItemInfo($stockcurrid) {
        $this->db = new DBConn();
        $query = "select * from it_stockcurr where id = $stockcurrid";
        $obj_stockcurr = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_stockcurr) && $obj_stockcurr != NULL) {
            return $obj_stockcurr;
        } else {
            return NULL;
        }
    }

    function getStockItemInfoByBatchcode($id, $fromlocid, $fromloctype) {
        $this->db = new DBConn();
        $addqry = "";
        if ($fromloctype == LocationType::DC) {
            $addqry = "and dcid = $fromlocid";
        } else {
            $addqry = "and crid = $fromlocid";
        }
        $query = "select * from it_stockcurr where id = $id and qty > 0 $addqry";
        $obj_stockcurr = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_stockcurr) && $obj_stockcurr != NULL) {
            return $obj_stockcurr;
        } else {
            return NULL;
        }
    }

    function getGRNItemInfobyGRNid($prodid, $grnid) {
        $this->db = new DBConn();
        $query = "select sum(qty) as qty from it_grnitems where grnid=$grnid and product_id=$prodid";
        $obj_grnqtysum = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_grnqtysum) && $obj_grnqtysum != NULL) {
            return $obj_grnqtysum;
        } else {
            return NULL;
        }
    }

    function getBatchCodeByProductid($productid, $fromlocid, $fromloctype) {
        $this->db = new DBConn();
        $addqry = "";
        if ($fromloctype == LocationType::DC) {
            $addqry = "and sc.dcid = $fromlocid";
        } else {
            $addqry = "and sc.crid = $fromlocid";
        }
        //$query = "select id,batchcode,qty from it_stockcurr where prodid = $productid and qty > 0 $addqry";
        $query = "select sc.id,sc.batchcode,sc.qty,gl.length,gl.no_of_pieces from it_stockcurr sc,it_grnitems gl where sc.batchcode = gl.batchcode and sc.prodid =$productid and sc.qty > 0 $addqry order by sc.batchcode";
        //error_log("\nMSLmainnn query: ".$query."\n",3,"../ajax/tmp.txt");
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getStockItemInfoById($id, $usertype, $userid) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        $addqry = "";
        if ($usertype == LocationType::DC) {
            $addqry = "and dcid = $userid";
        } else {
            //$addqry = "and crid in (select id from it_rfc_master where userid = $userid )";
            $addqry = "and crid in ($crid)";
        }
        $query = "select * from it_stockcurr where id = $id and qty > 0 $addqry";
        $obj_stockcurr = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_stockcurr) && $obj_stockcurr != NULL) {
            return $obj_stockcurr;
        } else {
            return NULL;
        }
    }

    function getGRNInfoByBatchcode($batchcode) {
        $this->db = new DBConn();
        $query = "select * from it_grnitems where batchcode = $batchcode";
        $obj_stockcurr = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_stockcurr) && $obj_stockcurr != NULL) {
            return $obj_stockcurr;
        } else {
            return NULL;
        }
    }

    /* function insertStockTransferItem($transferid,$stocklineid,$prodid,$batchcode,$availableqty,$qty,$pieces,$transferIn) {

      $objgrnline = $this->getGRNInfoByBatchcode($batchcode);
      $addquery = "";

      if ($prodid) {
      $prodid_db = $prodid;
      $addquery .= " ,prodid = " . $prodid_db;
      }

      if ($batchcode) {
      $batchcode_db = $this->db->safe($batchcode);
      $addquery .= " ,batchcode = " . $batchcode_db;
      }

      if ($pieces) {
      $pieces_db = $pieces;
      $addquery .= " ,no_of_pieces = " . $pieces_db;
      }

      if ($qty) {
      $qty_db = $qty;
      $addquery .= " ,qty = " . $qty_db;
      }

      if ($objgrnline != NULL) {
      $value = $objgrnline->totalrate;
      $addquery .= " ,value = round(" . $value.",3)";
      }

      if($transferIn == 1){
      $addquery .= " ,transferinpcs = true";
      }

      $query = "insert into it_stock_transfer_items set transferid = $transferid $addquery";
      //echo  $query;
      $this->db = new DBConn();
      $grnItem_id = $this->db->execInsert($query);
      if ($grnItem_id > 0) {
      $updatedQty = $availableqty - $qty;
      $updateqry = "update it_stockcurr set qty = $updatedQty where id = $stocklineid and  batchcode = $batchcode";
      //echo $updateqry;
      $this->db->execUpdate($updateqry);
      }
      $this->db->closeConnection();
      } */

    function insertStockTransferItem($transferid, $prodid, $availableqty, $qty, $pieces, $transferIn, $batchcodearray) {


        if (isset($batchcodearray)) {
            $len = sizeof($batchcodearray);
            for ($i = 0; $i < $len; $i++) {
                $batchcodearr = explode("::", $batchcodearray[$i]);
                $stockid = $batchcodearr[0];
                $batchcode = $batchcodearr[1];
                if ($len == 1) {
                    $qtys = $qty;
                    $noofpcs = $pieces;
                } else {
                    $qtys = $batchcodearr[3];
                    $noofpcs = $batchcodearr[4];
                }

                $objgrnline = $this->getGRNInfoByBatchcode($batchcode);
                $addquery = "";

                if ($prodid) {
                    $prodid_db = $prodid;
                    $addquery .= " ,prodid = " . $prodid_db;
                }

                if ($batchcode) {
                    $batchcode_db = $this->db->safe($batchcode);
                    $addquery .= " ,batchcode = " . $batchcode_db;
                }

                if ($noofpcs) {
                    $pieces_db = $noofpcs;
                    $addquery .= " ,no_of_pieces = " . $pieces_db;
                }

                if ($qtys) {
                    $qty_db = $qtys;
                    $addquery .= " ,qty = " . $qty_db;
                }

                if ($objgrnline != NULL) {
                    $value = $objgrnline->totalrate;
                    $addquery .= " ,value = $value";
                }

                if ($transferIn == 1) {
                    $addquery .= " ,transferinpcs = true";
                }

                if ($stockid) {
                    $addquery .= " ,stockcurrid = " . $stockid;
                }

                $query = "insert into it_stock_transfer_items set transferid = $transferid $addquery";
                //echo  $query;      
                $this->db = new DBConn();
                $grnItem_id = $this->db->execInsert($query);
                if ($grnItem_id > 0) {
                    //$updatedQty = $availableqty - $qty;
                    $updateqry = "update it_stockcurr set qty = qty - $qtys where id = $stockid and  batchcode = $batchcode";
                    //echo $updateqry;
                    $this->db->execUpdate($updateqry);
                }
            }
        }



        $this->db->closeConnection();
    }

    function saveStockTransfer($transferid, $stockTransferStatus, $StockDiaryReason, $totalQty, $totalValue, $userid) {
        $this->db = new DBConn();
        $query = "update it_stock_transfer set status = $stockTransferStatus, tot_qty = $totalQty, tot_value = $totalValue ,updatedby = $userid  where id = $transferid";
        $id = $this->db->execUpdate($query);
        $obj_stocktransfer = $this->getStockTransferDetails($transferid);
        if (isset($obj_stocktransfer)) {
            $addqry = "";
            $tolocationtype = $obj_stocktransfer->to_location_type;
            if ($tolocationtype == LocationType::CR) {
                $addqry = ",crid = $obj_stocktransfer->to_location_id";
            } else {
                $addqry = ",dcid = $obj_stocktransfer->to_location_id";
            }
        }
//        if($id > 0){
//            $obj_stocktransferitems = $this->getStockTransferItems($transferid);
//            foreach ($obj_stocktransferitems as $transferitems) {
//                    $stockdiaryquery = "insert into it_stockdiary set prodid = $transferitems->prodid,"
//                            . " batchcode = $transferitems->batchcode, reason = $StockDiaryReason, qty = $transferitems->qty $addqry";
//                    $this->db->execInsert($stockdiaryquery);
//                    
//                    $stockcurrentquery = "insert into it_stockcurr set prodid = $transferitems->prodid, batchcode = $transferitems->batchcode, "
//                            . "qty = $transferitems->qty $addqry";
//                    $this->db->execInsert($stockcurrentquery);
//                }
//        }
        $this->db->closeConnection();
    }

//pullStockTransfer

    function pullStockTransfer($transferid, $stockTransferStatus, $StockDiaryReason, $userid) {
        $this->db = new DBConn();
        $query = "update it_stock_transfer set status = $stockTransferStatus,updatedby = $userid, pullby = $userid  where id = $transferid";
        $id = $this->db->execUpdate($query);
        $obj_stocktransfer = $this->getStockTransferDetails($transferid);
        if (isset($obj_stocktransfer)) {
            $addqry = "";
            $tolocationtype = $obj_stocktransfer->to_location_type;
            if ($tolocationtype == LocationType::CR) {
                $addqry = ",crid = $obj_stocktransfer->to_location_id";
            } else {
                $addqry = ",dcid = $obj_stocktransfer->to_location_id";
            }
        }
        if ($id > 0) {
            $obj_stocktransferitems = $this->getStockTransferItems($transferid);
            foreach ($obj_stocktransferitems as $transferitems) {
                $stockdiaryquery = "insert into it_stockdiary set prodid = $transferitems->prodid,"
                        . " batchcode = $transferitems->batchcode, reason = $StockDiaryReason, qty = $transferitems->qty $addqry";
                $this->db->execInsert($stockdiaryquery);

                $stockcurrentquery = "insert into it_stockcurr set prodid = $transferitems->prodid, batchcode = $transferitems->batchcode, "
                        . "qty = $transferitems->qty $addqry";
                $this->db->execInsert($stockcurrentquery);
            }
        }
        $this->db->closeConnection();
    }

//getcountstockpull

    function getcountstockpull($currstoreid, $status) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        //$query = "select count(*) as count from it_stock_transfer sc,it_rfc_master rfc where sc.to_location_id = rfc.id and sc.to_location_type = 2 and sc.status = $status and rfc.userid = $currstoreid";
        $query = "select count(*) as count from it_stock_transfer sc,it_rfc_master rfc where sc.to_location_id = rfc.id and sc.to_location_type = 2 and sc.status = $status and rfc.id = $crid";
        $obj_awaitingcount = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_awaitingcount) && $obj_awaitingcount != NULL) {
            return $obj_awaitingcount->count;
        } else {
            return NULL;
        }
    }

    function getAllActiveStock($usertype, $userid) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        if ($usertype == UserType::RFC) {
            //$addqry = " and sc.crid in (select id from it_rfc_master where userid = $userid )";
            $addqry = " and sc.crid in ($crid)";
        } else {
            $addqry = " and sc.dcid = $userid";
        }
        $query = "select sc.id,sc.dcid,sc.crid,sc.prodid,sc.batchcode,sum(sc.qty),sc.createtime,p.name as prod,p.desc1 as desc_1,p.desc2 as desc_2,"
                . "p.thickness as thickness,p.hsncode as hsncode,p.kg_per_pc,ctg.name as category  from it_stockcurr sc, it_products p,it_users u,it_rfc_master rfc,it_categories ctg "
                //. "where p.id = sc.prodid and p.ctg_id = ctg.id and u.id = rfc.userid and rfc.id = sc.crid and sc.qty > 0 $addqry group by prodid order by sc.id";
                . "where p.id = sc.prodid and p.ctg_id = ctg.id and u.crid = rfc.id and rfc.id = sc.crid and sc.qty > 0 $addqry group by prodid order by sc.id";
        //echo $query;
        $obj_stockcurrent = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockcurrent) && $obj_stockcurrent != NULL) {
            return $obj_stockcurrent;
        } else {
            return NULL;
        }
    }

    function getBatchCodes($productid, $usertype, $userid) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        $addqry = "";
        if ($usertype == UserType::RFC) {
            //$addqry = "and sc.crid =  (select id from it_rfc_master where userid = $userid )";
            $addqry = "and sc.crid = ($crid)";
        } else {
            $addqry = "and sc.dcid = $userid";
        }
        //$query = "select id,batchcode,qty from it_stockcurr where prodid = $productid and qty > 0 $addqry";
        $query = "select sc.id,sc.batchcode,round(sc.qty,2) as qty,gl.length from it_stockcurr sc,it_grnitems gl where sc.batchcode = gl.batchcode and sc.prodid =$productid and sc.qty > 0 $addqry";
        //error_log("\nRFCbatchde query: " . $query . "\n", 3, "../ajax/tmp.txt");
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getCurrStockByBatchcode($id) {
        $this->db = new DBConn();
        $query = "select * from it_stockcurr where id = $id and qty > 0";
        $obj_stockcurr = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_stockcurr) && $obj_stockcurr != NULL) {
            return $obj_stockcurr;
        } else {
            return NULL;
        }
    }

    function getDCStockSummery($dcid) {
        $this->db = new DBConn();

        $query = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,s.qty,round((s.qty/((gl.length/1000)*p.kg_per_pc)),2) as noofpcs, gl.rate,gl.totalrate,round(s.qty * gl.totalrate,2) as value,s.createtime"
                . " from it_products p,it_stockcurr s,it_categories c,it_grnitems gl where p.id = s.prodid and s.batchcode=gl.batchcode and  p.ctg_id = c.id and  s.dcid = $dcid order by s.id desc";
        //echo $query;
        $obj_stockdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockdetail) && $obj_stockdetail != NULL) {
            return $obj_stockdetail;
        } else {
            return NULL;
        }
    }

    function getCRStockSummery($crid, $uploaddate) {
        $this->db = new DBConn();

//        $query = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,s.qty,gl.totalrate,round(s.qty * gl.totalrate,2) as value,s.createtime"
//                . " from it_products p,it_stockcurr s,it_categories c,it_grnitems gl where p.id = s.prodid and s.batchcode=gl.batchcode and  p.ctg_id = c.id and  s.crid = $crid order by s.id desc";
//        
//        $query = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,round(s.qty,2) as qty,pr.price as price,"
//                . "round(s.qty * pr.price,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_product_price pr "
//                . "where p.id = s.prodid and s.prodid = pr.product_id and  p.ctg_id = c.id and pr.applicable_date = date(now()) "
//                . "and s.crid = $crid order by s.id desc";
//        $query = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,round(s.qty,2) as qty,pr.price as price,"
//                . "round(s.qty * pr.price,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_product_price pr "
//                . "where p.id = s.prodid and s.prodid = pr.product_id and  p.ctg_id = c.id and pr.applicable_date = date('$uploaddate') "
//                . "and s.crid = $crid order by s.id desc";
        $query = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,s.batchcode,gl.length ,round(s.qty,2) as qty,pr.price as price,"
                . "round(s.qty * pr.price,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_product_price pr,it_grnitems gl "
                . "where p.id = s.prodid and s.prodid = pr.product_id and s.batchcode = gl.batchcode and  p.ctg_id = c.id and "
                . "pr.applicable_date = date('$uploaddate') and s.crid = $crid order by s.id desc";
        //echo $query;
        $obj_stockdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockdetail) && $obj_stockdetail != NULL) {
            return $obj_stockdetail;
        } else {
            return NULL;
        }
    }

    function getGrnStockSummery($dcid) {
        $this->db = new DBConn();

        $query = "select d.dc_name,g.grnno,p.name,gl.batchcode,p.desc1,p.desc2,p.thickness,p.hsncode,gl.qty,gl.no_of_pieces,gl.totalrate,gl.cgstval,gl.sgstval,"
                . "gl.totalvalue,g.grndate,gl.createtime from it_dc_master d,it_grn g,it_grnitems gl,it_products p where g.dcid=d.id and g.id=gl.grnid and"
                . " p.id=gl.product_id and g.dcid = $dcid order by gl.createtime desc";
        //echo $query;
        $obj_stockdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockdetail) && $obj_stockdetail != NULL) {
            return $obj_stockdetail;
        } else {
            return NULL;
        }
    }

    function getPOStockSummery($dcid, $startDate, $endDate) {
        $this->db = new DBConn();

        $query = "select pr.pono,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,pl.sku,pl.qty,pl.no_of_pieces,pl.rate,pl.lcrate,
           pl.cgstval,pl.sgstval,pl.totalrate,pl.totalvalue,pl.createtime from it_products p,it_polines pl,it_purchaseorder pr 
           where p.id = pl.product_id and pr.id = pl.po_id and pr.delivery_id = $dcid and pr.createtime BETWEEN '$startDate' and '$endDate' "
                . "order by pr.createtime desc";
        //echo $query;
        $obj_podetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_podetail) && $obj_podetail != NULL) {
            return $obj_podetail;
        } else {
            return NULL;
        }
    }

    function updateInvoicepaymentmode($salesid, $userid, $chargetype) {
        $this->db = new DBConn();
        $tablename = $this->getSalesTableName($userid);
        $query = "update " . $tablename . " set paymentmode = $chargetype , updatetime = now() where id =" . $salesid;
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getInvoiceItemById($itemid, $userid) {
        $this->db = new DBConn();
        $inv_item_table = $this->getSalesItemsTableName($userid);
        $crdetails = $this->getCRDetailsByUserId($userid);
        $query = "select it.* from $inv_item_table as it where  it.id = $itemid";
        //echo $query;
        $obj = $this->db->fetchObject($query);
        if (isset($obj)) {
            $updateStockqry = "update it_stockcurr set qty = qty + $obj->qty where batchcode = $obj->batchcode and crid = $crdetails->id and id = $obj->stockcurrid";
            //error_log("\nupdateQry query: ".$updateStockqry."\n",3,"tmp.txt");
            $this->db->execUpdate($updateStockqry);
            $deleteqry = "delete from $inv_item_table where id =  $itemid";
            $this->db->execQuery($deleteqry);
        }
        $this->db->closeConnection();
        return true;
    }

    function cancelSales($salesid, $userid) {
        $this->db = new DBConn();
        $crdetails = $this->getCRDetailsByUserId($userid);
        $tablename = $this->getSalesTableName($userid);
        $inv_item_table = $this->getSalesItemsTableName($userid);
        $inv_item_details = $this->getInvoiceItems($salesid, $userid);
        if (isset($inv_item_details)) {
            foreach ($inv_item_details as $items) {
                $stockcurrqry = "update it_stockcurr set qty = qty + $items->qty where id = $items->stockcurrid and crid = $crdetails->id";
                $this->db->execUpdate($stockcurrqry);
                $deleteqry = "delete from $inv_item_table where id =  $items->id";
                $this->db->execQuery($deleteqry);
            }
        }
        $deleteinvqry = "delete from $tablename where id = $salesid";
        $this->db->execQuery($deleteinvqry);
        $this->db->closeConnection();
        return true;
    }

    function getCRSalesSummery($crid) {
        $this->db = new DBConn();
        $crdetails = $this->getCRInfoById($crid);
        $crcode = $crdetails->crcode;
        $query = "select c.invoice_no,c.saledate,c.cname,c.cphone,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,cl.batchcode,gl.length,cl.qty,cl.mrp,cl.cuttingcharges,cl.rate,cl.taxable,cl.cgst_amt,cl.sgst_amt,cl.total,cl.createtime"
                . " from it_" . "$crcode c,it_" . "$crcode" . "_items cl,it_products p,it_grnitems gl where c.id = cl.invoice_id and p.id = cl.product_id and gl.batchcode = cl.batchcode and c.status = 1 order by cl.createtime desc";
        //echo $query;
        $obj_salesdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_salesdetail) && $obj_salesdetail != NULL) {
            return $obj_salesdetail;
        } else {
            return NULL;
        }
    }

    function deleteGRNItem($itemid) {
        $this->db = new DBConn();
        $query = "delete from it_grnitems where id = $itemid";
        $this->db->execQuery($query);
        $this->db->closeConnection();
    }

    function getSelectedCRStock($crid, $applicable_date) {
        $this->db = new DBConn();
        $crid_db = $this->db->safe($crid);
        $applicable_date_db = $this->db->safe($applicable_date);
        //$query = "select p.id,p.name,p.desc1,p.desc2,p.thickness from it_products p, it_stockcurr s where p.id = s.prodid and s.crid in ($crid_db)";
        $query = "select p.id,p.name,p.desc1,p.desc2,p.thickness from it_products p, it_stockcurr s,it_product_price pr where p.id = s.prodid and"
                . " pr.product_id=p.id  and s.crid in ($crid) and pr.product_id not in (select product_id from it_product_price where applicable_date =$applicable_date_db)"
                . " group by p.id";
        //error_log("\nCR stockkkkkk query: " . $query . "\n", 3, "../ajax/tmp.txt"); 
        $obj_stockdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockdetail) && $obj_stockdetail != NULL) {
            return $obj_stockdetail;
        } else {
            return NULL;
        }
    }

//insertCRPrice

    function insertCRPrice($datepicker, $productpricestatus, $userid) {
        $this->db = new DBConn();
        //$len = sizeof($selcr);
        $cr = "";
        /* for ($i = 0; $i < $len; $i++) {
          if ($selcr[0] == 0) {
          // All is selected
          $cr = 0;
          break;
          } else {
          if ($cr != "") {
          $cr = $cr . "," . $selcr[$i];
          } else {
          $cr = $selcr[$i];
          }
          }
          } */
        if ($datepicker) {
            $datepicker_db = $this->db->safe($datepicker);
        }

        //$query = "insert into it_price set applicable_date = $datepicker_db ,crid = $cr,createdby =$userid ";
        $query = "insert into it_price set applicable_date = $datepicker_db ,createdby =$userid ";
        //echo $query;

        $price_id = $this->db->execInsert($query);

        $this->db->closeConnection();
        return $price_id;
    }

//getCRPricedetails

    function getCRPricedetails($priceid) {
        $this->db = new DBConn();
        //$crid_db = $this->db->safe($crid);
        $query = "select * from it_price where id = $priceid";
        //$query = "select rf.crcode,p.* from it_rfc_master rf,it_price p where rf.id = p.crid and p.id = $priceid group by crid";
        //error_log("\nCR price query: " . $query . "\n", 3, "../ajax/tmp.txt");    
        $obj_crpricedetail = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_crpricedetail) && $obj_crpricedetail != NULL) {
            return $obj_crpricedetail;
        } else {
            return NULL;
        }
    }

//getProductPriceByPriceId

    function getProductPriceByPriceId($priceid) {
        $this->db = new DBConn();
        //$crid_db = $this->db->safe($crid);
        $query = "select p.id,p.name as prod,p.desc1,p.desc2,p.thickness,pr.* from it_products p,it_product_price pr where pr.product_id = p.id and price_id = $priceid";
        //error_log("\ncr pricing query: ".$query."\n",3,"tmp.txt");
        $obj_stockdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockdetail) && $obj_stockdetail != NULL) {
            return $obj_stockdetail;
        } else {
            return NULL;
        }
    }

//fetchLastProductPriceByProdId

    function fetchLastProductPriceByProdId($productid, $applicable_date) {
        $this->db = new DBConn();
        $applicable_date_db = $this->db->safe($applicable_date);
        $query = "select id,product_id,price,max(applicable_date) as last_date from it_product_price where product_id = $productid and applicable_date < $applicable_date_db";
        //error_log("\ncr stock query: ".$query."\n",3,"tmp.txt");
        $obj_pricedetail = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_pricedetail) && $obj_pricedetail != NULL) {
            return $obj_pricedetail;
        } else {
            return NULL;
        }
    }

    function insertProductPriceItems($priceid, $prod_id, $crid, $newprice, $userid) {
        $this->db = new DBConn();
        $pricedetails = $this->getCRPricedetails($priceid);
        $appliacbledate_db = $this->db->safe($pricedetails->applicable_date);
        $lastPriceObj = $this->getLastprice($crid, $prod_id, $appliacbledate_db);
        if (isset($lastPriceObj) && $lastPriceObj != NULL) {
            $lastprice = $lastPriceObj->lastprice;
        } else {
            $lastprice = 0;
        }

        $query = "insert into it_product_price set price_id = $priceid, crid = $crid, product_id = $prod_id, price = $newprice, lastprice = $lastprice, applicable_date = $appliacbledate_db,status = 0,createdby =$userid ";
        //echo $query;
        $price_id = $this->db->execInsert($query);
        //}

        $this->db->closeConnection();
        return $price_id;
    }

    function getSetOfProducts() {
        $this->db = new DBConn();
        $query = "select distinct shortname from it_products";
        //error_log("\ncr stock query: ".$query."\n",3,"tmp.txt");
        $obj_setofprod = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_setofprod) && $obj_setofprod != NULL) {
            return $obj_setofprod;
        } else {
            return NULL;
        }
    }

//getProductList

    function getProductList($shortname, $applicabledate) {
        $this->db = new DBConn();
        //$query = "select p.id,pr.price,pr.lastprice from it_product_price pr, it_products p where p.id = pr.product_id and p.shortname = '$shortname' group by p.id;";
//        $query = "select p.id,pr.id,pr.price,pr.lastprice,pr.applicable_date from it_product_price pr, it_products p where p.id = pr.product_id and"
//                . " p.shortname = '$shortname' and pr.applicable_date =(select max(applicable_date) from it_product_price where applicable_date < '$applicabledate')"
//                . " group by p.id";
        $query = "select p.id as prod_id,pr.id,pr.price,pr.lastprice,pr.applicable_date from it_product_price pr, it_products p,it_stockcurr sc "
                . "where p.id = pr.product_id and pr.product_id = sc.prodid and p.id = sc.prodid and p.shortname = '$shortname' and "
                . "pr.applicable_date =(select max(applicable_date) from it_product_price where applicable_date < '$applicabledate') group by p.id";
        //echo $query;
        $obj_setofprod = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_setofprod) && $obj_setofprod != NULL) {
            return $obj_setofprod;
        } else {
            return NULL;
        }
    }

    function getLastprice($crid, $prod_id, $appliacbledate_db) {
        $this->db = new DBConn();
        $query = "select * from it_product_price where crid = $crid and product_id = $prod_id and applicable_date = (select max(applicable_date) "
                . "from it_product_price where applicable_date < $appliacbledate_db)";
        //echo $query;
        $obj_prod = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_prod) && $obj_prod != NULL) {
            return $obj_prod;
        } else {
            return NULL;
        }
    }

//getProdPricingByDate

    function getProdPricingByDate($crid, $prodid, $applicabledate) {
        $this->db = new DBConn();
        $applicable_date_db = $this->db->safe($applicabledate);
        $query = "select * from it_product_price where crid = $crid and product_id = $prodid and applicable_date=$applicable_date_db";
        //echo $query;
        $obj_prod = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj_prod) && $obj_prod != NULL) {
            return $obj_prod;
        } else {
            return NULL;
        }
    }

//insertListProductPriceItems

    function insertListProductPriceItems($priceid, $prodid, $crid, $lastprice, $newprice, $userid) {
        $this->db = new DBConn();
        $pricedetails = $this->getCRPricedetails($priceid);
        $appliacbledate_db = $this->db->safe($pricedetails->applicable_date);

        $query = "insert into it_product_price set price_id = $priceid, crid = $crid, product_id = $prodid, price = $newprice, "
                . "lastprice = $lastprice, applicable_date = $appliacbledate_db,status = 0,createdby =$userid ";
        //echo $query;
        $price_id = $this->db->execInsert($query);
        //}

        $this->db->closeConnection();
        return $price_id;
    }

    function updateProductPriceItems($prodPriceid, $priceid, $prod_id, $crid, $newprice, $userid) {
        $this->db = new DBConn();
        $pricedetails = $this->getCRPricedetails($priceid);
        $appliacbledate_db = $this->db->safe($pricedetails->applicable_date);
        $lastPriceObj = $this->getLastprice($crid, $prod_id, $appliacbledate_db);
        if (isset($lastPriceObj) && $lastPriceObj != NULL) {
            $lastprice = $lastPriceObj->lastprice;
        } else {
            $lastprice = 0;
        }

        $query = "update it_product_price set price_id = $priceid, crid = $crid, product_id = $prod_id, price = $newprice, lastprice = $lastprice, applicable_date = $appliacbledate_db,status = 0,createdby =$userid where id = $prodPriceid, price_id = $priceid and crid = $crid and product_id = $prod_id";
        //echo $query;
        $price_id = $this->db->execUpdate($query);
        //}

        $this->db->closeConnection();
        return $price_id;
    }

    function getPrevdayProdList($crid, $priceid) {
        $this->db = new DBConn();
        $pricedetails = $this->getCRPricedetails($priceid);
        $appliacbledate_db = $this->db->safe($pricedetails->applicable_date);

//        $query = "select pr.* from it_product_price pr, it_stockcurr sc where pr.product_id = sc.prodid and pr.crid = $crid and pr.is_approved = true and pr.applicable_date=(select max(applicable_date) "
//                . "from it_product_price where applicable_date < $appliacbledate_db)";
        $query = "select pr.* from it_product_price pr, it_stockcurr sc where pr.product_id = sc.prodid and sc.crid = $crid and sc.qty > 0 and pr.is_approved = true"
                . " and pr.applicable_date=(select max(applicable_date) from it_product_price where applicable_date < $appliacbledate_db)";
        //echo $query;
        $obj_list = $this->db->fetchObjectArray($query);
        if (isset($obj_list) && $obj_list != NULL) {
            return $obj_list;
        } else {
            return NULL;
        }
        $this->db->closeConnection();
    }

    function saveProductPricing($priceid, $productPriceStatus) {
        $this->db = new DBConn();
        $query = "update it_price set status = $productPriceStatus where id = $priceid";
        $id = $this->db->execUpdate($query);

        $query = "update it_product_price set status = $productPriceStatus where price_id = $priceid";
        $price_id = $this->db->execUpdate($query);

        $this->db->closeConnection();
        return $price_id;
    }

    function getProductPriceCount($priceid) {
        $this->db = new DBConn();

        $query = "select crid,count(*) as count from it_product_price where price_id = $priceid and is_approved = false";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function createNewPrice($userid) {
        $this->db = new DBConn();

        $query = "insert into it_price set applicable_date = date(now()) , price_datetime = now(),createdby =$userid ";
        //echo $query;

        $price_id = $this->db->execInsert($query);

        $this->db->closeConnection();
        return $price_id;
    }

    function getSelectedCRStockSet($crid, $applicabledate) {
        $this->db = new DBConn();
        $crid_db = $this->db->safe($crid);
        $applicable_date_db = $this->db->safe($applicable_date);
        //$query = "select p.id,p.name,p.desc1,p.desc2,p.thickness from it_products p, it_stockcurr s where p.id = s.prodid and s.crid in ($crid_db)";
        $query = "select distinct shortname as shortname from it_products p, it_stockcurr s,it_product_price pr where p.id = s.prodid and"
                . " pr.product_id=p.id  and s.crid in ($crid) and pr.product_id not in (select product_id from it_product_price where applicable_date =$applicable_date_db)"
                . " group by shortname";
        //error_log("\nCR stockkkkkk query: " . $query . "\n", 3, "../ajax/tmp.txt"); 
        $obj_stockdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockdetail) && $obj_stockdetail != NULL) {
            return $obj_stockdetail;
        } else {
            return NULL;
        }
    }

    function getSalesInvoice() {
        $this->db = new DBConn();
        $query = "select * from it_cr270001";
        $obj_inv = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_inv) && $obj_inv != NULL) {
            return $obj_inv;
        } else {
            return NULL;
        }
    }

    function updateInvNum($id, $invoice_num) {
        $this->db = new DBConn();
        $invoice_num_db = $this->db->safe($invoice_num);
        $query = "update it_cr270001 set invoice_no = $invoice_num_db where id= $id";
        $updateid = $this->db->execUpdate($query);

        $this->db->closeConnection();
        return $updateid;
    }

    /* select c.invoice_no,c.cname,c.cphone,sum(cl.qty) as qty,sum(cl.rate) as rate,sum(cl.taxable) as
     *  taxable,sum(cl.cgst_amt) as cgst_amt,sum(cl.sgst_amt) as sgst_amt,sum(cl.total) as total,cl.createtime from
     *  it_cr270001 c,it_cr270001_items cl,it_products p where c.id = cl.invoice_id and p.id = cl.product_id and c.status = 1
     *  group by c.invoice_no order by cl.createtime desc;
     */

	 function getAggCRSalesSummery($crid) {
        $this->db = new DBConn();
        $crdetails = $this->getCRInfoById($crid);
        $crcode = $crdetails->crcode;
        $query = "select id, saledate, customer_id, invoice_no, total_amount from it_$crcode where  status = 1;";
        //echo $query;
        $obj_salesdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_salesdetail) && $obj_salesdetail != NULL) {
            return $obj_salesdetail;
        } else {
            return NULL;
        }
    }


    function updateCustomer($custid, $gstin, $panno) {
        $this->db = new DBConn();
        error_log("\nCR db gstin: " . $gstin . "\n", 3, "../ajax/tmp.txt");
        $addqry = "";
        if ($gstin != "") {
            $addqry = "gstno = " . $this->db->safe($gstin);
        }

        if ($panno != "") {
            $addqry = ",panno = " . $this->db->safe($panno);
        }

        $query = "update it_customers set $addqry updatetime= now() where id = $custid";
        error_log("\nCR CUST query: " . $query . "\n", 3, "../ajax/tmp.txt");
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }

    function getAggCRStockSummery($crid, $uploaddate) {
        $this->db = new DBConn();
        $query = "select c.name as ctg,p.name,p.desc1,p.desc2,p.thickness,p.hsncode,p.stdlength,round(sum(s.qty),2) as qty,pr.price as price,"
                . "round(round(sum(s.qty),2) * pr.price,2) as value,s.createtime from it_products p,it_stockcurr s,it_categories c,it_product_price pr where "
                . "p.id = s.prodid and s.prodid = pr.product_id and p.ctg_id = c.id and pr.applicable_date = date('$uploaddate') and s.crid = $crid "
                . "group by p.id order by s.id desc";
        //echo $query;
        $obj_stockdetail = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj_stockdetail) && $obj_stockdetail != NULL) {
            return $obj_stockdetail;
        } else {
            return NULL;
        }
    }

    function getDistrictsByState($stateid) {
        $this->db = new DBConn();
        $query = "select * from districts where state_id = $stateid";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }

    function getCRByDistrict($distid) {
        $this->db = new DBConn();
        $query = "select * from it_rfc_master where inactive = 0 and districtid = $distid";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function updateCustomers($custid, $name, $address, $statesel, $city, $phone, $email, $gstno, $panno, $userid) {
        $this->db = new DBConn();

        $namedb = $this->db->safe($name);
        $address = isset($address) && $address != "" ? " ,address = " . $this->db->safe($address) : "";
        $statesel = ",state_id = " . $statesel;
        $city = isset($city) && $city != "" ? " ,city = " . $this->db->safe($city) : "";
        $phone = isset($phone) && $phone != "" ? " ,phone = " . $this->db->safe($phone) : "";
        $email = isset($email) && $email != "" ? " ,email = " . $this->db->safe($email) : "";
        $gstno = isset($gstno) && $gstno != "" ? " ,gstno = " . $this->db->safe($gstno) : "";
        $panno = isset($panno) && $panno != "" ? " ,panno = " . $this->db->safe($panno) : "";

        $query = "update it_customers set name = $namedb $address $statesel $city $phone $email $gstno $panno where id = $custid";
        //echo $query;
        $customer_id = $this->db->execInsert($query);
        $this->db->closeConnection();
        return $customer_id;
    }

    function verifyTallyLogin($username, $password) {
        $this->db = new DBConn();
        $username_db = $this->db->safe($username);
        $password_db = $this->db->safe($password);
        $query = "select * from it_users where username = $username_db and password = $password_db";
        //echo $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getCustomerMastersByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select c.*,s.state from it_customers c,states s where c.state_id = s.id and  c.createtime > $datetime_db";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getProductMastersByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select c.name as catname,p.* from it_products p,it_categories c where c.id = p.ctg_id and p.createtime > $datetime_db";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getCRTableNameById($crid) {
        $this->db = new DBConn();
        //$query = "select crcode from it_rfc_master where userid = $userid";
        $query = "select crcode from it_rfc_master where id = $crid";
        $obj_cr = $this->db->fetchObject($query);
        $crcode = $obj_cr->crcode;
        $tablename = "it_" . $crcode;
        $this->db->closeConnection();
        return $tablename;
    }

    function getCRItemTableNameById($crid) {
        $this->db = new DBConn();
        //$query = "select crcode from it_rfc_master where userid = $userid";
        $query = "select crcode from it_rfc_master where id = $crid";
        //error_log("\nSalesQry query: ".$query."\n",3,"tmp.txt");
        $obj_cr = $this->db->fetchObject($query);
        $crcode = $obj_cr->crcode;
        $tablename = "it_" . $crcode . "_items";
        $this->db->closeConnection();
        return $tablename;
    }

    function getCRDetailsById($crid) {
        $this->db = new DBConn();
        //$query = "select s.state as dealerstate, rf.* from it_rfc_master rf,states s where rf.state = s.id and  rf.userid = $userid";
        $query = "select s.state as dealerstate, rf.* from it_rfc_master rf,states s where rf.state = s.id and  rf.id = $crid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getInvoiceHeaderDetails($invid, $crid) {
        $this->db = new DBConn();
        //$inv_item_table = $this->getSalesItemsTableName($userid);
        $inv_table = $this->getCRTableNameById($crid);
        $query = "select * from " . $inv_table . " where id = $invid";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getInvoiceItemsByCrid($saleid, $crid) {
        $this->db = new DBConn();
        $inv_item_table = $this->getCRItemTableNameById($crid);
        $query = "select p.hsncode, p.name as product,sp.name as spec,p.desc1 as desc_1,p.desc2 as desc_2,p.thickness as thickness,"
                . "p.hsncode as hsncode, it.* from $inv_item_table as it, it_products p,it_specifications sp where it.product_id = p.id "
                . "and p.spec_id = sp.id and it.invoice_id = $saleid";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getSaleDetailsByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        //$query = "select p.name,p.shortname,p.desc1,p.desc2,p.thickness,p.hsncode,cs.* from it_cr_salesreport cs,it_products p where p.id = cs.prod_id and cs.createtime > $datetime";
        $query = "select id,crid,crcode,invoice_no,reference_no,invoice_date,customer_id,cname,cphone,invoice_type,paymentmode,round(sum(total),2) as netvalue,"
                . "round(sum(total)) as invoicevalue,(round(sum(total))-round(sum(total),2)) as roundoff from it_cr_salesreport"
                . " where createtime > $datetime_db and createtime > '2019-01-10 00:00:00'  group by invoice_no order by id limit 6";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getInvoiceItemsByInvoiceNo($invoiceno) {
        $this->db = new DBConn();
        $invoiceno_db = $this->db->safe($invoiceno);
        $query = "select p.name,p.shortname,p.desc1,p.desc2,p.thickness,p.hsncode,cs.* from it_cr_salesreport cs,it_products p where p.id = cs.prod_id and cs.invoice_no = $invoiceno_db";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getDCMastersByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select dc.*,s.state as sstate from it_dc_master dc,states s where dc.state = s.id and  dc.createtime > $datetime_db";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getCRMastersByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select rfc.*,s.state as sstate from it_rfc_master rfc,states s where rfc.state = s.id and  rfc.createtime > $datetime_db";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getPODetailsByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select s.company_name as supplierName,py.term as paymentterm, po.* from it_suppliers s,it_purchaseorder po,it_payment_terms py "
                . "where s.id = po.supplier_id and py.id = po.payment_id and po.createtime > $datetime_db and po.po_status = 5 order by id";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getPOItemsBypiod($poid) {
        $this->db = new DBConn();
        $query = "select s.company_name as supplierName,py.term as paymentterm, po.* from it_suppliers s,it_purchaseorder po,it_payment_terms py where s.id = po.supplier_id and py.id = po.payment_id and po.createtime > $datetime_db";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getSupplierMastersByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select sp.*,s.state as sstate from it_suppliers sp,states s where sp.state = s.id and sp.createtime > $datetime_db";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getGRNDetailsByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select g.*,po.pono as pono,s.company_name as supplier,dc.dc_name from it_grn g,it_purchaseorder po,it_suppliers s,it_dc_master dc where g.suppid = s.id and g.poid = po.id and g.dcid = dc.id and g.createtime > $datetime_db";
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getStockPullDetailsByDate($datetime) {
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select st.*,dc.dc_name,cr.crcode from it_stock_transfer st,it_dc_master dc,it_rfc_master cr where "
                . "st.from_location_id = dc.id and st.to_location_id = cr.id and st.createtime > $datetime_db order by st.id";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }

    function getInvoiceDetailsByInvoiceNo($invno) {
        $this->db = new DBConn();
        $invno_db = $this->db->safe($invno);
        $query = "select * from it_cr270001 where invoice_no = $invno_db";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }
    
    function fetchNextCNNumber($stateid) {
        $this->db = new DBConn();
        $query = "select * from it_cnnum where stateid = $stateid";
        //return $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj->num + 1;
        } else {
            return NULL;
        }
    }
    
    function insertCreditNote($invid, $invdate, $invoiceno, $customer_id, $cname, $cphone, $cndate, $cnnum, $cnstatus, $userid, $discount){
        $this->db = new DBConn();
        $invno_db = $this->db->safe($invoiceno);
        $invdate_db = $this->db->safe($invdate);
        $cname_db = $this->db->safe($cname);
        $cphone_db = $this->db->safe($cphone); 
        $cnnum_db = $this->db->safe($cnnum);
        $cndate_db = $this->db->safe($cndate);
        $query = "insert into it_creditnote set cnno = $cnnum_db, crid = 1, cndate = $cndate_db, invoiceid = $invid, invoice_no = $invno_db, invoice_date = $invdate_db,"
                . " customerid = $customer_id, cname = $cname_db, cphone = $cphone_db, status = $cnstatus, createdby = $userid, discount = $discount";
        $id = $this->db->execInsert($query);
        if ($id > 0) {
            $query = "update it_cnnum set num = num + 1";
            $this->db->execUpdate($query);
        }
        $this->db->closeConnection();
        return $id;
    }
    
    function insertCNItem($userid,$invid,$cnid,$prodid,$invItemid,$qty,$baseratebeforedisc,$discrate,$rate,$batchcode,$nou,$cnstatus,$usertype){
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        $tax_rate = $this->getTaxRate($prodid);
        $invItemDetails = $this->getInvItemDetails($invItemid,$prodid, $usertype, $userid);
        $addQuery = "";
        if(isset($invItemDetails) && $invItemDetails != ""){
            
                $addQuery = ", invactualrate = $invItemDetails->actualrate";
                $addQuery .= ", invmrp = $invItemDetails->mrp";
                if(isset($invItemDetails->cuttingcharges)){
                    $addQuery .= ", invcuttingcharges = $invItemDetails->cuttingcharges";
                }
                
                $addQuery .= ", invqty = $invItemDetails->qty";
                $addQuery .= ", invrate = $invItemDetails->rate";
                if(isset($invItemDetails->paymentcharges)){
                    $addQuery .= ", invpaymentcharges = $invItemDetails->paymentcharges";
                }
                $addQuery .= ", invtaxable = $invItemDetails->taxable";
                $addQuery .= ", invcgstpct = $invItemDetails->cgst_percent";
                $addQuery .= ", invsgstpct = $invItemDetails->sgst_percent";
                $addQuery .= ", invigstpct = $invItemDetails->igst_percent";
                $addQuery .= ", invcgstval = $invItemDetails->cgst_amt";
                $addQuery .= ", invsgstval = $invItemDetails->sgst_amt";
                $addQuery .= ", invigstval = $invItemDetails->igst_amt";

                $trate = ($tax_rate / 100);
                $baserate = $discrate;
                $taxable_amt = round($baserate / $trate, 2);
                $tax_amt = $baserate * $trate;
                $cgst_per = round($tax_rate / 2, 2);
                $sgst_per = round($tax_rate / 2, 2);
                $igst_per = round($tax_rate, 2);
                $cgst_amt = round($tax_amt / 2, 2);
                $sgst_amt = round($tax_amt / 2, 2);
                $igst_amt = round($tax_amt, 2);

                $mrp = round($baserate + $cgst_amt + $sgst_amt, 2);
                //print $mrp;

                $total = round($qty * $mrp, 2);

                $batchcode_db = $this->db->safe($batchcode);

                $query = "insert into it_creditnote_items set status = $cnstatus, cnid = $cnid, invoicelineid = $invItemid, batchcode = $batchcode_db, "
                        . "product_id = $prodid, actualrate = $discrate, mrp = $mrp, qty = $qty, rate = $discrate, taxable = $taxable_amt,cgstpct = $cgst_per, "
                        . "cgstval = $cgst_amt, sgstpct = $sgst_per, sgstval = $sgst_amt, igstpct = $igst_per, igstval = $igst_amt, total = $total $addQuery";
                //echo $query;
                //return;
                $item_id = $this->db->execInsert($query);
                if ($item_id > 0) {
                    $stockqry = "update it_stockcurr set qty = qty + $qty where batchcode = $batchcode_db and crid = $crid";
                    $updated_id = $this->db->execUpdate($stockqry);
                }
            //}
            $this->db->closeConnection();
            return $item_id;
        }
        //}
    }
    
    function saveCN($cnid,$cnStatus,$totalQty,$totalValue,$StockDiaryReason){
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        $query = "update it_creditnote set status = $cnStatus, tot_qty = $totalQty, tot_value = $totalValue  where id = $cnid";
        //echo $query."<br>";
        $id = $this->db->execUpdate($query);
        if ($id > 0) {
            $obj_cnitems = $this->getCNItems($cnid);
            if ($obj_cnitems != NULL) {
                foreach ($obj_cnitems as $cnitems) {
                    $stockdiaryquery = "insert into it_stockdiary set crid = $crid, prodid = $cnitems->product_id, batchcode = $cnitems->batchcode, reason = $StockDiaryReason, qty = $cnitems->qty";
                    $this->db->execInsert($stockdiaryquery);
                    
                }
            }
        }
        $this->db->closeConnection();
    }
            
    function getCNDetails($id){
        $this->db = new DBConn();
        $query = "select * from it_creditnote where id = $id";
        //return $query;
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj)) {
            return $obj;
        } else {
            return NULL;
        }
    }
    
    function getCNItems($cnid){
        $this->db = new DBConn();
        $query = "select p.hsncode, p.name as product,sp.name as spec,p.desc1 as desc_1,p.desc2 as desc_2,p.thickness as thickness,p.hsncode as hsncode, it.* from it_creditnote_items as it, it_products p,it_specifications sp where it.product_id = p.id and p.spec_id = sp.id and it.cnid = $cnid";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }
    
    function getInvItemDetails($itemid,$productid, $usertype, $userid){
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        $addqry = "";
        
        //$query = "select id,batchcode,qty from it_stockcurr where prodid = $productid and qty > 0 $addqry";
        $query = "select * from it_cr270001_items where id = $itemid and product_id = $productid";
        //error_log("\nRFCbatchde query: " . $query . "\n", 3, "../ajax/tmp.txt");
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        if (isset($obj) && $obj != NULL) {
            return $obj;
        } else {
            return NULL;
        }
    }
    
    function getCreditNoteItemById($itemid,$userid){
        $this->db = new DBConn();
        //$inv_item_table = $this->getSalesItemsTableName($userid);
        $crdetails = $this->getCRDetailsByUserId($userid);
        $query = "select it.* from it_creditnote_items as it where  it.id = $itemid";
        //echo $query;
        $obj = $this->db->fetchObject($query);
        if (isset($obj)) {
            $updateStockqry = "update it_stockcurr set qty = qty - $obj->qty where batchcode = $obj->batchcode and crid = $crdetails->id";
            //error_log("\nupdateQry query: ".$updateStockqry."\n",3,"tmp.txt");
            $this->db->execUpdate($updateStockqry);
            $deleteqry = "delete from it_creditnote_items where id =  $itemid";
            $this->db->execQuery($deleteqry);
        }
        $this->db->closeConnection();
        return true;
    }
    
    function getCreditNoteDetailsByDate($datetime){
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select * from it_creditnote where status = 1 and createtime > $datetime_db";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }
    
    function getImprestByDate($datetime){
        $this->db = new DBConn();
        $datetime_db = $this->db->safe($datetime);
        $query = "select * from it_imprest_details where ctime > $datetime_db";
        //echo $query;
        $obj = $this->db->fetchObjectArray($query);
        $this->db->closeConnection();
        return $obj;
    }
    /* Mayur functions*/
  
    function getCollectionForOpenSale($crid){
        $this->db = new DBConn();
        $query = "select closing_balance as cash, debitcard, creditcard, cheque, creditnote from it_collection_register where crid = $crid and closetime is not null and closetime in (select max(closetime) from it_collection_register)";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
        
    }
 
   function insertIntoCollectionRegister($userid, $crid, $openingCash){
        $this->db = new DBConn();
        $stockdiaryquery = "insert into it_collection_register set crid = $crid, opentime = now() , open_byuser = $userid, opening_balance= $openingCash, createtime = now()";
        $insertId = $this->db->execInsert($stockdiaryquery);
        return $insertId;
    } 
    function checkOpenSaleStatus($userid){
        $this->db = new DBConn();
        $CRobj = $this->getCRDetailsByUserId($userid);
        $crid = $CRobj->id;
        if(isset($crid)){
            $query = "select id from it_collection_register where crid = $crid and closetime is null and opentime in (select max(opentime) from it_collection_register )";
            $obj = $this->db->fetchObject($query);
            $this->db->closeConnection();
            if(isset($obj)){
               return $obj;
            } else {
               $obj = (object) [
                'id' => 0,
                ];
                $this->db->closeConnection();
                return $obj; 
            }
        } else {
            $obj = (object) [
                'id' => 0,
              ];
            $this->db->closeConnection();
            return $obj;
        }
    }
    
    function insert_into_payments_diary($saleid,$userid, $collecRegId, $paymodeId, $transactionType, $tot_amt){
        $this->db = new DBConn();
        $paymentdiaryqry = "insert into it_payments_diary set invoice_id = $saleid, collection_reg_id = $collecRegId , paymentType = $paymodeId, amount = $tot_amt, transaction_type = '$transactionType', byuser = $userid, createtime = now()";
//        error_log("\n payments_diary    : $paymentdiaryqry \n",3,"../tmp.txt");
        $insertId = $this->db->execInsert($paymentdiaryqry);
        return $insertId;
    }
    
    function getSaleCollectionByCollectionRegId($collecRegID){
	$this->db = new DBConn();
        $query = "select (select COALESCE(SUM(amount),0) from it_payments_diary where paymentType = 4 and collection_reg_id = $collecRegID) as 'credit_card',".
                "(select COALESCE(SUM(amount),0) from it_payments_diary where paymentType = 1 and collection_reg_id = $collecRegID) as 'debit_card',".
                "(select COALESCE(SUM(amount),0) from it_payments_diary where paymentType = 3 and collection_reg_id = $collecRegID) as 'cash',".
                "(select deposit_in_bank from it_collection_register where id = $collecRegID) as 'deposit_in_bank'";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;    
	}
    
       function closeSaleCollectionRegInfo($collecRegID,$closecash,$debit_card,$credit_card, $saleCash,$userid){
        $this->db = new DBConn();
        $query = "update it_collection_register set closetime = now(), cash = $saleCash, debitcard = $debit_card, creditcard = $credit_card, closing_balance = $closecash, close_byuser = $userid  where id = $collecRegID and closetime is null";
//        error_log("\n payments_diary    : $paymentdiaryqry \n",3,"../tmp.txt");
        $insertId = $this->db->execUpdate($query);
        return $insertId;
    } 
    function getImpressReasons() {
        $this->db = new DBConn();
        $query = "select id,title, sign from it_imprest_reason";
        $reg = $this->db->fetchAllObjects($query);
        $this->db->closeConnection();
        return $reg;
    }
    
    function insertIntoImprestDetails($amount, $description, $voucher_no, $userid, $crid){
        $this->db = new DBConn();
        $paymentdiaryqry = "insert into it_imprest_details set amount = $amount , voucher_no = '$voucher_no' , description = '$description' , by_user = $userid, crid = $crid, ctime = now() ";
//        error_log("\n payments_diary    : $paymentdiaryqry \n",3,"../tmp.txt");
        $insertId = $this->db->execInsert($paymentdiaryqry);
        return $insertId;
    }
    
    function  getSignByReasonId($reason){
                $this->db = new DBConn();
        $query = "select sign from it_imprest_reason where id = $reason";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }
    
    function getVoucherNum(){
        $this->db = new DBConn();
        $query = "select num from it_imprest_num";
        //error_log("\n Invoice no query: ".$query."\n",3,"../ajax/tmp.txt");
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj->num;
    }
    
    function updateVoucherNum(){
        $this->db = new DBConn();
        $query = "update it_imprest_num set num = num + 1";
        $this->db->execUpdate($query);
        $this->db->closeConnection();
    }
    
    function getPaymentType(){
        $this->db = new DBConn();
        $query = "select id, chargetypedesc from it_extra_charges where isactive = 1 and in_deposite = 1";
        $objs = $this->db->fetchAllObjects($query);
        $this->db->closeConnection();
        return $objs;
    }
    
    function insertDepositDiary($userid, $crid, $coll_reg_id, $amount, $receiptno, $description, $paymentType){
        $this->db = new DBConn();
        $depositdiaryqry = "insert into it_deposit_diary set crid = $crid , coll_reg_id = $coll_reg_id , amount = $amount , receipt_no = $receiptno, description = '$description', by_user = $userid, payment_type = $paymentType ";
//        error_log("\n payments_diary    : $paymentdiaryqry \n",3,"../tmp.txt");
        $insertId = $this->db->execInsert($depositdiaryqry);
        return $insertId;
    }
    
    function updateDepositDetailsIntoCollReg($coll_reg_id, $amount){
        $this->db = new DBConn();
        $query = "update it_collection_register set deposit_in_bank = deposit_in_bank + $amount where id = $coll_reg_id and closetime is null";
//        error_log("\n payments_diary    : $paymentdiaryqry \n",3,"../tmp.txt");
        $insertId = $this->db->execUpdate($query);
        return $insertId;
    }
    /*Mayur functions end*/

    function insert_into_it_cr_salesreport($saleid, $userid) {
        $this->db = new DBConn();
        $currStore = getCurrStore();
        $crid = $currStore->crid;
        $crcode = $this->getCRCode($userid);
        $crcode_db = $this->db->safe($crcode);
        $invoiceDetails = $this->getSalesInfo($userid, $saleid);
        $invoice_no = $invoiceDetails->invoice_no;
        $invarr = explode("-", $invoice_no);
        $reference_no = $this->db->safe($invarr[0]);
        $invoiceno = $this->db->safe($invarr[1]);
        $invoice_date = $this->db->safe($invoiceDetails->saledate);
        $customer_id = $invoiceDetails->customer_id;
        $cname = $invoiceDetails->cname;
        $cphone = $invoiceDetails->cphone;
        $invoice_type = $invoiceDetails->invoice_type;
        $paymentmode = $invoiceDetails->paymentmode;
        $status = $invoiceDetails->status;
        $invoiceItems = $this->getInvoiceItems($saleid, $userid);
        foreach ($invoiceItems as $items) {
            $addquery = "";
            if ($customer_id) {
                $addquery = ", customer_id = $customer_id";
            }

            if ($cname) {
                $cname_db = $this->db->safe($cname);
                $addquery = ", cname = $cname_db";
            }

            if ($cphone) {
                $cphone_db = $this->db->safe($cphone);
                $addquery = ", cphone = $cphone_db";
            }

            if (isset($items->paymentcharges)) {
                $addquery = ", paymentcharges = $items->paymentcharges";
            }

            $query = "insert into it_cr_salesreport set invoice_id  = $saleid, crid = $crid, crcode = $crcode_db, reference_no = $reference_no, "
                    . "invoice_no = $invoiceno, invoice_date = $invoice_date, "
                    . "invoice_type = $invoice_type, paymentmode = $paymentmode, prod_id = $items->product_id, batchcode = $items->batchcode, "
                    . "qty = $items->qty, actualrate = $items->actualrate, mrp = $items->mrp, cuttingcharges = $items->cuttingcharges, "
                    . "rate = $items->rate, taxable = $items->taxable, cgst_percent = $items->cgst_percent, "
                    . "cgst_amt = $items->cgst_amt, sgst_percent = $items->sgst_percent, sgst_amt = $items->sgst_amt, igst_percent = $items->igst_percent, "
                    . "igst_amt  = $items->igst_amt, total = $items->total, status = $status $addquery";
            //error_log("\nsale report Qry query: ".$query."\n",3,"tmp.txt");
            $insert_id = $this->db->execInsert($query);
        }
        $this->db->closeConnection();
    }

    function getTallyUserDetail() {
        $this->db = new DBConn();
        $query = "select * from it_users where username = 'shradhatally'";
        $obj = $this->db->fetchObject($query);
        $this->db->closeConnection();
        return $obj;
    }

}

?>
