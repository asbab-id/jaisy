<pre>

<?php
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

$input3 = "'a' terdapat 'abc' dan 'b' tidak terdapat 'abc'";
print_r(cekStatemen($input3));

$input4 = "'aku' tidak merupakan 'kamu' atau 'dia' merupakan 'dia' dan 'kamux' merupakan 'kamu'";
print_r(cekStatemen($input4));
/////////////////////////////////////////////////////////////////////////////////////////////

function bandingTerdapat($x, $w, $note='ya'){
    // terdapat('abc', 'b'); // true
    if($note=='ya'){
        return str_contains($w, $x);
    }elseif($note=='tidak'){
        if(str_contains($w, $x) == false){
            return true;
        }else{
            return false;
        }
    }
}

function bandingMerupakan($w, $x, $note='ya'){
    // merupakan('abc', 'abc'); // true
    // merupakan('abc', ['fza', 'rya', 'abc']); // true
    if(gettype($x) == "string"){
        // return $w == $x;
        if($note=='ya'){
            return $w == $x;
        }elseif($note=='tidak'){
            if($w !== $x){
                return true;
            }else{
                return false;
            }
        }
    }
    // elseif(gettype($x) == "array"){
    //     return in_array($w, $x);
    // }
}

function cekStatemen($a) {
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
                // Pisahkan bagian yang dibandingkan dengan pembandingnya
                list($left, $right) = explode(' tidak merupakan ', $statement);
                $left = trim($left, " '");
                $right = trim($right, " '");
                $result[] = [
                    'operator' => 'tidak merupakan',
                    'banding' => $left,
                    'pembanding' => $right,
                    'bool' => bandingMerupakan($left, $right, 'tidak')
                ];
            } else {
                list($left, $right) = explode(' merupakan ', $statement);
                $left = trim($left, " '");
                $right = trim($right, " '");
                $result[] = [
                    'operator' => 'merupakan',
                    'banding' => $left,
                    'pembanding' => $right,
                    'bool' => bandingMerupakan($left, $right)
                ];
            }
        } elseif (strpos($statement, 'terdapat') !== false) {
            if (strpos($statement, 'tidak terdapat') !== false) {
                // Pisahkan bagian yang dibandingkan dengan pembandingnya
                list($left, $right) = explode(' tidak terdapat ', $statement);
                $left = trim($left, " '");
                $right = trim($right, " '");
                $result[] = [
                    'operator' => 'tidak terdapat',
                    'banding' => $left,
                    'pembanding' => $right,
                    'bool' => bandingTerdapat($left, $right, 'tidak')
                ];
            } else {
                list($left, $right) = explode(' terdapat ', $statement);
                $left = trim($left, " '");
                $right = trim($right, " '");
                $result[] = [
                    'operator' => 'terdapat',
                    'banding' => $left,
                    'pembanding' => $right,
                    'bool' => bandingTerdapat($left, $right)
                ];
            }
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

    $resultStatement = '';
    for($i=0; $i<count($finalResult); $i++){
        if($finalResult[$i]['operator_logika'] != null){ // statement

        }
    }


    return ['statement' => $finalResult];
}

// Contoh penggunaan
// $input1 = "'aku' merupakan 'aku'";
//print_r(cekStatemen($input1));

// $input2 = "'aku' merupakan 'aku' dan 'kamu' merupakan 'kamu'";
//print_r(cekStatemen($input2));

// $input3 = "'a' terdapat 'abc' dan 'b' tidak terdapat 'abc'";
// print_r(cekStatemen($input3));

// $input4 = "'aku' tidak merupakan 'kamu' atau 'dia' merupakan 'dia' dan 'kamux' merupakan 'kamu'";
// print_r(cekStatemen($input4));


?>
