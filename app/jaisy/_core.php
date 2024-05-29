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
$GLOBALS['listBaris'] = [];
$GLOBALS['listVar'] = [];
$GLOBALS['listPrint'] = [];
$GLOBALS['tmpJikaMaka'] = [];
$GLOBALS['listenJika'] = [];


function jaisyInterpreter($jaisy){
    // $jaisy = trim($jaisy);
    $jaisy = str_replace("\r\n", "\n", $jaisy);
    $pecah_baris = explode("\n",$jaisy);
    array_unshift($pecah_baris, '//jaisy interpreter');
    $GLOBALS['totalBaris'] = count($pecah_baris);
    $GLOBALS['listBaris'] = $pecah_baris;
    
    
    for($i=1;$i<count($pecah_baris);$i++){
        render($i, $pecah_baris[$i]);
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
    printJaisy($barisKe, $code);
}

function varJaisy($barisKe, $code, $note=''){
    $firstChar = substr($code, 0, 1);
    $pecahSpasi = explode(' ', $code);
    $firstWord = $pecahSpasi[0];

    if($firstChar == '$'){
        preg_match('/\$(.*?)\s*=\s*(.*)/', $code, $matches);
    
        if ($matches) {
            // echo $code;
            $namaVar = $matches[1];
            $isiVar = $matches[2];
            // $GLOBALS['listVar'][$matches[1]] = $matches[2];
            $cariFungsi = explode(' : ', $isiVar);
            // debug('cariFUngsi', $cariFungsi);
            if(count($cariFungsi) == 1){ // jika tidak terdapat fungsi
                // echo $matches[2];
                if(isset($GLOBALS['listVar'][$namaVar]) && $note !== 'updateVar'){
                    error($barisKe, 'variabel $'.$namaVar.' sudah ada');
                }else{
                    $GLOBALS['listVar'][$namaVar] = parseX($barisKe, $matches[2], 'exceptCharSpace');
                }
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
    }elseif($firstWord == 'update'){
        unset($pecahSpasi[0]);
        $isiUpdate = implode(' ', $pecahSpasi);
        // update $namaVar = data
        if(substr($pecahSpasi[1], 0, 1) == '$'){
            varJaisy($barisKe,$isiUpdate, 'updateVar');
        }else{
            error($barisKe, 'Tidak Sesuai Aturan');
        }
    }else{
        if($note == 'varOnly'){
            if($firstChar !== '$'){
                error($barisKe, 'Tidak Sesuai Aturan');
            }
        }
    }

}


function printJaisy($barisKe, $code){
    $pecahSpasi = explode(' ', $code);
    $firstWord = $pecahSpasi[0];
    // print_r($pecahSpasi);
    // echo $firstWord;
    // debug('printJaisy', $code);
    if($firstWord == 'print'){
        unset($pecahSpasi[0]);
        $isiPrint = implode(' ', $pecahSpasi);
        // echo $isiPrint;
        if (isset($pecahSpasi[1])) {
            $GLOBALS['listPrint'][] = parseX($barisKe, $isiPrint);
        }else{
            $GLOBALS['listPrint'][] = '';
        }
    }
}


function commentJaisy($code){
    $pecah = explode('//', $code);
    return $pecah[0];
}

function parseX($barisKe, $data, $note=''){
    // ini dulu : print halo $nama @spasi #AL
    // output   : print haloFAZA  ال
    // nanti $fungsi($arg1, $arg2, $arg3, $arg4)
    $data = trim($data);
    $data = isolasiPetik($barisKe, $data);
    $pecahSpasi = explode(' ', $data);
    // debug('pecahSpasi', $pecahSpasi);
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

    $output = implode('', $pecahSpasi);
    return parseIsolasiPetik($barisKe, $output);
}

function parseVar($barisKe, $data){
    // debug('parseVar', $data);
    if(isset($GLOBALS['listVar'][$data])){
        return $GLOBALS['listVar'][$data];
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

function jikaMakaJaisy($barisKe, $code){
    $pecahSpasi = explode(' ', $code);
    $firstWord = $pecahSpasi[0];
    if($firstWord == 'jika'){
        $GLOBALS['listenJika'] = []; // reset listener
        $idJikaMaka = count($GLOBALS['tmpJikaMaka']);
        unset($pecahSpasi[0]);
        $isiJika = implode(' ', $pecahSpasi);
        // echo $isiJika;
        if(in_array('merupakan', $pecahSpasi)){
            // echo 'merupakan';
            // echo $idJikaMaka;
            $pecahPerbandingan = explode('merupakan', $isiJika);
            $perbandinganA = $pecahPerbandingan[0];
            $perbandinganB = $pecahPerbandingan[1];

            $GLOBALS['tmpJikaMaka'][$idJikaMaka]['jika'] = ['isi' => $isiJika,
                                                            'isTrue' => merupakan(parseX($barisKe, $perbandinganA), parseX($barisKe, $perbandinganB)),
                                                            'isCompleted' => false
                                                            ];
            $GLOBALS['listenJika'] = ['idJikaMaka' => $idJikaMaka, 'point' => 'jika'];
        }
    }elseif($firstWord == 'atau_jika'){
        unset($pecahSpasi[0]);
        $isiAtauJika = implode(' ', $pecahSpasi);
        $id = $GLOBALS['listenJika']['idJikaMaka'];
        $point = $GLOBALS['listenJika']['point'];
        // echo $isiJika;
        if(in_array('merupakan', $pecahSpasi)){
            // echo 'merupakan';
            // echo $idJikaMaka;
            $pecahPerbandingan = explode('merupakan', $isiAtauJika);
            $perbandinganA = $pecahPerbandingan[0];
            $perbandinganB = $pecahPerbandingan[1];

            $GLOBALS['tmpJikaMaka'][$id]['atau_jika'][] = ['isi' => $isiAtauJika,
                                                           'isTrue' => merupakan(parseX($barisKe, $perbandinganA), parseX($barisKe, $perbandinganB))
                                                            ];
            $GLOBALS['listenJika'] = ['idJikaMaka' => $id, 'point' => 'atau_jika', 'idAtauJika' => count($GLOBALS['tmpJikaMaka'][$id]['atau_jika'])-1];
            // echo $GLOBALS['listenJika']['idAtauJika'];
        }
    }elseif($firstWord == 'maka'){
        // $idTmpJikaMaka = count($GLOBALS['tmpJikaMaka'])-1;
        $id = $GLOBALS['listenJika']['idJikaMaka'];
        $point = $GLOBALS['listenJika']['point'];
        $isCompleted = $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'];
        $idAtauJika = $GLOBALS['listenJika']['idAtauJika'] ?? false;
        unset($pecahSpasi[0]);
        $isiMaka = implode(' ', $pecahSpasi);
        $GLOBALS['tmpJikaMaka'][$id][$point]['maka']['isi'] = $isiMaka;

        // echo json_encode($GLOBALS['listenJika']);
        // echo $idAtauJika;
        // echo $id .PHP_EOL. $point .PHP_EOL. $idAtauJika .PHP_EOL. $isiMaka;
        if($isCompleted == false){
            if(!is_int($idAtauJika)){
                // echo 'jika';
                // echo json_encode($GLOBALS['tmpJikaMaka']);
                // echo $id . $point . $atauJika . $isiMaka;
                if($GLOBALS['tmpJikaMaka'][$id][$point]['isTrue'] == true){
                    varJaisy($barisKe, $isiMaka, 'varOnly');
                    $cekNextBaris = explode(' ', trim($GLOBALS['listBaris'][$barisKe+1]));
                    if($cekNextBaris[0] !== 'kemudian'){
                        $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'] = true;
                    }
                }
            }else{
                // echo 'jika_tidak';
                if($GLOBALS['tmpJikaMaka'][$id][$point][$idAtauJika]['isTrue'] == true){
                    varJaisy($barisKe, $isiMaka, 'varOnly');
                    $cekNextBaris = explode(' ', trim($GLOBALS['listBaris'][$barisKe+1]));
                    if($cekNextBaris[0] !== 'kemudian'){
                        $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'] = true;
                    }
                }
            }
        }
        
        // echo json_encode($GLOBALS['tmpJikaMaka']);
    }elseif($firstWord == 'jika_tidak'){
        // $idTmpJikaMaka = count($GLOBALS['tmpJikaMaka'])-1;
        $id = $GLOBALS['listenJika']['idJikaMaka'];
        $isCompleted = $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'];
        // $point = $GLOBALS['listenJika']['point'];
        unset($pecahSpasi[0]);
        $isiJikaTidak = implode(' ', $pecahSpasi);
        $GLOBALS['tmpJikaMaka'][$id]['jika']['jika_tidak']['isi'] = $isiJikaTidak;
        $GLOBALS['listenJika']['point'] = 'jika_tidak';
        $GLOBALS['tmpJikaMaka'][$id]['jika']['jika_tidak']['isTrue'] = false;
        if($isCompleted == false){
            varJaisy($barisKe, $isiJikaTidak, 'varOnly');
            $GLOBALS['tmpJikaMaka'][$id]['jika']['jika_tidak']['isTrue'] = true;
        }
        // echo json_encode($GLOBALS['tmpJikaMaka']);
    }elseif($firstWord == 'kemudian'){
        // $idTmpJikaMaka = count($GLOBALS['tmpJikaMaka'])-1;
        $id = $GLOBALS['listenJika']['idJikaMaka'];
        $point = $GLOBALS['listenJika']['point'];
        $isCompleted = $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'];
        $idAtauJika = $GLOBALS['listenJika']['idAtauJika'] ?? false;
        unset($pecahSpasi[0]);
        $isiKemudian = implode(' ', $pecahSpasi);
        // $GLOBALS['tmpJikaMaka'][$id][$point]['kemudian'][]['isi'] = $isiKemudian;

        // echo json_encode($GLOBALS['listenJika']);
        // echo $idAtauJika;
        // echo $id .PHP_EOL. $point .PHP_EOL. $idAtauJika .PHP_EOL. $isiMaka;
        if($point == 'jika_tidak'){
            // echo $point.'::::'. $isiKemudian;
            if($GLOBALS['tmpJikaMaka'][$id]['jika']['jika_tidak']['isTrue'] == true){
                varJaisy($barisKe, $isiKemudian, 'varOnly');
                $GLOBALS['tmpJikaMaka'][$id]['jika'][$point]['kemudian'][]['isi'] = $isiKemudian;
            }
        }elseif($isCompleted == false && isset($GLOBALS['tmpJikaMaka'][$id]['jika']['maka'])){
            if(!is_int($idAtauJika)){ // kemudian -> maka -> jika
                // echo 'jika';
                // echo json_encode($GLOBALS['tmpJikaMaka']);
                // echo $id . $point . $atauJika . $isiMaka;
                if($GLOBALS['tmpJikaMaka'][$id][$point]['isTrue'] == true){
                    // echo $point.'::::'. $isiKemudian;
                    varJaisy($barisKe, $isiKemudian, 'varOnly');
                    $GLOBALS['tmpJikaMaka'][$id][$point]['maka']['kemudian'][]['isi'] = $isiKemudian;
                    $cekNextBaris = explode(' ', trim($GLOBALS['listBaris'][$barisKe+1]));
                    if($cekNextBaris[0] !== 'kemudian'){
                        $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'] = true;
                    }
                }
            }else{ // kemudian -> maka -> atau_jika
                // echo 'jika_tidak';
                if($GLOBALS['tmpJikaMaka'][$id][$point][$idAtauJika]['isTrue'] == true){
                    // echo $point.'::::'. $isiKemudian;
                    varJaisy($barisKe, $isiKemudian, 'varOnly');
                    $GLOBALS['tmpJikaMaka'][$id][$point]['maka']['kemudian'][]['isi'] = $isiKemudian;
                    $cekNextBaris = explode(' ', trim($GLOBALS['listBaris'][$barisKe+1]));
                    if($cekNextBaris[0] !== 'kemudian'){
                        $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'] = true;
                    }
                }
            }
        }else{
            error($barisKe, 'Tidak Sesuai Aturan');
        }
    }


    // function perbandingan($data){
    //     $list = ['merupakan', 'terdapat'];
    // }

} // end jikaMakaJaisy


function isolasiPetik($barisKe, $data){
    return preg_replace_callback(
        '/(["\'])(.*?)\1/',
        function ($matches) {
            // Ganti spasi dalam teks yang diapit petik dengan `!^`_^!`
            $innerText = str_replace(' ', '!^`_^!`', $matches[2]);
            // Kembalikan teks dengan petik yang sama seperti aslinya
            // return $matches[1] . $innerText . $matches[1];
            return $innerText;
        },
        $data
    );
}

function parseIsolasiPetik($barisKe, $data){
    return str_replace('!^`_^!`', ' ', $data);
}

function cekSyntax($barisKe, $data){
    // $data = trim($data);
    $allowFirstKeyword = ['print', 'jika', 'maka', 'atau_jika', 'jika_tidak', 'kemudian', 'buat_fungsi', 'tutup_fungsi'];
    $pecahSpasi = explode(' ', $data);
    $firstWord = $pecahSpasi[0];
    $twoFirstChar = substr($data, 0, 2);

    if(substr($data, 0, 1) == ' '){
        error($barisKe, 'Tidak Sesuai Aturan');
    }
    // var_dump(!in_array($firstWord, $allowFirstKeyword) && $twoFirstChar !== '//');
    if(!in_array($firstWord, $allowFirstKeyword)){ // jika tidak diawali dengan keyword yang diizinkan dan bukan komentar
        preg_match('/\$(.*?)\s*=\s*(.*)/', $data, $matches); // mencari variabel ($var = data)
        if (!$matches) { // jika tidak variabel (belum handle for)
            if($data !== '' && $twoFirstChar !== '//'){ // jika tidak baris kosong dan tidak komentar
                error($barisKe, 'Tidak Sesuai Aturan');
            }
        }
    }

}



// print

function printOutput(){
    // $output = [];
    // foreach($GLOBALS['listPrint'] as $key){
    //     $key = trim($key);
    //     if(isset($GLOBALS['listVar'][$key])){
    //         $output[] = $GLOBALS['listVar'][$key];
    //     }else{
    //         $output[] = $key;
    //     }
    // }

    // return implode("\n",$output);
    return implode("\n", $GLOBALS['listPrint']);
}

function error($barisKe, $report){
    $GLOBALS['error'] = 'Error baris ke '.$barisKe.' // '.$report;
    // exit();
}


// print_r($GLOBALS['listVar']);
// print_r($GLOBALS['listPrint']);
// print_r($GLOBALS['listFungsi']);



// helper
function debug($varName, $array) {
    // return;
    echo '<pre>';
    echo "Nama Variabel: $varName\n";
    var_dump($array);
    echo '</pre>';
}