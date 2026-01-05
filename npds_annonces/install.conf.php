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
/* NMIG : NPDS Module Installer Generator                               */
/* ---------------------------------------------------------------------*/
/* Version 2.0 - 2015                                                   */
/* Générateur de fichier de configuration pour Module-Install 1.1       */
/* Développé par Boris - http://www.lordi-depanneur.com                 */
/* Module-Install est un installeur inspiré du programme d'installation */
/* d'origine du module Hot-Projet développé par Hotfirenet              */
/* ---------------------------------------------------------------------*/
/* Version 2.1 for NPDS 16 jpb 2017                                     */
/************************************************************************/

global $ModInstall;

#autodoc $name_module: Nom du module #required (no space and exotism)
$name_module = "npds_annonces";

#autodoc $path_adm_module: chemin depuis $ModInstall #required SI admin avec interface
$path_adm_module = 'admin/adm';

#autodoc $affich: pour l'affichage du nom du module dans l'admin
$affich = 'npds_annonces';

#autodoc $icon: icon pour l'admin : c'est un nom de fichier(sans extension) !! #required SI admin avec interface
$icon = 'npds_annonces';

#autodoc $list_fich : Modifications de fichiers: Dans le premier tableau, tapez le nom du fichier
#autodoc et dans le deuxième, A LA MEME POSITION D'INDEX QUE LE PREMIER, tapez le code à insérer dans le fichier.
#autodoc Si le fichier doit être créé, n'oubliez pas les < ? php et ? > !!! (sans espace!).
#autodoc Synopsis: $list_fich = array(array("nom_fichier1","nom_fichier2"), array("contenu_fichier1","contenu_fichier2"));
$list_fich = '';

#autodoc $sql = array(""): Si votre module doit exécuter une ou plusieurs requêtes SQL, tapez vos requêtes ici.
#autodoc Attention! UNE requête par élément de tableau!
#autodoc Synopsis: $sql = array("requête_sql_1","requête_sql_2");
$sql = array("CREATE TABLE ".$NPDS_Prefix."g_annonces (
  id bigint(20) NOT NULL auto_increment,
  id_user bigint(20) DEFAULT NULL,
  id_cat mediumint(11) DEFAULT NULL,
  tel varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  tel_2 varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  code varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  ville varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  date varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  text mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  en_ligne tinyint(1) NOT NULL DEFAULT '0',
  prix decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
"CREATE TABLE ".$NPDS_Prefix."g_categories (
  id_cat mediumint(11) NOT NULL auto_increment,
  id_cat2 mediumint(11) NOT NULL DEFAULT '0',
  categorie varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id_cat)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
"INSERT INTO ".$NPDS_Prefix."g_categories VALUES (0, 0, 'Divers');"
);

#autodoc $blocs = array(array(""), array(""), array(""), array(""), array(""), array(""), array(""), array(""), array(""))
#autodoc                titre      contenu    membre     groupe     index      rétention  actif      aide       description
#autodoc Configuration des blocs
$blocs = array(array("Petites Annonces"), array("include#modules/npds_annonces/bloc1.php"), array("0"), array(""), array("1"), array("0"), array("1"), array(""), array("Petites Annonces"));

#autodoc $txtdeb : Vous pouvez mettre ici un texte de votre choix avec du html qui s'affichera au début de l'install
#autodoc Si rien n'est mis, le texte par défaut sera automatiquement affiché
$txtdeb = '';

#autodoc $txtfin : Vous pouvez mettre ici un texte de votre choix avec du html qui s'affichera à la fin de l'install
$txtfin = '<p>Merci d\'utiliser npds_annonces<br /><br /><a href="http://npds.org" target="_blank">npds.org</a></p>';

#autodoc $end_link: Lien sur lequel sera redirigé l'utilisateur à la fin de l'install (si laissé vide, redirigé sur index.php)
#autodoc N'oubliez pas les '\' si vous utilisez des guillemets !!!
$end_link = '';
?>