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

// cartouche de sécurité ==> requis !!
if (!strstr($PHP_SELF,'admin.php')) Access_Error();
if (strstr($ModPath,'..') || strstr($ModStart,'..') || stristr($ModPath, 'script') || stristr($ModPath, 'cookie') || stristr($ModPath, 'iframe') || stristr($ModPath, 'applet') || stristr($ModPath, 'object') || stristr($ModPath, 'meta') || stristr($ModStart, 'script') || stristr($ModStart, 'cookie') || stristr($ModStart, 'iframe') || stristr($ModStart, 'applet') || stristr($ModStart, 'object') || stristr($ModStart, 'meta'))
   die();

$f_meta_nom ='npds_annonces';
//==> controle droit
admindroits($aid,$f_meta_nom);
//<== controle droit
global $NPDS_Prefix;

include ("modules/$ModPath/annonce.conf.php");
include ("modules/$ModPath/lang/annonces-$language.php");

   $result= sql_query("SELECT id FROM $NPDS_Prefix.g_annonces WHERE en_ligne='1'");
   $num_ann_total= sql_num_rows($result);
   $result= sql_query("SELECT id FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='0'");
   $num_ann_apub_total= sql_num_rows($result);
   $result= sql_query("SELECT id FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='2'");
   $num_ann_archive_total= sql_num_rows($result);

   GraphicAdmin($hlpfile);

   echo '
   <div class="border rounded p-3">
      <h2><img class="align-middle me-2" src="modules/npds_annonces/npds_annonces.png" alt="icon_npds_annonces"><a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=npds_annonces&amp;ModStart=admin/adm">Annonces</a></h2>
      <hr />
      <p class="lead">'.ann_translate("Annonces en ligne").'<span class="badge bg-success float-end">'.$num_ann_total.'</span></p>
      <p class="lead">'.ann_translate("Annonces à valider").'<span class="badge bg-danger float-end">'.$num_ann_apub_total.'</span></p>
      <p class="lead">'.ann_translate("Annonces archivées").'<span class="badge bg-secondary float-end">'.$num_ann_archive_total.'</span></p>
      <hr />
      <p><a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath='.$ModPath.'&amp;ModStart=admin/adm_cat" class="btn btn-outline-primary btn-sm">'.ann_translate("Ajouter ou modifier une catégorie").'</a></p>
      <hr />';

   $num_ann_apub=array();$num_ann_archive=array();$num_ann=array();
   $result= sql_query("SELECT id_cat, COUNT(en_ligne) FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='1' GROUP BY id_cat");
   while (list($cat, $count)= sql_fetch_row($result)) {
      $num_ann[$cat]=$count;
   }
   $result= sql_query("SELECT id_cat, COUNT(en_ligne) FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='0' GROUP BY id_cat");
   while (list($cat, $count)= sql_fetch_row($result)) {
      $num_ann_apub[$cat]=$count;
   }
   $result= sql_query("SELECT id_cat, COUNT(en_ligne) FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='2' GROUP BY id_cat");
   while (list($cat, $count)= sql_fetch_row($result)) {
      $num_ann_archive[$cat]=$count;
   }
   $content='';
   $select= sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='0' ORDER BY id_cat");
   while ($i= sql_fetch_assoc($select)) {
      $allcat=array('');
      $sous_content='';
      $id_cat=$i['id_cat'];
      $allcat[]=$i['id_cat'];
      $categorie=stripslashes($i['categorie']);
      $select2= sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='$id_cat' ORDER BY id_cat");
      $cumu_num_ann=0;$cumu_num_ann_apub=0;$cumu_num_ann_archive=0;
      $content .= '
      <div class="card my-3">
         <div class="card-header" role="tab" id="">
            <h5 class="mb-0">
           <a data-bs-toggle="collapse" href="#cat_'.$id_cat.'" aria-expanded="true" aria-controls="cat_'.$id_cat.'"><i data-bs-toggle="tooltip" data-bs-placement="top" title="'.ann_translate("Cliquer pour déplier").'" class="toggle-icon fa fa-caret-down fa-lg me-2"></i></a>';
      while ($i2= sql_fetch_assoc($select2)) {
         $id_catx=$i2['id_cat'];
         $allcat[]=$i2['id_cat'];
         $categoriex=stripslashes($i2['categorie']);
         if (array_key_exists($id_catx,$num_ann))
            $cumu_num_ann += $num_ann[$id_catx];
         if (array_key_exists($id_catx,$num_ann_apub))
            $cumu_num_ann_apub += $num_ann_apub[$id_catx];
         if (array_key_exists($id_catx,$num_ann_archive))
            $cumu_num_ann_archive += $num_ann_archive[$id_catx];
         $sous_content .='
            <div class="my-2 mx-3 px-1">
               <h5>
                  <a data-bs-toggle="tooltip" data-bs-placement="top" title="'.ann_translate("Cliquer pour administrer").'" href="admin.php?op=Extend-Admin-SubModule&amp;ModPath='.$ModPath.'&amp;ModStart=admin/adm_ann&amp;id_cat='.$id_catx.'"><span class="ms-1 ps-4">'.$categoriex.'</span></a>
                  <span class="float-end">
                     <span data-bs-toggle="tooltip" data-bs-placement="left" title="'.ann_translate("Annonces archivées dans la sous-catégorie").'" class="badge bg-secondary me-2">';
         if (array_key_exists($id_catx,$num_ann_archive))
            $sous_content .= $num_ann_archive[$id_catx];
         $sous_content .='</span>
                     <span data-bs-toggle="tooltip" data-bs-placement="left" title="'.ann_translate("Annonces à valider dans la sous-catégorie").'" class="badge bg-danger me-2">';
         if (array_key_exists($id_catx,$num_ann_apub))
            $sous_content .= $num_ann_apub[$id_catx];
         $sous_content .='</span>
                     <span data-bs-toggle="tooltip" data-bs-placement="left" title="'.ann_translate("Annonces en ligne dans la sous-catégorie").'" class="badge bg-success">';
         if (array_key_exists($id_catx,$num_ann))
            $sous_content .= $num_ann[$id_catx];
         $sous_content .='</span>
                  </span>
               </h5>
            </div>';
      }
      $oo = trim(implode('|', $allcat),'|');
      if (array_key_exists($id_cat,$num_ann) or array_key_exists($id_cat,$num_ann_apub) or array_key_exists($id_cat,$num_ann_archive)){
//      var_dump($num_ann_apub);
//      if ($cumu_num_ann!=($num_ann[$id_cat]+$cumu_num_ann) or $cumu_num_ann_apub!=($num_ann_apub[$id_cat]+$cumu_num_ann_apub))
         $sous_content .='
            <div class="my-2 mx-3 px-1">
               <h5>
                  <a data-bs-toggle="tooltip" data-bs-placement="top" title="'.ann_translate("Cliquer pour administrer").'" href="admin.php?op=Extend-Admin-SubModule&amp;ModPath='.$ModPath.'&amp;ModStart=admin/adm_ann&amp;id_cat='.$id_cat.'"><span class="ms-1 ps-4">'.ann_translate("Autres").'</span></a>
                  <span class=" float-end">
                     <span class="badge bg-danger me-2">';
         if (array_key_exists($id_cat,$num_ann_apub))
            $sous_content .=
                     (($num_ann_apub[$id_cat]-$cumu_num_ann_apub)+($cumu_num_ann_apub));
         $sous_content .='</span>
                     <span class="badge bg-success">';
         if (array_key_exists($id_cat,$num_ann))
            $sous_content .= (($num_ann[$id_cat]-$cumu_num_ann)+($cumu_num_ann));
         else
            $sous_content .= $cumu_num_ann;
         $sous_content .= '</span>
                  </span>
               </h5>
            </div>';
            }
      $content .= '
            <a data-bs-toggle="tooltip" data-bs-placement="top" title="'.ann_translate("Cliquer pour administrer").'" href="admin.php?op=Extend-Admin-SubModule&amp;ModPath='.$ModPath.'&amp;ModStart=admin/adm_ann&amp;id_cat='.$oo.'">'.$categorie.'</a>
            <span class=" float-end">
               <span data-bs-toggle="tooltip" data-bs-placement="left" title="'.ann_translate("Annonces archivées dans la catégorie").'" class="badge bg-secondary me-2">';
      if (array_key_exists($id_cat,$num_ann_archive))
         $content .= ($num_ann_archive[$id_cat]+$cumu_num_ann_archive);
      else
         $content .= $cumu_num_ann_archive;
      $content .= '</span>
               <span data-bs-toggle="tooltip" data-bs-placement="left" title="'.ann_translate("Annonces à valider dans la catégorie").'" class="badge bg-danger me-2">';
      if (array_key_exists($id_cat,$num_ann_apub))
         $content .= ($num_ann_apub[$id_cat]+$cumu_num_ann_apub);
      else
         $content .= $cumu_num_ann_apub;
      $content .= '</span>
               <span data-bs-toggle="tooltip" data-bs-placement="left" title="'.ann_translate("Annonces en ligne dans la catégorie").'" class="badge bg-success">';
      if (array_key_exists($id_cat,$num_ann))
         $content .= ($num_ann[$id_cat]+$cumu_num_ann);
      else
         $content .= $cumu_num_ann;
      $content .= '</span>
            </span>
         </h5>
         </div>
         <div id="cat_'.$id_cat.'" class="collapse" role="tabpanel" aria-labelledby="heading_'.$id_cat.'">';
   $content .= $sous_content;
   $content .='
         </div>
      </div>';
   }
   echo $content;
   echo '
      <div><p><a href="modules.php?ModPath='.$ModPath.'&amp;ModStart=index" class="btn btn-outline-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> '.ann_translate("P.A en ligne").'</a></p></div>
   </div>';
   include ("footer.php");
?>