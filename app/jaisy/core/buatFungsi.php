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

        foreach($listArg as $key => $value){
            if(substr($listArg[$key], 0, 1) !== '$'){
                error($barisKe, 'tidak Sesuai Aturan. Harus diawali dengan variabel $');
            }
        }

        $GLOBALS['tmpBuatFungsi'][$namaFungsi]['arg'] = $listArg;
        $GLOBALS['listenBuatFungsi']['idBuatFungsi'] = count($GLOBALS['tmpBuatFungsi']) - 1;

        // menangkap list baris antara tutup_fungsi
        for($i=$barisKe; $i<$GLOBALS['totalBaris']; $i++){
            // $GLOBALS['listBaris'][$i] = trim($GLOBALS['listBaris'][$i]);
            $firstWordScan = explode(' ', trim($GLOBALS['listBaris'][$i]))[0];
            if($i !== $barisKe){
                $GLOBALS['tmpBuatFungsi'][$namaFungsi]['aksi'][] = [$i, $GLOBALS['listBaris'][$i]];
            }

            // echo $firstWordScan . PHP_EOL;
            if(!in_array($firstWordScan, ['buat_fungsi', 'tutup_fungsi', 'jika', 'maka', 'atau_jika', 'jika_tidak', 'kemudian'])){
                // echo $firstWordScan;
                if(substr(trim($firstWordScan), 0, 1) !== '$' && substr(trim($firstWordScan), 0, 2) !== '//'){ // jika di dalam buat_fungsi bukan variabel
                    error($i, 'tidak Sesuai Aturan. (buat_fungsi)');
                    break;
                }
            }

            // if(!in_array($firstWordScan, ['jika', 'maka', 'atau_jika', 'jika_tidak', 'kemudian'])){
            //     $GLOBALS['listBaris'][$i] = '//isolasi_Fungsi__Jaisy___System'.$GLOBALS['listBaris'][$i];
            // }
            
            $GLOBALS['listBaris'][$i] = '//isolasi_Fungsi__Jaisy___System'.$GLOBALS['listBaris'][$i];

            if($firstWordScan == 'tutup_fungsi'){ // baris tutup fungsi
                $GLOBALS['tmpBuatFungsi'][$namaFungsi]['barisTutupFungsi'] = $i;
                array_pop($GLOBALS['tmpBuatFungsi'][$namaFungsi]['aksi']);
                break;
            }
        }
    }
}


