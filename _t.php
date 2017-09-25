<?php
/**
 * Linux command to extract translations
 *
 * This script will convert words.txt to structure used in words.php
 */


// #: src/BunnyMenu.php:89
// msgid "Forum BT 2.0"
// msgstr ""

if (!file_exists('words.txt')) {
    echo "words.txt not found\n";
    echo "To extract translation strings, run\n";
    echo "find . -iname \"*.php\" | xargs xgettext --from-code=UTF-8 -k_t -k_th -o words.txt\n";
    exit(1);
}
$lines = file('words.txt');

$src = '';

$msgid = '';
$msgidsrc = '';

$words = [];
foreach($lines as $line) {
    if (substr($line, 0, 7) == "#: src/") {
        $n = substr($line, 7, strrpos($line, ':') - 7);
        if ($src !== $n) {
            $src = $n;
            echo "\t\t// $src\n";
        }
        $msgidsrc = $line;
    }
    if (substr($line, 0, 6) == 'msgid ') {
        $s = strpos($line, '"')+1;
        $e = strrpos($line, '"');
        $msgid = substr($line, $s, $e-$s);
        if (empty($msgid)) {
            if ($msgidsrc !== '') {
                echo "\t\t//>> empty $msgidsrc\n";
            }
        } else if (isset($words[$msgid])) {
            echo "\t\t//>> duplicate ($msgid)@$msgidsrc\n";
        } else {
            echo "\t\t'$msgid' => ['en' => '$msgid'],\n";
            $words[$msgid] = 1;
        }
    }
}

