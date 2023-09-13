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
$ModPath='npds_annonces';

include ("modules/$ModPath/annonce.conf.php");
settype($num_ann_total,'integer');

if ($title=='') $title ='[french]Petites Annonces[/french][english]Offers[/english][spanish]Anuncios[/spanish][german]Stellenanzeigen[/german][chinese]&#x5E7F;&#x544A;[/chinese]';
$title = aff_langue("$title");

$num_ann=array();
$result = sql_query("SELECT id_cat, COUNT(en_ligne) FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='1' GROUP BY id_cat");
while (list($cat, $count) = sql_fetch_row($result)) {
   $num_ann[$cat]=$count;
   $num_ann_total+=$count;
}
$content = '<p class="lead"><span class="badge rounded-pill bg-secondary float-end">'.$num_ann_total.'</span> [french]Annonce(s) publiée(s)[/french][english]Offer(s) published[/english][spanish]Anuncios publicados[/spanish][german]Ver&#xF6;ffentlichte Anzeigen[/german][chinese]&#x520A;&#x767B;&#x5E7F;&#x544A;[/chinese]</p>';
$select = sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='0' ORDER BY id_cat");
while ($i= sql_fetch_assoc($select)) {
   $allcat=array('');
   $sous_content='';
   $id_cat=$i['id_cat'];
   $allcat[]=$i['id_cat'];
   $categorie=stripslashes($i['categorie']);
   $select2=sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='$id_cat' ORDER BY id_cat");
   $cumu_num_ann=0;
   $content .= '
   <div class="card my-2">
      <div class="card-header card-title bg-transparent px-1">';
   while ($i2=sql_fetch_array($select2)) {
      $id_catx=$i2['id_cat'];
      $allcat[]=$i2['id_cat'];
      $categoriex=stripslashes($i2['categorie']);

      $sous_content .='
         <div class="row g-0 ms-1 me-2 my-1">
            <div class="col-11">';
      if(array_key_exists($id_catx,$num_ann))
         $sous_content .='
               <a href="modules.php?ModPath=npds_annonces&amp;ModStart=list_ann&amp;id_cat='.$id_catx.'&amp;categorie='.urlencode($categoriex).'&amp;num_ann='.$num_ann[$id_catx].'">
                  <div class="n-ellipses"><span>'.$categoriex.'</span></div>
               </a>';
      else $sous_content .='
               <div class="n-ellipses">'.$categoriex.'</div>';
      $sous_content .='
            </div>
            <div class="col-1">
               <span class="badge rounded-pill bg-secondary">';
      if(array_key_exists($id_catx, $num_ann))
         $sous_content .= $num_ann[$id_catx];
      $sous_content.= '</span>
            </div>
         </div>';
      if(array_key_exists($id_catx, $num_ann))
         $cumu_num_ann += $num_ann[$id_catx];
   }

   $oo = trim(implode('|', $allcat),'|');
   if(array_key_exists($id_cat, $num_ann)) $ibid = $num_ann[$id_cat]+$cumu_num_ann; else $ibid = $cumu_num_ann;
   if($ibid!=0)
      $content .='
               <div class="row g-0 ms-1 me-2 my-1">
                  <div class="col-11 n-ellipses">
                     <a data-bs-toggle="collapse" href="#catbb3_'.$id_cat.'" aria-expanded="true" aria-controls="catbb3_'.$id_cat.'"><i data-bs-toggle="tooltip" data-bs-placement="right" title="[french]Cliquer pour déplier[/french][english]Click to expand[/english][spanish]Haga clic para expandir[/spanish][german]Zum Erweitern klicken[/german][chinese]&#x5355;&#x51FB;&#x4EE5;&#x5C55;&#x5F00;[/chinese]" class="toggle-icon fa fa-caret-down fa-2x me-2 align-middle"></i></a>
                     <a href="modules.php?ModPath=npds_annonces&amp;ModStart=list_ann&amp;id_cat='.$oo.'&amp;categorie='.$categorie.'&amp;num_ann='.$ibid.'"><span>'.$categorie.'</span></a></div>
                  <div class="col-1 text-end">
                     <span class="badge rounded-pill bg-secondary">'.$ibid.'</span>
                  </div>
               </div>';
   else 
      $content .= '<div class="n-ellipses">'.$categorie.'</div>';
   if ($cumu_num_ann!=($ibid))
      if ($cumu_num_ann!=($num_ann[$id_cat]+$cumu_num_ann))
         $sous_content .='
         <div class="row g-0 mb-2 ms-1 me-2 my-1">
            <div class="col-11">
               <a href="modules.php?ModPath=npds_annonces&amp;ModStart=list_ann&amp;id_cat='.$id_cat.'&amp;categorie='.$categorie.'&amp;num_ann='.(($num_ann[$id_cat]-$cumu_num_ann)+($cumu_num_ann)).'"><span>[french]Autres[/french][english]Other[/english][spanish]Otro[/spanish][german]Ander[/german][chinese]&#x5176;&#x4ED6;[/chinese]</span></a>
            </div>
            <div class="col-1">
               <span class="badge rounded-pill bg-secondary">'.(($num_ann[$id_cat]-$cumu_num_ann)+($cumu_num_ann)).'</span>
            </div>
         </div>';
   $content .= '
      </div>
      <div id="catbb3_'.$id_cat.'" class="collapse pt-1" role="tabpanel" aria-labelledby="headingb3_'.$id_cat.'">';
   $content .= $sous_content;
   $content .='
      </div>
   </div>';
}
$content .='
   <p class="text-center mt-3"><a href="modules.php?ModPath=npds_annonces&amp;ModStart=index" class="btn btn-outline-primary btn-sm me-2">[french]Consulter[/french][english]Consult[/english][spanish]Consultar[/spanish][german]Konsultieren[/german][chinese]&#x534F;&#x5546;[/chinese]</a>';

if ($user)
   $content .=' <a href="modules.php?ModPath=npds_annonces&amp;ModStart=annonce_form" class="btn btn-outline-primary btn-sm">[french]Ajouter[/french][english]Add[/english][spanish]A&#xF1;adir[/spanish][german]Hinzuf&#xFC;gen[/german][chinese]&#x589E;&#x52A0;[/chinese]</a>';
$content .='
   </p>';
if ($admin) 
   $content .='
   <div class="mt-2 text-end">
      <a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=npds_annonces&amp;ModStart=admin/adm" data-bs-toggle="tooltip" title="[french]Administration[/french][english]Administration[/english][spanish]Administraci&#xF3;n[/spanish][german]Verwaltung[/german][chinese]&#x884C;&#x653F;[/chinese]"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i></a>
   </div>';
$content = aff_langue($content);
?>