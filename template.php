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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title><?= $this->titre() ?></title>
      <meta http-equiv="content-language" content="fr" />
      <meta name="author" content="L214" />
      <meta name="KEYWORDS" content="visuel,L214,listage,répertoire" />
      <meta name="DESCRIPTION" content="Listage du répertoire <?= $this->repertoire()->path ?>" />
      <link rel="shortcut icon" href="<?= $this->racineRelative() ?>icones/favicons/visuels.l214.com.png" type="image/x-icon" />
      <link rel="stylesheet" type="text/css" href="<?= $this->racineRelative() ?>style.css?1" media="screen,print,projection" />
   </head>
   <body>
      <p id="logo">
         <a href="http://www.l214.com" title="Visiter le site de www.L214.com"><img src="/logos/L214-75x75.png" width="75" height="75" alt="L214" /></a>
      </p>
	<div id="recherche">
		<script>
			(function() {
				var cx = '000261657407088771557:hu-kfuhrvvs';
				var gcse = document.createElement('script');
				gcse.type = 'text/javascript';
				gcse.async = true;
				gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
					'//www.google.com/cse/cse.js?cx=' + cx;
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(gcse, s);
				})();
		</script>
		<gcse:search></gcse:search>
	</div>
      <h1>///// <?= $this->cheminDeFer(); ?></h1>
      <? if($this->texte() != '') : ?>
         <p class="texte"><?= $this->texte() ?></p>
      <? endif; ?>
      <?
      if($this->typeAffichage() == AFFICHAGE_BANNIERES) {
         $this->afficheBannieres();
      }
      else {
         $this->afficheRepertoires();
         $this->afficheImages();
         $this->afficheReste();
      }
      ?>
   </body>
</html>