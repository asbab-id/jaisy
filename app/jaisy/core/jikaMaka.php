<?php


// Function untuk cek Perbandingan
function cekStatemen($barisKe, $a, $note='') {
    // echo $a;
    try {
        $result = [];
        // Pecah pernyataan menggunakan ' dan ' dan ' atau ' sebagai pemisah
        $statements = preg_split('/ dan | atau /', $a, -1, PREG_SPLIT_DELIM_CAPTURE);

        // Membuat array untuk menyimpan operator logika yang digunakan
        $logicalOperators = [];
        preg_match_all('/ dan | atau /', $a, $logicalOperators);

        foreach ($statements as $statement) {
            $statement = trim($statement);

            if (strpos($statement, 'merupakan') !== false) {
                if (strpos($statement, 'tidak merupakan') !== false) {
                    $pecah = explode(' tidak merupakan ', $statement);
                    if(count($pecah)>1){
                        list($left, $right) = $pecah;
                        $left = parseX($barisKe, trim($left));
                        $right = parseX($barisKe, trim($right));
                        $result[] = [
                            'operator' => 'tidak merupakan',
                            'banding' => $left,
                            'pembanding' => $right,
                            'bool' => ($left !== $right)
                        ];
                    }else{throw new Exception('Tidak ditemukan Banding/Pembanding.');}
                } else {
                    $pecah = explode(' merupakan ', $statement);
                    if(count($pecah)>1){
                        list($left, $right) = $pecah;
                        $left = parseX($barisKe, trim($left));
                        $right = parseX($barisKe, trim($right));
                        $result[] = [
                            'operator' => 'merupakan',
                            'banding' => $left,
                            'pembanding' => $right,
                            'bool' => ($left === $right)
                        ];
                    }else{throw new Exception('Tidak ditemukan Banding/Pembanding.');}
                }
            } elseif (strpos($statement, 'terdapat') !== false) {
                if (strpos($statement, 'tidak terdapat') !== false) {
                    $pecah = explode(' tidak terdapat ', $statement);
                    if(count($pecah)>1){
                        list($left, $right) = $pecah;
                        $left = parseX($barisKe, trim($left));
                        $right = parseX($barisKe, trim($right));
                        $result[] = [
                            'operator' => 'tidak terdapat',
                            'banding' => $left,
                            'pembanding' => $right,
                            'bool' => (strpos($right, $left) === false)
                        ];
                    }else{throw new Exception('Tidak ditemukan Banding/Pembanding.');}
                } else {
                    $pecah = explode(' terdapat ', $statement);
                    if(count($pecah)>1){
                        list($left, $right) = explode(' terdapat ', $statement);
                        $left = parseX($barisKe, trim($left));
                        $right = parseX($barisKe, trim($right));
                        $result[] = [
                            'operator' => 'terdapat',
                            'banding' => $left,
                            'pembanding' => $right,
                            'bool' => (strpos($right, $left) !== false)
                        ];
                    }else{throw new Exception('Tidak ditemukan Banding/Pembanding.');}
                }
            } else {
                // Kembalikan error jika tidak ada 'merupakan' atau 'terdapat'
                throw new Exception('Tidak ditemukan Operator.');
            }
        }

        // Gabungkan hasil dengan operator logika jika ada
        $finalResult = [];
        foreach ($result as $key => $res) {
            $finalResult[] = $res;
            if (isset($logicalOperators[0][$key])) {
                $finalResult[] = ['operator_logika' => trim($logicalOperators[0][$key])];
            }
        }

        // Evaluasi hasil berdasarkan operator logika
        $hasil = null;
        $currentBool = null;
        foreach ($finalResult as $item) {
            if (isset($item['bool'])) {
                if ($currentBool === null) {
                    $currentBool = $item['bool'];
                } else {
                    if ($hasil === 'dan') {
                        $currentBool = $currentBool && $item['bool'];
                    } elseif ($hasil === 'atau') {
                        $currentBool = $currentBool || $item['bool'];
                    }
                }
            } elseif (isset($item['operator_logika'])) {
                $hasil = $item['operator_logika'];
            }
        }

        return ['raw' => $a, 'statement' => $finalResult, 'hasil' => $currentBool];

    } catch (Exception $e) {
        // return ['error' => $e->getMessage()];
        error($barisKe, 'Tidak Sesuai Aturan Jika Maka. ' . $e->getMessage());
    }
}








