<?php
define('remotefilename','/FOSS/PHP/HumanMathTest/index.php');
define('LAST_MODIFIED','September 27, 2018 @ 1:58 pm');
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
 *	$Id: /FOSS/PHP/HumanMathTest/index.php,v $
 */
/**
*	Test fixture for the HumanMathTest class
*
*	Author:			Fred Koschara
*	Creation Date:	May sixteenth, 2018
*
*	Revision History:
*	   Date		  by		Description
*	2018/09/27	wfredk	added "Limitations and Scope" section
*	2018/05/23	wfredk	original development
*		|						|
*	2018/05/16	wfredk	original development
*/
define('PAGE_TITLE','HumanMathTest Class Test Fixture');
require_once 'AreaVars.php';

//==============================================================================
// callable functions

/**
* creates the essential content of the page
*
* @return nothing (browser output)
*/
function Content()
{	global $chTag,$dfltTest,$minusTest,$PHP_SELF,$plusTest,$randTest,$timesTest;
//	include SCRIPT_ROOT.'/ShowGlobals.php';
//+++++++++++++++++++++++++++++ page contents +++++++++++++++++++++++++++++++ ?>

<p>
<b>HumanMathTest</b> implements a math test 'bot deterrent PHP class for use in
online forms, written and Copyright <?php echo COPYRIGHT_YEARS ?> by Fred
Koschara, and released under the terms of the <?php
	ElnkUrlT('BSD-3-clause license',
			 'https://opensource.org/licenses/BSD-3-Clause') ?>.  It can be
<?php ClassUrl('HumanMathTest.zip','TxtColor','downloaded') ?> by clicking
the <?php
	ClassUrl('HumanMathTest.zip','bold LblColor','Download') ?> button at
the upper right corner of this page.  The ZIP file includes the source code for
the <a href="example.php">simple example page</a>, this page (which illustrates
using many of the options available), and the fully documented implementation
<span class="bold OptColor">HumanMathTest.php</span> file.
</p>
<p>
This class is used to create an image to be included in an online form that
shows a simple math test the visitor must solve when submitting the form.  The
operands and operation are stored in the <span class="code">$_SESSION</span>
data for the page.  After submitting the form, the visitor's answer is checked
by calling the <span class="code">verify()</span> method to compare their entry
vs. the session data.  If an error is found, the form submission should be
rejected.
</p>

<h3>Limitations and Scope</h3>

<p>
One problem with this sort of form protection is that the math problems need
to be very simple for average users to succeed. Answers are limited to integers
in the 0-99 range so that users can calculate them. Even without using optical
character recognition to parse the image and calculate the correct answer, a bot
attempting numerous trials will succeed with a small but harmful percentage once
it knows the field requires numeric input and the valid range. This is a start
but the challenge is to make it harder for bots to defeat while keeping it easy
enough for humans of almost all capabilities to succeed.
</p><p>
Used in isolation, this test is far from adequate protection on a form where
anything more than minimal security is needed because of the statistically high
possibility of getting a correct answer from a random selection.
</p><p>
This class, as written, is intended to reduce noise submissions on a survey
form, not to preventing someone from biasing the outcome.
</p>

<hr class="trpspace" />
<p class="glueDown">Please consider supporting my work:</p>
<ul class="glueUp">
	<li><?php ElnkUrlT('Donations','http://wfredk.com/donate.php') ?> are
		always welcome</li>
	<li><?php ElnkUrlT('Buy a (prepublication) copy',
	'https://RaceToSpaceProject.com/shopping/bookpreorder.php') ?> of the <?php
	ClassUrl('https://RaceToSpaceProject.com','bookTitle',
			 'Race To Space') ?> book</li>
	<li>A selection from my <?php ElnkUrlT('Amazon Wish List',
	 'https://www.amazon.com/gp/registry/wishlist/I09W8MA0P802/'
	.'ref=nav_wishlist_lists_1?sort=universal-title') ?> would be nice</li>
	<li><?php ElnkUrlT('PayPal.me','https://www.paypal.me/wfredk') ?></li>
</ul>
<p>Email: foss (at) L5Software (dot) com</p>
<hr />

<form action="<?php echo $PHP_SELF ?>" method="post">
	<fieldset>
<?php FormWriteHidden('formDone',1) ?>
	</fieldset>
	<table class="centeredForm">
		<tr>
			<td class="right">
				<span class="bold LblColor">Plus</span>:&nbsp;
			</td>
			<td class="top right"><?php echo $plusTest->image('plus') ?></td>
			<td class="center bold LblColor">is</td>
			<td class="left"><input size="3" maxlength="2" value="" <?php
	AttrNi('plusAns') ?> /></td>
		</tr><tr>
			<td class="right">
				<span class="bold LblColor">Times</span>:&nbsp;
			</td>
			<td class="top right">
				<?php echo $timesTest->silentImage('times') ?>
			</td>
			<td class="center OptColor bold">=</td>
			<td class="left"><input size="3" maxlength="2" value="" <?php
	AttrNi('timesAns') ?> /></td>
		</tr><tr>
			<td class="right">
				<span class="bold LblColor">Minus</span>:&nbsp;
			</td>
			<td class="top right"><?php echo $minusTest->image('minus') ?></td>
			<td class="center">=></td>
			<td class="left"><input size="3" maxlength="2" value="" <?php
	AttrNi('minusAns') ?> /></td>
		</tr><tr>
			<td class="center" colspan="4"><hr class="half" /></td>
		</tr><tr>
			<td>&nbsp;</td>
			<td class="right"><input type="button" <?php
	?>value="<?php echo $randTest->newAlt() ?>" /></td>
			<td>&nbsp;</td>
		</tr><tr>
			<td class="right">
				<span class="bold LblColor">Random</span>:&nbsp;
			</td>
			<td class="top right">
				<?php echo $randTest->silentImage('rand') ?>
			</td>
			<td class="center LblColor">equals</td>
			<td class="left"><input size="3" maxlength="2" value="" <?php
	AttrNi('randAns') ?> /></td>
		</tr><tr>
			<td class="center" colspan="4"><hr class="half" /></td>
		</tr><tr>
			<td class="right">
				<span class="bold LblColor">Default</span>:&nbsp;
			</td>
			<td class="top right"><?php echo $dfltTest->image('dflt') ?></td>
			<td class="center bold OptColor bigger">=</td>
			<td class="left"><input size="3" maxlength="2" value="" <?php
	AttrNi('dfltAns') ?> /></td>
		</tr><tr>
			<td class="center" colspan="4">
				<hr />
				<span class="RqdColor">For Next Time:</span><br />
				These controls adjust the tests <b>after</b> form submission.
				<hr />
			</td>
		</tr><tr>
			<td class="top right">
				<span class="bold LblColor">Plus</span>:&nbsp;
			</td>
			<td class="left OptColor" colspan="3">
				<?php Radio('plusMsg',PM_DEFAULT) ?>&lt; default &gt;<br />
				<?php Radio('plusMsg',PM_EMPTY) ?>&lt; empty &gt;<br />
				<?php Radio('plusMsg',PM_PLAIN) ?>Sorry, wrong answer!<br />
				<?php Radio('plusMsg',PM_REVERSE) ?>%s is not %d<br />
				<?php Radio('plusMsg',PM_A_ONLY) ?>The answer %d is wrong<br />
				<?php Radio('plusMsg',PM_Q_ONLY) ?>Bad entry for %s<br />
				<?php Radio('plusMsg',PM_ARRAY) ?>&lt; array value &gt;<br />
				<?php Radio('plusMsg',PM_BOGUS) ?>&lt; bogus empty value &gt;
			</td>
		</tr><tr>
			<td class="right">
				<span class="bold LblColor">Minus</span>:&nbsp;
			</td>
			<td class="left OptColor" colspan="3">
				<input type="checkbox" value="1" <?php
	AttrNi('chgTag');
	if ($chTag) echo ATTR_CHECKED ?> />&nbsp; Change $tag test, see error log
			</td>
		</tr><tr>
			<td class="top right">
				<span class="bold LblColor">Times</span>:&nbsp;
			</td>
			<td class="left OptColor" colspan="3">
				<?php Radio('timesSize',TS_NORMAL) ?>Normal &nbsp; &nbsp;
				<?php Radio('timesSize',TS_BIG) ?>Big &nbsp; &nbsp;
				<?php Radio('timesSize',TS_NARROW) ?>Narrow &nbsp; &nbsp;
				<?php Radio('timesSize',TS_TINY) ?>Tiny &nbsp; &nbsp;
				<?php Radio('timesSize',TS_ERROR) ?>Error
			</td>
		</tr><tr>
			<td class="top right">
				<span class="bold LblColor">Random</span>:&nbsp;
			</td>
			<td class="left OptColor" colspan="3">
				<?php Radio('randOps',RO_ADDSUB) ?>Plus/Minus &nbsp; &nbsp;
				<?php Radio('randOps',RO_ADDMUL) ?>Plus/Times &nbsp; &nbsp;
				<?php Radio('randOps',RO_MULSUB) ?>Times/Minus &nbsp; &nbsp;
				<?php Radio('randOps',RO_ALL) ?>All Operators &nbsp; &nbsp;
				<?php Radio('randOps',RO_ERROR) ?>Error
			</td>
		</tr>
	</table>
	<table cellpadding="20" class="bareCenteredNoPad trpspace"><tr>
		<td><input type="reset" /></td>
		<td> &nbsp; </td>
		<td><input type="submit" class="rightButton" value="Submit" /></td>
	</tr></table>
</form>

<hr class="delimiter" />

<pre>
<?php
	echo '$plusTest: '.$plusTest
		.'$timesTest: '.$timesTest
		.'$minusTest: '.$minusTest
		.'$randTest: ';
	echo $randTest;	// echo treats it as a string even without concatenation
	echo '$dfltTest: '.$dfltTest
		."\n" ?>
</pre>
<?php
//----------------------------- page contents ----------------------------------
}	// Content()

