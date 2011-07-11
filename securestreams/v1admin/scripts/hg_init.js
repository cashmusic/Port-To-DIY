/*	

hg_init.js v1.0

mercury (Hg) bootstrap script
part of the mercury (Hg) client-side script library
more information/downloads available at: http://mercury.cashmusic.org

requires:
mootools v 1.2
HgCore

usage:
provides domready block, any module customizations, custom routines and 
Hg initialization — THIS FILE SHOULD *NOT* BE COMPRESSED to allow for debug
and any quick changes

*/
var Hg; // store Hg main object in global namespace
window.addEvent('domready', function(){
	Hg = new HgCore();
	Hg.bootstrap();
});