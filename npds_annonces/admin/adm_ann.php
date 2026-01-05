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
if (!strstr($PHP_SELF,'admin.php')) { Access_Error(); }
if (strstr($ModPath,'..') || strstr($ModStart,'..') || stristr($ModPath, 'script') || stristr($ModPath, 'cookie') || stristr($ModPath, 'iframe') || stristr($ModPath, 'applet') || stristr($ModPath, 'object') || stristr($ModPath, 'meta') || stristr($ModStart, 'script') || stristr($ModStart, 'cookie') || stristr($ModStart, 'iframe') || stristr($ModStart, 'applet') || stristr($ModStart, 'object') || stristr($ModStart, 'meta')) {
   die();
}
// For More security
$f_meta_nom ='npds_annonces';
//==> controle droit
admindroits($aid,$f_meta_nom);
//<== controle droit

include 'modules/'.$ModPath.'/annonce.conf.php';
include 'modules/'.$ModPath.'/lang/annonces-'.$language.'.php';
settype($action,'string');

if ($editeur)
   $max = 1;

if ($action == 'Valider') {
   settype($id,'integer');
   settype($id_cat,'integer');
   settype($Xid_cat,'integer');
   $tel = addslashes(trim(removeHack($tel)));
   $tel_2 = addslashes(trim(removeHack($tel_2)));
   $code = trim(removeHack($code));
   $ville = addslashes(trim(removeHack($ville)));
   $text = removeHack(stripslashes(FixQuotes($xtext)));
   $prix = str_replace(',','.',$prix);
   settype($prix, 'double');

   $query = "UPDATE ".$NPDS_Prefix."g_annonces SET id_cat='$Xid_cat', tel='$tel', tel_2='$tel_2', code='$code', ville='$ville', date='".time()."', text='$text', en_ligne='1', prix='$prix' WHERE id='$id'";
   $succes = sql_query($query);
}
if ($action == 'Supprimer') {
   settype($id,'integer');
   $query = "DELETE FROM ".$NPDS_Prefix."g_annonces WHERE id='$id'";
   $succes = sql_query($query);
   if ($succes)
      $succes = sql_query($query);
}

   GraphicAdmin($hlpfile);

   echo '
   <div class="border rounded p-3">
      <h2><img class="align-middle me-2" src="modules/npds_annonces/npds_annonces.png" alt="icon_npds_annonces"><a href="admin.php?op=Extend-Admin-SubModule&amp;ModPath=npds_annonces&amp;ModStart=admin/adm">Annonces</a></h2>
      <hr />';
   if (!isset($id_cat_sel)) {
      if(!strstr($id_cat, '|')) {
         $q = "='$id_cat'";
         settype($id_cat,'integer');
      } else
         $q = " REGEXP '[[:<:]]".str_replace('|', '[[:>:]]|[[:<:]]',$id_cat)."[[:>:]]'";

      $id_cat_sel = $id_cat;
      $select = sql_query("SELECT categorie FROM ".$NPDS_Prefix."g_categories WHERE id_cat$q");
      list($categorie) = sql_fetch_row($select);
      echo '
      <p class="lead">'.ann_translate('Catégorie').' : <span class="">'.stripslashes($categorie).'</span></p>';
   }
   if (!isset($min))
      $min = 0;

   if (!isset($sel)) {
      $query_count = "SELECT COUNT(*) FROM ".$NPDS_Prefix."g_annonces WHERE id_cat$q";
      $succes_count = sql_query($query_count);
      $count = sql_fetch_row($succes_count);
      $count = $count[0];
      $sel2 = "WHERE id_cat$q ORDER BY en_ligne,id DESC LIMIT $min,$max";
   } else
      $sel2 = $sel == 1 ? "ORDER BY en_ligne,id DESC LIMIT 0,1" : "ORDER BY en_ligne,id DESC LIMIT 0,$sel" ;

   $query = "SELECT * FROM ".$NPDS_Prefix."g_annonces $sel2";
   $succes = sql_query($query);
   while ($values = sql_fetch_assoc($succes)) {
      $id = $values['id'];
      $id_user = $values['id_user'];
      $id_cat= $values['id_cat'];
      $tel = stripslashes($values['tel']);
      $tel_2 = stripslashes($values['tel_2']);
      $code = $values['code'];
      $ville = stripslashes($values['ville']);
      $text = stripslashes($values['text']);
      $prix = $values['prix'];

//recup données utilisateur de l'annonce
      $query_2 = "SELECT uname, email FROM ".$NPDS_Prefix."users WHERE uid='$id_user'";
      $succes_2 = sql_query($query_2);
      list ($nom, $mail) = sql_fetch_row($succes_2);
      echo '
      <p class="lead mb-3">'.ann_translate('Annonce').' ID : ['.$id.']';
      if ($values['en_ligne'] == '1')
         echo '<span class="badge bg-success float-end">'.ann_translate('En ligne').'</span>';
      elseif ($values['en_ligne'] == '0')
         echo '<span class="badge bg-danger mt-1 float-end">'.ann_translate('En attente').'</span>';
      else
         echo '<span class="badge bg-secondary float-end">'.ann_translate('En archive').'</span>';
      echo '</p>';

      echo '
      <form id="annadmin" method="post" action="admin.php" name="adminForm">
         <input type="hidden" name="op" value="Extend-Admin-SubModule" />
         <input type="hidden" name="ModPath" value="'.$ModPath.'" />
         <input type="hidden" name="ModStart" value="admin/adm_ann" />
         <input type="hidden" name="id_cat" value="'.$id_cat_sel.'" />
         <input type="hidden" name="min" value="'.$min.'" />
         <input type="hidden" name="id" value="'.$id.'" />';
      if (isset($sel))
         echo '
         <input type="hidden" name="sel" value="'.$sel.'" />';
      echo '
         <div class="row g-2">
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input class="form-control" disabled="disabled" value="'.$nom.'" />
                  <label for="">'.ann_translate('Nom').'</label>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input class="form-control" disabled="disabled" value="'.$mail.'" />
                  <label for="">'.ann_translate('Email').'</label>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="" name="tel" placeholder="'.$tel.'" value="'.$tel.'" />
                  <label for="">'.ann_translate('Tél fixe').'</label>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="" name="tel_2" placeholder="'.$tel_2.'" value="'.$tel_2.'" />
                  <label for="">'.ann_translate('Tél portable').'</label>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="" name="code" placeholder="'.$code.'" value="'.$code.'" />
                  <label for="">'.ann_translate('Code postal').'</label>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="" name="ville" placeholder="'.$ville.'" value="'.$ville.'">
                  <label for="">'.ann_translate('Ville').'</label>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <select class="form-select" name="Xid_cat">';

      $select = sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='0' ORDER BY id_cat");
      while($e = sql_fetch_assoc($select)) {
         echo "<option value='".$e['id_cat']."'";
         if ($e['id_cat'] == $id_cat) echo 'selected="selected"';
         echo ">".stripslashes($e['categorie'])."</option>\n";
         $select2 = sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='".$e['id_cat']."' ORDER BY id_cat");
         while ($e2 = sql_fetch_assoc($select2)) {
            echo "<option value='".$e2['id_cat']."'";
            if ($e2['id_cat'] == $id_cat) echo "selected=\"selected\"";
            echo ">&nbsp;&nbsp;&nbsp;".stripslashes($e2['categorie'])."</option>\n";
         }
      }
      echo '
                  </select>
                  <label for="">'.ann_translate('Catégorie').' <span class="text-danger">*</span></label>
               </div>
            </div>';
      if ($aff_prix)
         echo '
            <div class="col-sm-6">
               <div class="form-floating mb-3">
                  <input type="text" name="prix" class="form-control" id="" value="'.$prix.'" placeholder="'.$prix.'">
                  <label for="" required="required">'.ann_translate('Prix en').' '.aff_langue($prix_cur).' <span class="text-danger">*</span></label>
               </div>
            </div>';
      else
         echo '
            <input type="hidden" name="prix" value="'.$prix.'" />';
      echo '
         </div>
            <div class="col-sm-12 mt-0">
            <div class="mb-3">
               <label for="" class="form-label">'.ann_translate("Libellé de l'annonce").' <span class="text-danger">*</span></label>
               <textarea name="xtext" class="tin form-control" rows="50">'.$text.'</textarea>';
      if ($editeur)
         echo aff_editeur('xtext', 'true');
      echo '
            </div>
            </div>
         <button class="btn btn-primary my-2 me-3" type="submit" name="action" value="Valider">'.ann_translate('Valider').'</button>
         <button class="btn btn-danger my-2" type="submit" name="action" value="Supprimer">'.ann_translate('Supprimer').'</button>
      </form>';
   }

   $pp = false;
   if (!isset($sel)) {
   echo '
   <hr />
   <nav aria-label="">
     <ul class="pagination pagination-lg justify-content-end">';
      if ($min > 0) {
         echo '
         <li class="page-item"><a class="page-link" href="admin.php?op=Extend-Admin-SubModule&amp;ModPath='.$ModPath.'&amp;ModStart=admin/adm_ann&amp;id_cat='.$id_cat_sel.'&amp;min='.($min-$max).'"><i class="fa fa-angle-double-left mx-2"></i><span class="hidden-sm-down">'.ann_translate('Précédente').'</span></span></a></li>';
         $pp = true;
      }
      if (($min + $max) < $count)
         echo '
         <li class="page-item"><a class="page-link" href="admin.php?op=Extend-Admin-SubModule&amp;ModPath='.$ModPath.'&amp;ModStart=admin/adm_ann&amp;id_cat='.$id_cat_sel.'&amp;min='.($min+$max).'"><span class="hidden-sm-down">'.ann_translate('Suivante').' </span><i class="fa fa-angle-double-right mx-2"></i></a></li>';
   echo '
      </ul>
   </nav>';
   }
   echo '
   </div>';
   $arg1 = 'var formulid = ["annadmin"];';
   $tinyfield = 'xtext';
   $fv_parametres = '
      tel: {
         validators: {
            regexp: {
               regexp:/^\d{2,14}$/,
               message: "0-9"
            }
         }
      },
      tel_2: {
         validators: {
            regexp: {
               regexp:/^\d{2,14}$/,
               message: "0-9"
            }
         }
      },
      prix: {
         validators: {
            regexp: {
               regexp:/^\d{2,14}$/,
               message: "0-9"
            }
         }
      },';

   if ($editeur) {
   $arg1 .= '
   const fv = FormValidation.formValidation(document.getElementById(formulid), {
      fields: {
         '.$tinyfield.': {
             validators: {
                 callback: {
                     message: "Le libellé de l\'annonce ne peut être vide",
                     callback: function (value) {
                         const text = tinyMCE.activeEditor.getContent({
                             format: "text",
                         });
                         return text.length >= 1;
                     },
                 },
             },
         },
      },
      plugins: {
         declarative: new FormValidation.plugins.Declarative({
            html5Input: true,
         }),
         trigger: new FormValidation.plugins.Trigger(),
         submitButton: new FormValidation.plugins.SubmitButton(),
         defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
         bootstrap5: new FormValidation.plugins.Bootstrap5({rowSelector: ".mb-3"}),
         icon: new FormValidation.plugins.Icon({
            valid: "fa fa-check",
            invalid: "fa fa-times",
            validating: "fa fa-sync",
            onPlaced: function(e) {
               e.iconElement.addEventListener("click", function() {
                  fv.resetField(e.field);
               });
            },
         }),
      },
   });';
   } else {
      $fv_parametres .= '
      '.$tinyfield.': {
         validators: {
            notEmpty: {
               message: "Le libellé de l\'annonce ne peut être vide"
            }
         }
      },';
   }
   adminfoot('fv',$fv_parametres,$arg1,'');
?>