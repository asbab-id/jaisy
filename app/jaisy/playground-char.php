<?php
require_once '_charPegonList.php';


// ga jadi deh wkwkwk

echo "<table border='1'>";
echo "<tr><th>ARAB</th><th>#pegon</th><th>@kode</th></tr>";

foreach ($GLOBALS['charPegonList'] as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row[0]) . "</td>";
    echo "<td>" . htmlspecialchars($row[1]) . "</td>";
    echo "<td>" . htmlspecialchars($row[2]) . "</td>";
    echo "</tr>";
}

echo "</table>";

echo '<pre>';
print_r($GLOBALS['charPegonList']);