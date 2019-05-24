<?php
    date_default_timezone_get("Europe/Paris");
    use Module\AjaxCore;
    
    if(empty($_GET)) {
        return false;
    }
    
    $action = htmlspecialchars($_GET['action']);
    
    if(!is_string($action) || empty($action)) {
        return false;
    }
    
    $vendor = '../../vendor/autoload.php';
    
    if(!file_exists($vendor)) {
        echo "Autoload not found";
        exit;
    }
    
    require $vendor;
    session_start();

    $core = new AjaxCore($action);

    if(!empty($core->getError())) {
        echo $core->getError();
        return;
    }

    if(empty($core->getContent())) {
        return;
    }

    echo $core->getContent();