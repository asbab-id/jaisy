<?php

function commentJaisy($code){
    $pecah = explode('//', $code);
    return $pecah[0];
}