<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Module npds_annonces 3.1                                             */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

function ann_translate($phrase) {
   return (htmlentities($phrase,ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML401,cur_charset));
}
?>