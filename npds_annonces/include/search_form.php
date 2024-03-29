<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*                                                                      */
/* Module npds_annonces 3.1                                             */
/*                                                                      */
/*                                                                      */
/* Basé sur gadjo_annonces v 1.2 - Adaptation 2008 par Jireck et lopez  */
/* MAJ conformité XHTML pour REvolution 10.02 par jpb/phr en mars 2010  */
/* MAJ Dev - 2011                                                       */
/* Changement de nom du module version Rev16 par jpb/phr janv 2017      */
/************************************************************************/

// For More security
if (!stristr($_SERVER['PHP_SELF'],'modules.php')) die();
if (strstr($ModPath,'..') || strstr($ModStart,'..') || stristr($ModPath, 'script') || stristr($ModPath, 'cookie') || stristr($ModPath, 'iframe') || stristr($ModPath, 'applet') || stristr($ModPath, 'object') || stristr($ModPath, 'meta') || stristr($ModStart, 'script') || stristr($ModStart, 'cookie') || stristr($ModStart, 'iframe') || stristr($ModStart, 'applet') || stristr($ModStart, 'object') || stristr($ModStart, 'meta'))
   die();
// For More security

global $language, $NPDS_Prefix;
settype($search,'string');
$cl_i =''; $cl_a=''; $cl_m='';

if($ModStart=='index') $cl_i='active';
else if($ModStart=='modif_ann') $cl_m='active';
else if($ModStart=='annonce_form') $cl_a='active';

echo '
   <div class="card mb-3 border-0">
      <nav class="navbar navbar-expand-lg navbar-dark bg-primary rounded">
         <div class="container-fluid">
           <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuannonce" aria-controls="menuannonce" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
           </button>
           <div class="collapse navbar-collapse" id="menuannonce">
             <ul class="navbar-nav me-auto">
               <li class="nav-item '.$cl_i.'">
                 <a class="nav-link" href="modules.php?ModPath='.$ModPath.'&amp;ModStart=index">'.ann_translate("Annonce").'</a>
               </li>';
   if ($user)
      echo '
               <li class="nav-item">
                 <a class="nav-link '.$cl_a.'" href="modules.php?ModPath='.$ModPath.'&amp;ModStart=annonce_form">'.ann_translate("Passer P.A").'</a>
               </li>
               <li class="nav-item">
                 <a class="nav-link '.$cl_m.'" href="modules.php?ModPath='.$ModPath.'&amp;ModStart=modif_ann">'.ann_translate("Gérer P.A").'</a>
               </li>';
   echo '
             </ul>
             <form id="annonsearch" method="post" action="modules.php" class="d-flex my-2 my-lg-0">
               <input type="hidden" name="ModPath" value="'.$ModPath.'" />
               <input type="hidden" name="ModStart" value="search" />
               <input class="form-control me-sm-2" type="search" placeholder="'.ann_translate("Rechercher dans les annonces").'" aria-label="Search" />
               <button class="btn btn-outline-light my-2 my-sm-0" type="submit" name="action">'.ann_translate("Valider").'</button>
             </form>
           </div>
     </div>
   </nav>';
   if (!$user)
      echo '
      <div class="blockquote my-3">
         <p class="lead">'.ann_translate("Pour passer ou gérer vos annonces vous devez être membre inscrit connecté").' <br /><a class="btn btn-outline-primary mt-2" href="user.php">Connexion</a></p>
      </div>';
echo '
   </div>';
?>