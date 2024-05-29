const codeEditor = document.getElementById('code-editor');
const lineNumbers = document.getElementById('line-numbers');
const suggestionBox = document.querySelector('.suggestion');

let tmpStrSugestJaisy = '';
let undoStack = [];
let redoStack = [];
let specialKeydown = false;

codeEditor.focus();
updateLineNumbers();
syntaxHighlight();
saveState();

// Menambahkan event listener untuk beforeunload
window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
});

// Menambahkan event listener untuk keydown
codeEditor.addEventListener('keydown', function(event) {
    specialKeydown = event.ctrlKey && ['z', 'y', 'a', 'c', 'v'].includes(event.key.toLowerCase());
    if (specialKeydown && event.key !== 'Enter') {
        // Handle Ctrl+Z (Undo) dan Ctrl+Y (Redo)
        if (event.ctrlKey && event.key.toLowerCase() === 'z') {
            undo();
        } else if (event.ctrlKey && event.key.toLowerCase() === 'y') {
            redo();
        }
        debug('Special keydown event triggered:', event.key);
    }
    if(event.key === 'Enter'){
        event.preventDefault();
        // navigator.clipboard.writeText('text to be copied');
        document.execCommand('insertText', false, '\n');
    }
});

// Menambahkan event listener untuk keyup
codeEditor.addEventListener('keyup', debounce(function(event) {
    if (!specialKeydown) {
        saveState();
        editor(event);
    }
    // Reset flag
    specialKeydown = false;
}, 300));

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}












function editor(event) {
    updateLineNumbers();
    syntaxHighlight(event);
}

function updateLineNumbers() {
    const lines = codeEditor.innerText.split('\n');

    if (lines[lines.length - 1] === '' && lines[lines.length - 2] === '') {
        lines.pop();
    }

    const linesCount = lines.length;
    const lineNumbersHTML = Array.from({ length: linesCount }, (_, index) => `<div>${index + 1}</div>`).join('');
    lineNumbers.innerHTML = lineNumbersHTML;
}


