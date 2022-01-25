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

if (file_exists('modules/'.$ModPath.'/admin/pages.php'))
   include ('modules/'.$ModPath.'/admin/pages.php');

include ("modules/$ModPath/annonce.conf.php");
include ("modules/$ModPath/lang/annonces-$language.php");
settype($op,'string');
   if ($op=="Soumettre") {
      if (autorisation(1)) {
         if (($id_user!='') and ($prix!='')) {
            $id_user=removeHack($id_user);
            $id_cat=removeHack($id_cat);
            $tel=addslashes(trim(removeHack($tel)));
            $tel_2=addslashes(trim(removeHack($tel_2)));
            $code=addslashes(trim(removeHack($code)));
            $ville=addslashes(trim(removeHack($ville)));
            $text=removeHack(stripslashes(FixQuotes($xtext)));
            $prix=str_replace(",",".",$prix);
            settype($prix, "double");
            settype ($id_user,"integer");
            settype ($id_cat,"integer");

            $query="INSERT INTO ".$NPDS_Prefix."g_annonces (id, id_user, id_cat, tel, tel_2, code, ville, date, text, en_ligne, prix)";
            $query.=" VALUES ('0','$id_user', '$id_cat', '$tel', '$tel_2', '$code', '$ville', '".time()."', '$text', '0', '$prix')";
            $res=sql_query($query);

            $quer="SELECT categorie FROM ".$NPDS_Prefix."g_categories WHERE id_cat='$id_cat'";
            $sel=sql_query($quer);
            $sel=sql_fetch_assoc($sel);
            $categorie=$sel['categorie'];

            global $notify_email, $notify_from;
            $message='catégorie : '.StripSlashes($categorie).'<br /><br />';
            $message.="texte de l'annonce : ".StripSlashes(StripSlashes($text)).'<br />';
            include ("signat.php");
            @send_email($notify_email, "Nouvelle annonce publiée (module annonces)", $message, $notify_from , false, "html");
            redirect_url ("modules.php?ModPath=$ModPath&ModStart=index");
            die();
         }
         else {
            redirect_url("modules.php?ModPath=$ModPath&ModStart=index");
         }
      }
   }
   $filename = 'modules/'.$ModPath.'/intro.html';

   include ("header.php");
   echo aff_langue($mess_acc);
   include ("modules/$ModPath/include/search_form.php");

   echo '
   <div class="card">
      <div class="card-body">
         <h3>'.ann_translate("Ajouter une annonce").'</h3>
         <p class="text-center"><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#intro">'.ann_translate("Mode d'emploi").'</button>&nbsp;&nbsp;<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#ment">'.ann_translate("Mentions légales").'</button></p>
         <div class="modal fade" id="intro" tabindex="-1" role="dialog" aria-labelledby="explication" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h4 class="modal-title" id="explication">'.ann_translate("Mode d'emploi").'</h4>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">';
               include($filename);
               echo '
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">'.ann_translate("Fermer").'</button>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal fade" id="ment" tabindex="-1" role="dialog" aria-labelledby="mentlegal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h4 class="modal-title" id="mentlegal">'.ann_translate("Mentions légales des conditions d'utilisation des petites annonces").'</h4>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">';
         include('modules/'.$ModPath.'/corps.html');
         echo '
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-primary" data-bs-dismiss="modal">'.ann_translate("Fermer").'</button>
                  </div>
               </div>
            </div>
         </div>';

   if (isset($user))
      $userinfo=getusrinfo($user);
   else
      redirect_url("modules.php?ModPath=$ModPath&ModStart=index");

   echo '
         <form id="newann" method="post" action="modules.php" name="adminForm">
            <input type="hidden" name="ModPath" value="'.$ModPath.'" />
            <input type="hidden" name="ModStart" value="annonce_form" />
            <input type="hidden" name="id_user" value="'.$userinfo['uid'].'" />
            <input type="hidden" name="op" value="Soumettre" />
            <p class="lead">'.aff_langue($mess_requis).'</p>
            <div class="mb-3">
               <label for="xtext" class="form-label">'.ann_translate("Libellé de l'annonce").' <span class="text-danger">*</span></label>
               <textarea id="xtext" name="xtext" class="tin form-control" rows="40" ></textarea>';
   if ($editeur)
      echo aff_editeur('xtext','');
   echo '
            </div>
            <div class="form-floating mb-3">
               <select class="form-select" name="id_cat">';
   $select = sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='0' ORDER BY id_cat");
   settype($sel,'string');
   while($e= sql_fetch_assoc($select)) {
      echo '
                  <option value="'.$e['id_cat'].'"';
      echo $sel=='' ? 'selected="selected"' : '';
      echo '>'.stripslashes($e['categorie']).'</option>';
      $select2 = sql_query("SELECT * FROM ".$NPDS_Prefix."g_categories WHERE id_cat2='".$e['id_cat']."' ORDER BY id_cat");
      while ($e2 = sql_fetch_assoc($select2)) {
         echo '<option value="'.$e2['id_cat'].'"';
         echo '>&nbsp;&nbsp;&nbsp;'.stripslashes($e2['categorie']).'</option>';
      }
   }
   echo '
               </select>
               <label for="id_cat">'.ann_translate("Catégorie").' / '.ann_translate("Sous-catégorie").' <span class="text-danger">*</span></label>
            </div>
            <div class="row">
               <div class="col-sm-6">
                  <div class="form-floating mb-3">
                     <input type="text" name="ville" class="form-control" id="ville" placeholder="" />
                     <label for="ville" class="col-sm-4 col-form-label">'.ann_translate("Ville").'</label>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-floating mb-3">
                     <input type="text" class="form-control" name="code" id="newancode" placeholder="" />
                     <label for="newancode" class="col-sm-4 col-form-label">'.ann_translate("Code postal").'</label>
                     <span class="help-block">format 00000</span>
                  </div>
               </div>';
   if ($aff_prix) {
      settype($prix,'string');// en attendant de savoir vraiment ce qui peut et doit arriver pour cette valeur
      echo '
               <div class="col-sm-6">
                     <div class="form-floating mb-3">
                        <input type="text" name="prix" class="form-control" id="prix" required="required"/>
                        <label for="prix">'.ann_translate("Prix en").' '.aff_langue($prix_cur).' <span class="text-danger">*</span></label>
                     </div>
               </div>
               <div class="col-sm-6"></div>';
   } else
      echo '
               <input type="hidden" name="prix" value="0" />';

   echo'
               <div class="col-sm-6">
                  <div class="form-floating mb-3">
                     <input type="text" class="form-control" name="" id="an_username" value="'.$userinfo["uname"].'" disabled="disabled" />
                     <label for="an_username">'.ann_translate("Nom").'</label>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-floating mb-3">
                     <input type="email" class="form-control" name="" id="an_usermail" value="'.$userinfo['email'].'" disabled="disabled" />
                     <label for="an_usermail">'.ann_translate("Email").'</label>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="mb-3">
                     <label class="form-label" for="newantel">'.ann_translate("Tél fixe").'</label>
                     <div class="input-group">
                        <span class="input-group-text">+33.0</span>
                        <input type="text" class="form-control" id="newantel" name="tel" placeholder="'.ann_translate("sans").' 0" />
                     </div>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="mb-3">
                     <label class="form-label" for="newantel_2">'.ann_translate("Tél portable").'</label>
                     <div class="input-group">
                        <span class="input-group-text">+33.0</span>
                        <input type="text" class="form-control" id="newantel_2" name="tel_2" placeholder="'.ann_translate("sans").' 0" />
                     </div>
                  </div>
               </div>
            </div>
            <div class="mb-3">
            <div class="form-check">
               <input class="form-check-input" type="checkbox" value="" id="consentement" name="consentement" required="required" />
               <label class="form-check-label" for="consentement">
