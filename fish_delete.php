<?
require_once "common.php";
checkday();
page_header("User l�schen");
output("Dein Charakter, sein Inventar und alle seine Kommentare wurden gel�scht!");
addnav("Erwachen","fish.php?op=awake");
page_footer();
