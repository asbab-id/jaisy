<?php


// Pendeteksi tipe data
function detectTypeList($input) {
    if (preg_match('/^\[.*\]$/', $input)) {
        if (preg_match('/\w+\s*:\s*[^,]+/', $input)) {
            return 'associative_list';
        } else {
            return 'numerical_list';
        }
    }
    return 'non_list';
}

// Mengubah string list numerik menjadi array
function listToArray($list) {
    $list = trim($list, '[]');
    $arrayList = [];
    $currentElement = '';
    $escaped = false;
    $insideList = 0;

    for ($i = 0; $i < strlen($list); $i++) {
        if ($escaped) {
            $currentElement .= $list[$i];
            $escaped = false;
        } else if ($list[$i] === '\\') {
            $escaped = true;
        } else if ($list[$i] === '[') {
            $insideList++;
            $currentElement .= $list[$i];
        } else if ($list[$i] === ']') {
            $insideList--;
            $currentElement .= $list[$i];
        } else if ($list[$i] === ',' && $insideList === 0) {
            $arrayList[] = trim($currentElement);
            $currentElement = '';
        } else {
            $currentElement .= $list[$i];
        }
    }
    if ($currentElement !== '') {
        $arrayList[] = trim($currentElement);
    }

    $arrayList = array_map(function($item) {
        return detectTypeList($item) === 'numerical_list' || detectTypeList($item) === 'associative_list' ? listToArray($item) : $item;
    }, $arrayList);

    array_unshift($arrayList, null);
    unset($arrayList[0]);
    return $arrayList;
}

// Mengubah array menjadi string list numerik
function arrayToList($array) {
    $array = array_map(function($item) {
        if (is_array($item)) {
            return arrayToList($item);
        }
        return str_replace(',', '\,', $item);
    }, $array);
    return '[' . implode(', ', $array) . ']';
}

// Mengubah string list asosiatif menjadi array
function associativeListToArray($list) {
    $list = trim($list, '[]');
    $arrayList = [];
    $currentElement = '';
    $escaped = false;
    $insideList = 0;
    $key = null;

    for ($i = 0; $i < strlen($list); $i++) {
        if ($escaped) {
            $currentElement .= $list[$i];
            $escaped = false;
        } else if ($list[$i] === '\\') {
            $escaped = true;
        } else if ($list[$i] === '[') {
            $insideList++;
            $currentElement .= $list[$i];
        } else if ($list[$i] === ']') {
            $insideList--;
            $currentElement .= $list[$i];
        } else if ($list[$i] === ':' && $insideList === 0) {
            $key = trim($currentElement);
            $currentElement = '';
        } else if ($list[$i] === ',' && $insideList === 0) {
            $arrayList[$key] = trim($currentElement);
            $currentElement = '';
            $key = null;
        } else {
            $currentElement .= $list[$i];
        }
    }
    if ($key !== null && $currentElement !== '') {
        $arrayList[$key] = trim($currentElement);
    }

    foreach ($arrayList as $key => $value) {
        $arrayList[$key] = detectTypeList($value) === 'numerical_list' || detectTypeList($value) === 'associative_list' ? associativeListToArray($value) : $value;
    }

    return $arrayList;
}

// Mengubah array menjadi string list asosiatif
function arrayToAssociativeList($array) {
    $items = [];
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $value = arrayToAssociativeList($value);
        } else {
            $value = str_replace(',', '\,', $value);
        }
        $key = str_replace(',', '\,', $key);
        $items[] = "$key: $value";
    }
    return '[' . implode(', ', $items) . ']';
}

// Membaca elemen dari list
function readList($list, $key) { // di sini terdapat sedikit anomali. ']'
    $type = detectTypeList($list);
    if ($type == 'numerical_list') {
        $arrayList = listToArray($list);
        if(isset($arrayList[$key])){
            if(is_array($arrayList[$key])){
                return isset($arrayList[$key]) ? arrayToList($arrayList[$key]) : null;
            }else{
                if(substr($arrayList[$key], 0, 1) == '['){
                    return isset($arrayList[$key]) ? $arrayList[$key].']' : null;
                }else{
                    return isset($arrayList[$key]) ? $arrayList[$key] : null;
                }
            }
        }else{
            return 'error numerical_list tidak terdapat $key yang tepat';
        }
    } elseif ($type == 'associative_list') {
        $arrayList = associativeListToArray($list);
        // var_dump($arrayList);
        if(isset($arrayList[$key])){
            if(substr($arrayList[$key], 0, 1) == '['){
                return isset($arrayList[$key]) ? $arrayList[$key].']' : null;
            }else{
                return isset($arrayList[$key]) ? $arrayList[$key] : null;
            }
        }else{
            return 'error associative_list tidak terdapat $key yang tepat';
        }
    }
    return null;
}

