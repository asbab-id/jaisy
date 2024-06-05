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

function parseVar($barisKe, $data, $note='ifFalseReturnRaw'){
    // debug('parseVar', $data);
    if(isset($GLOBALS['listVar'][$data])){
        return $GLOBALS['listVar'][$data];
    }else{
        if($note == 'ifFalseReturnRaw'){
            return $data;
        }else{
            error($barisKe, 'variabel $'.$data.' tidak ditemukan');
        }
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