/**
* writes a radiobutton to the page, selecting the active one
*
* @param string $group, radiobutton group name
* @param string $value, value for the current radiobutton
* @return nothing (browser output)
*/
function Radio($group,$value)
{	echo '<input type="radio" name="'.$group.'" value="'.$value.'"';
	if ($GLOBALS[$group]==$value) echo ATTR_CHECKED;
	echo ' />&nbsp;';
}	// Radio($group,$value)

//==============================================================================
// exceptional operations

//------------------------------------------------------------------------------
// configurable controls

//------------------------------------------------------------------------------
// global constants, variables and code
define('PM_DEFAULT',0);	// use default message
define('PM_EMPTY',1);	// use empty message
define('PM_PLAIN',2);	// 'Sorry, wrong answer!' (no inserts)
define('PM_REVERSE',3);	// '%s is not %d' (parameter order reverse of default)
define('PM_A_ONLY',4);	// 'The answer %d is wrong' (only insert answer)
define('PM_Q_ONLY',5);	// 'Bad entry for %s' (only insert question)
define('PM_BOGUS',6);	// use non-string empty value to log the error
define('PM_ARRAY',7);	// use non-empty array to log the error

define('RO_ADDSUB',0);
define('RO_ADDMUL',1);
define('RO_MULSUB',2);
define('RO_ALL',3);
define('RO_ERROR',4);	// see error log for report