<small>'.aff_langue('[french]En soumettant ce formulaire j\'accepte que les informations saisies soient exploit&#xE9;es dans le cadre de l\'utilisation et du fonctionnement de ce site.[/french][english]By submitting this form, I accept that the information entered will be used in the context of the use and operation of this website.[/english][spanish]Al enviar este formulario, acepto que la informaci&oacute;n ingresada se utilizar&aacute; en el contexto del uso y funcionamiento de este sitio web.[/spanish][german]Mit dem Absenden dieses Formulars erkl&auml;re ich mich damit einverstanden, dass die eingegebenen Informationen im Rahmen der Nutzung und des Betriebs dieser Website verwendet werden.[/german][chinese]&#x63D0;&#x4EA4;&#x6B64;&#x8868;&#x683C;&#x5373;&#x8868;&#x793A;&#x6211;&#x63A5;&#x53D7;&#x6240;&#x8F93;&#x5165;&#x7684;&#x4FE1;&#x606F;&#x5C06;&#x5728;&#x672C;&#x7F51;&#x7AD9;&#x7684;&#x4F7F;&#x7528;&#x548C;&#x64CD;&#x4F5C;&#x8303;&#x56F4;&#x5185;&#x4F7F;&#x7528;&#x3002;[/chinese]').'</small>               </label>
            </div>
            </div>
            <button type="submit" class="btn btn-primary my-3">'.ann_translate("Soumettre").'</button>
         </form>';

         echo '<small>'.aff_langue('[french]Pour conna&icirc;tre et exercer vos droits notamment de retrait de votre consentement &agrave; l\'utilisation des donn&eacute;es collect&eacute;es veuillez consulter notre <a href="static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1">politique de confidentialit&eacute;</a>.[/french][english]To know and exercise your rights, in particular to withdraw your consent to the use of the data collected, please consult our <a href="static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1">privacy policy</a>.[/english][spanish]Para conocer y ejercer sus derechos, en particular para retirar su consentimiento para el uso de los datos recopilados, consulte nuestra <a href="static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1">pol&iacute;tica de privacidad</a>.[/spanish][german]Um Ihre Rechte zu kennen und auszu&uuml;ben, insbesondere um Ihre Einwilligung zur Nutzung der erhobenen Daten zu widerrufen, konsultieren Sie bitte unsere <a href="static.php?op=politiqueconf.html&amp;npds=1&amp;metalang=1">Datenschutzerkl&auml;rung</a>.[/german][chinese]&#x8981;&#x4E86;&#x89E3;&#x5E76;&#x884C;&#x4F7F;&#x60A8;&#x7684;&#x6743;&#x5229;&#xFF0C;&#x5C24;&#x5176;&#x662F;&#x8981;&#x64A4;&#x56DE;&#x60A8;&#x5BF9;&#x6240;&#x6536;&#x96C6;&#x6570;&#x636E;&#x7684;&#x4F7F;&#x7528;&#x7684;&#x540C;&#x610F;&#xFF0C;&#x8BF7;&#x67E5;&#x9605;&#x6211;&#x4EEC;<a href="static.php?op=politiqueconf.html&#x26;npds=1&#x26;metalang=1">&#x7684;&#x9690;&#x79C1;&#x653F;&#x7B56;</a>&#x3002;[/chinese]').'</small>';


   $filename = "modules/$ModPath/pied.html";
   if (file_exists($filename))
      include($filename);

   echo '
      </div>
   </div>';
   $tinyfield = 'xtext';
   $arg1 = 'var formulid = ["newann"]; const blabla="blabla";';
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
   $arg1 .='
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
      $fv_parametres .='
      '.$tinyfield.': {
         validators: {
            notEmpty: {
               message: "Le libellé de l\'annonce ne peut être vide"
            }
         }
      },';
   }
   adminfoot('fv',$fv_parametres,$arg1,'foo');
?>