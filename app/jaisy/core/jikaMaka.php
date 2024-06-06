<?php


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
            $firstWordBarisSebelum = explode(' ', trim($GLOBALS['listBaris'][$barisKe-1]))[0];
            if($firstWordBarisSebelum !== 'maka'){
                error($barisKe, 'instruksi maka belum ada.');
            }
        }
    }
}