define('TS_NORMAL',0);
define('TS_BIG',1);
define('TS_NARROW',2);
define('TS_TINY',3);
define('TS_ERROR',4);	// see error log for report

use L5Software\FormTools\HumanMathTest as HumanMathTest;
require_once 'HumanMathTest.php';

GlobalInit();	// read post and session data into global variables
$chTag=!empty($chTag);
$dfltAns=(empty($dfltAns) ? '' : FormTxtDcode($dfltAns));
$formDone=!empty($formDone);
$minusAns=(empty($minusAns) ? '' : FormTxtDcode($minusAns));
$plusAns=(empty($plusAns) ? '' : FormTxtDcode($plusAns));
$plusMsg=(empty($plusMsg) ? PM_DEFAULT : FormTxtDcode($plusMsg));
$randAns=(empty($randAns) ? '' : FormTxtDcode($randAns));
$randOps=(empty($randOps) ? RO_ADDSUB : FormTxtDcode($randOps));
$timesAns=(empty($timesAns) ? '' : FormTxtDcode($timesAns));
$timesSize=(empty($timesSize) ? TS_NORMAL : FormTxtDcode($timesSize));

$bReload=!$formDone;	// protect against page reloads

$dfltTest=new HumanMathTest($bReload);

$config=array('operator'=>HumanMathTest::OP_MINUS,'tag'=>'MINUS');
$minusTest=new HumanMathTest($bReload,NULL,$config);	// only subtraction here

$config=HumanMathTest::defaultConfig();
$config['operator']=HumanMathTest::OP_PLUS;	// only allow addition here
$config['tag']='PLUS';
$plusTest=new HumanMathTest($bReload,NULL,$config);

$style['bkgnd']='777';	// set background to medium gray
$config=array			// prohibit multiplication questions
		(	'errMsg'=>'%s does not equal %d',	// set error msg @ construction
			'operator'=>(HumanMathTest::OP_PLUS|HumanMathTest::OP_MINUS),
			'tag'=>'RAND'
		);
$randTest=new HumanMathTest($bReload,$style,$config);

$style=HumanMathTest::defaultStyle();
$style['bkgnd']='ffffff';
$style['color']='9d5907';	// color used if incorrect hex string found
$config=array
		(	'operator'=>HumanMathTest::OP_TIMES,	// only allow multiplication
			'tag'=>'_TIMES'
		);
$timesTest=new HumanMathTest($bReload,$style,$config);

