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

function cleanSpasi($str){
    //cleanSPasi('   oi   '); // 'oi'
    return trim($str);
}

function hapus($str, $hps){
    // hapus('wdatabasew', 'w'); // 'database'
    if(terdapat($str, $hps)){
        return str_replace($hps, "", $str);
    }else{
        return $str;
    }
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

function antara($str, $pembuka, $penutup, $ke=null, $callback=null) {
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
//  logic lacak (bool) //////////
// //////////////////////////////////////////////////////////////////////////////////////////////////////
function terdapat($w, $x){
    // terdapat('abc', 'b'); // true
    return str_contains($w, $x);
}

function merupakan($w, $x){
    // merupakan('abc', 'abc'); // true
    // merupakan('abc', ['fza', 'rya', 'abc']); // true
    if(gettype($x) == "string"){
        return $w == $x;
    }elseif(gettype($x) == "array"){
        return in_array($w, $x);
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