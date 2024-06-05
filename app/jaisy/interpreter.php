<?php
require_once 'core/_core.php';

header("Content-Type: application/json");

// Mendapatkan data JSON yang dikirim dari klien
$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['jaisy'])) {
    $jaisy = $input['jaisy'];

    // Misalnya, kita ingin menyimpan data ini ke dalam database atau melakukan pemrosesan lain

    // Mengirimkan respon kembali ke klien
    echo json_encode([
        'status' => 'success',
        'message' => 'Data received successfully',
        'output' => jaisyInterpreter($jaisy),
        'debug' => [
            'listVar' => $GLOBALS['listVar'],
            'listPrint' => $GLOBALS['listPrint'],
            'input' => $jaisy,
            'totalBaris' => $GLOBALS['totalBaris'],
            'jikaMaka' => $GLOBALS['tmpJikaMaka'],
            'listBaris' => $GLOBALS['listBaris'],
            'listFungsi' => $GLOBALS['tmpBuatFungsi']
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input'
    ]);
}