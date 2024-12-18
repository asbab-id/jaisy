<?php

/// /////////////////////////////////////////////////////////////////////////////////////////////////////
//  manupulasi str //////////
// //////////////////////////////////////////////////////////////////////////////////////////////////////
function gunduler($lafd){
    // gunduler("بَابُ") // "باب"
	$data = str_replace([
        "ُ", // dumm
        "َ", // fath
        "ِ", // kasr
        "ٌ", // dummatain
        "ً", // fathatain
        "ٍ", // kasratain
        "ْ", // sukun
        "ّ", // tasydid
    ], "", $lafd);
	return $data;
}

function clean_spasi($str){
    //cleanSPasi('   oi   '); // 'oi'
    return trim($str);
}

function hitung($str){
    //hitung('oi'); // 2
    return strlen($str);
}

function hapus($str, $hps){
    // hapus('wdatabasew', 'w'); // 'database'
    if(terdapat($str, $hps)){
        return str_replace($hps, "", $str);
    }else{
        return $str;
    }
}


function ganti($str, $hps, $jadi){
    // ganti('wdatabasew', 'w', 'x'); // 'database'
    if(terdapat($str, $hps)){
        return str_replace($hps, $jadi, $str);
    }else{
        return $str;
    }
}

function cari($str, $cari){
    $pos = mb_strpos($str, $cari);
    if($pos !== false){
        return $pos + 1;
    } else {
        return '0';
    }
}


function tambah($str, $ke, $tambah) {
    // Pastikan posisinya valid
    if ($ke < 1 || $ke > mb_strlen($str) + 1) {
        return 'Posisi tidak valid';
    }

    // Pindahkan posisi ke basis 0 untuk mb_substr
    // $ke--;

    // Pisahkan string menjadi dua bagian dan tambahkan karakter di antaranya
    $awal = mb_substr($str, 0, $ke);
    $akhir = mb_substr($str, $ke);

    // Gabungkan bagian-bagian dengan karakter tambahan
    return $awal . $tambah . $akhir;
}


function ambil($str, $posisi, $jumlah) {
    $panjangStr = mb_strlen($str);

    if ($posisi === 'awal') {
        if ($jumlah === 'semua') {
            return $str;
        } else {
            return mb_substr($str, 0, (int)$jumlah);
        }
    } elseif ($posisi === 'akhir') {
        if ($jumlah === 'semua') {
            return $str;
        } else {
            return mb_substr($str, $panjangStr - (int)$jumlah, (int)$jumlah);
        }
    } else {
        $posisi = (int)$posisi - 1; // Ubah posisi ke indeks berbasis 0

        if ($jumlah === 'semua') {
            return mb_substr($str, $posisi);
        } elseif ($jumlah < 0) {
            $jumlah = abs($jumlah);
            $mulai = $posisi - $jumlah + 1; // Hitung posisi awal yang baru untuk jumlah negatif
            return mb_substr($str, $mulai, $jumlah);
        } else {
            return mb_substr($str, $posisi, (int)$jumlah);
        }
    }
}



function mtk($operasi) {
    // Hilangkan spasi yang tidak perlu dari operasi
    $operasi = str_replace(' ', '', $operasi);

    // Pisahkan string operasi menjadi angka dan operator
    $pattern = '/(-?\d+(\.\d+)?)([\+\-\*\/])(-?\d+(\.\d+)?)/';
    preg_match_all($pattern, $operasi, $matches, PREG_SET_ORDER);

    // Inisialisasi hasil dengan angka pertama dari operasi
    $hasil = (float)$matches[0][1];

    // Loop melalui setiap operasi yang cocok dan lakukan perhitungan
    foreach ($matches as $match) {
        $angka2 = (float)$match[4];
        $operator = $match[3];

        switch ($operator) {
            case '+':
                $hasil += $angka2;
                break;
            case '-':
                $hasil -= $angka2;
                break;
            case '*':
                $hasil *= $angka2;
                break;
            case '/':
                // Handle division by zero
                if ($angka2 == 0) {
                    return "Pembagian dengan nol tidak diperbolehkan.";
                }
                $hasil /= $angka2;
                break;
            default:
                return "Operasi matematika tidak valid.";
        }
    }

    return $hasil;
}

function antara($str, $awal, $akhir) {
    $posAwal = mb_strpos($str, $awal);
    if ($posAwal === false) {
        return ""; // Jika marker awal tidak ditemukan
    }

    $posAkhir = mb_strpos($str, $akhir, $posAwal + mb_strlen($awal));
    if ($posAkhir === false) {
        return ""; // Jika marker akhir tidak ditemukan setelah marker awal
    }

    // Ambil substring di antara marker awal dan akhir
    $panjangAwal = $posAwal + mb_strlen($awal);
    return mb_substr($str, $panjangAwal, $posAkhir - $panjangAwal);
}

/// /////////////////////////////////////////////////////////////////////////////////////////////////////
//  pecah (array/string) //////////
// //////////////////////////////////////////////////////////////////////////////////////////////////////
// contoh callback ----> $tes_callback = pecah('نِلْتُ، أَنُولُ، نُلْ،', '،', 0, function($str){ return 'fiil madhi adalah'.$str;});

