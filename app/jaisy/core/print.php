<?php

function printJaisy($barisKe, $code){
    $pecahSpasi = explode(' ', $code);
    $firstWord = $pecahSpasi[0];
    if($firstWord == 'print'){
        unset($pecahSpasi[0]);
        $isiPrint = implode(' ', $pecahSpasi);
        if (isset($pecahSpasi[1])) {
            $data = parseX($barisKe, $isiPrint);
            $GLOBALS['listPrint'][] = parseCharSpasi($barisKe, $data);
        }else{
            $GLOBALS['listPrint'][] = '';
        }
    }
}