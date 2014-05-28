<?php
define ('AFFICHAGE_NORMAL', 0);
define ('AFFICHAGE_EVOLUTION', 1);
define ('AFFICHAGE_HD', 2);
define ('AFFICHAGE_BANNIERES', 3);
define ('AFFICHAGE_CHOIX_TAILLE', 4);
// define ('AFFICHAGE_CODE_INCLUSION', 4);

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

/// //////////////////////////////////////////////
/// ##############################################
/// ##############################################
/// ##############################################
/// //////////////////////////////////////////////
class BetterImage {

   public $nom;
   public $alt;
   public $title;
   public $cheminImage = '';
   public $cheminRepParent = '';
   public $urlImage = '';
   public $info;
   public $basename = '';
   public $extension = '';
   public $taille;
   public $width = 0;
   public $height = 0;
   public $resolution;
   public $poids = 0;
   public $cheminHD = '';
   public $cheminHDRelatif = '';
   public $cheminSVG = '';
   public $cheminSVGRelatif = '';
   public $cheminTailles = '';
   public $cheminTaillesRelatif = '';

   /// ////////////////////////////////////////////////////
   /// ////////////////////////////////////////////////////
   public function __construct($imgNomFichier, $chemin, $alt, $title) {

      $this->nom = $imgNomFichier;
      $this->alt = $alt;
      $this->title = $title; /// dans le cas d'un lien
      $this->cheminImage = $chemin .'/'. $imgNomFichier;
      $this->cheminRepParent = $chemin;
      $this->urlImage = 'http://'. $_SERVER['HTTP_HOST'] .'/'. $this->cheminImage;
      $this->info = pathinfo($imgNomFichier);
      $this->basename =  basename($imgNomFichier,'.'.$this->info['extension']);
      $this->extension = $this->info['extension'];
      $this->taille = getimagesize($this->cheminImage);
      $this->width = $this->taille[0];
      $this->height = $this->taille[1];
      $this->resolution = $this->width .'x'. $this->height;
      $this->poids = filesize($this->cheminImage);
      $cheminHD = $chemin .'/HD/'. $this->basename .'.pdf';
      if(file_exists($cheminHD) == true) {
         $this->cheminHD = $cheminHD;
         $this->cheminHDRelatif = 'HD/'. $this->basename .'.pdf';
      }
      else {
         $cheminHD = $chemin .'/HD/'. $this->basename .'.jpg';
         if(file_exists($cheminHD) == true) {
            $this->cheminHD = $cheminHD;
            $this->cheminHDRelatif = 'HD/'. $this->basename .'.jpg';
         }
         else {
            $cheminHD = $chemin .'/HD/'. $this->basename .'.png';
            if(file_exists($cheminHD) == true) {
               $this->cheminHD = $cheminHD;
               $this->cheminHDRelatif = 'HD/'. $this->basename .'.png';
            }
         }
      }
      $cheminSVG = $chemin .'/'. $this->basename .'.svg';
      if(file_exists($cheminSVG) == true) {
         $this->cheminSVG = $cheminSVG;
         $this->cheminSVGRelatif = $this->basename .'.svg';
      }
      $cheminTailles = $chemin .'/'. $this->basename .'--tailles';
      if(file_exists($cheminTailles) == true) {
         $this->cheminTailles = $cheminTailles;
         $this->cheminTaillesRelatif = $this->basename .'--tailles';
      }
   }

   /// ////////////////////////////////////////////////////
   /// ////////////////////////////////////////////////////
   public function codeAInclure($lien) {
      $code = '';
      if($lien != '') {
         $code .= '<a style="border:none;text-decoration:none;" title="'. $this->title .'" href="'. $lien .'">';
      }
      $code .= '<img style="border:none;" src="'. $this->urlImage .'" width="'. $this->width .'" height="'. $this->height .'" alt="'. $this->alt .'" />';
      if($lien != '') {
         $code .= '</a>';
      }
      return $code;
   }

   /// ////////////////////////////////////////////////////
   /// ////////////////////////////////////////////////////
   public function affiche() {
      ?>
      <img src="<?= $this->urlImage ?>" width="<?= $this->width ?>" height="<?= $this->height ?>" alt="<?= $this->alt ?>" />
      <?
   }
}

/// //////////////////////////////////////////////
/// ##############################################
/// ##############################################
/// ##############################################
/// //////////////////////////////////////////////
class BetterIndex {

