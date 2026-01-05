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

function aff_annonces($select) {
   global $ModPath, $aff_prix, $prix_cur,$obsol;
   $j = 0;
   echo '
   <script type="text/javascript" src="lib/flatpickr/dist/flatpickr.min.js"></script>
   <script type="text/javascript" src="lib/flatpickr/dist/l10n/'.language_iso(1,'','').'.js"></script>
   <script type="text/javascript">
      //<![CDATA[
         $(document).ready(function() {
            $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "lib/flatpickr/dist/themes/npds.css"});
         })
      //]]>
   </script>';

   while ($i = sql_fetch_array($select)) {
      $id_user = $i['id_user'];
      $tel = stripslashes($i['tel']);
      $tel_2 = stripslashes($i['tel_2']);
      $code = $i['code'];
      $ville = stripslashes($i['ville']);
      $date = $i['date'];
      $dateend = date('d-m-Y',$date + ($obsol * 30 * 86400));
      $date = date('d-m-Y',$date);
      $text = removehack(stripslashes($i['text']));
      $prix = $i['prix'];

      //recup des données utilisateur utiles pour l'affichage
      settype ($id_user,'integer');
      $result = sql_query("SELECT uname, email FROM users WHERE uid='$id_user'");
      list($nom, $mail) = sql_fetch_row($result);

      $ibid = '
      <div class="card my-3">
         <div class="card-body">
            <h4>'.ann_translate("Annonce de").' '.$nom.' '.userpopover($nom,'48').' <span id="validdate'.$j.'" class="float-end">'.$date.'</span></h4>
            <hr />
            <div class="card-text">';
      if ($aff_prix)
         $ibid .= '
               <div class="text-end h3">
                  <span class=" badge bg-success heading1">'.$prix.' '.aff_langue($prix_cur).'</span>
               </div>';
      $ibid .= '
            </div>
            <div class="card-text mt-3">
               '.$text.'
            </div>
            <div class="card-text row">';
      if ($ville)
         $ibid .= '
               <div class="col-md-12"><strong>'.ann_translate('Ville').'</strong> : '.$ville.'</div>';
      if ($code)
         $ibid .= '
               <div class="col-md-12"><strong>'.ann_translate('Code postal').'</strong> : '.$code.'</div>';
      $ibid .= '
            </div>';
      $ibid .= "<p>##imp##</p>";
      $ibid .= '
         </div>
      </div>';

      $ibid2 = '
      <hr />
      <form method="post" action="modules.php" class="text-end">
         <input type="hidden" name="ModPath" value="'.$ModPath.'" />
         <input type="hidden" name="ModStart" value="print" />
         <input type="hidden" name="text" value="'.rawurlencode(str_replace("##imp##","",$ibid)).'" />
         <a class="btn btn-primary me-2" href="mailto:'.anti_spam($mail).'"><i class="fa fa-at fa-lg" aria-hidden="true"></i></a>';
      if ($tel != '')
         $ibid2 .= '
         <span class="me-2"><i class="fa fa-phone fa-2x align-middle" aria-hidden="true"></i> <a data-rel="external" href="tel:+33'.$tel.'" target="_blank">+33'.$tel.'</a></span>';
      if ($tel_2 != '')
         $ibid2 .= '
         <span class="me-2"><i class="fa fa-mobile fa-2x align-middle" aria-hidden="true"></i> <a data-rel="external" href="tel:+33'.$tel_2.'" target="_blank">+33'.$tel_2.'</a></span>';
      $ibid2 .= '
         <button class="btn btn-primary" type="image" name="image"><i class="fa fa-print fa-lg"></i></button>
      </form>
      <script type="text/javascript">
      //<![CDATA[
         const fp'.$j.' = flatpickr("#validdate'.$j.'", {
            mode: "range",
            dateFormat: "d-m-Y",
            defaultDate: ["'.$date.'", "'.$dateend.'"],
//enable:["'.$date.'", "'.$dateend.'"],
enable: [
        {
            from: "'.$date.'",
            to: "'.$dateend.'"
        },],
/*
            enable: [
                    function(date) {
                        return false;
                    }
                ]
*/
         })
      //]]>
      </script>';
      echo str_replace("##imp##",$ibid2,$ibid)."";
   $j++;
   }
}
?>