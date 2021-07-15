<?php
if (isset($_GET['noinit']) && !empty($_GET['noinit']))
{
    $strNoInit = strval($_GET['noinit']);
    if ($strNoInit == 'N')
    {
        if (isset($_SESSION['NO_INIT']))
            unset($_SESSION['NO_INIT']);
    }
    elseif ($strNoInit == 'Y')
    {
        $_SESSION['NO_INIT'] = 'Y';
    }
}

if (!(isset($_SESSION['NO_INIT']) && $_SESSION['NO_INIT'] == 'Y'))
{
    if (file_exists($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/my_functions.php"))
        require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/my_functions.php");
}