<?php

// PETIK
function isolasiPetik($barisKe, $data){
    $output =  preg_replace_callback(
        '/(["\'])(.*?)\1/',
        function ($matches) use ($barisKe) {
            $innerText = str_replace(' ', '!^`_^!`', $matches[2]);
            $innerText = isolasiDolar($barisKe, $innerText);
            // return $matches[1] . $innerText . $matches[1];
            return $innerText;
        },
        $data
    );
    // echo $output;
    return $output;
}

function parseIsolasiPetik($barisKe, $data){
    $output = str_replace('!^`_^!`', ' ', $data);
    $output = parseIsolasiDolar($barisKe, $output);
    return $output;
}





// SPASI
function isolasiSpasi($barisKe, $data){
    return str_replace(' ', '!^`_^!!!`', $data);
}

function parseIsolasiSpasi($barisKe, $data){
    return str_replace('!^`_^!!!`', ' ', $data);
}

function parseCharSpasi($barisKe, $data){
    $data = str_replace(' @ ', ' ', $data);
    $data = str_replace(' # ', ' ', $data);
    $data = str_replace(' $ ', ' ', $data);
    return $data;
}



// DOLAR
function isolasiDolar($barisKe, $data){
    return str_replace('$', '`-_-`_!', $data);
}

function parseIsolasiDolar($barisKe, $data){
    return str_replace('`-_-`_!', '$',  $data);
}