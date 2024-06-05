<?php

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