function pecahHuruf($w, $ke=null, $callback=null){
    // pecahHuruf('abc'); // ['a', 'b', 'c']
    // pecahHuruf('abc', 'terakhir-1'); // 'b'
    $result = mb_str_split($w);
    
    if($ke !== null){
        $count = count($result)-1;
        
        if(terdapat($ke, 'terakhir') == true){
            $ke = str_replace('terakhir', $count, $ke);
        }

        $command = operasiMtk($ke);
        $result = $result[$command];
    }

    if (is_callable($callback)) {
        return $callback($result);
    } else {
        return $result;
    }
    
}

function pecah($w, $s, $ke=null, $callback=null){
    // pecah('a-b-c', '-'); // ['a', 'b', 'c']
    // pecah('a-b-c', '-', 'terakhir-1'); // 'b'

    if(!terdapat($w, $s)){
        return $w;
    }

    $result = explode($s, $w);
    
    if($ke !== null){
        $count = count($result)-1;
        
        if(terdapat($ke, 'terakhir') == true){
            $ke = str_replace('terakhir', $count, $ke);
        }

        $command = operasiMtk($ke);
        $result = $result[$command];
    }

    if (is_callable($callback)) {
        return $callback($result);
    } else {
        return $result;
    }
}

function antara_ori($str, $pembuka, $penutup, $ke=null, $callback=null) {
    // antara('aku memakan (roti) dan (susu)', '(', ')');        //['roti', 'susu']
    // antara('aku memakan (roti) dan (susu)', '(', ')', 'terakhir-1'); // 'roti'
    if(!terdapat($str,$pembuka) && !terdapat($str, $penutup)){
        return false;
    }

    $result = array();
    $pattern = '/' . preg_quote($pembuka, '/') . '(.*?)' . preg_quote($penutup, '/') . '/';
    if (preg_match_all($pattern, $str, $matches)) {
        foreach ($matches[1] as $match) {
            $result[] = $match;
        }
    }
    
    if($ke !== null){
        $count = count($result)-1;
        
        if(terdapat($ke, 'terakhir') == true){
            $ke = str_replace('terakhir', $count, $ke);
        }

        $command = operasiMtk($ke);
        $result = $result[$command];
    }

    if (is_callable($callback)) {
        return $callback($result);
    } else {
        return $result;
    }
}





/// /////////////////////////////////////////////////////////////////////////////////////////////////////
//  ambil (string) //////////
// //////////////////////////////////////////////////////////////////////////////////////////////////////
function akhir($w, $x=1){
    // akhir('abc', 1); // 'c'
    return mb_substr($w, '-'.$x);
}

function tengah($w, $ke, $brp){
    // tengah('abcdefg', 2, 3); // 'cde'
    $ke = $ke - 1;
    return mb_substr($w, $ke, $brp);
}

function awal($w, $x=1){
    // awal('abc', 1); // 'a'
    return mb_substr($w,0, $x);
}












/// /////////////////////////////////////////////////////////////////////////////////////////////////////
//  logic lacak (bool) //////////
// //////////////////////////////////////////////////////////////////////////////////////////////////////
function terdapat($x, $w){
    // terdapat('b', 'abc'); // true
    // return str_contains($w, $x);
    if(str_contains($w, $x)){
        return 'iya';
    }else{
        return 'tidak';
    }
}

function merupakan($w, $x){
    // merupakan('abc', 'abc'); // true
    // merupakan('abc', ['fza', 'rya', 'abc']); // true
    if(gettype($x) == "string"){
        // return $w == $x;
        if($w == $x){
            return 'iya';
        }else{
            return 'tidak';
        }
    }elseif(gettype($x) == "array"){
        return in_array($w, $x);
    }
}



/// /////////////////////////////////////////////////////////////////////////////////////////////////////
//  logic operasi (???) //////////
// //////////////////////////////////////////////////////////////////////////////////////////////////////
function operasiMtk($str) {
    // Pecah ekspresi menjadi token
    $tokens = preg_split('/([+\-*\/])/', $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    // Inisialisasi hasil dengan operan pertama
    $hasil = intval($tokens[0]);

    // Iterasi melalui token
    for ($i = 1; $i < count($tokens); $i += 2) {
        // Ambil operator dan operand berikutnya
        $operator = $tokens[$i];
        $operand = intval($tokens[$i + 1]);

        // Lakukan operasi matematika sesuai dengan operator
        switch ($operator) {
            case '+':
                $hasil += $operand;
                break;
            case '-':
                $hasil -= $operand;
                break;
            case '*':
                $hasil *= $operand;
                break;
            case '/':
                if ($operand != 0) {
                    $hasil /= $operand;
                } else {
                    echo "Error: Pembagian dengan nol.";
                    return;
                }
                break;
            default:
                echo "Error: Operator tidak valid.";
                return;
        }
    }

    // Mencetak hasil evaluasi
    return $hasil;
}




// str_contains ga bisa di php7 wkwk
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}