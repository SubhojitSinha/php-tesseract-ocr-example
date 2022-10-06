<?php
ob_implicit_flush(true);
ob_end_flush();

$root        = dirname(__FILE__);
$image_path  = $root.DIRECTORY_SEPARATOR."images";
$ocr_path    = $root.DIRECTORY_SEPARATOR."temp";
$final_file  = $root.DIRECTORY_SEPARATOR."final.txt";
$files_array = getDirectoryFileListArray($image_path);
$language    = 'ben';

$file1 = fopen($final_file, 'a+');
echo "1. Main File Open.<br>";
echo "2. OCR Started.<br>";

$i = 0;
foreach($files_array as $file)
{
    $i++;
    $file_name   = pathinfo($file, PATHINFO_FILENAME);
    $source_file = $image_path.DIRECTORY_SEPARATOR.$file;
    $dest_file   = $ocr_path.DIRECTORY_SEPARATOR.$file_name;
    $ocr_command = "tesseract $source_file $dest_file --oem 1 -l $language --psm 3";
    
    echo "<pre>\t[$i]. File Name: `$file`</pre>";
    
    try 
    {
        // Execute the command
        echo exec($ocr_command);
        echo "<pre>\t     Command: ".$ocr_command."</pre>";

        // Read the ocr content and append to a main file
        $dest_file = $dest_file.".txt";
        $file2     = file_get_contents($dest_file);
        fwrite($file1, $file2);
        echo "<pre>\t     Append: Complete</pre>";
    } 
    catch (Exception $e) 
    {
        echo $e->getMessage();
    }
}       
fclose($file1);
echo "3. Main File Closed<br>";

// Remove non required files
// -------------------------------
$ocr_files = getDirectoryFileListArray($ocr_path);
echo "4. OCR File remove.<br>";

$i=0;
foreach($ocr_files as $file) 
{
    $i++;
    $file = $ocr_path.DIRECTORY_SEPARATOR.$file;
    if(is_file($file))
    {
        // Delete the given file
        unlink($file); 
        echo "<pre>\t[ $i ]-- Deleted : $file </pre>";
    } 
}
echo "5. Batch OCR complete.<br>";

// -------------------------------

function getDirectoryFileListArray($path)
{
    $files_array = [];
    $files = scandir($path);
    $files = array_diff(scandir($path), array('.', '..'));

    foreach($files as $file)
    {
        array_push($files_array, $file);
    }
    sort($files_array);

    return $files_array;
}
