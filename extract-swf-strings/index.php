<?php
$gamefilesPath = __DIR__."/../another-server/cdn/custom-gamefiles-path/";
$gamefilesUrl  = "http://localhost/another-server/cdn/custom-gamefiles-path/";

\header("Content-Type: text/plain");

function getFilesRecursive($dir, $regex) {
    $files = \scandir($dir);

    $out = [];
    foreach($files as $file) {
        if($file=="." || $file=="..") continue;
        if(\is_dir("{$dir}{$file}")) {
            $newFiles = getFilesRecursive("{$dir}{$file}/", $regex);
            foreach($newFiles as $newFile) {
                $out[] = $newFile;
            }
        } else {
            if(\preg_match($regex, $file)) {
                $out[] = "{$dir}{$file}";
            }
        }
    }
    return $out;
}

if(\is_dir(__DIR__."/extracted")) {
    //echo "Dir ./extracted exists. Using current extracted data.\n";
} else {
    \mkdir(__DIR__."/extracted");
    echo "echo \"Please run this console commands to continue.\"\n";
    foreach(getFilesRecursive($gamefilesPath, '/.+\.swf/') as $swf) {
        \mkdir($outFolder = __DIR__."/extracted/".\md5($swf));
        echo "java -jar \"C:\\Program Files (x86)\\FFDec\\ffdec.jar\" -export script {$outFolder} {$swf}\n";
    }
    die;
}

$swfMatches = [];
foreach(getFilesRecursive(__DIR__."/extracted/", '/.+\.as/') as $as) {
    $data = \file_get_contents($as);
    $lines = \preg_split('/\r?\n/', $data);
    foreach($lines as $line) {
        if(\preg_match('/\.swf/', $line)) {
            if(!isset($swfMatches[$as])) {
                $swfMatches[$as] = [];
            }
            $swfMatches[$as][] = $line;
        }
    }
}

$filesToDownload = [];
foreach($swfMatches as $as => $matches) {
    foreach($matches as $match) {
        if(!\preg_match('/"(.+?\.swf)"/', $match, $fileNameOut)) {
            echo "Erro ao obter o nome do arquivo\n";
            echo " - Arquivo: {$as}\n";
            echo " - Linha: {$match}\n";
            die;
        }
        $fileName = $fileNameOut[1];
        if($fileName=="IGA/IGAViewer.swf") {
            echo $as;die;
        }
        $filesToDownload[\md5($fileName)] = $fileName;
    }
}

foreach($filesToDownload as $key => $fileToDownload) {
    if(\file_exists("{$gamefilesPath}{$fileToDownload}")) {
        // echo "{$fileToDownload} found, skipped\n";
        unset($filesToDownload[$key]);
        continue;
    }
    // echo "{$fileToDownload} not found. Will be downloaded\n";
}

$downloaded = [];
$error = [];
foreach($filesToDownload as $key => $fileToDownload) {
    $hasDownloaded = @\file_get_contents("{$gamefilesUrl}{$fileToDownload}")!==false;
    if($hasDownloaded) {
        $downloaded[] = $fileToDownload;
    } else {
        $error[] = $fileToDownload;
    }
}

echo \json_encode([
    "downloaded" => $downloaded,
    "error" => $error,
]);