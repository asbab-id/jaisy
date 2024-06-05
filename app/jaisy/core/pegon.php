<?php


function parsePegon($barisKe, $data){
    $raw = trim($data);
    $DATA = $raw;

    $DATA = mb_ereg_replace('_',  " ", $DATA);

    $DATA = mb_ereg_replace('AL',  "ال", $DATA);
    $DATA = mb_ereg_replace('TS',  "ث", $DATA);
    $DATA = mb_ereg_replace('KH',  "خ", $DATA);
    $DATA = mb_ereg_replace('DZ',  "ذ", $DATA);
    $DATA = mb_ereg_replace('SY',  "ش", $DATA);
    $DATA = mb_ereg_replace('SH',  "ص", $DATA);
    $DATA = mb_ereg_replace('DH',  "ض", $DATA);
    $DATA = mb_ereg_replace('TH',  "ط", $DATA);
    $DATA = mb_ereg_replace('ZH',  "ظ", $DATA);
    $DATA = mb_ereg_replace('GH',  "غ", $DATA);
    $DATA = mb_ereg_replace('H',  "ح", $DATA);
    $DATA = mb_ereg_replace('E',  "ع", $DATA);
    $DATA = mb_ereg_replace('A',  "ا", $DATA);

    $DATA = mb_ereg_replace('M',  "أ", $DATA);
    $DATA = mb_ereg_replace('W',  "إ", $DATA);
    $DATA = mb_ereg_replace('O',  "ء", $DATA);
    $DATA = mb_ereg_replace('U',  "ؤ", $DATA);
    $DATA = mb_ereg_replace('I',  "ئ", $DATA);
    $DATA = mb_ereg_replace('T',  "ة", $DATA);
    $DATA = mb_ereg_replace('Y',  "ى", $DATA);

    $data = mb_strtolower($DATA);

    $data = mb_ereg_replace('^ia',  "إييا", $data);
    $data = mb_ereg_replace('^ua',  "أووا", $data);

    $data = mb_ereg_replace(' ia',  " إييا", $data);
    $data = mb_ereg_replace('ia',  "ييا", $data);
    $data = mb_ereg_replace('ua',  "ووا", $data);

    $data = mb_ereg_replace('nga',  "ڠا", $data);
    $data = mb_ereg_replace('ngi',  "ڠي", $data);
    $data = mb_ereg_replace('ngu',  "ڠو", $data);
    $data = mb_ereg_replace('nge~', "ڠٓ", $data);
    $data = mb_ereg_replace('nge',  "ڠي", $data);
    $data = mb_ereg_replace('ngo',  "ڠو", $data);

    $data = mb_ereg_replace('nya',  "پا", $data);
    $data = mb_ereg_replace('nyi',  "پي", $data);
    $data = mb_ereg_replace('nyu',  "پو", $data);
    $data = mb_ereg_replace('nye~', "پٓ", $data);
    $data = mb_ereg_replace('nye',  "پي", $data);
    $data = mb_ereg_replace('nyo',  "پو", $data);

    $data = mb_ereg_replace('ng',  "ڠ", $data);
    $data = mb_ereg_replace('ny',  "پ", $data);

    $data = mb_ereg_replace('ba',  "با", $data);
    $data = mb_ereg_replace('bi',  "بي", $data);
    $data = mb_ereg_replace('bu',  "بو", $data);
    $data = mb_ereg_replace('be~', "بٓ", $data);
    $data = mb_ereg_replace('be',  "بي", $data);
    $data = mb_ereg_replace('bo',  "بو", $data);

    $data = mb_ereg_replace('ca',  "چا", $data);
    $data = mb_ereg_replace('ci',  "چي", $data);
    $data = mb_ereg_replace('cu',  "چو", $data);
    $data = mb_ereg_replace('ce~', "جٓ", $data);
    $data = mb_ereg_replace('ce',  "چي", $data);
    $data = mb_ereg_replace('co',  "چو", $data);

    $data = mb_ereg_replace('da',  "دا", $data);
    $data = mb_ereg_replace('di',  "دي", $data);
    $data = mb_ereg_replace('du',  "دو", $data);
    $data = mb_ereg_replace('de~', "دٓ", $data);
    $data = mb_ereg_replace('de',  "دي", $data);
    $data = mb_ereg_replace('do',  "دو", $data);

    $data = mb_ereg_replace('fa',  "فا", $data);
    $data = mb_ereg_replace('fi',  "في", $data);
    $data = mb_ereg_replace('fu',  "فو", $data);
    $data = mb_ereg_replace('fe~', "فٓ", $data);
    $data = mb_ereg_replace('fe',  "في", $data);
    $data = mb_ereg_replace('fo',  "فو", $data);

    $data = mb_ereg_replace('ga',  "ڮا", $data);
    $data = mb_ereg_replace('gi',  "ڮي", $data);
    $data = mb_ereg_replace('gu',  "ڮو", $data);
    $data = mb_ereg_replace('ge~', "ڮٓ", $data);
    $data = mb_ereg_replace('ge',  "ڮي", $data);
    $data = mb_ereg_replace('go',  "ڮو", $data);

    $data = mb_ereg_replace('ha',  "ها", $data);
    $data = mb_ereg_replace('hi',  "هي", $data);
    $data = mb_ereg_replace('hu',  "هو", $data);
    $data = mb_ereg_replace('he~', "ه‍ٓ", $data);
    $data = mb_ereg_replace('he',  "هي", $data);
    $data = mb_ereg_replace('ho',  "هو", $data);

    $data = mb_ereg_replace('ja',  "جا", $data);
    $data = mb_ereg_replace('ji',  "جي", $data);
    $data = mb_ereg_replace('ju',  "جو", $data);
    $data = mb_ereg_replace('je~', "جٓ", $data);
    $data = mb_ereg_replace('je',  "جي", $data);
    $data = mb_ereg_replace('jo',  "جو", $data);

    $data = mb_ereg_replace('ka',  "كا", $data);
    $data = mb_ereg_replace('ki',  "كي", $data);
    $data = mb_ereg_replace('ku',  "كو", $data);
    $data = mb_ereg_replace('ke~', "كٓ", $data);
    $data = mb_ereg_replace('ke',  "كي", $data);
    $data = mb_ereg_replace('ko',  "كو", $data);

    $data = mb_ereg_replace('la',  "لا", $data);
    $data = mb_ereg_replace('li',  "لي", $data);
    $data = mb_ereg_replace('lu',  "لو", $data);
    $data = mb_ereg_replace('le~', "لٓ", $data);
    $data = mb_ereg_replace('le',  "لي", $data);
    $data = mb_ereg_replace('lo',  "لو", $data);

    $data = mb_ereg_replace('ma',  "ما", $data);
    $data = mb_ereg_replace('mi',  "مي", $data);
    $data = mb_ereg_replace('mu',  "مو", $data);
    $data = mb_ereg_replace('me~', "مٓ", $data);
    $data = mb_ereg_replace('me',  "مي", $data);
    $data = mb_ereg_replace('mo',  "مو", $data);

    $data = mb_ereg_replace('na',  "نا", $data);
    $data = mb_ereg_replace('ni',  "ني", $data);
    $data = mb_ereg_replace('nu',  "نو", $data);
    $data = mb_ereg_replace('ne~', "نٓ", $data);
    $data = mb_ereg_replace('ne',  "ني", $data);
    $data = mb_ereg_replace('no',  "نو", $data);

    $data = mb_ereg_replace('pa',  "ڤا", $data);
    $data = mb_ereg_replace('pi',  "ڤي", $data);
    $data = mb_ereg_replace('pu',  "ڤو", $data);
    $data = mb_ereg_replace('pe~', "ڤٓ", $data);
    $data = mb_ereg_replace('pe',  "ڤي", $data);
    $data = mb_ereg_replace('po',  "ڤو", $data);

    $data = mb_ereg_replace('qa',  "قا", $data);
    $data = mb_ereg_replace('qi',  "قي", $data);
    $data = mb_ereg_replace('qu',  "قو", $data);
    $data = mb_ereg_replace('qe~', "قٓ", $data);
    $data = mb_ereg_replace('qe',  "قي", $data);
    $data = mb_ereg_replace('qo',  "قو", $data);

    $data = mb_ereg_replace('ra',  "را", $data);
    $data = mb_ereg_replace('ri',  "ري", $data);
    $data = mb_ereg_replace('ru',  "رو", $data);
    $data = mb_ereg_replace('re~', "رٓ", $data);
    $data = mb_ereg_replace('re',  "ري", $data);
    $data = mb_ereg_replace('ro',  "رو", $data);

    $data = mb_ereg_replace('sa',  "سا", $data);
    $data = mb_ereg_replace('si',  "سي", $data);
    $data = mb_ereg_replace('su',  "سو", $data);
    $data = mb_ereg_replace('se~', "سٓ", $data);
    $data = mb_ereg_replace('se',  "سي", $data);
    $data = mb_ereg_replace('so',  "سو", $data);

    $data = mb_ereg_replace('ta',  "تا", $data);
    $data = mb_ereg_replace('ti',  "تي", $data);
    $data = mb_ereg_replace('tu',  "تو", $data);
    $data = mb_ereg_replace('te~', "تٓ", $data);
    $data = mb_ereg_replace('te',  "تي", $data);
    $data = mb_ereg_replace('to',  "تو", $data);

    $data = mb_ereg_replace('va',  "فا", $data);
    $data = mb_ereg_replace('vi',  "في", $data);
    $data = mb_ereg_replace('vu',  "فو", $data);
    $data = mb_ereg_replace('ve~', "فٓ", $data);
    $data = mb_ereg_replace('ve',  "في", $data);
    $data = mb_ereg_replace('vo',  "فو", $data);

    $data = mb_ereg_replace('wa',  "وا", $data);
    $data = mb_ereg_replace('wi',  "وي", $data);
    $data = mb_ereg_replace('wu',  "وو", $data);
    $data = mb_ereg_replace('we~', "وٓ", $data);
    $data = mb_ereg_replace('we',  "وي", $data);
    $data = mb_ereg_replace('wo',  "وو", $data);

    $data = mb_ereg_replace('ya',  "يا", $data);
    $data = mb_ereg_replace('yi',  "يي", $data);
    $data = mb_ereg_replace('yu',  "يو", $data);
    $data = mb_ereg_replace('ye~', "يٓ", $data);
    $data = mb_ereg_replace('ye',  "يي", $data);
    $data = mb_ereg_replace('yo',  "يو", $data);

    $data = mb_ereg_replace('za',  "زا", $data);
    $data = mb_ereg_replace('zi',  "زي", $data);
    $data = mb_ereg_replace('zu',  "زو", $data);
    $data = mb_ereg_replace('ze~', "زٓ", $data);
    $data = mb_ereg_replace('ze',  "زي", $data);
    $data = mb_ereg_replace('zo',  "زو", $data);

    $data = mb_ereg_replace('xa',  "كا", $data);
    $data = mb_ereg_replace('xi',  "كي", $data);
    $data = mb_ereg_replace('xu',  "كو", $data);
    $data = mb_ereg_replace('xe~', "كٓ", $data);
    $data = mb_ereg_replace('xe',  "كي", $data);
    $data = mb_ereg_replace('xo',  "كو", $data);

    $data = mb_ereg_replace('b',  "ب", $data);
    $data = mb_ereg_replace('c',  "چ", $data);
    $data = mb_ereg_replace('d',  "د", $data);
    $data = mb_ereg_replace('f',  "ف", $data);
    $data = mb_ereg_replace('g',  "ڮ", $data);
    $data = mb_ereg_replace('h',  "ه", $data);
    $data = mb_ereg_replace('j',  "ج", $data);
    $data = mb_ereg_replace('k',  "ك", $data);
    $data = mb_ereg_replace('l',  "ل", $data);
    $data = mb_ereg_replace('m',  "م", $data);
    $data = mb_ereg_replace('n',  "ن", $data);
    $data = mb_ereg_replace('p',  "ڤ", $data);
    $data = mb_ereg_replace('q',  "ق", $data);
    $data = mb_ereg_replace('r',  "ر", $data);
    $data = mb_ereg_replace('s',  "س", $data);
    $data = mb_ereg_replace('t',  "ت", $data);
    $data = mb_ereg_replace('v',  "ف", $data);
    $data = mb_ereg_replace('w',  "و", $data);
    $data = mb_ereg_replace('x',  "ك", $data);
    $data = mb_ereg_replace('y',  "ي", $data);
    $data = mb_ereg_replace('z',  "ز", $data);
    
    $data = mb_ereg_replace('a',  "أ", $data);
    $data = mb_ereg_replace('i',  "إي", $data);
    $data = mb_ereg_replace('u',  "أو", $data);
    $data = mb_ereg_replace('e~', "أٓ", $data);
    $data = mb_ereg_replace('e',  "أي", $data);
    $data = mb_ereg_replace('o',  "أو", $data);

    $data = mb_ereg_replace('1',  "١", $data);
    $data = mb_ereg_replace('2',  "٢", $data);
    $data = mb_ereg_replace('3',  "٣", $data);
    $data = mb_ereg_replace('4',  "٤", $data);
    $data = mb_ereg_replace('5',  "٥", $data);
    $data = mb_ereg_replace('6',  "٦", $data);
    $data = mb_ereg_replace('7',  "٧", $data);
    $data = mb_ereg_replace('8',  "٨", $data);
    $data = mb_ereg_replace('9',  "٩", $data);
    $data = mb_ereg_replace('0',  "٠", $data);


    $data = mb_ereg_replace('\077',  "؟", $data);
    $data = mb_ereg_replace('\054',  "،", $data);
    $data = mb_ereg_replace('\056',  ".", $data);
    $data = mb_ereg_replace('\~',  "ٓ", $data);

    return $data;
}