function callFungsiBuat($barisKe, $fungsi, $arg){
    // var_dump($arg);
    $argIsiFungsi = $GLOBALS['tmpBuatFungsi'][$fungsi]['arg'];
    if(isset($argIsiFungsi[0])){$GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($argIsiFungsi[0], 1)] = $arg[0];}
    if(isset($argIsiFungsi[1])){$GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($argIsiFungsi[1], 1)] = $arg[1];}
    if(isset($argIsiFungsi[2])){$GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($argIsiFungsi[2], 1)] = $arg[2];}
    if(isset($argIsiFungsi[3])){$GLOBALS['tmpBuatFungsi'][$fungsi]['var'][substr($argIsiFungsi[3], 1)] = $arg[3];}

    for($i=0; $i<count($GLOBALS['tmpBuatFungsi'][$fungsi]['aksi']); $i++){
        $barisAksi = $GLOBALS['tmpBuatFungsi'][$fungsi]['aksi'][$i][1];
        $firstWordScan = explode(' ', trim($barisAksi))[0];

        if(in_array($firstWordScan, ['jika', 'maka', 'atau_jika', 'jika_tidak', 'kemudian'])){
            $parseVarFungsi = parseVarFungsi($barisKe, $fungsi, $barisAksi);
            // $parsed = parseX($barisKe, $parseVarFungsi);
            // var_dump($parsed);
            jikaMakaJaisy($barisKe, $parseVarFungsi, 'fungsi');
        }

        if(substr(trim($barisAksi), 0, 1) == '$'){
            preg_match('/\$(.*?)\s*=\s*(.*)/', $barisAksi, $matches);
            if ($matches) {
                // echo $code;
                $namaVar = $matches[1];
                $isiVar = $matches[2];
                $parsedIsiVar = parseVarFungsi($barisKe, $fungsi, $isiVar);
                $parsedConcatIsivar = parseClearConcat($barisKe, $parsedIsiVar);
                $parseXIsiVar = parseX($barisKe,  $parsedConcatIsivar, 'spasi');
                $GLOBALS['tmpBuatFungsi'][$fungsi]['var'][$namaVar]  = $parseXIsiVar;
                // if($namaVar == 'hasil'){
                //     $GLOBALS['tmpBuatFungsi'][$fungsi]['var']['hasil']  = parseVarFungsi($barisKe, $fungsi, $isiVar);
                // }else{
                //     $GLOBALS['tmpBuatFungsi'][$fungsi]['var'][$namaVar] = parseVarFungsi($barisKe, $fungsi, $isiVar);
                // }
            }
        }
    }
    $output = $GLOBALS['tmpBuatFungsi'][$fungsi]['var']['hasil'];
    // $output = parseX($barisKe,  $GLOBALS['tmpBuatFungsi'][$fungsi]['var']['hasil'], '');
    // $output = parseIsolasiDolar($barisKe, $output);
    unset($GLOBALS['tmpBuatFungsi'][$fungsi]['var']); // jangan lupa nyalain lagi guyssssssssssssssssssssss

    if(isset($GLOBALS['tmpVarFungsi'])){
        foreach($GLOBALS['tmpVarFungsi'] as $key => $value){
            unset($GLOBALS['listVar'][$value]);
        }
    }
    return $output;
} // end callFungsiBuat



function parseVarFungsi($barisKe, $fungsi, $data){
    $pecahSpasi = explode(' ', $data);
    // var_dump($pecahSpasi);
    // $var = substr(trim($pecahSpasi[0]), 1);
    for($i=0; $i<count($pecahSpasi); $i++){
        // var_dump($pecahSpasi[$i]);
        // echo PHP_EOL.PHP_EOL.PHP_EOL;

        $namaVar = substr($pecahSpasi[$i], 1);
        if(isset($GLOBALS['tmpBuatFungsi'][$fungsi]['var'][$namaVar])){ // variabel scope dalam fungsi
            $isiVar = $GLOBALS['tmpBuatFungsi'][$fungsi]['var'][$namaVar];
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
                    // echo $pecahSpasi[$i];
                    // echo parseVarFungsi($barisKe, $fungsi, $arg1);
                    // echo $fungsi2(parseVarFungsi($barisKe, $fungsi, $arg1);
                    $pecahSpasi[$i] = $fungsi2(parseVarFungsi($barisKe, $fungsi, $arg1), parseVarFungsi($barisKe, $fungsi, $arg2), parseVarFungsi($barisKe, $fungsi, $arg3), parseVarFungsi($barisKe, $fungsi, $arg4));
                    // echo $pecahSpasi[$i];
                }elseif(isset($GLOBALS['tmpBuatFungsi'][substr($fungsi2, 1)])){
                    $fungsi3 = substr($fungsi2, 1);
                    $pecahSpasi[$i] = callFungsiBuat($barisKe, $fungsi3, [$arg1, $arg2, $arg3, $arg4]);
                }
            }else{
                // echo 'ga ada function' . PHP_EOL;
                // $isiVar = isolasiSpasi($barisKe, $isiVar);
                $pecahSpasi[$i] = $isiVar;
            }
        }else{ // variabel global
            $firstChar = substr($pecahSpasi[$i], 0, 1);
            if($firstChar == '$'){
                $namaVar = substr($pecahSpasi[$i], 1);
                // echo parseVar($barisKe, $namaVar, 'ifFalseReturnRaw');
                $pecahSpasi[$i] = parseVar($barisKe, $namaVar, 'ifFalseReturnRaw');
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

    // echo $output;
    return $output;
}