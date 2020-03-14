<?php

class attributes {

    public function getBillingSoftware() {
//        global $g_bill_sw;

        $g_bill_sw = array(
            "0" => "Tally",
            "1" => "Busy SS 3.6(Busy Infotech Pvt Ltd, Delhi)",
            "2" => "FinPlus",
            "3" => "Tally erp 9",
            "4" => "Tally 9.2",
            "5" => "ABSTech Logiks Private Limited",
            "6" => "Soft (DOS Based)",
            "7" => "Tally 9",
            "8" => "Busy 12.0 Real1.4g",
            "9" => "True Inventory & Accounts By Goyal Softwares",
            "10" => "Miracle",
            "11" => "Power Pack a DOS base system",
            "12" => "Wings Accounting Biz",
            "13" => "Dos Based billing software"
        );
        return $g_bill_sw;
    }

    public function getSystemConf() {
        $g_sys_conf = array(
            "0" => "Windows XP SP 2 32 bit",
            "1" => "Windows XP SP 3 32 bit",
            "2" => "Windows 7 Ultimate 32 bit",
            "3" => "Windows 7 Home Basic 32 bit",
            "4" => "Windows 7 Ultimate 32 bit",
            "5" => "Windows Vista Home Basic SP 1 32 bit",
            "6" => "Windows 8 Pro 32 bit",
            "7" => "Windows 7 Ultimate 64 bit",
            "8" => "Windows 7 Home Premium 64 bit",
            "9" => "Windows 7 Home Basic SP 1 64 Bit",
            "10" => "Windows 8 Single Laguage 64 bit"
        );
        return $g_sys_conf;
    }

    function getPrinter() {
        $g_printer = array(
            "0" => "Epson LQ-1050+",
            "1" => "Canon LBP 2900",
            "2" => "TVS MSP 345 Star on server",
            "3" => "Samsung SCX-4300 series",
            "4" => "HP Laser Jet 1080",
            "5" => "TVS MSP 250 STAR",
            "6" => "HP Laserjet 1020",
            "7" => "HP Laserjet P1008",
            "8" => "HP Deskjet 1000 J110 Series",
            "9" => "Canon Bubble-Jet BJ-10e",
            "10" => "Samsung ML 1640",
        );
        return $g_printer;
    }

}

?>