   private $_repertoire = '';
   private $_imagesAAfficher;
   /** Tous les fichiers qu'ils soient des images à afficher ou non */
   private $_fichiers;
   private $_repertoires;
   private $_reste;
   private $maxFileSize = 1024000; /** 1000 * 1024 = 1000Ko; **/
   private $ini;
   private $_racine = ''; /** racine physique de l'installation **/
   private $_racineRelative = ''; /** racine relative à partir de la page demandée **/
   private $_titre = '';
   private $_typeAffichage = AFFICHAGE_NORMAL;

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function __construct($dir = '') {

      $this->ini = parse_ini_file('conf.ini');
      if($dir == '') {
         $dir = './';
      }
      $this->charge($dir);
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function repertoire() { return $this->_repertoire; }
   public function imagesAAfficher() { return $this->_imagesAAfficher; }
   public function fichiers() { return $this->_fichiers; }
   public function repertoires() { return $this->_repertoires; }
   public function reste() { return $this->_reste; }
   public function lienAFaire() { return (isset($this->ini['lien'])) ? $this->ini['lien'] : ''; }
   public function altPourImages() { return (isset($this->ini['alt'])) ? $this->ini['alt'] : ''; }
   public function titlePourImages() { return (isset($this->ini['title'])) ? $this->ini['title'] : ''; }
   public function texte() { return (isset($this->ini['texte'])) ? $this->ini['texte'] : ''; }
   public function bordure() { return (isset($this->ini['bordure'])) ? $this->ini['bordure'] : false; }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function charge($dir) {

      if (file_exists($dir) == false) {
         erreur(__METHOD__ .' : le répertoire à lister n\'existe pas : '. $dir);
         return;
      }
      $this->_repertoire = dir($dir);
      if($this->_repertoire === false) {
         erreur(__METHOD__ .' le fichier '. $dir .' n\'est pas un répertoire');
         return;
      }

      if(basename($dir) == 'HD') {
         $this->metTypeAffichage(AFFICHAGE_HD);
      }
      elseif(basename($dir) == 'evolution') {
         $this->metTypeAffichage(AFFICHAGE_EVOLUTION);
      }
      if(isset($_GET['typeAffichage']) == true) {
         $this->metTypeAffichage(constant($_GET['typeAffichage']));
      }

      /// Réinitialisation des tableau
      $this->_imgsAAfficher = array();
      $this->_reste = array();
      $this->_repertoires = array();

      if(file_exists($dir.'/conf.ini')) {

         $iniLocal = parse_ini_file($dir.'/conf.ini');
         // L'opérateur + retourne le tableau de droite auquel sont ajoutés
         // les éléments du tableau de gauche. Pour les clés présentes dans
         // les 2 tableaux, les éléments du tableau de gauche seront utilisés
         // alors que les éléments correspondants dans le tableau de droite
         // seront ignorés.
         $this->ini =  $iniLocal + $this->ini;
         if(isset($this->ini['typeAffichage'])) {
            $this->metTypeAffichage(constant($this->ini['typeAffichage']));
         }
      }

      while(($fichier = $this->repertoire()->read()) !== false) {
         $cheminFichier = $dir .'/'. $fichier;

         if($fichier == '.'
            || $fichier == '..'
            || $fichier == 'conf.ini'
            || $fichier == '.htaccess'
            || $fichier == 'bin'
            || $fichier == 'robots.txt'
            || $fichier == 'favicon.ico'
            || $fichier == 'anciens-fichiers'
            || $fichier == 'AAA-COMMENT-CA-MARCHE'
            || preg_match('/.*--tailles/', $fichier)
            || preg_match('/.*~/', $fichier)
            || preg_match('/.*\.backup/i', $fichier)
            || preg_match('/.*\.webprj/i', $fichier)
            || preg_match('/.*\.session/i', $fichier)
            || preg_match('/.*\.css/i', $fichier)
            || preg_match('/.*\.php/i', $fichier)) {
            continue;
         }

         if(is_dir($cheminFichier) == true) {
            $this->_repertoires[] = $fichier.'/';
            continue;
         }

         /** Tous les fichiers qu'ils soient des images à afficher ou non **/
         $this->_fichiers[] = $fichier;
         
         if($this->typeAffichage() == AFFICHAGE_HD) {
            $this->_reste[] = $fichier;
            continue;
         }
         if(strtolower(substr($fichier, -4)) == '.png') {
            if(file_exists(substr($cheminFichier, 0, -4).'.jpg') == false
                  && filesize($cheminFichier) < $this->maxFileSize) {
               $this->ajouteImage($fichier, $dir, $this->altPourImages(), $this->titlePourImages());
            }
            else {
               $this->_reste[] = $fichier;
            }
            continue;
         }
         if(strtolower(substr($fichier, -4)) == '.jpg') {
            if(filesize($cheminFichier) < $this->maxFileSize) {
               $this->ajouteImage($fichier, $dir, $this->altPourImages(), $this->titlePourImages());
            }
            else {
               $this->_reste[] = $fichier;
            }
            continue;
         }
         if(strtolower(substr($fichier, -4)) == '.gif') {
            if(filesize($cheminFichier) < $this->maxFileSize) {
               $this->ajouteImage($fichier, $dir, $this->altPourImages(), $this->titlePourImages());
            }
            else {
               $this->_reste[] = $fichier;
            }
            continue;
         }
         $this->_reste[] = $fichier;
      }
      $this->repertoire()->close();

      /// Retrouve la racine physique du site
      $this->_racine = substr(__FILE__ , 0, -10);

      /// Retrouve le chemin de la page demandée à partir de la racine physique du site
      $cheminAPartirDeRacineSite = substr($_SERVER['SCRIPT_FILENAME'], strlen($this->racine()));

      /// Calcule le chemin relatif pour arriver à la racine du site
      $this->_racineRelative = '';
      if($this->estALaRacine() == false) {
         $cheminExplose = explode('/', $this->repertoire()->path);
         $nombreDeNiveaux = count($cheminExplose);
         $this->_racineRelative = '';
         for($niveau=0; $niveau<$nombreDeNiveaux; $niveau++) {
            $this->_racineRelative .= '../';
         }
      }

      /// titre
      $titre = 'Visuels L214';
      if($this->estALaRacine() == false) {
         $titre = $this->repertoire()->path .' | '. $titre;
      }
      $this->metTitre($titre);
   }

   /// /////////////////////////////////////////////////////
   /// /////////////////////////////////////////////////////
   function racine() { return $this->_racine; }

   /// /////////////////////////////////////////////////////
   /// /////////////////////////////////////////////////////
   function estALaRacine() { return ($this->repertoire()->path == './'); }

   /// /////////////////////////////////////////////////////
   /// /////////////////////////////////////////////////////
   function racineRelative() { return $this->_racineRelative; }

   /// /////////////////////////////////////////////////////
   /// /////////////////////////////////////////////////////
   function titre() { return $this->_titre; }
   function metTitre($titre) { $this->_titre = $titre; }

   /// /////////////////////////////////////////////////////
   /// /////////////////////////////////////////////////////
   function typeAffichage() { return $this->_typeAffichage; }
   function metTypeAffichage($typeAffichage) { $this->_typeAffichage = $typeAffichage; }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function ajouteImage($imageNomFichier, $chemin, $alt, $title) {

      $this->_imagesAAfficher[] = new BetterImage($imageNomFichier, $chemin, $alt, $title);
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function cheminRelatifParent($niveau) {

      $cheum = '';
      for($i=0; $i<$niveau; $i++){
         $cheum .= '../';
      }
      return $cheum;
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function affiche() {

      sort($this->_repertoires);
      /// Ordre des image en fonction du type d'affichage
      if(count($this->imagesAAfficher()) != 0) {
         /// Si un affiche est défini dans le conf.ini du répertoire
         if(isset($this->ini['ordre']) == true) {
            /// Fonction de comparaison dans l'ordre de l'image
            /// qui pèse le moins à celle qui pèse le plus
            $imagesEnOrdre = array();
            foreach($this->_imagesAAfficher as $bImg) {
               $pos = array_search($bImg->nom, $this->ini['ordre']);
               if($pos === false) {
                  $pos = count($imagesEnOrdre)+999999;
               }
               $imagesEnOrdre[$pos] = $bImg;
            }
            ksort($imagesEnOrdre);
            $this->_imagesAAfficher = $imagesEnOrdre;

         }
         /// AFFICHAGE_EVOLUTION
         elseif($this->typeAffichage() == AFFICHAGE_EVOLUTION) {
            rsort($this->_imagesAAfficher);
         }
         /// AFFICHAGE_CHOIX_TAILLE
         elseif($this->typeAffichage() == AFFICHAGE_CHOIX_TAILLE) {
            /// Fonction de comparaison dans l'ordre de l'image
            /// qui pèse le moins à celle qui pèse le plus
            function comparePoids($bImg1, $bImg2) {
               if ($bImg1->poids == $bImg2->poids) {
                  return 0;
               }
               return ($bImg1->poids < $bImg2->poids) ? -1 : 1;
            }
            uasort($this->_imagesAAfficher, 'comparePoids');
         }
         /// PAR DEFAUT
         else {
            sort($this->_imagesAAfficher);
         }
      }
      sort($this->_reste);
      require('template.php');
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function cheminDeFer() {

      /// Si c'est la racine
      if($this->estALaRacine()) {
         return 'visuels';
      }

      /// Sinon écriture du chemin de fer
      $reps = explode('/', $this->repertoire()->path);
      $iRep = 0;
      $cheminDeFerHTML = '<a href="'. $this->cheminRelatifParent(count($reps)) .'">visuels</a> / ';
      foreach($reps as $rep) {
         $niveauDuRep = count($reps) - 1 - $iRep;
         $cheum = $this->cheminRelatifParent($niveauDuRep);
         if($cheum != '') {
            $cheminDeFerHTML .= '<a href="'. $cheum .'">'. $rep .'</a> / ';
         }
         else {
            $cheminDeFerHTML .= $rep;
         }
         $iRep++;
      }
      return $cheminDeFerHTML;
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function afficheRepertoires() {
      if(count($this->repertoires()) < 1) {
         return;
      }
      ?>
         <h2>Répertoires</h2>
         <ul class="reps">
				<?php foreach($this->repertoires() as $rep) : ?>
					<?
						if($rep == 'HD/') {
							$fichiersDuRep = '';
 							$repIndex = new BetterIndex($this->repertoire()->path .'/'. $rep);
 							foreach($repIndex->fichiers() as $fichier) {
 								if($fichiersDuRep != '') {
									$fichiersDuRep .= ' | ';
 								}
 								$fichiersDuRep .= $fichier;
 							}
						}
					?>
               <li>
						<a href="<?= $rep ?>">
							<?= $rep ?>
						</a>
							<? if($rep == 'HD/') : ?>
								<span class="fichiersDuRep"><?= $fichiersDuRep ?></span>
							<? endif; ?>
					</li>
            <?php endforeach; ?>
         </ul>
      <?php
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function afficheBannieres() {
      if(count($this->repertoires()) < 1) {
         return;
      }
      print '<h2>Sélectionnez votre bannière</h2>';
      foreach($this->imagesAAfficher() as $bImg) {
         $this->afficheBanniere($bImg);
      }
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function afficheBanniere($bImg) {

      $rep = $bImg->basename.'/';
      if(in_array($rep, $this->repertoires()) == false) {
//          erreur("BetterIndex::afficheBanniere : le répertoire <strong>$rep</strong> correspondant à la bannière <strong>$bImg->nom</strong> n'existe pas");
         $this->afficheImage($bImg);
         return;
      }
      ?>
         <div class="presentation-banniere">
            <a href="<?= $rep?>?typeAffichage=AFFICHAGE_CHOIX_TAILLE"><? $bImg->affiche(); ?> &larr;&nbsp;sélectionner </a>
         </div>
      <?
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function afficheImages() {
      if(count($this->imagesAAfficher()) != 0) {
         if($this->typeAffichage() == AFFICHAGE_CHOIX_TAILLE) {
            print "<h2>Choisir la taille et copier le code associé sur son site</h2>\n";
            print "<p style=\"margin-left:3em;\">Si le code ne fonctionne pas, <a style='text-decoration:underline;' href='http://www.l214.com/web/contact'>contactez sans hésiter notre webpianoteur</a>.</p>\n";
            print "<p style=\"margin-left:3em;\">Si vous avez besoin d'une taille spécifique, pas de problème, nous pouvons vous la fournir, il suffit de <a style='text-decoration:underline;' href='http://www.l214.com/web/contact'>nous la demander&nbsp;&larr;</a>.</p>\n";
            print "<p style=\"margin-left:3em;\"><a href=\"../\">&larr; Retour</a></p>\n";
         }
         else {
            print "<h2>Images</h2>\n";
         }
         foreach($this->imagesAAfficher() as $bImg) {
            $this->afficheImage($bImg);
         }
      }
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function afficheImage($bImg) {

      ?>
      <div class="presentation-image">
         <? $numeroVersion = '';
            if($this->typeAffichage() == AFFICHAGE_EVOLUTION) {
               $finNomImage = substr($bImg->nom, -6,-4);
               if(is_numeric($finNomImage)) {
                  $numeroVersion = '<span class="numVersion">#'. $finNomImage .'</span>';
               }
            }
         ?>
         <div class="infos">
            <? /** Titre **/ ?>
            <h3 id="<?= $bImg->basename ?>"><?= $numeroVersion ?> <?= $bImg->nom ?></h3>

            <? /** Précisions **/ ?>
            <p class="precisions">
               <span class="poids"><?= $bImg->resolution ?> <?= $this->poidsLisible($bImg->poids) ?></span>
               <? if($bImg->cheminHD != ''): ?>
                  &nbsp; [<a href="<?= $bImg->cheminHDRelatif ?>">&rarr;&nbsp;Version&nbsp;HD</a> <?= $this->affichePoidsFichier($bImg->cheminHD) ?>]
               <? endif; ?>
               <? if($bImg->cheminSVG != ''): ?>
                  &nbsp; [<a href="<?= $bImg->cheminSVGRelatif ?>">&rarr;&nbsp;SVG modifiable</a> <?= $this->affichePoidsFichier($bImg->cheminSVG) ?>]
               <? endif; ?>
               <? if($bImg->cheminTailles != ''): ?>
                  &nbsp; [<a href="<?= $bImg->cheminTaillesRelatif ?>">&rarr;&nbsp;voir les différentes tailles</a>]
               <? endif; ?>
            </p>
         </div>
         <? /** Image **/ ?>
         <? $bordureClass = ($this->bordure() == true) ? ' bordure' : ''; ?>
         <p class="imgs <?= $bordureClass ?>">
            <? $bImg->affiche(); ?>
         </p>


         <? /** Code d'inclusion **/ ?>
         <div class="lien-associe">
            <? if($this->typeAffichage() == AFFICHAGE_CHOIX_TAILLE) : ?>
               Code à inclure dans votre site ou blog&nbsp;:
               <textarea cols="60" rows="3" onclick="this.focus();this.select()"><?= $bImg->codeAInclure($this->lienAFaire()) ?></textarea>

            <? elseif($this->typeAffichage() != AFFICHAGE_EVOLUTION) : ?>
               <a href="/telecharge.php?f=<?= $bImg->cheminImage ?>">Télécharger</a> -
               URL <input type="text" size="80" name="url-<?= $bImg->basename ?>" onclick="this.focus();this.select()" value="<?= $bImg->urlImage ?>" /><br />

               <? /** <a href="/<?= $bImg->cheminImage ?>">Voir seul</a> - **/ ?>
               Codes HTML avec lien : <input type="text" size="2" name="url-<?= $bImg->basename ?>" onclick="this.focus();this.select()" 
                      value="<?= htmlspecialchars($bImg->codeAInclure($this->lienAFaire())) ?>" />
               Sans lien : <input type="text" size="2" name="url-<?= $bImg->basename ?>" onclick="this.focus();this.select()" 
                      value="<?= htmlspecialchars($bImg->codeAInclure()) ?>" />
            <? endif; ?>
         </div>
      </div>
      <?
   }

   /// /////////////////////////////////////////////////////////
   ///
   /// /////////////////////////////////////////////////////////
   public function afficheReste() {
      if(count($this->reste()) != 0) {
         ?>
            <h2>Fichiers</h2>
            <ul class="fichiers">
               <?php foreach($this->reste() as $fichier) : ?>
                  <?php $cheminFichier = $this->repertoire()->path .'/'. $fichier; ?>
                  <li>
                     <a href="<?= $fichier ?>"><?= $fichier ?></a>
                     <?php $this->affichePoidsFichier($cheminFichier); ?>
                  </li>
               <?php endforeach; ?>
            </ul>
         <?php
      }
   }

   /// /////////////////////////////////////////////////////////
   /// Prend un fichier à peser en argument
   /// /////////////////////////////////////////////////////////
   public function affichePoidsFichier($cheminFichier) {

      $poids = filesize($cheminFichier);
      ?>
         <span class="poids"><?= $this->poidsLisible($poids) ?></span>
      <?
   }

   /// /////////////////////////////////////////////////////////
   ///  Prend un poids à afficher comme argument
   /// /////////////////////////////////////////////////////////
   public function poidsLisible($poids) {

      $unites = array(' o', ' Ko', ' Mo', ' Go', ' To');
      for ($i = 0; $poids >= 1024 && $i < 4; $i++) $poids /= 1024;
      $poids = round($poids, 1);
      $unite = $unites[$i];
      return $poids . $unite;
   }
}

$rep = (isset($_GET['rep'])) ? $_GET['rep'] : '';
$betterInder = new BetterIndex($rep);
if(isset($_GET['inclusionHTML']) == true) {
   $betterInder->afficheImages();
}
else {
   $betterInder->affiche();
}
?>