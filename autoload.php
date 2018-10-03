<?php
spl_autoload_register('indeedUapAutoloader');
function indeedUapAutoloader($fullClassName='')
{
    if (strpos($fullClassName, "Indeed\Uap\Migration")!==FALSE){
       $path = UAP_PATH . 'classes/Migration/';
    } else if (strpos($fullClassName, "Indeed\Uap\Db")!==FALSE){
       $path = UAP_PATH . 'classes/Db/';
    } else if (strpos($fullClassName, "Indeed\Uap\Services")!==FALSE){
       $path = UAP_PATH . 'public/services/';
    } else if (strpos($fullClassName, "Indeed\Uap")!==FALSE){
       $path = UAP_PATH . 'classes/';
    }
    if (empty($path)) return;

    $classNameParts = explode('\\', $fullClassName);
    if (!$classNameParts) return;
    $lastElement = count($classNameParts) - 1;
    if (empty($classNameParts[$lastElement])) return;
    $fullPath = $path . $classNameParts[$lastElement] . '.php';

    if (!file_exists($fullPath)) return;
    include $fullPath;
}
