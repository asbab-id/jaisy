<?php
require_once '_fungsi.php';
require_once '_charPegonList.php';


// $listVar        = [];
// $listPrint      = [];
$GLOBALS['fungsiJaisy']     = [
                                'gunduler', 'clean_spasi', 'hitung',
                                'hapus', 'ganti', 'cari', 'tambah',
                                'pecah_huruf', 'pecah',
                                'antara', 'awal', 'akhir'
                            ];
$GLOBALS['listVar'] = [];
$GLOBALS['listPrint'] = [];


function jaisyInterpreter($jaisy){
    $jaisy = trim($jaisy);
    $pecah_baris = explode("\n",$jaisy);
    array_unshift($pecah_baris, '//jaisy interpreter');
    $GLOBALS['totalBaris'] = count($pecah_baris);
    
    for($i=1;$i<count($pecah_baris);$i++){
        render($i, $pecah_baris[$i]);
    }

    if(isset($GLOBALS['error'])){
        return $GLOBALS['error'];
    }else{
        return printOutput();
    }
}

function render($barisKe, $code){
    $code = commentJaisy($code);
    varJaisy($barisKe, $code);
    printJaisy($barisKe, $code);
}

function varJaisy($barisKe, $code){
    $firstChar = substr($code, 0, 1);

    if($firstChar == '$'){
        preg_match('/\$(.*?)\s*=\s*(.*)/', $code, $matches);
    
        if ($matches) {
            $namaVar = $matches[1];
            $isiVar = $matches[2];
            // $GLOBALS['listVar'][$matches[1]] = $matches[2];
            $cariFungsi = explode(' : ', $isiVar);
            // debug('cariFUngsi', $cariFungsi);
            if(count($cariFungsi) == 1){ // jika tidak terdapat fungsi
                $GLOBALS['listVar'][$namaVar] = parseX($barisKe, $matches[2]);
            }else{
                $fungsi = $cariFungsi[0];
                $arg1 = $cariFungsi[1];
                $arg2 = $cariFungsi[2] ?? null;
                $arg3 = $cariFungsi[3] ?? null;
                $arg4 = $cariFungsi[4] ?? null;
                if(in_array($fungsi, $GLOBALS['fungsiJaisy'])){
                    $GLOBALS['listVar'][$namaVar] = $fungsi(parseX($barisKe, $arg1), parseX($barisKe, $arg2), parseX($barisKe, $arg3), parseX($barisKe, $arg4));
                }
            }
        }else{
            error($barisKe, 'Tidak Sesuai Aturan');
        }
    }

}

function printJaisy($barisKe, $code){
    $firstWord = explode(' ', $code)[0];
    if($firstWord == 'print'){
        if (preg_match('/^print\s(\$?)(.*)$/', $code, $matches)) {
            // $matches[2] akan berisi teks yang diinginkan
            $GLOBALS['listPrint'][] = parseX($barisKe, $matches[2]);
        }
    }
}


function commentJaisy($code){
    $pecah = explode('//', $code);
    return $pecah[0];
}

function parseX($barisKe, $data){
    // ini dulu : print halo $nama @spasi #AL
    // output   : print haloFAZA  ال
    // nanti $fungsi($arg1, $arg2, $arg3, $arg4)
    $data = trim($data);
    $pecahSpasi = explode(' ', $data);
    debug('pecahSpasi', $pecahSpasi);
    foreach($pecahSpasi as $key => $value){
        $value = trim($value);
        $firstChar = substr($value, 0, 1);
        if($value == '$' || $value == '@' || $value == '#'){
            $pecahSpasi[$key] = ' ';
        }elseif($firstChar == '$'){
            $pecahValue = explode('$', $value);
            $pecahSpasi[$key] = parseVar($barisKe, $pecahValue[1]);
        }elseif($firstChar == '@'){
            $pecahValue = explode('@', $value);
            $pecahSpasi[$key] = parseChar($barisKe, $pecahValue[1]);
        }elseif($firstChar == '#'){
            $pecahValue = explode('#', $value);
            $pecahSpasi[$key] = parsePegon($barisKe, $pecahValue[1]);
        }else{
            $pecahSpasi[$key] = $value;
        }
    }

    return implode('', $pecahSpasi);
}

function parseVar($barisKe, $data){
    // debug('parseVar', $data);
    if(isset($GLOBALS['listVar'][$data])){
        return parseX($barisKe, $GLOBALS['listVar'][$data]);;
    }else{
        error($barisKe, 'variabel $'.$data.' tidak ditemukan');
    }
}

function parseChar($barisKe, $data){
    foreach ($GLOBALS['charPegonList'] as $row) {
        if ($row[2] === $data) {
            return $row[0];
        }
    }

    // return jika tidak ditemukan
    error($barisKe, 'char '.$data.' tidak ditemukan');
}
function parsePegon($barisKe, $data){
    return $data;
}




// print

function printOutput(){
    $output = [];
    foreach($GLOBALS['listPrint'] as $key){
        $key = trim($key);
        if(isset($GLOBALS['listVar'][$key])){
            $output[] = $GLOBALS['listVar'][$key];
        }else{
            $output[] = $key;
        }
    }

    return implode("\n",$output);
}

function error($barisKe, $report){
    $GLOBALS['error'] = 'error baris ke '.$barisKe.' // '.$report;
    // exit();
}


// print_r($GLOBALS['listVar']);
// print_r($GLOBALS['listPrint']);
// print_r($GLOBALS['listFungsi']);



// helper
function debug($varName, $array) {
    return;
    echo '<pre>';
    echo "Nama Variabel: $varName\n";
    var_dump($array);
    echo '</pre>';
}