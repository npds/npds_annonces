<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/*                                                                      */
/* Module npds_annonces 3.0                                             */
/*                                                                      */
/*                                                                      */
/* Basé sur gadjo_annonces v 1.2 - Adaptation 2008 par Jireck et lopez  */
/* MAJ conformité XHTML pour REvolution 10.02 par jpb/phr en mars 2010  */
/* MAJ Dev - 2011                                                       */
/* Changement de nom du module version Rev16 par jpb/phr janv 2017      */
/************************************************************************/

// For More security
if (!strstr($PHP_SELF,'admin.php')) Access_Error();
if (strstr($ModPath,'..') || strstr($ModStart,'..') || stristr($ModPath, 'script') || stristr($ModPath, 'cookie') || stristr($ModPath, 'iframe') || stristr($ModPath, 'applet') || stristr($ModPath, 'object') || stristr($ModPath, 'meta') || stristr($ModStart, 'script') || stristr($ModStart, 'cookie') || stristr($ModStart, 'iframe') || stristr($ModStart, 'applet') || stristr($ModStart, 'object') || stristr($ModStart, 'meta'))
   die();
// For More security

$f_meta_nom ='npds_annonces';
//==> controle droit
admindroits($aid,$f_meta_nom);
//<== controle droit

include ("modules/$ModPath/annonce.conf.php");
include ("modules/$ModPath/lang/annonces-$language.php");
settype($action,'string');
   GraphicAdmin($hlpfile);

   echo '
   <div id="adm_men">
      <p class="lead">'.aff_langue($mess_acc).'</p>
      <h3>'.ann_translate("Administration des catégories et sous-catégories").'</h3>
      <hr />';

// Categories
if ($action=='ajouter') {
   if ($categorie!='') {
      $query="INSERT INTO $table_cat (id_cat,id_cat2,categorie) VALUES ('','0','".addslashes($categorie)."')";
      $result= sql_query($query);
   } elseif ($categorieSCAT!="") {
      $query="INSERT INTO $table_cat (id_cat,id_cat2,categorie) VALUES ('',$id_catSCAT,'".addslashes($categorieSCAT)."')";
      $result= sql_query($query);
   }
} elseif ($action=='supprimer') {
   if ($id_cat) {
// annonces
      $query="DELETE FROM $table_annonces WHERE id_cat='$id_cat'";
      $succes= sql_query($query);
      $query="SELECT id_cat FROM $table_cat WHERE id_cat2='$id_cat'";
      $succes= sql_query($query);
      while(list($id_Scat)= sql_fetch_row($succes)) {
         $query="DELETE FROM $table_annonces WHERE id_cat='$id_Scat'";
         $succes2= sql_query($query);
      }
// categories
      $query="DELETE FROM $table_cat WHERE id_cat2='$id_cat'";
      $succes= sql_query($query);
      $query="DELETE FROM $table_cat WHERE id_cat='$id_cat'";
      $succes= sql_query($query);
   } elseif ($id_catSCAT) {
      $query="DELETE FROM $table_annonces WHERE id_cat='$id_catSCAT'";
      $succes= sql_query($query);
      $query="DELETE FROM $table_cat WHERE id_cat='$id_catSCAT'";
      $succes= sql_query($query);
   }
} elseif ($action=='Modifier') {
   if (isset($categorie)) {
      $query="UPDATE $table_cat SET categorie='".addslashes($categorie)."' WHERE id_cat=$id_cat";
      $succes= sql_query($query);
   } elseif (isset($categorieSCAT)) {
      $query="UPDATE $table_cat SET categorie='".addslashes($categorieSCAT)."' WHERE id_cat=$id_catSCAT";
      $succes= sql_query($query);
   }
}

echo '
<form method="post" action="admin.php">
   <input type="hidden" name="op" value="Extend-Admin-SubModule" />
   <input type="hidden" name="ModPath" value="'.$ModPath.'" />
   <input type="hidden" name="ModStart" value="admin/adm_cat" />
   <div class="form-group row justify-content-end">
      <label for="" class="col-sm-4 form-control-label"><i class="fa fa-plus fa-lg" aria-hidden="true"></i> '.ann_translate("Ajouter une catégorie").'</label>
      <div class="col-sm-8">
         <input type="text" name="categorie" class="form-control">
      </div>
   </div>
   <div class="form-group row justify-content-end">
      <div class="col-sm-offset-4 col-sm-8">
         <button name="action" class="btn btn-outline-primary" type="submit" value="ajouter"><i class="fa fa-check" aria-hidden="true"></i> '.ann_translate("Valider").'</button>
      </div>
   </div>
   <hr />
   <div class="form-group row justify-content-end">
      <label for="" class="col-sm-4 form-control-label"><i class="fa fa-plus fa-lg" aria-hidden="true"></i> '.ann_translate("Ajouter une sous-catégorie dans").'</label>
      <div class="col-sm-8">
         <select class="form-control custom-select" name="id_catSCAT">';
