<?php

// PETIK
function isolasiPetik($barisKe, $data){
    return preg_replace_callback(
        '/(["\'])(.*?)\1/',
        function ($matches) {
            $innerText = str_replace(' ', '!^`_^!`', $matches[2]);
            // return $matches[1] . $innerText . $matches[1];
            return $innerText;
        },
        $data
    );
}

function parseIsolasiPetik($barisKe, $data){
    return str_replace('!^`_^!`', ' ', $data);
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