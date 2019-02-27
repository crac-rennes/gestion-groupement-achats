<?php
preg_match_all("|<[^>]+>(.*)</[^>]+>|U",
    "<b>exemple : </b><div align=left>ceci est un test</div>",
    $out, PREG_PATTERN_ORDER);
echo $out[0][0] . ", " . $out[0][1] . "\n";
echo $out[1][0] . ", " . $out[1][1] . "\n";
?>