function jikaMakaJaisy($barisKe, $code, $note=''){
    $pecahSpasi = explode(' ', $code);
    $firstWord = $pecahSpasi[0];
    if($note == ''){
        $noteVarJaisy = 'varOnly';
    }else{
        $noteVarJaisy = 'varOnlyFungsi';
    }
    // echo $barisKe . ' ' . $firstWord . PHP_EOL;
    if($firstWord == 'jika'){
        // echo 'jika ';
        $GLOBALS['listenJika'] = []; // reset listener
        $idJikaMaka = count($GLOBALS['tmpJikaMaka']);
        unset($pecahSpasi[0]);
        $isiJika = implode(' ', $pecahSpasi);
        // echo $isiJika;
        $parseIsiJika = cekStatemen($barisKe, $isiJika);
        // print_r($parseIsiJika);
        if(isset($parseIsiJika['hasil'])){
            // echo 'merupakan';
            // echo $idJikaMaka;
            // $pecahPerbandingan = explode('merupakan', $isiJika);
            // $perbandinganA = $pecahPerbandingan[0];
            // $perbandinganB = $pecahPerbandingan[1];

            $GLOBALS['tmpJikaMaka'][$idJikaMaka]['jika'] = ['isi' => $isiJika,
                                                            'isTrue' => $parseIsiJika['hasil'],
                                                            'isCompleted' => false
                                                            ];
            $GLOBALS['listenJika'] = ['idJikaMaka' => $idJikaMaka, 'point' => 'jika'];
        }
    }elseif($firstWord == 'atau_jika'){
        if(isset($GLOBALS['listenJika']['idJikaMaka'])){
            $id = $GLOBALS['listenJika']['idJikaMaka'];
        }else{
            error($barisKe, 'Terdapat Instruksi Jika Maka  yang Tidak Valid.');
            return;
        }
        
        unset($pecahSpasi[0]);
        $isiAtauJika = implode(' ', $pecahSpasi);
        $parseIsiAtauJika = cekStatemen($barisKe, $isiAtauJika);
        $point = $GLOBALS['listenJika']['point'];
        // echo $isiJika;
        if(isset($parseIsiAtauJika['hasil'])){
            // echo 'merupakan';
            // echo $idJikaMaka;
            // $pecahPerbandingan = explode('merupakan', $isiAtauJika);
            // $perbandinganA = $pecahPerbandingan[0];
            // $perbandinganB = $pecahPerbandingan[1];

            $GLOBALS['tmpJikaMaka'][$id]['atau_jika'][] = ['isi' => $isiAtauJika,
                                                           'isTrue' => $parseIsiAtauJika['hasil']
                                                            ];
            $GLOBALS['listenJika'] = ['idJikaMaka' => $id, 'point' => 'atau_jika', 'idAtauJika' => count($GLOBALS['tmpJikaMaka'][$id]['atau_jika'])-1];
            // echo $GLOBALS['listenJika']['idAtauJika'];
        }
    }elseif($firstWord == 'maka'){
        // $idTmpJikaMaka = count($GLOBALS['tmpJikaMaka'])-1;
        if(isset($GLOBALS['listenJika']['idJikaMaka'])){
            $id = $GLOBALS['listenJika']['idJikaMaka'];
        }else{
            error($barisKe, 'Terdapat Instruksi Jika Maka  yang Tidak Valid.');
            return;
        }

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
                    varJaisy($barisKe, $isiMaka, $noteVarJaisy);
                    $cekNextBaris = explode(' ', trim($GLOBALS['listBaris'][$barisKe+1]));
                    if($cekNextBaris[0] !== 'kemudian'){
                        $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'] = true;
                    }
                }
            }else{
                // echo 'jika_tidak';
                if($GLOBALS['tmpJikaMaka'][$id][$point][$idAtauJika]['isTrue'] == true){
                    varJaisy($barisKe, $isiMaka, $noteVarJaisy);
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
        if(isset($GLOBALS['listenJika']['idJikaMaka'])){
            $id = $GLOBALS['listenJika']['idJikaMaka'];
        }else{
            error($barisKe, 'Terdapat Instruksi Jika Maka  yang Tidak Valid.');
            return;
        }

        $isCompleted = $GLOBALS['tmpJikaMaka'][$id]['jika']['isCompleted'];
        // $point = $GLOBALS['listenJika']['point'];
        unset($pecahSpasi[0]);
        $isiJikaTidak = implode(' ', $pecahSpasi);
        $GLOBALS['tmpJikaMaka'][$id]['jika']['jika_tidak']['isi'] = $isiJikaTidak;
        $GLOBALS['listenJika']['point'] = 'jika_tidak';
        $GLOBALS['tmpJikaMaka'][$id]['jika']['jika_tidak']['isTrue'] = false;
        if($isCompleted == false){
            varJaisy($barisKe, $isiJikaTidak, $noteVarJaisy);
            $GLOBALS['tmpJikaMaka'][$id]['jika']['jika_tidak']['isTrue'] = true;
        }
        // echo json_encode($GLOBALS['tmpJikaMaka']);
    }elseif($firstWord == 'kemudian'){
        // $idTmpJikaMaka = count($GLOBALS['tmpJikaMaka'])-1;
        if(isset($GLOBALS['listenJika']['idJikaMaka'])){
            $id = $GLOBALS['listenJika']['idJikaMaka'];
        }else{
            error($barisKe, 'Terdapat Instruksi Jika Maka  yang Tidak Valid.');
            return;
        }
        
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
                varJaisy($barisKe, $isiKemudian, $noteVarJaisy);
                $GLOBALS['tmpJikaMaka'][$id]['jika'][$point]['kemudian'][]['isi'] = $isiKemudian;
            }
        }elseif($isCompleted == false && isset($GLOBALS['tmpJikaMaka'][$id]['jika']['maka'])){
            if(!is_int($idAtauJika)){ // kemudian -> maka -> jika
                // echo 'jika';
                // echo json_encode($GLOBALS['tmpJikaMaka']);
                // echo $id . $point . $atauJika . $isiMaka;
                if($GLOBALS['tmpJikaMaka'][$id][$point]['isTrue'] == true){
                    // echo $point.'::::'. $isiKemudian;
                    varJaisy($barisKe, $isiKemudian, $noteVarJaisy);
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
                    varJaisy($barisKe, $isiKemudian, $noteVarJaisy);
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