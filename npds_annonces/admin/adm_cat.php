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
   <div class="border rounded p-3">
      <h2><img class="align-middle me-2" src="modules/npds_annonces/npds_annonces.png" alt="icon_npds_annonces"><a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=npds_annonces&amp;ModStart=admin/adm">Annonces</a></h2>
      <hr />
      <h3>'.ann_translate("Administration des catégories et sous-catégories").'</h3>';

// Categories
if ($action=='ajouter') {
   if ($categorie!='') {
      $query="INSERT INTO ".$NPDS_Prefix."g_categories (id_cat,id_cat2,categorie) VALUES ('0','0','".addslashes($categorie)."')";
      $result= sql_query($query);
   } elseif ($categorieSCAT!="") {
      $query="INSERT INTO ".$NPDS_Prefix."g_categories (id_cat,id_cat2,categorie) VALUES ('0',$id_catSCAT,'".addslashes($categorieSCAT)."')";
      $result= sql_query($query);
   }
} elseif ($action=='supprimer') {
   if ($id_cat) {
// annonces
      $query="DELETE FROM $NPDS_Prefix.g_annonces WHERE id_cat='$id_cat'";
      $succes= sql_query($query);
      $query="SELECT id_cat FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='$id_cat'";
      $succes= sql_query($query);
      while(list($id_Scat)= sql_fetch_row($succes)) {
         $query="DELETE FROM $NPDS_Prefix.g_annonces WHERE id_cat='$id_Scat'";
         $succes2= sql_query($query);
      }
// categories
      $query="DELETE FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='$id_cat'";
      $succes= sql_query($query);
      $query="DELETE FROM ".$NPDS_Prefix."g_categories WHERE id_cat='$id_cat'";
      $succes= sql_query($query);
   } elseif ($id_catSCAT) {
      $query="DELETE FROM $NPDS_Prefix.g_annonces WHERE id_cat='$id_catSCAT'";
      $succes= sql_query($query);
      $query="DELETE FROM ".$NPDS_Prefix."g_categories WHERE id_cat='$id_catSCAT'";
      $succes= sql_query($query);
   }
} elseif ($action=='Modifier') {
   if (isset($categorie)) {
      $query="UPDATE ".$NPDS_Prefix."g_categories SET categorie='".addslashes($categorie)."' WHERE id_cat=$id_cat";
      $succes= sql_query($query);
   } elseif (isset($categorieSCAT)) {
      $query="UPDATE ".$NPDS_Prefix."g_categories SET categorie='".addslashes($categorieSCAT)."' WHERE id_cat=$id_catSCAT";
      $succes= sql_query($query);
   }
}

echo '
<form id="annadcat" method="post" action="admin.php">
   <input type="hidden" name="op" value="Extend-Admin-SubModule" />
   <input type="hidden" name="ModPath" value="'.$ModPath.'" />
   <input type="hidden" name="ModStart" value="admin/adm_cat" />
   <h4 class="my-3"><i class="fa fa-plus-square align-middle me-2" aria-hidden="true"></i> '.ann_translate("Ajouter une catégorie").'</h4>
      <div class="form-floating mb-3">
         <input type="text" id="addcat" name="categorie" class="form-control" required="required"/>
         <label for="addcat">'.ann_translate("Catégorie").'</label>
      </div>
      <button name="action" class="btn btn-primary" type="submit" value="ajouter"><i class="fa fa-check me-2" aria-hidden="true"></i>'.ann_translate("Valider").'</button>
</form>
<hr />
<form id="annadscat" method="post" action="admin.php">
   <input type="hidden" name="op" value="Extend-Admin-SubModule" />
   <input type="hidden" name="ModPath" value="'.$ModPath.'" />
   <input type="hidden" name="ModStart" value="admin/adm_cat" />
   <h4 class="my-3"><i class="fa fa-plus-square me-2 align-middle" aria-hidden="true"></i>'.ann_translate("Ajouter une sous-catégorie dans").'</h4>
   <div class="row mb-3">
      <div class="col-sm-8">
         <div class="form-floating mb-3">
            <select class="form-select" name="id_catSCAT">';
$query_list="SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='0' ORDER BY id_cat";
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
               <label for="">'.ann_translate("Ajouter une sous-catégorie dans").'</label>
            </div>
         </div>
         <div class="col-sm-8">
            <div class="form-floating mb-3">
               <input type="text" class="form-control" id="addscat" name="categorieSCAT" required="required" />
               <label for="addscat">'.ann_translate("Sous-catégorie").'</label>
            </div>
         </div>
         <div class="col-sm-4">
            <button name="action" class="btn btn-primary mt-2" type="submit" value="ajouter"><i class="fa fa-check" aria-hidden="true"></i> '.ann_translate("Valider").'</button>
         </div>
      </div>
   </form>
   <hr />
   <h4 class="my-3"><i class="fa fa-edit me-2 align-middle" aria-hidden="true"></i>'.ann_translate("Modifier").' '.ann_translate("Catégorie").' / '.ann_translate("Sous-catégorie").'</h4>';
$select= sql_query("SELECT id_cat, categorie FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='0' ORDER BY id_cat");
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
      <div class="mb-3 row">
         <div class="col-sm-8">
            <div class="form-floating">
               <input type="text" id="categorie'.$cj.'" name="categorie" class="form-control m-y-1" value="'.$categorie.'" required="required"/>
               <label for="categorie'.$cj.'">'.ann_translate("Catégorie").'</label>
            </div>
         </div>
         <div class="col-sm-4 text-end">
            <input type="submit" name="action" class="btn btn-outline-primary me-3 mt-2" value="Modifier" />
            <button type="submit" class="btn btn-outline-danger mt-2" name="action" value="supprimer"><i class="far fa-trash" aria-hidden="true"></i></button>
         </div>
      </div>
   </form>';

   $select2= sql_query("SELECT id_cat, categorie FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='".$i['id_cat']."' ORDER BY id_cat");
   $scj=0;
   while ($i2= sql_fetch_assoc($select2)) {
      echo '
   <form method="post" action="admin.php">
      <input type="hidden" name="op" value="Extend-Admin-SubModule" />
      <input type="hidden" name="ModPath" value="'.$ModPath.'" />
      <input type="hidden" name="ModStart" value="admin/adm_cat" />
      <input type="hidden" name="id_catSCAT" value="'.$i2['id_cat'].'" />
      <div class="mb-3 row">
         <div class="col-sm-8">
            <div class="form-floating ms-4 ">
               <input type="text" class="form-control m-y-1" maxlength="55" id="categoriescat'.$scj.'" name="categorieSCAT" value="'.stripslashes($i2['categorie']).'" required="required" />
               <label for="categoriescat'.$scj.'">'.ann_translate("Sous-catégorie").'</label>
           </div>
        </div>
         <div class="col-sm-4 text-end">
            <button class="btn btn-outline-primary me-3 mt-2" type="submit" name="action" value="Modifier">'.ann_translate("Modifier").'</button>
            <button type="submit" class="btn btn-outline-danger mt-2" name="action" value="supprimer"><i class="far fa-trash" aria-hidden="true"></i></button>
         </div>
      </div>
   </form>';
   $scj++;
   }
   $cj++;
}
   echo '
   </div>';
   $arg1 = 'var formulid = ["annadcat","annadscat"];';
   adminfoot('fv','',$arg1,'');

   
   include ("footer.php");
?>