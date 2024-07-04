function staticHighlight(dom) {
    var code = dom;
    var abc = code.innerText.trim();
    var regex = [];

    // diapit petik
    regex.push([/(?<!<[^>]*)'([^']+)'(?![^<]*>)/g, '<b style="color:orange">\'$1\'</b>']);
    regex.push([/(?<!<[^>]*)"([^"]+)"(?![^<]*>)/g, '<b style="color:orange">"$1"</b>']);
    regex.push([/(?<!<[^>]*)\`([^\`]+)\`(?![^<]*>)/g, '<b style="color:orange">`$1`</b>']);

    // Highlight untuk kata yang diawali dengan @
    regex.push([/(?<!\w)@(\w+)/g, '<b style="color:salmon">@$1</b>']);
    // Highlight untuk kata yang diawali dengan #
    regex.push([/(?<!\w)#(\w+)/g, '<b style="color:tomato">#$1</b>']);

    // Highlight untuk karakter ' : '
    regex.push([/ : /g, ' <b style="color:black;background-color:cornsilk">:</b> ']);

    // Highlight untuk karakter ' @ ', ' $ ', ' # '
    regex.push([/ \@ /g, ' <b style="color:grey;background-color:cornsilk;font-weight:normal;font-style:italic">@</b> ']);
    regex.push([/ \$ /g, ' <b style="color:grey;background-color:cornsilk;font-weight:normal;font-style:italic">$</b> ']);
    regex.push([/ \# /g, ' <b style="color:grey;background-color:cornsilk;font-weight:normal;font-style:italic">#</b> ']);

    // Highlight untuk karakter ' @ ' dan ' # ' dan ' $ '
    // regex.push([/ @ /g, ' <b style="color:gray>@</b> ']);
    // regex.push([/ # /g, ' <b style="color:gray>#</b> ']);
    // regex.push([/ \$ /g, ' <b style="color:gray>$</b> ']);



    // Highlight untuk kata yang di awal baris adalah $ dan jika diikuti gunduler, ...
    regex.push([/^\$\w+\s*=\s*(gunduler|clean_spasi|hitung|hapus|ganti|cari|tambah|ambil|mtk|antara)\b/gm, function(match) {
        return match.replace(/\b(gunduler|clean_spasi|hitung|hapus|ganti|cari|tambah|ambil|mtk|antara)\b/g, '<b style="color:lime">$1</b>');
    }]);

    // regex.push([/^\$\w+\s*=\s*(hapus|ganti|tambah|cari)\b/gm, function(match) {
    //     return match.replace(/\b(hapus|ganti|tambah|cari)\b/g, '<b style="color:palegreen">$1</b>');
    // }]);

    // regex.push([/^\$\w+\s*=\s*(pecah_huruf|pecah)\b/gm, function(match) {
    //     return match.replace(/\b(pecah_huruf|pecah)\b/g, '<b style="color:olivedrab">$1</b>');
    // }]);

    // regex.push([/^\$\w+\s*=\s*(antara|akhir|awal|tengah)\b/gm, function(match) {
    //     return match.replace(/\b(antara|akhir|awal|tengah)\b/g, '<b style="color:darkseagreen">$1</b>');
    // }]);




    // Highlight untuk kata "print" di awal baris
    regex.push([/^\b(print)\b/gm, '<b style="color:magenta">$1</b>']);

    // Highlight untuk kata "update" di awal baris
    regex.push([/^\b(update)\b/gm, '<b style="color:lightskyblue;font-weight:normal">$1</b>']);

    // Highlight untuk kata "terdapat" dan "merupakan" jika di awal baris terdapat kata "jika" atau "atau_jika"
    regex.push([/^\b(jika|atau_jika)\b.*?\b(terdapat)\b.*$/gm, function(match) {
        return match.replace(/\b(terdapat)\b/g, '<b style="color:saddlebrown;text-decoration:underline">$1</b>');
    }]);


    regex.push([/^\b(jika|atau_jika)\b.*?\b(merupakan)\b.*$/gm, function(match) {
        return match.replace(/\b(merupakan)\b/g, '<b style="color:saddlebrown;text-decoration:underline">$1</b>');
    }]);



    // Highlight untuk kata "dan" dan "atau" dan "tidak" jika di awal baris terdapat kata "jika" atau "atau_jika"
    regex.push([/^\b(jika|atau_jika)\b.*?\b(dan)\b.*$/gm, function(match) {
        return match.replace(/\b(dan)\b/g, '<b style="color:darkblue;background-color:cornsilk">$1</b>');
    }]);

    regex.push([/^\b(jika|atau_jika)\b.*?\b(atau)\b.*$/gm, function(match) {
        return match.replace(/\b(atau)\b/g, '<b style="color:darkblue;background-color:cornsilk">$1</b>');
    }]);

    regex.push([/^\b(jika|atau_jika)\b.*?\b(tidak)\b.*$/gm, function(match) {
        return match.replace(/\b(tidak)\b/g, '<b style="color:tan;text-decoration:underline">$1</b>');
    }]);


    // Highlight untuk kata yang diawali dengan $
    regex.push([/(?<!\w)(\$\w+)/g, '<b style="color:royalblue">$1</b>']);

    // Highlight untuk kata di awal baris
    regex.push([/^\b(jika|atau_jika|jika_tidak)\b/gm, '<b style="color:aqua;text-decoration:underline;background-color:cornsilk"">$1</b>']);
    regex.push([/^\b(maka)\b/gm, '<b style="color:aqua;text-decoration:underline">$1</b>']);
    regex.push([/^\b(kemudian)\b/gm, '<b style="color:#97f3f3">$1</b>']);

    // Highlight untuk kata di awal baris
    regex.push([/^\b(buat_fungsi|tutup_fungsi)\b/gm, '<b style="color:lightblue;text-decoration:underline">$1</b>']);

    // Highlight untuk komentar yang diawali dengan //
    regex.push([/\/\/.*$/gm, '<b style="background-color:lightgray;opacity:0.5">$&</b>']);

    // Highlight untuk kata yang diawali dengan *
    regex.push([/(?<!\w)(\*\w+)/g, '<b style="color:teal">$1</b>']);

    regex.forEach(function(item) {
        abc = abc.replace(item[0], item[1]);
    });

    // console.log(abc); ////////////////////////////////////////

    // isolasi dan parsing tag b
    abc = abc.replace(/(<b)\s*(style="[^"]*")/g, '$1___$2');
    abc = abc.replace(/<b___/g, '<b ');

            

    code.innerHTML = abc.trim();
}