<?php
require_once '_fungsi.php';
require_once '_charPegonList.php';
require_once '_core.php';


// $listVar        = [];
// $listPrint      = [];
$GLOBALS['fungsiJaisy']     = [
                                'gunduler', 'clean_spasi', 'hitung',
                                'hapus', 'ganti', 'cari', 'tambah',
                                'pecah_huruf', 'pecah',
                                'antara', 'awal', 'akhir'
                            ];

$input_tmp =
'// print ini seharusnya kena komen
//print hallo
//print // kosong

$tanpaPetik = saya ucapkan @SYIN // komen
print $tanpaPetik
print aku malah @JIM @SYIN

//$petik = "anjay"
//print $petik

$jaisy       = جَيْشٌ
$tesGunduler = gunduler : $jaisy         // جيش  
print $tesGunduler

//$tesHapus  = hapus : $tesGunduler : @syin        // جي
//print $tesHapus
';


echo '<style>*{white-space:pre}</style>';
echo "<h2><u>input :</u></h2><br>$input_tmp<hr>";
echo "<h2><u>output :</u></h2><br><div style='background-color:lightgray'>".jaisyInterpreter($input_tmp)."</div><hr>";