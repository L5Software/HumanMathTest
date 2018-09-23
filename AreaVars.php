<?php
/* remotefilename=/FOSS/PHP/HumanMathTest/AreaVars.php
 *
 *	Copyright © 2018 by FKE Internet.  All rights reserved.
 *
 *	$Id: /FOSS/PHP/HumanMathTest/AreaVars.php,v $
 */
/**
*	Section-specific globals for the Test section
*
*	Author:			Fred Koschara
*	Creation Date:	May twenty-first, 2018
*	Last Modified:	May 23, 2018 @ 1:30 am
*
*	Revision History:
*	   Date		  by		Description
*	2018/05/23	wfredk	original development
*		|						|
*	2018/05/21	wfredk	original development
*/
global	$bNoHelpIcons,$bNoLblLnks,$bRbnNoColon,$pgBnrAlt,$pgBnrImg,$pgBnrPath,
		$sDir,$sessionLimiterValue;
// we can't use COPYRIGHT_OWNER, SITE_SLOGAN here b/c they're not define()d yet
if (!isset($pgBnrAlt)) $pgBnrAlt='FKE Internet - Hosting the Future';
if (!isset($pgBnrImg)) $pgBnrImg='DownloadSoftware.gif';
if (!isset($pgIconHeight)) $pgIconHeight=70;
if (!isset($pgIconWidth)) $pgIconWidth=180;
if (!isset($pgBnrLnk)) $pgBnrLnk='HumanMathTest.zip';
//	$pgBnrLnk=dirname($_SERVER['PHP_SELF']).'/HumanMathTest.zip';
if (!isset($pgBnrPath)) $pgBnrPath='/graphics';
$sDir='test';
$sessionLimiterValue='nocache';
include_once dirname($_SERVER['DOCUMENT_ROOT']).'/config/SiteVars.php';

include_once SCRIPT_ROOT.'/FormWriteHidden.php';

JsInclude('centerFooter');
//
// EOF: AreaVars.php
