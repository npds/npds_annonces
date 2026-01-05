<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2002-2026 by Philippe Brunier                     */
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

if (file_exists('modules/'.$ModPath.'/admin/pages.php'))
   include 'modules/'.$ModPath.'/admin/pages.php';
include 'modules/'.$ModPath.'/annonce.conf.php';
include 'modules/'.$ModPath.'/lang/annonces-'.$language.'.php';

if (isset($user)) {
   settype($id,'integer');
   settype($id_cat,'integer');
   settype($op,'string');
   settype($succes,'string');
   if ($op == 'Modifier') {
      $tel = trim(removeHack($tel));
      $tel_2 = trim(removeHack($tel_2));
      $code = trim(removeHack($code));
      $ville = addslashes(trim(removeHack($ville)));
      $text = removeHack(stripslashes(FixQuotes($xtext)));
      $prix = str_replace(',','.',$prix);
      settype($prix, 'double');

      $query = "UPDATE ".$NPDS_Prefix."g_annonces";
      $query .= " SET id_cat='$id_cat', tel='$tel', tel_2='$tel_2', code='$code', ville='$ville', date='".time()."', text='$text', en_ligne='0', prix='$prix'";
      $query .= " WHERE id='$id' AND id_user='$cookie[0]'";
      $succes = sql_query($query);
      global $notify_email, $notify_from;
      $message = ann_translate('Catégorie').' : '.StripSlashes($categorie).'<br /><br />';
      $message.= "Texte de l'annonce : ".StripSlashes(StripSlashes($text))."<br />";
      include 'signat.php';
      @send_email($notify_email, 'Annonce revalidation (module annonces)', $message, $notify_from , false, 'html');
   }
   if ($op == 'Supprimer') {
      $query = "DELETE FROM ".$NPDS_Prefix."g_annonces WHERE id='$id' AND id_user='$cookie[0]'";
      $succes = sql_query($query);
   }
   if ($succes)
      redirect_url ("modules.php?ModPath=$ModPath&ModStart=$ModStart");
}

   $succes = sql_query("SELECT count(*) FROM ".$NPDS_Prefix."g_annonces WHERE id_user='$cookie[0]' AND en_ligne='1'");
   $count = sql_fetch_row($succes);
   $count = $count[0];
   if ($count == 0)
      redirect_url("modules.php?ModPath=$ModPath&ModStart=index");
   else {
      include 'header.php';
      echo aff_langue($mess_acc);
      include 'modules/'.$ModPath.'/include/search_form.php';
      echo '
   <div class="card">
      <div class="card-body">
         <h3>'.ann_translate("Gestion de vos annonces").'</h3>
         <div class=" blockquote lead">'.aff_langue($del_sup_chapo).'<br /> <strong>'.$cookie[1].'</strong>, '.ann_translate("vous avez").' <span class="badge badge-pill bg-success">'.$count.'</span> '.ann_translate("annonce(s) en ligne").'</div>
         <div class="alert alert-warning">'.aff_langue($warning).'</div>
         <hr />';
   }
   $query = "SELECT count(*) FROM ".$NPDS_Prefix."g_annonces WHERE id_user='$cookie[0]' AND en_ligne='0'";
   $succes = sql_query($query);
   $count2 = sql_fetch_row($succes);

   if (!isset($min)) $min = 0;
   $inf = $min + 1;
   $max = 1;

   settype ($min, 'integer');
   settype ($max, 'integer');
   $query = "SELECT * FROM ".$NPDS_Prefix."g_annonces WHERE id_user='$cookie[0]' AND en_ligne='1' ORDER BY id DESC LIMIT $min,$max";
   $result = sql_query($query);
   $j = 0;
   while ($i = sql_fetch_array($result)) {
      $id = $i['id'];
      $tel = stripslashes($i['tel']);
      $tel_2 = stripslashes($i['tel_2']);
      $code = $i['code'];
      $ville = stripslashes($i['ville']);
      $text = stripslashes($i['text']);
      $id_cat_sel = $i['id_cat'];
      $prix = $i['prix'];
      echo '
      <h4 class="mb-4">'.ann_translate('Annonce').'<span class="float-end"><span class="badge bg-secondary me-2">ID '.$id.'</span><span class="badge bg-success">'.ann_translate('En ligne').'</span></span></h4>
      <form id="modifannonce'.$j.'" method="post" action="modules.php" name="adminForm">
         <input type="hidden" name="ModPath" value="'.$ModPath.'" />
         <input type="hidden" name="ModStart" value="'.$ModStart.'" />
         <input type="hidden" name="id" value="'.$id.'" />
         <div class="mb-3 row">
            <label for="manxtext'.$j.'" class="col-sm-12 col-form-label">'.ann_translate("Libellé de l'annonce").'</label>
            <div class="col-sm-12">
               <textarea name="xtext" id="manxtext'.$j.'" class="tin form-control" rows="40">'.$text.'</textarea>';
         if ($editeur)
            echo aff_editeur("xtext", "true");
         echo '
            </div>
         </div>
         <div class="form-floating mb-3">
            <select class="form-select" name="id_cat" id="id_cat'.$j.'">';
      $select = sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='0' ORDER BY id_cat");
      while($e = sql_fetch_assoc($select)) {
         echo '<option value="'.$e['id_cat'].'"';
         if ($e['id_cat'] == $id_cat_sel) echo ' selected="selected" ';
         echo '>'.stripslashes($e['categorie'])."</option>\n";
         $select2 = sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='".$e['id_cat']."' ORDER BY id_cat");
         while ($e2 = sql_fetch_assoc($select2)) {
            echo '<option value="'.$e2['id_cat'].'"';
            if ($e2['id_cat'] == $id_cat_sel) echo ' selected="selected" ';
            echo '>&nbsp;&nbsp;&nbsp;'.stripslashes($e2['categorie'])."</option>\n";
         }
      }
      echo '
            </select>
            <label for="id_cat'.$j.'" class="col-sm-4 col-form-label">'.ann_translate('Catégorie').'</label>
         </div>
         <div class="row mb-3 g-3">
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input type="text" name="ville" class="form-control" id="manville'.$j.'" value="'.$ville.'" placeholder="'.$ville.'" />
                  <label for="manville'.$j.'" >'.ann_translate('Ville').'</label>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input type="text" class="form-control" name="code" id="mancode'.$j.'" value="'.$code.'" placeholder="'.$code.'" />
                  <label for="mancode'.$j.'">Code postal</label>
               </div>
            </div>
         </div>';
      if ($aff_prix)
         echo '
      <div class="form-floating mb-3">
         <input type="text" name="prix" class="form-control" id="" value="'.$prix.'" placeholder="'.$prix.'" />
         <label for="">'.ann_translate('Prix en').' '.aff_langue($prix_cur).'</label>
      </div>';
      else
         echo '
      <input type="hidden" name="prix" value="'.$prix.'" />';
      echo '
         <div class="row mb-3 g-3">
            <div class="col-sm-6">
               <label class="form-label" for="mantel'.$j.'">'.ann_translate('Tél fixe').'</label>
               <div class="input-group">
                  <div class="input-group-text">+33.0</div>
                  <input type="text" class="form-control" name="tel" id="mantel'.$j.'" value="'.$tel.'" placeholder="'.$tel.'" />
               </div>
            </div>
            <div class="col-sm-6">
               <label class="form-label" for="mantel2_'.$j.'" >'.ann_translate('Tél portable').'</label>
               <div class="input-group">
                  <div class="input-group-text">+33.0</div>
                  <input type="text" class="form-control" name="tel_2" id="mantel2_'.$j.'" value="'.$tel_2.'" placeholder="'.$tel_2.'" />
               </div>
            </div>
         </div>
         <div class="my-4">
            <button type="submit" name="op" class="btn btn-primary me-3" value="Modifier">'.ann_translate('Modifier').'</button>
            <button type="submit" name="op" class="btn btn-danger" value="Supprimer"><i class="fa fa-trash fa-lg" aria-hidden="true"></i> '.ann_translate('Supprimer').'</button>
         </div>
      </form>';
   $j++;
   }

   $pp = false;
   echo '
      <hr />
      <nav aria-label="">
         <ul class="pagination pagination-sm justify-content-end">';
   if ($min > 0) {
      echo '
            <li class="page-item"><a class="page-link" href="modules.php?ModPath='.$ModPath.'&amp;ModStart=modif_ann&amp;min='.($min-$max).'">'.ann_translate('Précédente').'</a></li>';
      $pp = true;
   }
   if (($min + $max) < $count)
      echo '
            <li class="page-item"><a class="page-link" href="modules.php?ModPath='.$ModPath.'&amp;ModStart=modif_ann&amp;min='.($min+$max).'">'.ann_translate('Suivante').'</a></li>';
   echo '
         </ul>
      </nav>
      <h4><span class="badge badge-pill bg-warning">'.$count2[0].'</span> '.ann_translate('annonce(s)').' '.ann_translate('en attente de validation').'</h4>
      </div>
   </div>';
include 'footer.php';
?>