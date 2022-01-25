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
include ("modules/$ModPath/admin/pages.php");
include ("modules/$ModPath/annonce.conf.php");
include ("modules/$ModPath/lang/annonces-$language.php");
include ("header.php");
echo aff_langue($mess_acc);

// Purge
$obsol=time()-($obsol*30*86400);
$query="UPDATE ".$NPDS_Prefix."g_annonces SET en_ligne='2' WHERE (date<'$obsol')";
$succes= sql_query($query);

include ("modules/$ModPath/include/search_form.php");
settype($num_ann_apub_total,'integer');
settype($num_ann_total,'integer');
settype($content,'string');
settype($ibid,'integer');
$num_ann=array();
$result= sql_query("SELECT id_cat, COUNT(en_ligne) FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='1' GROUP BY id_cat");
while (list($cat, $count)= sql_fetch_row($result)) {
   $num_ann[$cat]=$count;
   $num_ann_total+=$count;
}
$result = sql_query("SELECT id_cat, COUNT(en_ligne) FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='0' GROUP BY id_cat");
while (list($cat, $count)= sql_fetch_row($result)) {
   $num_ann_apub[$cat]=$count;
   $num_ann_apub_total+=$count;
}
$result= sql_query("SELECT id_cat, COUNT(en_ligne) FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='2' GROUP BY id_cat");
while (list($cat, $count)= sql_fetch_row($result)) {
   $num_ann_archive[$cat]=$count;
}

echo '<p class="lead">'.ann_translate("Il y a").' <span class="badge bg-success">'.$num_ann_total.'</span> '.ann_translate("annonce(s)").' '.ann_translate("publiée(s)").'</p>';

$select= sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='0' ORDER BY id_cat");
while ($i= sql_fetch_assoc($select)) {
   $allcat=array('');
   $sous_content='';
   $id_cat=$i['id_cat'];
   $allcat[]=$i['id_cat'];
   $categorie=stripslashes($i['categorie']);
   $select2= sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='$id_cat' ORDER BY id_cat");
   $cumu_num_ann=0;
   $content .= '
   <div class="card my-3">
      <h6 class="card-header card-title bg-transparent border-primary lead">';

   while ($i2= sql_fetch_assoc($select2)) {
      $id_catx=$i2['id_cat'];
      $allcat[]=$i2['id_cat'];
      $categoriex=stripslashes($i2['categorie']);
      $sous_content .='
         <div class="mb-2 mx-4 my-1">
            <a data-bs-toggle="tooltip" data-bs-placement="top" title="'.ann_translate("Cliquer pour visualiser").'" href="modules.php?ModPath=npds_annonces&amp;ModStart=list_ann&amp;id_cat='.$id_catx.'&amp;categorie='.urlencode($categoriex).'&amp;num_ann=';
      if(array_key_exists($id_catx,$num_ann))
         $sous_content .= $num_ann[$id_catx];
      $sous_content .='"><span class="ms-3">'.$categoriex.'</span>
            </a>
            <span class="badge badge-pill bg-success float-end">';
      if(array_key_exists($id_catx, $num_ann))
         $sous_content .= $num_ann[$id_catx];
      $sous_content.= '</span>
         </div>';
      if(array_key_exists($id_catx, $num_ann))
         $cumu_num_ann += $num_ann[$id_catx];
   }

   $oo = trim(implode('|', $allcat),'|');
   if(array_key_exists($id_cat, $num_ann)) $ibid = $num_ann[$id_cat]+$cumu_num_ann; else $ibid = $cumu_num_ann;
   if($ibid!=0)
      $content .='
      <a data-bs-toggle="collapse" href="#catb3_'.$id_cat.'" aria-expanded="true" aria-controls="catb3_'.$id_cat.'"><i data-bs-toggle="tooltip" data-bs-placement="top" title="'.ann_translate("Cliquer pour déplier").'" class="toggle-icon fa fa-caret-down fa-lg me-2"></i></a>
      <a data-bs-toggle="tooltip" data-bs-placement="top" title="'.ann_translate("Cliquer pour visualiser").'" href="modules.php?ModPath=npds_annonces&amp;ModStart=list_ann&amp;id_cat='.$oo.'&amp;categorie='.$categorie.'&amp;num_ann='.$ibid.'">'.$categorie.'</a>
      <span class="badge badge-pill bg-success me-1 float-end">'.$ibid.'</span>';
   else 
      $content .= $categorie;
   if ($cumu_num_ann!=($ibid))

//   if ($cumu_num_ann!=($num_ann[$id_cat]+$cumu_num_ann))
      $sous_content .='
         <div class="mb-2 mx-4 my-1">
            <a data-bs-toggle="tooltip" data-bs-placement="top" title="'.ann_translate("Cliquer pour visualiser").'" href="modules.php?ModPath=npds_annonces&amp;ModStart=list_ann&amp;id_cat='.$id_cat.'&amp;categorie=&amp;num_ann='.(($num_ann[$id_cat]-$cumu_num_ann)+($cumu_num_ann)).'"><span class="ms-3">'.ann_translate("Autres").'</span></a>
            <span class="badge badge-pill bg-success float-end">'.(($num_ann[$id_cat]-$cumu_num_ann)+($cumu_num_ann)).'</span>
         </div>';
   $content .= '
      </h6>
      <div id="catb3_'.$id_cat.'" class="collapse pt-2" role="tabpanel" aria-labelledby="headingb3_'.$id_cat.'">';
   $content .= $sous_content;
   $content .='
      </div>
   </div>';
}
echo $content;
if (($admin) and $num_ann_apub_total>0)
   echo '
   <hr />
   <a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath='.$ModPath.'&amp;ModStart=admin/adm" class="btn btn-primary position-relative">
      <i class="fa fa-cogs fa-lg me-2" aria-hidden="true"></i>'.ann_translate("annonce(s) à valider").'
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
       '.$num_ann_apub_total.'
       <span class="visually-hidden">'.ann_translate("annonce(s) à valider").'</span>
     </span>
   </a>';
include ("footer.php");
?>