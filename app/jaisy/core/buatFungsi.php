<?php

function buatFungsiJaisy($barisKe, $code){
    $pecahSpasi = explode(' ', $code);
    $firstWord = $pecahSpasi[0];
    if($firstWord == 'buat_fungsi'){
        unset($pecahSpasi[0]);
        $isiBuatFungsi = implode(' ', $pecahSpasi);
        $namaFungsi = $pecahSpasi[1];

        if(isset($GLOBALS['tmpBuatFungsi'][$namaFungsi])){
            error($barisKe, 'Fungsi Sudah Ada');
        }

        $GLOBALS['tmpBuatFungsi'][$namaFungsi]['isi'] = $isiBuatFungsi;
        $listArg = explode(' : ', $isiBuatFungsi); array_shift($listArg);
        $GLOBALS['tmpBuatFungsi'][$namaFungsi]['arg'] = $listArg;
        $GLOBALS['listenBuatFungsi']['idBuatFungsi'] = count($GLOBALS['tmpBuatFungsi']) - 1;

        // menangkap list baris antara tutup_fungsi
        $o = 0;
        for($i=$barisKe; $i<$GLOBALS['totalBaris']; $i++){
            $o = $i+1;
            // $GLOBALS['listBaris'][$i] = trim($GLOBALS['listBaris'][$i]);
            $firstWordScan = explode(' ', trim($GLOBALS['listBaris'][$i]))[0];
            if($i !== $barisKe){
                $GLOBALS['tmpBuatFungsi'][$namaFungsi]['aksi'][] = [$i, $GLOBALS['listBaris'][$i]];
            }

            // echo $firstWordScan . PHP_EOL;
            if($firstWordScan !== 'buat_fungsi' && $firstWordScan !== 'tutup_fungsi'){
                // echo $firstWordScan;
                if(substr(trim($firstWordScan), 0, 1) !== '$' && substr(trim($firstWordScan), 0, 2) !== '//'){ // jika di dalam buat_fungsi bukan variabel
                    error($o, 'tidak Sesuai Aturan');
                    break;
                }
            }

            $GLOBALS['listBaris'][$i] = '//isolasi_Fungsi__Jaisy___System'.$GLOBALS['listBaris'][$i];

            if($firstWordScan == 'tutup_fungsi'){ // baris tutup fungsi
                $GLOBALS['tmpBuatFungsi'][$namaFungsi]['barisTutupFungsi'] = $i;
                array_pop($GLOBALS['tmpBuatFungsi'][$namaFungsi]['aksi']);
            }
        }
    }
}


function callFungsiBuat($barisKe, $fungsi, $arg){
    $argIsiFungsi = $GLOBALS['tmpBuatFungsi'][$fungsi]['arg'];
    if(isset($argIsiFungsi[0])){$GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($argIsiFungsi[0], 1)] = $arg[0];}
    if(isset($argIsiFungsi[1])){$GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($argIsiFungsi[1], 1)] = $arg[1];}
    if(isset($argIsiFungsi[2])){$GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($argIsiFungsi[2], 1)] = $arg[2];}
    if(isset($argIsiFungsi[3])){$GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($argIsiFungsi[3], 1)] = $arg[3];}

    for($i=0; $i<count($GLOBALS['tmpBuatFungsi'][$fungsi]['aksi']); $i++){
        $barisAksi = $GLOBALS['tmpBuatFungsi'][$fungsi]['aksi'][$i][1];
        preg_match('/\$(.*?)\s*=\s*(.*)/', $barisAksi, $matches);
        if ($matches) {
            // echo $code;
            $namaVar = $matches[1];
            $isiVar = $matches[2];
            $GLOBALS['tmpBuatFungsi'][$fungsi]['var'][$namaVar] = parseVarFungsi($barisKe, $fungsi, $isiVar);
            if($namaVar == 'hasil'){
                // var_dump(parseVarFungsi($barisKe, $fungsi, $isiVar));
            }
        }
    }
    $output = parseX($barisKe,  $GLOBALS['tmpBuatFungsi'][$fungsi]['var']['hasil'], 'isolasiSpasi');
    $output = parseIsolasiDolar($barisKe, $output);
    unset($GLOBALS['tmpBuatFungsi'][$fungsi]['var']);
    return $output;
} // end callFungsiBuat



function parseVarFungsi($barisKe, $fungsi, $data){
    $pecahSpasi = explode(' ', $data);
    // var_dump($pecahSpasi);
    // $var = substr(trim($pecahSpasi[0]), 1);
    for($i=0; $i<count($pecahSpasi); $i++){
        // var_dump($pecahSpasi[$i]);
        // echo PHP_EOL.PHP_EOL.PHP_EOL;

        if(isset($GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($pecahSpasi[$i], 1)])){
            $namaVar = substr($pecahSpasi[$i], 1);
            $isiVar = $GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($pecahSpasi[$i], 1)];
            // echo 'ada var ' . substr($pecahSpasi[$i], 1) . PHP_EOL;
            // echo 'isinya' . $isiVar . PHP_EOL;

            // echo $namaVar . 'adalah var' . PHP_EOL;

            $cariFungsi = explode(' : ', $isiVar);
            // var_dump($isiVar);
            if(count($cariFungsi) > 1){ // jika  terdapat fungsi
                $fungsi2 = $cariFungsi[0];
                // echo 'ada function' .$fungsi2. PHP_EOL;
                $arg1 = $cariFungsi[1];
                $arg2 = $cariFungsi[2] ?? null;
                $arg3 = $cariFungsi[3] ?? null;
                $arg4 = $cariFungsi[4] ?? null;
                if(in_array($fungsi2, $GLOBALS['listSkill'])){
                    $pecahSpasi[$i] = $fungsi2(parseVarFungsi($barisKe, $fungsi, $arg1), parseVarFungsi($barisKe, $fungsi, $arg2), parseVarFungsi($barisKe, $fungsi, $arg3), parseVarFungsi($barisKe, $fungsi, $arg4));
                }elseif(isset($GLOBALS['tmpBuatFungsi'][substr($fungsi2, 1)])){
                    $fungsi2 = substr($fungsi2, 1);
                    $pecahSpasi[$i] = callFungsiBuat($barisKe, $fungsi2, [$arg1, $arg2, $arg3, $arg4]);
                }
            }else{
                // echo 'ga ada function' . PHP_EOL;
                $pecahSpasi[$i] = $isiVar;
            }
        }else{
            $firstChar = substr($pecahSpasi[$i], 0, 1);
            if($firstChar == '$'){
                $pecahSpasi[$i] = parseVar($barisKe, $pecahSpasi[$i], 'ifFalseReturnRaw');
            }else{
                // echo 'bukan var';
                // $namaVar = substr($pecahSpasi[$i], 1);
                $pecahSpasi[$i] = parseIsolasiDolar($barisKe, $pecahSpasi[$i]);
            }
        }
        // echo $pecahSpasi[$i] . PHP_EOL;
    }
    // var_dump($pecahSpasi);
    $output = implode(' ', $pecahSpasi);

    return $output;
}