// Menulis elemen ke list
function writeList($list, $key, $value) {
    $type = detectTypeList($list);
    if ($type == 'numerical_list') {
        $arrayList = listToArray($list);
        $arrayList[$key] = $value;
        return arrayToList($arrayList);
    } elseif ($type == 'associative_list') {
        $arrayList = associativeListToArray($list);
        $arrayList[$key] = $value;
        return arrayToAssociativeList($arrayList);
    }
    return $list;
}

// Menambah elemen ke list numerik
function tambahList($list, $value) {
    $type = detectTypeList($list);
    if ($type == 'numerical_list') {
        $arrayList = listToArray($list);
        $arrayList[] = $value;
        return arrayToList($arrayList);
    } elseif ($type == 'associative_list') {
        return "Error: 'tambah' tidak dapat digunakan di dalam list asosiatif.";
    }
    return $list;
}

// Mengupdate elemen list
function updateList($list, $key, $value) {
    return writeList($list, $key, $value);
}

// Menghapus elemen dari list
function hapusList($list, $key) {
    $type = detectTypeList($list);
    if ($type == 'numerical_list') {
        $arrayList = listToArray($list);
        unset($arrayList[$key]);
        return arrayToList(array_values($arrayList)); // Reindex the array after removal
    } elseif ($type == 'associative_list') {
        $arrayList = associativeListToArray($list);
        unset($arrayList[$key]);
        return arrayToAssociativeList($arrayList);
    }
    return $list;
}


// $contohListMultidimensional = '[faza, [ilmi, akbar], asbab\, junior]';
// $contohListAsosiatifMultidimensional = '[nama: faza, alamat: [kota: jakarta, negara: indonesia\, raya]]';
// $contohString = 'ini string biasa';

// // Contoh penggunaan
// echo detectTypeList($contohListMultidimensional) . PHP_EOL; // output: numerical_list
// echo detectTypeList($contohListAsosiatifMultidimensional) . PHP_EOL; // output: associative_list
// echo detectTypeList($contohString) . PHP_EOL; // output: string

// echo readList($contohListMultidimensional, 2) . PHP_EOL; // output: [ilmi, akbar]
// echo readList($contohListAsosiatifMultidimensional, 'alamat') . PHP_EOL; // output: [kota: jakarta, negara: indonesia\, raya]

// // Contoh penggunaan setelah perbaikan
// echo tambahList($contohListMultidimensional, 'newElement') . PHP_EOL; // output: [faza, [ilmi, akbar], asbab\, junior, newElement]
// echo tambahList($contohListAsosiatifMultidimensional, 'newElement') . PHP_EOL; // output: Error: 'tambah' tidak dapat digunakan di dalam list asosiatif.

// echo updateList($contohListMultidimensional, 2, '[ilham, budi]') . PHP_EOL; // output: [faza, [ilham, budi], asbab\, junior]
// echo updateList($contohListAsosiatifMultidimensional, 'alamat', '[kota: surabaya, negara: indonesia]') . PHP_EOL; // output: [nama: faza, alamat: [kota: surabaya, negara: indonesia]]

// echo hapusList($contohListMultidimensional, 2) . PHP_EOL; // output: [faza, asbab\, junior]
// echo hapusList($contohListAsosiatifMultidimensional, 'alamat') . PHP_EOL; // output: [nama: faza]


// echo readList('[faza, [ilmi, akbar], asbab\, junior]', '2') . PHP_EOL;


// echo readList('[faza, raya]', '') .PHP_EOL;
// echo readList('[nama: faza, umur: 30, pacar: [flora, freya]]', 'nama') . PHP_EOL;
// echo readList('[nama: faza, umur: 30, pacar: [flora, freya]]', 'pacar') . PHP_EOL;
// echo readList('[faza, 30, [flora, freya]]', '3') . PHP_EOL;





// garap variabel.php + parse.php:parseVar()
//  ada 2 bentuk regex yang beda





// briefing syntax


/*
// untuk bahasa pemprograman saya. array disebut list.
$listNama = [faza, ilmi, asbab]
// array dimulai dari 1 bukan 0
print $listNama[1] // output: faza
print $listNama[2] // output: ilmi
print $listNama[3] // output: asbab 

tambah $listNama = akbar
print $listNama[4] // output: akbar
 
update $listNama[4] = milio
print $listNama[4] // output: milio


hapus $arrayNama[4]
print $arrayNama // output: [faza, ilmi, asbab]


// asosiatif list
$listBio = [nama : faza, umur : 22]
print $listBio[nama]

$listBio[alamat] = indonesia
print $listBio // [nama : faza, umur : 22, alamat : indonesia]

tambah $listBio = tes // error : tambah tidak bisa digunakan di asosiatif list

hapus $listBio[alamat]
print $listBio // [nama : faza, umur : 22]
*/