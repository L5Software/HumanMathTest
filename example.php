<?php
define('remotefilename','/FOSS/PHP/HumanMathTest/example.php');
define('LAST_MODIFIED','May 23, 2018 @ 2:48 am');
define('COPYRIGHT_YEARS',(($year=date('Y'))==2018 ? '' : '2018-').$year);
/*
 *	LICENSE: SPDX short identifier: BSD-3-Clause
 *
 *	Copyright 2018 by Fred Koschara
 *
 *	Redistribution and use in source and binary forms, with or without
 *	modification, are permitted provided that the following conditions are met:
 *
 *	1.	Redistributions of source code must retain the above copyright notice,
 *		this list of conditions and the following disclaimer.
 *
 *	2.	Redistributions in binary form must reproduce the above copyright
 *		notice, this list of conditions and the following disclaimer in the
 *		documentation and/or other materials provided with the distribution.
 *
 *	3.	Neither the name of the copyright holder nor the names of its
 *		contributors may be used to endorse or promote products derived from
 *		this software without specific prior written permission.
 *
 *	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 *	AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 *	ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 *	LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 *	CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *	POSSIBILITY OF SUCH DAMAGE.
 *
 *==============================================================================
 *
 *	Please consider supporting my work:
 *		http://wfredk.com/donate.php
 *		https://RaceToSpaceProject.com/shopping/bookpreorder.php
 *
 *	Email: foss (at) L5Software (dot) com
 *
 *==============================================================================
 *
 *	$Id: /FOSS/PHP/HumanMathTest/example.php,v $
 */
/**
*	Simplified example of using the HumanMathTest class
*
*	Author:			Fred Koschara
*	Creation Date:	May twentieth, 2018
*
*	Revision History:
*	   Date		  by		Description
*	2018/05/23	wfredk	original development
*		|						|
*	2018/05/20	wfredk	original development
*/
define('PAGE_TITLE','HumanMathTest Class Example');
define('FONT_PATH','fonts');

use L5Software\FormTools\HumanMathTest as HumanMathTest;
require_once 'HumanMathTest.php';

foreach ($_REQUEST as $key=>$value)
	if ($key!='PHPSESSID')
		$GLOBALS[$key]=$value;
if (isset($_SESSION) && is_array($_SESSION))
	foreach ($_SESSION as $key=>$value)
		$GLOBALS[$key]=$value;

$formDone=!empty($formDone);
$answer=empty($answer) ? '' : htmlspecialchars_decode(trim($answer));

$style=HumanMathTest::defaultStyle();
$style['bkgnd']='ffffff';
$style['color']='000000';
$mathTest=new HumanMathTest(!$formDone,$style);	// protect against page reloads

$errors=array();
$result=FALSE;

if ($formDone)	// validate submitted form entries
{	$ipPort=$_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];

	$bads=array();
	$bBogus=($bads['bogusData']=$mathTest->bogusData());
	$bBogus|=($bads['answer']=!empty($answer) && (strlen($answer)>2));
	if ($bBogus)
	{	$flags='';
		foreach ($bads as $key=>$bad) $flags.=' '.($bad ? $key : 'f');
		error_log('Invalid form data in '.__FILE__.' from '.$ipPort
				 .', flags:'.$flags);
		$errors[]='Invalid data.';
	}
	else	// no hacking attempts detected
	{	if (($err=$mathTest->verify($answer))) $errors[]=$err;

		if (!count($errors))	// no errors found
			$result='Your answer was correct.';
	}	// no hacking attempts detected
}	// if ($formDone) validate submitted form entries

/*
 *	-------- This is where processing the protected form would be done ---------
 */

//+++++++++++++++++++++++++++++ page contents +++++++++++++++++++++++++++++++ ?>
<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta name="resource-type" content="document" />
	<meta http-equiv="content-language" content="English" />
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-type" content="text/html" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="author" content="Fred Koschara" />
	<meta name="copyright" content="<?php echo COPYRIGHT_YEARS ?> by Fred Koschara" />
	<meta name="distribution" content="Global" />
	<meta name="rating" content="General" />
	<style type="text/css">
		*
		{	font-family:Verdana, Arial, Helvetica, sans-serif;
		}
		body
		{	margin:0;
			padding:7px;
			vertical-align:top;
		}
		form
		{	display:inline;	/* required to make InternetExploiter work right */
		}
		img, fieldset
		{	border:0;
			margin:0;
			padding:0;
		}
		div#footer
		{	font-style:italic;
			margin-top:50px;
			text-align:center;
		}
		h1#pageTitle
		{	margin-top:18px;
			text-align:center;
		}
		span#label
		{	color:#339933;
			font-weight:bold;
		}
		table#buttons
		{	border:0;
			margin:2em auto;
		}
		table#centeredForm
		{	border:0;
			margin:auto;
			padding:5px;
		}
		td#equals
		{	color:#999922;
			font-weight:bold;
			text-align:center;
			width:2.5em;
		}
	</style>
	<title><?php echo PAGE_TITLE ?></title>
</head>
<body>
<h1 id="pageTitle"><?php echo PAGE_TITLE ?></h1>
<hr />
<?php if ($formDone)
{	if (($cnt=count($errors)))
	{ ?>
<p>The following error<?php echo ($cnt==1 ? ' was' : 's were') ?> detected:</p>
<ul>
<?php	foreach ($errors as $err)
			echo '	<li>'.$err.'</li>'."\n" ?>
</ul>
<?php
	}
	else if ($result)
		echo '<p>'.$result.'</p>';
	echo '<hr />'."\n";
} ?>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<fieldset>
	<input type="hidden" name="formDone" id="formDone" value="1" />
</fieldset>
<table id="centeredForm"><tr>
	<td><span id="label">Math Test</span>:&nbsp;</td>
	<td><?php echo $mathTest->image('math') ?></td>
	<td id="equals">=</td>
	<td><input size="3" maxlength="2" value="" name="answer" id="answer" /></td>
</tr></table>
<table cellpadding="20" id="buttons"><tr>
	<td><input type="reset" /></td>
	<td> &nbsp; </td>
	<td><input type="submit" value="Submit" /></td>
</tr></table>
</form>

<div id="footer">
<hr />
<?php echo PAGE_TITLE ?> /
Last modified <?php echo LAST_MODIFIED ?><br />
<small>
	Copyright &copy; <?php echo COPYRIGHT_YEARS ?> by Fred Koschara.
	BSD-3-Clause License.
</small>
</div>
</body></html>
<?php
//----------------------------- page contents ----------------------------------
//
// EOF: example.php