if ($formDone)	// validate submitted form entries
{	$ipPort=$_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];

	$bads=array();
	$bBogus=($bads['bogusData/dflt']=$dfltTest->bogusData());
	$bBogus|=($bads['bogusData/minus']=$minusTest->bogusData());
	$bBogus|=($bads['bogusData/plus']=$plusTest->bogusData());
	$bBogus|=($bads['bogusData/rand']=$randTest->bogusData());
	$bBogus|=($bads['bogusData/times']=$timesTest->bogusData());
	$bBogus|=($bads['dfltAns']=!empty($dfltAns) && (strlen($dfltAns)>2));
	$bBogus|=($bads['minusAns']=!empty($minusAns) && (strlen($minusAns)>2));
	$bBogus|=($bads['plusAns']=!empty($plusAns) && (strlen($plusAns)>2));
	$bBogus|=($bads['randAns']=!empty($randAns) && (strlen($randAns)>2));
	$bBogus|=($bads['timesAns']=!empty($timesAns) && (strlen($timesAns)>2));
	if ($bBogus)
	{	$flags='';
		foreach ($bads as $key=>$bad) $flags.=' '.($bad ? $key : 'f');
		error_log('Invalid form data in '.__FILE__.' from '.$ipPort
				 .', flags:'.$flags);
		$gErrorList->add('Invalid data.');
		$formDone=FALSE;
	}
	else	// no hacking attempts detected
	{	if (($err=$plusTest->verify($plusAns))) $gErrorList->add($err);
		if (($err=$timesTest->verify($timesAns))) $gErrorList->add($err);
		if (($err=$minusTest->verify($minusAns))) $gErrorList->add($err);
		if (($err=$randTest->verify($randAns))) $gErrorList->add($err);
		if (($err=$dfltTest->verify($dfltAns))) $gErrorList->add($err);

		if ($gErrorList->noneHere())
			$gNoticeList->add('All answers were correct.');
		else $formDone=FALSE;
	}	// no hacking attempts detected
}	// if ($formDone) validate submitted form entries

/*
 *	-------- This is where processing the protected form would be done ---------
 */

if (!$bReload)	// form was submitted, process any change requests
{	if ($chTag)
		$minusTest->config(array('tag'=>'another','errMsg'=>"Don't do it!!"));

	if ($plusMsg!=PM_DEFAULT)
	{	$config=array('operator'=>HumanMathTest::OP_PLUS,'tag'=>'PLUS');
		switch ($plusMsg)
		{	case PM_EMPTY:	// use empty message
				$msg='';
				break;
			case PM_PLAIN:	// no inserts
				$msg='Sorry, wrong answer!';
				break;
			case PM_REVERSE:	// parameter order reverse of default
				$msg='%s is not %d';
				break;
			case PM_A_ONLY:	// only insert answer
				$msg='The answer %d is wrong';
				break;
			case PM_Q_ONLY:	// only insert question
				$msg='Bad entry for %s';
				break;
			case PM_BOGUS:	// use non-string empty value to log the error
				$msg=FALSE;
				break;
			case PM_ARRAY:	// use non-empty array to log the error
				$msg=array('invalid','string','list');
		}
		$config['errMsg']=$msg;
		$plusTest->config($config,TRUE);
	}

	if ($randOps!=RO_ADDSUB)
	{	$config=array('tag'=>'RAND',);	// tag MUST NOT be changed
		switch ($randOps)
		{	case RO_ADDMUL:
				$ops=HumanMathTest::OP_PLUS | HumanMathTest::OP_TIMES;
				break;
			case RO_MULSUB:
				$ops=HumanMathTest::OP_TIMES | HumanMathTest::OP_MINUS;
				break;
			case RO_ALL:
				$ops=HumanMathTest::OP_ALL;
				break;
			case RO_ERROR:
				$ops='error';
		}
		$config['operator']=$ops;
		$randTest->config($config,TRUE);
	}

	if ($timesSize!=TS_NORMAL)
	{	$style=array('bkgnd'=>'ffffff','color'=>'9d5907');
		switch ($timesSize)
		{	case TS_BIG:
				$style['height']=60;
				break;
			case TS_NARROW:
				$style['width']=90;
				break;
			case TS_TINY:
				$style['height']=20;
				$style['width']=45;
				break;
			case TS_ERROR:
				$style['bkgnd']=1;
				$style['color']='white';
				$style['height']='height';
				$style['width']=-1;
		}
		$timesTest->style($style);
	}
}

$headCss.=	// for CSP level 3 compliance
'	<style type="text/css">
		.bookTitle
		{	color:#ff6666;
			font-size:1.1em;
			font-style:italic;
			font-weight:bold;
			text-decoration:underline;
		}
	</style>
';

ShowSitePage();	// this eventually calls the above Content() function
//
// EOF: index.php