function syntaxHighlight(event = false) {
    var abc = codeEditor.innerText;
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

    // Highlight untuk karakter ' @ ' dan ' # ' dan ' $ '
    // regex.push([/ @ /g, ' <b style="color:gray>@</b> ']);
    // regex.push([/ # /g, ' <b style="color:gray>#</b> ']);
    // regex.push([/ \$ /g, ' <b style="color:gray>$</b> ']);



    // Highlight untuk kata yang di awal baris adalah $ dan jika diikuti gunduler, ...
    regex.push([/^\$\w+\s*=\s*(gunduler|clean_spasi|hitung)\b/gm, function(match) {
        return match.replace(/\b(gunduler|clean_spasi|hitung)\b/g, '<b style="color:lime">$1</b>');
    }]);

    regex.push([/^\$\w+\s*=\s*(hapus|ganti|tambah|cari)\b/gm, function(match) {
        return match.replace(/\b(hapus|ganti|tambah|cari)\b/g, '<b style="color:palegreen">$1</b>');
    }]);

    regex.push([/^\$\w+\s*=\s*(pecah_huruf|pecah)\b/gm, function(match) {
        return match.replace(/\b(pecah_huruf|pecah)\b/g, '<b style="color:olivedrab">$1</b>');
    }]);

    regex.push([/^\$\w+\s*=\s*(antara|akhir|awal|tengah)\b/gm, function(match) {
        return match.replace(/\b(antara|akhir|awal|tengah)\b/g, '<b style="color:darkseagreen">$1</b>');
    }]);




    // Highlight untuk kata "print" di awal baris
    regex.push([/^\b(print)\b/gm, '<b style="color:magenta">$1</b>']);

    // Highlight untuk kata "terdapat" dan "merupakan" jika di awal baris terdapat kata "jika" atau "atau_jika"
    regex.push([/^\b(jika|atau_jika)\b.*?\b(terdapat)\b/gm, function(match) {
        return match.replace(/\b(terdapat)\b/g, '<b style="color:saddlebrown">$1</b>');
    }]);

    regex.push([/^\b(jika|atau_jika)\b.*?\b(merupakan)\b/gm, function(match) {
        return match.replace(/\b(merupakan)\b/g, '<b style="color:saddlebrown">$1</b>');
    }]);



    // Highlight untuk kata "dan" dan "atau" dan "tidak" jika di awal baris terdapat kata "jika" atau "atau_jika"
    regex.push([/^\b(jika|atau_jika)\b.*?\b(dan)\b/gm, function(match) {
        return match.replace(/\b(dan)\b/g, '<b style="color:darkblue">$1</b>');
    }]);

    regex.push([/^\b(jika|atau_jika)\b.*?\b(atau)\b/gm, function(match) {
        return match.replace(/\b(atau)\b/g, '<b style="color:darkblue">$1</b>');
    }]);

    regex.push([/^\b(jika|atau_jika)\b.*?\b(tidak)\b/gm, function(match) {
        return match.replace(/\b(tidak)\b/g, '<b style="color:darkblue">$1</b>');
    }]);


    // Highlight untuk kata yang diawali dengan $
    regex.push([/(?<!\w)(\$\w+)/g, '<b style="color:royalblue">$1</b>']);

    // Highlight untuk kata di awal baris
    regex.push([/^\b(jika|maka|atau_jika|jika_tidak)\b/gm, '<b style="color:aqua;text-decoration:underline">$1</b>']);
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

    // call suggest
    if (/^[a-zA-Z0-9_]$/.test(event.key)) {
        tmpStrSugestJaisy = tmpStrSugestJaisy ? tmpStrSugestJaisy + event.key : event.key;
        suggestionBox.innerHTML = '💡: ' + suggest(tmpStrSugestJaisy, true);
    } else {
        tmpStrSugestJaisy = '';
    }

    var balik = saveCaretPosition(codeEditor);
            

    codeEditor.innerHTML = abc;
    // console.log(abc); /////////////////////////////
    if(event.key === 'Enter'){
        // event.stopPropagation();
        event.preventDefault();
        // document.execCommand('insertHTML', false, '<br>');

        updateLineNumbers();
        balik(1);
        debug('balik1');
    }else{
        balik();
        debug('balik0');
    }


    function suggest(query, value=false) {
        var result = [];
        var keyword = {
            'print': '<b style="color:magenta">print</b>',

            'jika': '<b style="color:aqua">jika</b>',
            'maka': '<b style="color:aqua">maka</b>',
            'jika_tidak': '<b style="color:aqua">jika_tidak</b>',
            'atau_jika': '<b style="color:aqua">atau_jika</b>',
            'kemudian': '<b style="color:#97f3f3">kemudian</b>',

            'buat_fungsi': '<br><br> <b style="color:lightblue">buat_fungsi</b> namanya <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b> <br> <b style="color:royalblue">$hasil</b> = data yang dikembalikan fungsi <br> <b style="color:lightblue">tutup_fungsi</b> namanya',
            'tutup_fungsi': '<b style="color:lightblue">tutup_fungsi</b>',

            'gunduler': '<b style="color:lime">gunduler</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b>',
            'clean_spasi': '<b style="color:lime">clean_spasi</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b>',
            'hitung': '<b style="color:lime">hitung</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b>',

            'hapus': '<b style="color:palegreen">hapus</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b> <b style="color:salmon">@syin</b>',
            'cari': '<b style="color:palegreen">cari</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b> <b style="color:salmon">@syin</b>',

            'ganti': '<b style="color:palegreen">ganti</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:salmon">@syin</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:salmon">@syin</b>',
            'tambah': '<b style="color:palegreen">tambah</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b> <b style="color:black;background-color:cornsilk">:</b> 2 <b style="color:black;background-color:cornsilk">:</b> <b style="color:salmon">@syin</b>',

            'pecah_huruf': '<b style="color:olivedrab">pecah_huruf</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b>',
            'pecah': '<b style="color:olivedrab">pecah</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text2</b>',

            'antara': '<b style="color:darkseagreen">antara</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text2</b>',
            'awal': '<b style="color:darkseagreen">awal</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b>',
            'akhir': '<b style="color:darkseagreen">akhir</b> <b style="color:black;background-color:cornsilk">:</b> <b style="color:royalblue">$text</b>',
        };


        var regex = new RegExp('^' + query);
        var keys = Object.keys(keyword);


        for (var i = 0; i < keys.length; i++) {
            var key = keys[i].trim();
            if (regex.test(key)) {
                if(value==true){
                    result.push(keyword[keys[i]]);
                }else{
                    result.push(key);
                }
            }
        }
        return result;
    } // close suggest
} // close syntaxHighlight





function saveCaretPosition(context) {
    var selection = window.getSelection();
    var range = selection.getRangeAt(0);
    range.setStart(context, 0);
    var len = range.toString().length;

    function getTextNodeAtPosition(root, index) {
        const NODE_TYPE = NodeFilter.SHOW_TEXT;
        var treeWalker = document.createTreeWalker(root, NODE_TYPE, function next(elem) {
            if (index > elem.textContent.length) {
                index -= elem.textContent.length;
                return NodeFilter.FILTER_REJECT;
            }
            return NodeFilter.FILTER_ACCEPT;
        });
        var c = treeWalker.nextNode();
        return {
            node: c ? c : root,
            position: index
        };
    }

    return function restore(geser_kanan=0) {
        var pos = getTextNodeAtPosition(context, len+geser_kanan);
        selection.removeAllRanges();
        var range = new Range();
        range.setStart(pos.node, pos.position);
        selection.addRange(range);
    };
}



// Menyimpan state saat ada perubahan
function saveState() {
    undoStack.push(codeEditor.innerHTML);
    redoStack = []; // Clear redo stack setelah perubahan baru
}

// Fungsi undo
function undo() {
    if (undoStack.length > 0) {
        redoStack.push(codeEditor.innerHTML);
        const previousState = undoStack.pop();
        codeEditor.innerHTML = previousState;
    }
}

// Fungsi redo
function redo() {
    if (redoStack.length > 0) {
        undoStack.push(codeEditor.innerHTML);
        const nextState = redoStack.pop();
        codeEditor.innerHTML = nextState;
    }
}






// debug
function debug(data){
    return;
    console.log(data);
}