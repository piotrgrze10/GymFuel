<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);

function rglob($pattern, $flags = 0, $path = '') {
    if (!$path && strpos($pattern, DIRECTORY_SEPARATOR) !== false) {
        $path = dirname($pattern) . DIRECTORY_SEPARATOR;
        $pattern = basename($pattern);
    }
    $paths = glob($path . '*', GLOB_ONLYDIR | GLOB_NOSORT);
    $files = glob($path . $pattern, $flags);
    foreach ($paths as $p) {
        $files = array_merge($files, rglob($pattern, $flags, $p . DIRECTORY_SEPARATOR));
    }
    return $files;
}

function stripCssJs($content) {
    
    $content = preg_replace('#/\*[\s\S]*?\*/#', '', $content);
    
    $content = preg_replace('#(^|\s)//.*$#m', '$1', $content);
    return $content;
}

function stripHtmlComments($content) {
    return preg_replace('//', '', $content);
}

function stripPhpComments($content) {
    
    $tokens = token_get_all($content);
    $out = '';
    foreach ($tokens as $token) {
        if (is_array($token)) {
            list($id, $text) = $token;
            if (in_array($id, [T_COMMENT, T_DOC_COMMENT])) {
                continue; 
            }
            $out .= $text;
        } else {
            $out .= $token;
        }
    }
    
    $out = stripHtmlComments($out);
    return $out;
}

$root = realpath(__DIR__ . '/..');
$processed = [];

$cssJs = array_merge(rglob('*.css', 0, $root . DIRECTORY_SEPARATOR), rglob('*.js', 0, $root . DIRECTORY_SEPARATOR));
foreach ($cssJs as $file) {
    $orig = file_get_contents($file);
    if ($orig === false) continue;
    file_put_contents($file . '.bak', $orig);
    $new = stripCssJs($orig);
    file_put_contents($file, $new);
    $processed[] = $file;
}

$htmlPhp = array_merge(rglob('*.html', 0, $root . DIRECTORY_SEPARATOR), rglob('*.htm', 0, $root . DIRECTORY_SEPARATOR), rglob('*.php', 0, $root . DIRECTORY_SEPARATOR));
foreach ($htmlPhp as $file) {
    $orig = file_get_contents($file);
    if ($orig === false) continue;
    file_put_contents($file . '.bak', $orig);
    $new = stripPhpComments($orig);
    file_put_contents($file, $new);
    $processed[] = $file;
}

header('Content-Type: text/plain; charset=utf-8');
echo "Stripped comments from " . count($processed) . " files.\n";
echo "Backups created with .bak extension.\n";
echo "You can delete backups after verification.";


