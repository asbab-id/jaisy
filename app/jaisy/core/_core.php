<?php
require_once 'listChar.php';
require_once 'skillFunction.php';

require_once 'variabel.php';
require_once 'print.php';
require_once 'komen.php';
require_once 'parse.php';
require_once 'jikaMaka.php';
require_once 'buatFungsi.php';
require_once 'cekSyntax.php';
require_once 'isolasi.php';
require_once 'pegon.php';





$GLOBALS['listSkill']     = [
                                'gunduler', 'clean_spasi', 'hitung',
                                'hapus', 'ganti', 'cari', 'tambah',
                                'pecah_huruf', 'pecah',
                                'antara', 'awal', 'akhir'
                            ];
$GLOBALS['listBaris'] = [];
$GLOBALS['listVar'] = [];
$GLOBALS['listPrint'] = [];
$GLOBALS['tmpJikaMaka'] = [];
$GLOBALS['listenJika'] = [];
$GLOBALS['tmpBuatFungsi'] = [];
$GLOBALS['listenBuatFungsi'] = [];


function jaisyInterpreter($jaisy){
    // $jaisy = trim($jaisy);
    $jaisy = str_replace("\r\n", "\n", $jaisy);
    $pecah_baris = explode("\n",$jaisy);
    array_unshift($pecah_baris, '//jaisy interpreter');
    $GLOBALS['totalBaris'] = count($pecah_baris);
    $GLOBALS['listBaris'] = $pecah_baris;
    
    
    for($i=1;$i<count($GLOBALS['listBaris']);$i++){
        render($i, $GLOBALS['listBaris'][$i]);
        if(isset($GLOBALS['error'])){break;} // stop jika ada error
    }

    if(isset($GLOBALS['error'])){
        return $GLOBALS['error'];
    }else{
        return printOutput();
    }
}

function render($barisKe, $code){
    $code = commentJaisy($code);
    cekSyntax($barisKe, $code);
    varJaisy($barisKe, $code);
    jikaMakaJaisy($barisKe, $code);
    buatFungsiJaisy($barisKe, $code);


    printJaisy($barisKe, $code);
}



function printOutput(){
    return implode("\n", $GLOBALS['listPrint']);
}

function error($barisKe, $report){
    $GLOBALS['error'] = 'Error baris ke '.$barisKe.' // '.$report;
}


function debug($varName, $array) {
    // return;
    echo '<pre>';
    echo "Nama Variabel: $varName\n";
    var_dump($array);
    echo '</pre>';
}