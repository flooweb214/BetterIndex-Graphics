<?php

$extentionsAcceptees = array('jpg', 'png', 'gif', 'jpeg', 'tiff', 'svg', 'pdf');

/// //////////////////////////////////////////////
///
/// //////////////////////////////////////////////
function erreur ($chaine) {
  if($chaine != '')
    print '<p class="erreur">'. $chaine ."</p>\n";
    return false;
}

/// //////////////////////////////////////////////
///
/// //////////////////////////////////////////////
function succes ($chaine) {
  //if($GLOBALS['GLOBAL_VERBOSE'])
    print '<p class="succes">'. $chaine ."</p>\n";
    return true;
}

/// #################################################"
/// Vérifications des paramètres
/// #################################################"

/// Le paramètre f existe
if(isset($_GET['f']) == false || $_GET['f'] == '') exit;

/// L'extention du fichier demandé est acceptée
$cheminPartis = pathinfo($_GET['f']);
if(in_array($cheminPartis['extension'], $extentionsAcceptees) == false) exit;

/// Le fichier existe
if(file_exists($_GET['f']) == false) exit;

header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename='.$cheminPartis['basename']);
readfile($_GET['f']);

?>