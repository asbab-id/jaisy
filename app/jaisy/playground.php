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
            <div id="code-editor" class="code-editor" contenteditable="true">// bab 1 : print dan komentar
print  Halloo // output : Halloo
// kode ini tidak akan dieksekusi karena ini adalah komentar



// bab 2 : shortcut string dan concat
print @JIM @YAK @SYIN // output : جيش 
print #aku @ #lagi @ #koding // output : أكو لاڮي كوديڠ
print #Alklam_hw_ALlfZH_Almrkb_bALwDHE // output : الكلام هو اللفظ المركب بالوضع
print Hallo @ Kehidupan. # Aku $ Hidup. // concat @,#,$ output : Hallo Kehidupan. Aku Hidup. 



// bab 3 : quoted string tidak akan merender shortcut string dan variabel
print 'ini adalah Jaisy Query Interpreter' @ "Ayo Kapan Ono!!!" // output : ini adalah Jaisy Query Interpreter Ayo Kapan Ono!!!
print 'Penulisan Arab : ' @JIM @YAK @SYIN @ JAISYUN // output : Penulisan Arab : جيش JAISYUN
print 'Saya menulis : @ALIF @LAM' // output : Saya menulis : @ALIF @LAM



// bab 3 : pendefinisian variabel dan update variabel
$nama = faza
$namaArab = @FAK @ALIF @ZAI
print 'Nama saya adalah : ' $nama '. dengan text arab : ' $namaArab // output : Nama saya adalah : faza. dengan text arab : فاز

update $nama = ilmi
update $namaArab = @AIN @LAM @MIM
print 'Nama saya adalah : ' $nama '. dengan text arab : ' $namaArab  // output : Nama saya adalah : ilmi. dengan text arab : علم



// bab 4 : pemanggilan skill (function bawaan dari bahasa pemprgoraman) 
$tesCleanSpasi = clean_spasi : ' text  ' // jika dalam php clean_spasi(' text  ')
print $tesCleanSpasi // output : text



// bab 5 : Logika if else
$cek = kata
jika $cek merupakan kata // if
maka $hasil = benar      // aksi jika if terpenuhi
kemudian $hasil2 = betul // aksi kedua jika if terpenuhi
atau_jika $cek merupakan entahlah // else if
maka $hasil = entah // aksi jika else if terpenuhi
kemudian $hasil2 = tidak @ tahu // aksi kedua jika else if terpenuhi
jika_tidak $hasil = salah // else sekaligus aksi jika if dan else if tidak terpenuhi
kemudian $hasil2 = ngawor // aksi kedua dari else

print $hasil // output : benar
print $hasil2 // output : betul



// bab 6 : membuat function. jika dalam php function salam($var1){return $hasil = ...}
buat_fungsi salam : $var1
$hasil = 'اَلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللهِ وَبَرَكَا تُهُ' @ $var1
tutup_fungsi


$tesSalam1 = *salam : Faza // pemanggilan fungsi. diawali tanda *. jika dalam php salam('Faza')
print $tesSalam1 // output : اَلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللهِ وَبَرَكَا تُهُ Faza

$tesSalam2 = *salam : Asbab
print $tesSalam2 // output : اَلسَّلَامُ عَلَيْكُمْ وَرَحْمَةُ اللهِ وَبَرَكَا تُهُ Asbab</div>
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
                alert('Error Interpreter System');
                console.error('Error:', error);
            });
        }
    </script>
    <script src="static/jaisy-editor.js"></script>
</body>
</html>
