<?php

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
                if(in_array($fungsi, $GLOBALS['listSkill'])){
                    $GLOBALS['listVar'][$namaVar] = $fungsi(parseX($barisKe, $arg1), parseX($barisKe, $arg2), parseX($barisKe, $arg3), parseX($barisKe, $arg4));
                }elseif(isset($GLOBALS['tmpBuatFungsi'][substr($fungsi, 1)])){
                    $fungsi = substr($fungsi, 1);
                    $GLOBALS['listVar'][$namaVar] = callFungsiBuat($barisKe, $fungsi, [$arg1, $arg2, $arg3, $arg4]);
                }
            }
        }else{
            error($barisKe, 'Tidak Sesuai Aturan Penulisan Variabel.');
        }
    }elseif($firstWord == 'update'){
        unset($pecahSpasi[0]);
        $isiUpdate = implode(' ', $pecahSpasi);
        // update $namaVar = data
        if(substr($pecahSpasi[1], 0, 1) == '$'){
            varJaisy($barisKe,$isiUpdate, 'updateVar');
        }else{
            error($barisKe, 'Tidak Sesuai Aturan Udate Variabel.');
        }
    }else{
        if($note == 'varOnly'){
            if($firstChar !== '$'){
                error($barisKe, 'Tidak Sesuai Aturan. (varOnly)');
            }
        }
    }

}