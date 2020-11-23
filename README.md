# npds_annonces
Modules annonces pour le CMS NPDS

Module simple de petites annonces interfacé et complètement intégré au portail NPDS.

Fonctionalités:
* Inscription comme membre du site obligatoire pour les visiteurs avant de pouvoir déposer une annonce
* Les utilisateurs peuvent modifier/supprimer leurs annonces
* Validité des annonces paramétrable (nombre de fois 30 jours) par l'administrateur
* Possibilité d'avoir plusieurs catégories et sous-catégories d'annonces
* Administration des annonces depuis le module Plugins du menu administration de NPDS (npds_annonces)
* Deux tables sql seulement (g_categories, g_annonces) leur création est automatique à la première connexion au  plugin d'administration
* Moteur de recherche intégré (spécifique aux annonces)
* Récupération des informations utilisateurs inscrits
* Envoi d'un mail à l'administrateur à chaque dépot d'annonces
* Et enfin la validation des annonces avant parution
