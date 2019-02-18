<?php
 /*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
define('WEBlife', 1);  //Set flag that this is a parent file
mb_internal_encoding('UTF-8');
mb_http_input('UTF-8'); 
mb_http_output('UTF-8');
// LINK: /interactive/cv_img.php?zone=[site|admin]

$zone =  (!empty($_GET['zone'])) ? $_GET['zone'] : false;
    
if($zone){
    
    // Define WLCMS_ZONE from $site_zone var
    switch($zone){
        case 'admin': define('WLCMS_ZONE', 'BACKEND');  break;//Set flag that this is a admin area
        case 'site' : define('WLCMS_ZONE', 'FRONTEND'); break;//Set flag that this is a site area
        default:  exit(); break;
    }

    session_start();

    // change to root dir
    chdir("..".DIRECTORY_SEPARATOR);

# ##############################################################################
// /// INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\\\\\
    require_once('include/functions/base.php');         // 1. Include base functions
    require_once('include/classes/Cookie.php');         // 2. Include Cookie class file
    require_once('include/system/SystemComponent.php'); // 3. Include DB configuration file Must be included before other
    require_once('include/classes/IValidator.php');     // 4. Include Image IValidator class
    $IValidator = new IValidator(getIValidatorPefix());
    $IValidator->RndLength  = IVALIDATOR_MAX_LENTH;
    $IValidator->FontsDir   = 'fonts';
// /// END INCLUDE LIST SOME REQUIRED FILES AND INITIAL GLOBAL VARS BLOCK \\\\\\
# ##############################################################################

    switch($zone){
        case 'admin':
            $IValidator->Width      = 75;
            $IValidator->Height     = 25;
            $IValidator->RndCodes = $IValidator->RndCodes.'abcdefghijklnmopqrstuvwxyz';
            $IValidator->Fonts    = array('arial.ttf', 'verdana.ttf');
            $IValidator->Themes   = array(
                array(
                    'background' => array(0, 80, 179),
                    'border'     => array(180, 180, 180),
                    'font'       => array(255,255,255)
                 )
            );
            break;
        case 'site':
            $IValidator->Width      = 85;
            $IValidator->Height     = 36;
            $IValidator->Fonts      = array('arial.ttf', 'verdana.ttf');
            $IValidator->Themes     = array(
                array(
                    'background' => array(214, 225, 232),
                    'border'     => array(214, 225, 232),
                    'font'       => array(32, 94, 53)
                 )
            );
            break;
        default: 
            exit();
            break;
    }
    $IValidator->generateImage();

}
