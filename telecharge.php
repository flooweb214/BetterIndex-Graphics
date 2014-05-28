<?
/* Copyright 2014 Tiflopin http://florent.ourth.fr/contact
 *
 * This file is part of BetterIndex-Graphics.
 *
 * BetterIndex-Graphics is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BetterIndex-Graphics is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BetterIndex-Graphics.  If not, see <http://www.gnu.org/licenses/>.
 */

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