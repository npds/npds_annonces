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

if ($title=='') $title="[french]Petites Annonces[/french][english]Offers[/english][spanish]Anuncios[/spanish][german]Stellenanzeigen[/german][chinese]&#x5E7F;&#x544A;[/chinese]";
$title = aff_langue($title);
$result = sql_query("SELECT id_cat, COUNT(en_ligne) FROM ".$NPDS_Prefix."g_annonces WHERE en_ligne='1' GROUP BY id_cat");
settype($num_ann_total,'integer');
$num_ann=array();
while (list($cat, $count) = sql_fetch_row($result)) {
   $num_ann[$cat]=$count;
   $num_ann_total+=$count;
}
$content = '
<p class="lead"><span class="badge rounded-pill bg-secondary float-end">'.$num_ann_total.'</span> [french]Annonce(s) publiée(s)[/french][english]Offer(s) published[/english][spanish]Anuncios publicados[/spanish][german]Ver&#xF6;ffentlichte Anzeigen[/german][chinese]&#x520A;&#x767B;&#x5E7F;&#x544A;[/chinese]</p>
<p class=""><a href="modules.php?ModPath=npds_annonces&amp;ModStart=index" class="btn btn-outline-primary btn-sm me-2">[french]Consulter[/french][english]Consult[/english][spanish]Consultar[/spanish][german]Konsultieren[/german][chinese]&#x534F;&#x5546;[/chinese]</a>';
if ($user)
   $content .=' <a href="modules.php?ModPath=npds_annonces&amp;ModStart=annonce_form" class="btn btn-outline-primary btn-sm">[french]Ajouter[/french] [english]Add[/english]</a>';
$content .='
   </p>';
if ($admin) 
   $content .='
   <div class="mt-2 text-end">
      <a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=npds_annonces&amp;ModStart=admin/adm" data-bs-toggle="tooltip" title="[french]Administration[/french][english]Administration[/english][spanish]Administraci&#xF3;n[/spanish][german]Verwaltung[/german][chinese]&#x884C;&#x653F;[/chinese]"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i></a>
   </div>';
$content = aff_langue($content);
?>