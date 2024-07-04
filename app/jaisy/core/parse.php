<?php


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
            // $pecahSpasi[$key] = ' ';
            $pecahSpasi[$key] =  ' '.$value.' ';
        }elseif($firstChar == '$'){
            // $pecahValue = explode('$', $value);
            // $pecahSpasi[$key] = parseVar($barisKe, $pecahValue[1]);
            $pecahSpasi[$key] = parseVar($barisKe, $value);
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

    if($note == 'spasi'){
        $output = implode(' ', $pecahSpasi);
    }else{
        $output = implode('', $pecahSpasi);
    }
    return parseIsolasiPetik($barisKe, $output);
}

function parseVar($barisKe, $data, $note=''){
    // debug('parseVar', $data);
    preg_match('/^\$(\w+)(?:\[(\w+)\])?$/', $data, $matches); // regex untuk namaVar dan namaArr. (tanpa isiVar)
    if($matches){
        // var_dump($matches);
        $namaVar = $matches[1];
        $namaArr = $matches[2] ?? null;
    
        if(isset($GLOBALS['listVar'][$namaVar])){
            $isiVar = $GLOBALS['listVar'][$namaVar];
            if(detectTypeList($isiVar) == 'non_list' && $namaArr == null){ // cek $var tapi isinya bukan list
                return $isiVar;
            }elseif(detectTypeList($isiVar) == 'non_list' && $namaArr !== null){ // cek $var[arr] tapi isinya bukan list
                error($barisKe, 'variabel $'.$namaVar.' tidak berupa list.');
            }elseif(detectTypeList($isiVar) !== 'non_list' && $namaArr == null){ // cek $var tapi isinya list (print all list/print_r)
                return $isiVar;
            }else{ // jika $isiVar berupa list
                // var_dump($isiVar);
                // var_dump($namaArr);
                return readList($isiVar, $namaArr);
            }
            // return $GLOBALS['listVar'][$namaVar];
        }else{
            if($note == 'ifFalseReturnRaw'){
                // echo $data;
                return $data;
            }else{
                error($barisKe, 'variabel $'.$namaVar.' tidak ditemukan');
            }
        }
    }else{
        error($barisKe, 'ada kesalahan saat parsing variabel $'.$data.' tidak ditemukan');
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


function parseClearConcat($barisKe, $data){
    $output = str_replace(' @ ', ' ', $data);
    $output = str_replace(' # ', ' ', $output);
    $output = str_replace(' $ ', ' ', $output);
    return $output;
}