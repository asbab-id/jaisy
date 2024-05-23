<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jaisy Editor</title>
    <style>
        .editor-wrapper {
            display: flex;
            border: black solid 1px;
        }

        .line-numbers {
            counter-reset: line;
            padding: 10px;
            background-color: #f0f0f0;
            font-family: monospace;
        }

        .code-editor {
            padding: 10px;
            font-family: monospace;
            outline: none;
            width: 100%;
            white-space: pre;
            overflow-x: scroll;
            background-color: #fcfcfc;
        }

        .suggestion {
            font-family: monospace;
            background-color: #d6d6d3;
            position: sticky;
            top: 0;
            border: black solid 1px;
            padding-left: 10px;
        }

        .word {
            white-space: pre-wrap;
        }

        .line {
            display: block;
        }

        .btn-run {
            background-color: #e1eee3;
            color: green;
            border-color: green;
            margin-top: 20px;
        }

        #output{
            margin-left: 10px;
            margin-top: 10px;
            margin-bottom: 10px;
            padding-left: 10px;
            padding-bottom: 20px;
            overflow: auto;
            background-color: #2d2dac2e;
            min-height: 100px;
            white-space: pre;
        }

    </style>
</head>
<body>
    <div class="jaisy-editor">
        <div class="suggestion"></div>
        <div class="editor-wrapper">
            <div id="line-numbers" class="line-numbers"></div>
            <div id="code-editor" class="code-editor" contenteditable="true">// ini adalah sample untuk syntax highlight
print alhamdulillah

// tipe data (text, angka, list)
$petik      = 'bismillah'
$tanpaPetik = saya ucapkan
$gabung1    = $tanpaPetik $petik        // saya ucapkanbismillah
$gabung2    = $tanpaPetik @spasi $petik // saya ucapkan bismillah
 

// CONTOH FUNCTION BAWAAN
// jika di javascript gunduler(var1, var2, var3)
// jika di jaisy      gunduler : var1 : var2 : var3
$jaisy       = 'جَيْشٌ'
$tesGunduler = gunduler : $jaisy         // جيش  

$tesCleanSpasi = clean_spasi : ' text  ' // text
$tesHitung     = hitung : $jaisy         // 6

$tesHapus  = hapus : $tesGunduler : @syin        // جي
$tesGanti  = ganti : $tesGunduler : @syin : @dal // جيد
$tesCari   = cari : $tesGunduler : @yak          // 2
$tesTambah = tambah : $tesGunduler : 2 : @wawu  // جيوش
print ganti : $tesGunduler : @syin : @dal // tidak bisa, hanya bisa digunakan saat define variable
$error1 = gunduler : $jaisy hapus : @syin // tidak bisa menggunakan function banyak dalam 1 baris. harus buat di baris baru dengan update variabel

$tesPecahHuruf = pecah_huruf : asbab               // ['a','s','b','a','b']
$tesPecah      = pecah : $gabung2 : @spasi         // ['saya', 'ucapkan', 'bismillah']
print $tesPecah[1] // saya

$tesAntara = antara : $tesGunduler : @jim : @syin // ي
$tesAwal   = awal : $tesGunduler                  // ج
$tesAkhir  = akhir : $tesGunduler                 // ش
$tesTengah = tengah : $tesGunduler                // ي ???????????????????


// CONTOH PERCABANGAN LOGIKA
// if
jika $tesGunduler merupakan 'جيش' // dalam js, ==
maka $hasil1 = 'hasil dari gunduler benar'

// bisa ditulis juga seperti ini, dengan diapit kurung kurawal
maka {

}

// if else
jika $gabung2 terdapat kamu // dalam js, includes
maka $hasil2 = ada kamu
jika_tidak $hasil2 = tidak ada kamu

// if, elseif, else
// contoh logika mencari isim
$tanwin =  ًٌٍ
jika $jaisy terdapat $tanwin // bolak-balik cek terdapatnya
maka $hasil3 = isim
atau_jika $jaisy terdapat #AL // elseif
maka $hasil3 = isim
jika_tidak $hasil3 = tidak isim // else

jika 'saya' merupakan 'sayang' atau 'saya' terdapat 'aya'
maka $hasil3 = bolehlah wkwkwk

jika 'saya' merupakan 'saya' dan 'kamu' merupakan 'kamu'
maka $hasil4 = ya iyalah wkwkwk


// contoh operasi mtk
$text = hitung 'abcd'
$mtk  = $text + 1 - 1 * 1 / 1 // 4





// todo : buat loop;  function 
// function salam($var1, $var2, $var3){}
buat_fungsi salam : $var1 : $var2 : $var3
$hasil = Assalamualaikum @spasi $var1 @spasi dapet salam dari @spasi $var2 @spasi dan @spasi $var3
// {}  bebas mau ngapain
// $hasil adalah return bawaan
tutup_fungsi salam

// salam(kamu, asbab, jaisy)
$tesSalam = *salam : kamu : asbab : jaisy // Assalamualaikum kamu dapet salam dari asbab dan jaisy
// @ # $ : = *  {} []
// kurang dari, lebih dari, sama dengan
// tidak 

// buat loop ?
$list = $tesPecah // ['saya', 'ucapkan', 'bismillah']
ulang $tesPecah ?   


// ikut php/js
for ($x = 0; $x <= 10; $x++) {
print "The number is: $x 
}


// membaca kamus dalam database
// getKamus('mg', 'key', 20, [=])
$makan = kamus : ah_mujarrod : key : 20 : [=]
print $makan // []
print $makan[result][1][arab] // أكَلَ ـُـ أكْلاً الطعامَ : تناوله</div>
        </div>
        <button id="run" onclick="runJaisy(this)" class="btn-run">▶ Run</button>
        <div id="output"></div>
    </div>


    <script>
        function runJaisy(){
            let abc = codeEditor.innerText;

            fetch('/interpreter', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 'jaisy': abc })
            })
            .then(response => response.json())
            .then(data => {
                // console.log('Success:', data);
                document.querySelector('#output').innerText = data['output'];
                console.log(data['debug']);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
    <script src="static/jaisy-editor.js"></script>
</body>
</html>