$query_list="SELECT * FROM $table_cat WHERE id_cat2='0' ORDER BY id_cat";
$list= sql_query($query_list);
settype($id_catSCAT,'string');
while($e= sql_fetch_assoc($list)) {
   $categorie=$e['categorie'];
   $id_cat=$e['id_cat'];
   echo '
               <option value="'.$id_cat.'"';
   if ($id_cat==$id_catSCAT) echo 'selected="selected"';
   echo '>';
   echo stripslashes($categorie);
   echo '</option>';
}
echo '
            </select>
         </div>
      </div>
      <div class="form-group row justify-content-end">
         <div class="col-sm-8">
            <input type="text" class="form-control" name="categorieSCAT">
         </div>
      </div>
      <div class="form-group row justify-content-end">
         <div class="col-sm-offset-4 col-sm-8">
            <button name="action" class="btn btn-outline-primary" type="submit" value="ajouter"><i class="fa fa-check" aria-hidden="true"></i> '.ann_translate("Valider").'</button>
         </div>
      </div>
   </form>
   <hr />
   <h4 class="my-3">'.ann_translate("Arborescense en ligne").'</h4>';
$select= sql_query("SELECT id_cat, categorie FROM $table_cat WHERE id_cat2='0' ORDER BY id_cat");
$count= sql_num_rows($select);
if (!$count)
   echo '<div class="alert alert-danger">'.ann_translate("Aucune catégorie pour le moment").'</div>';
$cj=0;
while ($i= sql_fetch_assoc($select)) {
   $id_cat=$i['id_cat'];
   $categorie=stripslashes($i['categorie']);
   echo '
   <form method="post" action="admin.php">
      <input type="hidden" name="op" value="Extend-Admin-SubModule" />
      <input type="hidden" name="ModPath" value="'.$ModPath.'" />
      <input type="hidden" name="ModStart" value="admin/adm_cat" />
      <input type="hidden" name="id_cat" value="'.$id_cat.'" />
      <div class="form-group row">
         <label for="categorie'.$cj.'" class="col-sm-2 col-form-label m-y-1">'.ann_translate("Catégorie").'</label>
         <div class="col-sm-6">
            <input type="text" id="categorie'.$cj.'" name="categorie" class="form-control m-y-1" value="'.$categorie.'" required="required"/>
         </div>
         <div class="col-sm-2">
            <input type="submit" name="action" class="btn btn-outline-primary form-control m-y-1" value="Modifier" />
         </div>
         <div class="col-sm-2">
         <button type="submit" class="btn btn-outline-danger form-control m-y-1" name="action" value="supprimer"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
         </div>
      </div>
   </form>';

   $select2= sql_query("SELECT id_cat, categorie FROM $table_cat WHERE id_cat2='".$i['id_cat']."' ORDER BY id_cat");
   $scj=0;
   while ($i2= sql_fetch_assoc($select2)) {
      echo '
   <form method="post" action="admin.php">
      <input type="hidden" name="op" value="Extend-Admin-SubModule" />
      <input type="hidden" name="ModPath" value="'.$ModPath.'" />
      <input type="hidden" name="ModStart" value="admin/adm_cat" />
      <input type="hidden" name="id_catSCAT" value="'.$i2['id_cat'].'" />
      <div class="form-group row">
         <label for="categoriescat'.$scj.'" class="col-sm-2 col-form-label m-y-1"><em>'.ann_translate("Sous-catégorie").'</em></label>
         <div class="col-sm-6">
            <input type="text" class="form-control m-y-1" maxlength="55" id="categoriescat'.$scj.'" name="categorieSCAT" value="'.stripslashes($i2['categorie']).'" required="required" />
         </div>
         <div class="col-sm-2">
            <button class="btn btn-outline-primary form-control m-y-1" type="submit" name="action" value="Modifier">'.ann_translate("Modifier").'</button>
         </div>
         <div class="col-sm-2">
            <button type="submit" class="btn btn-outline-danger form-control m-y-1" name="action" value="supprimer"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
         </div>
      </div>
   </form>';
   $scj++;
   }
   $cj++;
}

   echo '
   <p><a class="btn btn-outline-primary btn-sm" role="button" href="admin.php?op=Extend-Admin-SubModule&amp;ModPath='.$ModPath.'&amp;ModStart=admin/adm"><i class="fa fa-home" aria-hidden="true"></i> '.ann_translate("Admin P.A").'</a></p>
   </div>';
   include ("footer.php");
?>