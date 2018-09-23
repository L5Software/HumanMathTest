<?php
namespace L5Software\FormTools;

/* installed file=/FOSS/PHP/HumanMathTest/HumanMathTest.php
 *
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
 *	$Id: /FOSS/PHP/HumanMathTest/HumanMathTest.php,v $
 */
/**
*	Implements a math test 'bot deterrent class for use in online forms
*
*	Author:			Fred Koschara
*	Creation Date:	May tenth, 2018
*	Last Modified:	May 23, 2018 @ 1:30 am
*
*	Revision History:
*	   Date		  by		Description
*	2018/05/23	wfredk	original development
*		|						|
*	2018/05/10	wfredk	original development
*///

/**
* a class for adding a simple math test to forms to reduce 'bot submission noise
*
* This class is used to create an image to be included in an online form that
* shows a simple math test the visitor must solve when submitting the form.  The
* operands and operation are stored in the $_SESSION data for the page.  After
* submitting the form, the visitor's answer is checked by calling the verify()
* method to compare their entry vs. the session data.  If an error is found, the
* form submission should be rejected.
*
* By default, when the math test image is created and sent to the browser, the
* alt attribute of the <img> element includes a text representation of the math
* test question being asked.  This is done to keep forms accessible to visitors
* using assistive technologies such as screen readers.  However, writing code to
* read the alt attribute, translate it, and calculate the correct answer for the
* math test is an "almost" trivial task.  That would allow miscreants to bypass
* this security measure with a minimum of effort.  Consequently, the image()
* method is built such that passing FALSE as the $altRoot parameter will remove
* the math question from the <img> alt attribute.  If that is done, another
* means must be provided, e.g., for blind visitors, to learn what math question
* is being asked.  An example would be providing a button with a text-to-speech
* conversion of the string returned by the instance's alt() method.
*
* @license https://opensource.org/licenses/BSD-3-Clause		BSD-3-Clause
* @author Fred Koschara <foss@L5Software.com>
*/
class HumanMathTest
{	const DATA_TAG='FKE_HUMANMATHTEST_DATA';	// base $_SESSION identifier

	const ERR_MSG='Your Math Test answer %d for %s is incorrect.';	// default

	const OP_ALL=7;			// operator pseudo-selector for "all choices"
	const OP_MINUS=1;		// subtraction operator enablement flag
	const OP_PLUS=2;		// addition operator enablement flag
	const OP_TIMES=4;		// multiplication operator enablement flag

	protected $config;		// array, active configuration derived from $params
	protected $o1;			// int, first operand
	protected $o2;			// int, second operand
	protected $operator;	// int, selected math id, 0=plus, 1=times, 2=minus
	protected $opSet;		// array, enabled math operator identifiers
	protected $params;		// array, config parameters passed to __construct()
	protected $style;		// array, style data for creating math test image
	protected $tag;			// string, multi form session tag suffix, default=''

	// construction and initialization =========================================
	/**
	* initializes the class members to requested or default values
	*
	* Pass a flag indicating whether the form has been submitted as the $bReload
	* parameter.  For example, create a hidden input named 'formDone' with a
	* TRUE value in your HTML form.  Then, in the page startup code, generate
	* the flag's value using (empty($_POST['formDone')) as the parameter.
	*
	* Styling for the display of the math test question is controlled by the
	* passed $style array.  If NULL is passed (the default when only a $bReload
	* flag is used), the defaultStyle() method is called to set the test style.
	* The $style array can include the following members:
	*	'bkgnd', string, 3/6 char background hex color, or 'none' (transparent)
	*	'color', string, 3 or 6 character foreground (text) CSS hex color value
	*	'font', string, filespec of font file used for drawing text
	*	'height', int, image height (pixels), text height is 71% of this value
	*	'width', int, image width (pixels)
	* If any values are missing, they will be entered with default values.
	*
	* If the $style array includes a 'font' specification, it must include the
	* path to the font file, or the font file must be in the current directory.
	* If the $style array does NOT include a 'font' member, 'monofont.ttf' will
	* be used, which must be in the directory specified by first defined string
	* constant in the set ('FONT_PATH','SCRIPT_ROOT','ScriptRoot'), or it must
	* be in the current directory.  A font file compatible with PHP's GD library
	* extension must be used.
	*
	* The text of the math equation will be 71% of the height of the allocated
	* display space, and will be centered both horizontally and vertically on
	* the display surface.
	*
	* The $params array can include an 'operator' key to control which math
	* operations are or may be used in building the math test equation.  Three
	* public class constants - OP_PLUS, OP_TIMES and OP_MINUS - can be OR'd
	* together to activate the use of addition, multiplication or subtraction
	* equations, respectively.  For convenience, OP_ALL is also available to
	* enable all three types of operations.  This is the default behavior for
	* the class if the $params array does not include an 'operator' member or if
	* it has an empty value.  If only one type of math operator is to be used,
	* pass it as the 'operator' member of the $params array.  When two or three
	* types of operations are enabled, each test will use a random selection
	* from the enabled operators to create the math test.  For example, to leave
	* out multiplication from the choices, build your $params array using
	*	$params['operator']=(HumanMathTest::OP_PLUS | HumanMathTest::OP_MINUS);
	* The math tests produced will randomly have either addition or subtraction
	* operations used in creating the test equations.
	*
	* The error message displayed when a wrong answer is detected is determined
	* by the 'errMsg' member of the $params array.  It can be set up to include
	* the math test that was asked, the user's answer, neither, or both.  The
	* test question will be inserted into the error message if a '%s' format
	* specifier is found in the configuration string (but only at the _first_
	* occurrence of a '%s' specifier).  If a '%d' format specifier is included,
	* the user's answer will be inserted in its place (again, only at the first
	* '%d' specifier).  If neither a '%s' nor a '%d' is found in the format
	* string, it will be displayed as-is.  The '%s' and '%d' format specifiers
	* can be included in either order, but only the first of each will be used.
	* If $params does not include an 'errMsg' member, the default string
	*			'Your Math Test answer %d for %s is incorrect.'
	* will be used.  If 'errMsg' is otherwise empty (boolean FALSE, an empty
	* string, an array with no elements, etc.), 'HumanMathTest error' will be
	* used.  In addition, unless an empty string, specifically, is passed as
	* the empty 'errMsg' value, an error will be logged.
	*
	* The $params array can also include a 'tag' member string that will be
	* appended to the name of the $_SESSION data identifier used to store the
	* instance data for the class.  This is required to support multiple math
	* tests on a particular form, or on multiple forms that may be active in an
	* application simultaneously.  Otherwise, each instance would replace the
	* $_SESSION data with its own parameters, making it impossible to get a
	* correct verification result.  If a 'tag' member is present in the $params
	* array, it can only contain alphanumeric characters or underscores, or an
	* error will be logged and an empty tag used in its place.
	*
	* @param boolean $bReload, TRUE=page being [re]loaded, FALSE=form submitted
	* @param array $style, math test display styling, NULL=use defaultStyle()
	* @param array $params, configuration parameters, NULL=use defaultConfig()
	* @return HumanMathTest, instantiated class object
	*/
	public function __construct($bReload,$style=NULL,$params=NULL)
	{	$this->style($style);	// set the style for this instance

		$this->_config($params);	// protected method leaves $_SESSION intact

		if (!$bReload	// not (initial view or reload) == form submitted
		&&	!empty($_SESSION[$this->tag]))	// session data is available
		{	$math=$_SESSION[$this->tag];	// remember what was in the session
			unset($_SESSION[$this->tag]);	// discard it - no reuse allowed
			if (is_array($math) && array_key_exists('operator',$math)
			&&	array_key_exists('o1',$math)
			&&	array_key_exists('o2',$math))	// if all of the data exists
			{	if ($math['o1']<0 || $math['o1']>9
				||	$math['o2']<0 || $math['o2']>9
				||	$math['operator']<0 || $math['operator']>2
				||	$math['operator']==2 && $math['o1']<$math['o2'])
				{	error_log('Invalid '.__CLASS__.' data in $_SESSION from '
							 .$_SERVER['REMOTE_ADDR'].': '.print_r($math,TRUE));
					$this->operator=-214;	// invalid data found, flag it
					return;
				}	// end of "invalid data" block
				$this->o1=$math['o1'];
				$this->o2=$math['o2'];
				$this->operator=$math['operator'];
				if (!empty($math['errMsg']))	// error msg set before submit
					$this->config['errMsg']=$math['errMsg'];	// use it

			}	// if all of the data exists
			else $this->_init();	// incomplete data set, start over
		}
		else $this->_init();	// no $_SESSION data, form not submitted yet
	}	// __construct($bReload,$style=NULL,$params=NULL)

	// static public methods ===================================================
	/**
	* returns an array with the default configuration parameters defined:
	*	'errMsg', string, error message returned for wrong answers
	*		default='Your Math Test answer %d for %s is incorrect.'
	*	'operator', int, enables math operations, default=OP_ALL
	*		default=self::OP_ALL
	*	'tag', string, session id tag suffix for multiple instances on one form
	*		default=''
	*
	* A typical use would be for building a complete configuration to pass to
	* the class' constructor with only one or two parameters changed, e.g.
	*	$cfg=HumanMathTest::defaultConfig();
	*	// prohibit multiplication questions
	*	$cfg['operator']=(HumanMathTest::OP_PLUS|HumanMathTest::OP_MINUS);
	*	// create a new instance with the default style and set configuration
	*	$mathTest=new HumanMathTest($bReload,NULL,$cfg);
	*
	* @return array, configuration definition
	*/
	public static function defaultConfig()
	{	return array
				(	'errMsg'=>self::ERR_MSG,
					'operator'=>self::OP_ALL,
					'tag'=>'',
				);
	}	//  defaultConfig()

	/**
	* returns the default directory name for (path to) font files
	*
	* In order, string constants with these names are checked if they have been
	* defined: 'FONT_PATH', 'SCRIPT_ROOT' and 'ScriptRoot'.  The first one found
	* is expected to be the directory location for font files.  If none are set,
	* the current directory is assumed, and an empty string is returned.
	*
	* If a directory name is found, it will be returned with a slash (directory
	* separator) as the last character of the returned string:  The name of the
	* font file can be directly concatenated to build the font file's FQDN.
	*
	* @return string, directory name string
	*/
	public static function defaultFontPath()
	{	if (defined('FONT_PATH')) $pth=FONT_PATH;
		else if (defined('SCRIPT_ROOT')) $pth=SCRIPT_ROOT;
		else if (defined('ScriptRoot')) $pth=ScriptRoot;
		else return '';
		return (substr($pth,-1)==='/') ? $pth : $pth.'/';
	}	// defaultFontPath()

	/**
	* returns an array with the default style parameters defined:
	*	'bkgnd', string, 3/6 char background hex color, or 'none' (transparent)
	*		default='none'
	*	'color', string, 3 or 6 character foreground (text) CSS hex color value
	*		default='ffffff'
	*	'font', string, filespec of font file used for drawing text
	*		default=HumanMathTest::defaultFontPath().'monofont.ttf'
	*	'height', int, image height (pixels), text height is 71% of this value
	*		default=40
	*	'width', int, image width (pixels)
	*		default=120
	*
	* A typical use would be for building a complete style to pass to the class'
	* constructor with only one or two parameters changed, e.g.
	*	$style=HumanMathTest::defaultStyle();
	*	$style['bkgnd']='777';	// set background to medium gray
	*	$mathTest=new HumanMathTest($bReload,$style);
	*
	* @return array, style definition
	*/
	public static function defaultStyle()
	{	return array
				(	'bkgnd'=>'none',
					'color'=>'ffffff',
					'font'=>self::defaultFontPath().'monofont.ttf',
					'height'=>40,
					'width'=>120,
				);
	}	// defaultStyle()

	/**
	* checks if there is valid $_SESSION data for an instance of the class
	*
	* @param string $tag, the instance identifier to check
	* @return boolean, TRUE=valid session data exists, FALSE=no $_SESSION data
	*/
	public static function sessionHasData($tag)
	{	if (empty($_SESSION[$tag]))
			return FALSE;
		if (is_array($_SESSION[$tag]))
		{	$math=&$_SESSION[$tag];	// dereferenced handle for speed
			if (array_key_exists('operator',$math)
			&&	array_key_exists('o1',$math)
			&&	array_key_exists('o2',$math)	// all of the data exists
			&&	$math['o1']>=0 && $math['o1']<=9
			&&	$math['o2']>=0 && $math['o2']<=9
			&&	$math['operator']>=0 && $math['operator']<=2
			&&	($math['operator']!=2 || $math['o1']>=$math['o2']))
				return TRUE;	// valid session data exists
		}
		return FALSE;
	}	//  sessionHasData($tag)

	// instance public methods =================================================
	/**
	* returns a text representation of the current math question
	*
	* If the current form has already been submitted and the page is being
	* re-rendered (e.g., because of a form data error), this method will only
	* return the correct string after an image() (or silentImage()) call resets
	* the $_SESSION data.  To get the correct equation text for a new page
	* display before calling one of those methods (e.g., to position a "say the
	* math test" button earlier on the page than where the test is displayed),
	* call the newAlt() method instead, which will create a new math test.
	*
	* @return string, the math test question being asked
	*/
	public function alt()
	{	switch ($this->operator)	// build the question's text representation
		{	case 0:	$aOp='plus';	break;
			case 1:	$aOp='times';	break;
			case 2:	$aOp='minus';	break;
		}
		return $this->_dgt($this->o1).' '.$aOp.' '.$this->_dgt($this->o2);
	}	// alt()

	/**
	* class data integrity test, ensures an expected result can be calculated
	*
	* @return boolean, TRUE=inconsistent data in class member variables
	*/
	public function bogusData()
	{	return 	$this->o1<0 || $this->o1>9
			||	$this->o2<0 || $this->o2>9
			||	$this->operator<0 || $this->operator>2
			||	$this->operator==2 && $this->o1<$this->o2;
	}	// test()

	/**
	* resets the configuration for the current instance of the class
	*
	* The following members are recognized in the $config array:
	*	'errMsg', string, error message displayed for wrong answers
	*	'operator', int, selects enabled math operations
	*	'tag', string, multi form session id tag suffix
	*
	* If the $config parameter is NULL, the configuration defined by
	* defaultConfig() will be used.
	*
	* N.B.:	**** WARNING ***
	*		This method discards any session data previously stored by the
	*		current instance, and the internal equation control values.
	*		If it is called after the form was submitted and before verify()
	*		is called, the verification result **WILL BE INCORRECT**
	*
	* N.B.:	**** WARNING ***
	*		If this method is called to change the operator selection set or
	*		error message after the class has been instantiated, the 'tag'
	*		string MUST NOT be modified or the page will not be able to build
	*		the correct session data tag after the form is submitted.  If an
	*		attempt to change the 'tag' value is detected, an error will be
	*		logged and the configuration change will be discarded.
	*
	* @param array $config, configuration parameters, default=NULL
	* @param boolean $bQuiet, TRUE=don't log warnings re. resets, default=FALSE
	* @return nothing (class members initialized)
	*/
	public function config($config=NULL,$bQuiet=FALSE)
	{	$bNew=!empty($config['tag']);	// check for attempted 'tag' change
		$bOld=!empty($this->config['tag']);
		if ($bNew && !$bOld || !$bNew && $bOld
		||	$bNew && $bOld && $config['tag']!=$this->config['tag'])
		{	error_log('Attempt to change '.__CLASS__.' session tag from "'
					 .$this->config['tag'].'" to "'.$config['tag']
					 .'" is invalid, operation aborted');
			return;
		}

		if (!empty($this->tag))	// configuration is being changed
		{	if (!$bQuiet)
				error_log(($who=__CLASS__.'::'.$this->tag).' being reset');
			if($this->_sessionHasData())		// previous config saved data
			{	unset($_SESSION[$this->tag]);	// discard it
				if (!$bQuiet)
					error_log($who.' session data discarded');
			}
			unset($this->tag);	// invalidate the configuration
		}

		$this->_config($config);	// reset the configuration for this instance

		$this->_init();	// set up the operating controls using the new config
	}	// config($config=NULL)

	/**
	* creates the <img> element representing the math test to be solved
	*
	* N.B.:	This method *returns a string* which may be an error message that
	* 		needs to be displayed to the user.  Be sure to echo() the result!
	*
	* If the form including this element was already submitted, the $_SESSION
	* data previously used will have been discarded.  This method checks for
	* that condition and automatically re-initializes the session with a new
	* math test for verification when the form is submitted again:  There is no
	* manual reset required for the class.
	*
	* If the newAlt() method has been called since the form was submitted, the
	* session will already have new math test data in it, so no reinitialization
	* will be needed or performed:  Either this method or newAlt() will create a
	* new math test for the page display, but not both.
	*
	* @param string $id, HTML element ID attribute for the <img>, default=''
	* @param mixed $altRoot, boolean FALSE=use 'math test' as alt attribute
	* 						 string, prefix used to construct the alt attribute
	*					default='Math Test question: what is '
	* @return string, the <img> element, including the base64 encoded image data
	*/
	public function image($id='',$altRoot='Math Test question: what is ')
	{	if (!$this->_sessionHasData())	// form is being redisplayed
			$this->_init();	// create a new data set

		if (!($img=@imagecreate($this->style['width'],$this->style['height'])))
			return $this->_imageAbort('Cannot initialize new GD image stream');

		$co=$this->style['bkgnd'];	// set image background
		if ($co==='none')
			$bkgnd=imagecolorallocatealpha($img,0,0,0,127);
		else
		{	list($r,$g,$b)=$this->_parseColor($co);
			$bkgnd=imagecolorallocate($img,$r,$g,$b);
		}

		list($r,$g,$b)=$this->_parseColor($this->style['color']);
		$cTx=imagecolorallocate($img,$r,$g,$b);	// set text color

		$str=$this->_str();	// build the equation text string for the image

		// create a text box on the drawing surface and write the test onto it
		$fSize=$this->style['height']*0.71;	// font size 71% of image height
		if (!($textbox=imagettfbbox($fSize,0,$this->style['font'],$str)))
			return $this->_imageAbort('Error in imagettfbbox function');
		$x=($this->style['width']-$textbox[4])/2;	// center the text
		$y=($this->style['height']-$textbox[5])/2;
		// write the math test text onto the drawing surface
		if (!(imagettftext($img,$fSize,0,$x,$y,$cTx,$this->style['font'],$str)))
			return $this->_imageAbort('Error in imagettftext function');

		ob_start();				// start output buffering to capture the image
		imagepng($img);			// write the image data to the output stream
		$bytes=ob_get_clean();	// get the bytes of output data from the stream

		imagedestroy($img);		// discard the graphic library's resources now

		$alt=($altRoot===FALSE ? 'math test' : $altRoot.$this->alt().'?');

		return '<img src="data:image/png;base64,'.base64_encode($bytes)
			  .'" alt="'.$alt.'" width="'.$this->style['width'].'" height="'
			  .$this->style['height'].(empty($id) ? '' : '" id="'.$id).'" />';
	}	// image($id='',$altRoot='Math Test question: what is ')

	/**
	* returns the equation text for the current page view
	*
	* If the form including this element was already submitted, the $_SESSION
	* data previously used will have been discarded.  This method checks for
	* that condition and automatically re-initializes the session with a new
	* math test for verification when the form is submitted again:  There is no
	* manual reset required for the class.
	*
	* If the image() (or silentImage()) method has been called since the form
	* was submitted, the session will already have new math test data in it, so
	* no reinitialization will be needed or performed:  Either this method or
	* image() will create a new math test for the page display, but not both.
	*
	* @return string, the [new] math test question being asked
	*/
	public function newAlt()
	{	if (!$this->_sessionHasData())	// form is being redisplayed
			$this->_init();				// create a new data set
		return $this->alt();	// return the [new] equation text
	}	// newAlt()

	/**
	* image() variant returning an <img> with an anonymous alt attribute string
	*
	* Unless another assistive method is provided (e.g., a "say the math test"
	* button that does a text-to-speech conversion of the question), using this
	* method will result in a form not usable by some handicapped persons.
	*
	* @param string $id, HTML element ID attribute for the <img>, default=''
	* @return string, the <img> element, including the base64 encoded image data
	*/
	public function silentImage($id='')
	{	return $this->image($id,FALSE);
	}	// silentImage($id='')

	/**
	* [re]sets the styling of the math equation display
	*
	* The $style array can include the following members:
	*	'bkgnd', string, 3/6 char background hex color, or 'none' (transparent)
	*	'color', string, 3 or 6 character foreground (text) CSS hex color value
	*	'font', string, filespec of font file used for drawing text
	*	'height', int, image height (pixels), text height is 71% of this value
	*	'width', int, image width (pixels)
	* If any values are missing, they will be entered with default values.
	*
	* If the $style parameter is NULL, the style defined by defaultStyle() will
	* be used.
	*
	* @param array $style, math test display styling, default=NULL
	* @return nothing (class members initialized)
	*/
	public function style($style=NULL)
	{	if ($style===NULL)	// default style requested
			$this->style=self::defaultStyle();
		else $this->_ensureGoodStyle($style);
	}	// style($style=NULL)

	/**
	* verifies the user's entry is the correct answer for the math question
	*
	* @param string $entry, the number entered on the form
	* @return mixed, boolean FALSE=no errors found, string=error message
	*/
	public function verify($entry)
	{	switch ($this->operator)
		{	case 0:	// plus
				$answer=$this->o1+$this->o2;
				break;
			case 1:	// times
				$answer=$this->o1*$this->o2;
				break;
			case 2:	// minus
				$answer=$this->o1-$this->o2;
				break;
			default:
				return 'Invalid operation';
		}
		if ($entry==$answer) return FALSE;	// the human passed the test

		$sPos=strpos($this->config['errMsg'],'%s');	// check for inserts
		$ePos=strpos($this->config['errMsg'],'%d');

		if ($sPos!==FALSE || $ePos!==FALSE)	// insert entry and/or question
		{	if ($sPos===FALSE)	// only insert entry
				return sprintf($this->config['errMsg'],$entry);
			if ($ePos===FALSE)	// only insert question
				return sprintf($this->config['errMsg'],$this->_str());
			if ($sPos<$ePos)	// insert question before entry
				return sprintf($this->config['errMsg'],$this->_str(),$entry);
			// insert question after entry
			return sprintf($this->config['errMsg'],$entry,$this->_str());
		}

		return $this->config['errMsg'];	// no inserts, just return error message
	}	// verify($entry)

	/**
	* PHP "magic" class method, returns a string representation of the instance
	*
	* @return string, representation of the class instance and its data
	*/
	public function __toString()
	{	$operations=$sep='';
		foreach ($this->opSet as $operator)
		{	switch ($operator)
			{	case 0:	$operations.=$sep.'add';		break;
				case 1:	$operations.=$sep.'multiply';	break;
				case 2:	$operations.=$sep.'subtract';	break;
			}
			$sep=',';
		}
		$params=$this->params===NULL
			   ? ' NULL'
			   : "\n\t".$this->_configToString($this->params);
		return ($this->bogusData() ? 'Inv' : 'V').'alid '.__CLASS__.' instance
{	equation: '.$this->_str().' ('.$this->alt().')
	error message: "'.$this->config['errMsg'].'"
	operations enabled: '.$operations.'
	session data tag: '.$this->tag.'
	session has data: '.($this->sessionHasData($this->tag) ? 'TRU' : 'FALS').'E
	style:'.$this->_configToString($this->style,FALSE).'
	configured parameters:'.$params.'
	active configuration:
	'.$this->_configToString($this->config).'
}
';
	}	// __toString()

	// protected internal support methods ======================================
	/**
	* [re]sets the configuration for the current instance of the class
	*
	* N.B.:	This method, not the public construct() method, **MUST** be the one
	* 		called from the constructor so the $_SESSION data remains intact.
	*
	* @param array $config, configuration parameters, default=NULL
	* @return nothing (class members initialized)
	*/
	protected function _config($config)
	{	if (($this->params=$config)===NULL)	// remember passed config
			$this->config=self::defaultConfig();	// use default configuration
		else $this->_setActiveConfig();		// build the active configuration

		$array=array();	// configure the enabled operator set
		if (($ops=$this->config['operator']) & self::OP_PLUS)
			$array[]=0;
		if ($ops & self::OP_TIMES)
			$array[]=1;
		if ($ops & self::OP_MINUS)
			$array[]=2;
		$this->opSet=$array;

		if (($tag=$this->config['tag']) && substr($tag,0,1)!=='_')
			$tag='_'.$tag;	// prefix a separator for readability
		$this->tag=self::DATA_TAG.$tag;	// construct our own $_SESSION tag
	}	// _config($config)

	/**
	* returns a string representation of a configuration or style array
	*
	* @param array $array, the config or style array to process
	* @param boolean $bConfig, TRUE=config array, FALSE=style array
	* @return string, formatted array representation for use in __toString()
	*/
	protected function _configToString($array,$bConfig=TRUE)
	{	$config=explode("\n",trim(print_r($array,TRUE)));
		unset($config[1]);	// Horstmann style formatting
		$cnt=0;
		$lines=array();
		$nLim=count($config)-1;	// number of lines in report, not array count
		$sep="\t\t(\t";	// first line includes opening paren
		foreach ($config as $line)	// indent style array
		{	if (!$cnt++)
			{	$lines[]="\t".$line;	// Array is not fully indented
				continue;
			}
			$line=trim($line);
			if ($bConfig)
			{	if (substr($line,1,8)=='operator')
				{	$str=substr($line,($pos=strrpos($line,' ')+1));
					if ($str=='=>')	// no value
						$line.=' ""';
					else $line=substr($line,0,$pos).(is_numeric($str)
							  ? $this->_opFlagsFromBits($str)
							  : '"'.$str.'"');
				}
				else if (substr($line,1,3)=='tag')
				{	$at=strpos($line,' =>')+3;
					if (strlen($line)==$at)	// no tag
						$line.=' ""';
					else $line=substr($line,0,++$at).'"'.substr($line,$at).'"';
				}
			}
			$lines[]=$sep.$line;
			if ($cnt==2 && $nLim>2)	// subsequent lines just indent
				$sep="\t\t\t";
			else if ($cnt==$nLim)
				$sep="\t\t";	// align close paren under open paren
		}
		return implode("\n",$lines);
	}	// _configToString($config,$bConfig=TRUE)

	/**
	* returns the text name of a digit
	*
	* @param integer $digit, the number to be translated
	* @return string, the text name of the specified digit
	*/
	protected function _dgt($digit)
	{	switch ($digit)
		{	case 0:	return 'zero';
			case 1:	return 'one';
			case 2:	return 'two';
			case 3:	return 'three';
			case 4:	return 'four';
			case 5:	return 'five';
			case 6:	return 'six';
			case 7:	return 'seven';
			case 8:	return 'eight';
			case 9:	return 'nine';
		}
		return 'bad digit value';
	}	// _dgt($digit)

	/**
	* constructor test to ensure background and foreground colors are valid
	*
	* @param string $type, color type, either 'bkgnd' or 'color' (foreground)
	* @param string $noColor, hex CSS color used if color is missing
	* @param string $errColor, hex CSS color used if bad color specifier found
	*/
	protected function _ensureColor($type,$noColor,$errColor)
	{	if (empty($this->style[$type]))	// ensure there's a style here
			$this->style[$type]=$noColor;
		else	// color specification exists, ensure it's valid
		{	$co=strtolower(strval($this->style[$type]));
			if ($type!=='bkgnd' || $co!=='none')	// not set bkgnd to 'none'
			{	$e=ctype_xdigit($co) ? '' : 'Invalid hex string';
				$e.=((strlen($co)==6 || strlen($co)==3)
					 ? '' : ($e ? ', b' : 'B').'ad length');
				if ($e)
				{	error_log($e.' ('.$co.') as '.$type.' style in '.__CLASS__);
					$this->style[$type]=$errColor;
				}
			}
			else if ($type==='bkgnd')	// setting background to 'none'
				$this->style['bkgnd']=$co;	// ensure case matches
		}
	}	// _ensureColor($type,$name,$noColor,$errColor)

	/**
	* checks the style data passed to the constructor and fixes invalid entries
	*
	* On completion, the instance's $style array will include these entries:
	*	'bkgnd', string, 3/6 char background hex color, or 'none' (transparent)
	*	'color', string, 3 or 6 character foreground (text) CSS hex color value
	*	'font', string, filespec of font file used for drawing text
	*	'height', int, image height (pixels), text height is 71% of this value
	*	'width', int, image width (pixels)
	*
	* This method ensures an existing font file is specified.  It does NOT check
	* the validity of the font file, only whether it exists or not.
	*
	* If the size of the image is specified as less than 10 pixels high or less
	* than 30 pixels wide, it will not be large enough to display the equation
	* using a 5x7 font, and an error will be logged.  The size of the display
	* surface will not be modified, however.
	*
	* @param array $style, math test display style configuration
	* @return nothing (class members initialized)
	*/
	protected function _ensureGoodStyle($style)
	{	if (!empty($style) && is_array($style))	// check for good style
			$this->style=$style;
		else
		{	error_log('Invalid $style passed to '.__CLASS__.': '
					  .print_r($style,TRUE));
			$this->style=self::defaultStyle();
			$this->style['bkgnd']='ffffff';
			$this->style['color']='ff0000';
		}

		$this->_ensureColor('bkgnd','none','cccccc');	// set background color
		$this->_ensureColor('color','ffffff','3300cc');	// set text color

		if (!empty($this->style['font']) && !file_exists($this->style['font']))
		{	error_log('Font "'.$this->style['font'].'" for '.__CLASS__
					 .'does not exist, using default');
			$this->style['font']=FALSE;
		}
		if (empty($this->style['font']))	// set the font if not specified
			$this->style['font']=self::defaultFontPath().'monofont.ttf';

		if (empty($this->style['height']))	// an image height is required
			$this->style['height']=40;
		else
		{	$this->style['height']=intval($this->style['height']);
			if ($this->style['height']<=0)
			{	error_log('Invalid height in '.__CLASS__.', using 40px');
				$this->style['height']=40;
			}
		}
		if (empty($this->style['width']))	// an image width is required
			$this->style['width']=120;
		else
		{	$this->style['width']=intval($this->style['width']);
			if ($this->style['width']<=0)
			{	error_log('Invalid width in '.__CLASS__.', using 120px');
				$this->style['width']=120;
			}
		}
		if ($this->style['height']<10 || $this->style['width']<30)
			error_log('Excessively small display in '.__CLASS__.': '
					 .$this->style['width'].'x'.$this->style['height']
					 .' px (WxH)');	// too small for 5x7 characters
	}	//  _ensureGoodStyle($style)

	/**
	* writes an error message to the browser, logs the failure and terminates
	*
	* @param string $why, description of the failure, for logging use only
	* @return string, error message to be displayed by the browser
	*/
	protected function _imageAbort($why)
	{	error_log($why);
		return 'OOPS! It\'s broken!';
	}	// _imageAbort($why)

	/**
	* initializes class members to a random set of values, stores session data
	*
	* The math operands will never be both zero, and if the operation selected
	* is subtraction, the minuend will always be at least as large as the
	* subtrahend.  Consequently, the result of the math operation will always
	* be a positive number between zero and 81.
	*
	* @return nothing (class members, $_SESSION data initialized)
	*/
	protected function _init()
	{	do
		{	$this->o1=mt_rand(0,9);
			$this->o2=mt_rand(0,9);
		}
		while (!$this->o1 && !$this->o2);	// don't accept both being zero
		$op=($cnt=count($this->opSet))==1 ? 0 : mt_rand(0,($cnt-1));
		$this->operator=$this->opSet[$op];	// pick from enabled choices
		if ($this->operator==2 && $this->o2>$this->o1)	// only positive answers
		{	$swap=$this->o2;
			$this->o2=$this->o1;
			$this->o1=$swap;
		}
		$_SESSION[$this->tag]=array	// session data used after form submittal
							  (	'o1'=>$this->o1,
								'o2'=>$this->o2,
								'operator'=>$this->operator,
								'errMsg'=>$this->config['errMsg'],
							  );
	}	// _init()

	/**
	* returns a string representation of the enable operation flag bits
	*
	* @param numeric $bits, the bitfield flag values to translate
	* @return string, an OR'd list of the bits, or OP_ALL
	*/
	protected function _opFlagsFromBits($bits)
	{	if (($ops=intval($bits))==self::OP_ALL)
			return 'OP_ALL';

		$array=array();	// decode the enabled operator set
		if ($ops & self::OP_PLUS)
			$array[]='OP_ADD';
		if ($ops & self::OP_TIMES)
			$array[]='OP_TIMES';
		if ($ops & self::OP_MINUS)
			$array[]='OP_MINUS';

		if (!$array)	// no bits found
			return 'ERROR';

		return count($array)>1 ? '('.implode(' | ',$array).')' : $array[0];
	}	//  _opFlagsFromBits($bits)

	/**
	* converts a CSS hex color specification to an RGB triple
	*
	* @param string $color, hex CSS color value, 3 or 6 digits
	* @return array, converted R,G,B values
	*/
	protected function _parseColor($color)
	{	if (($cnt=strlen($color))==3)
		{	$r=hexdec($color[0].$color[0]);
			$g=hexdec($color[1].$color[1]);
			$b=hexdec($color[2].$color[2]);
		}
		else if ($cnt==6)
		{	$r=hexdec(substr($color,0,2));
			$g=hexdec(substr($color,2,2));
			$b=hexdec(substr($color,4,2));
		}
		else	// invalid CSS color specification, this should never happen
		{	$r=157;	// return an ugly shade of brown
			$g=89;
			$b=7;
		}
		return array($r,$g,$b);
	}	// _parseColor($color)

	/**
	* checks if there is valid $_SESSION data for this instance
	*
	* If $_SESSION data exists but is invalid, it is removed.
	*
	* @return boolean, TRUE=valid session data exists, FALSE=no $_SESSION data
	*/
	protected function _sessionHasData()
	{	if (!self::sessionHasData($this->tag))
		{	unset($_SESSION[$this->tag]);	// something's not right, discard it
			return FALSE;
		}
		return TRUE;	// valid session data exists
	}	//  _sessionHasData()

	/**
	* derives the active configuration from the array passed to the constructor
	*
	* On completion, the instance's $config array will include these entries:
	*	'errMsg', string, error message returned for wrong answers
	*	'operator', int, enables math operations, default=OP_ALL
	*	'tag', string, session id tag suffix for multiple instances on one form
	*
	* In addition, the $tag member will contain the full $_SESSION member name
	* for the instance's session data.
	*
	* Errors are logged to report invalid configuration parameters passed to
	* the constructor.  When an error is logged, a valid configuration value
	* is substituted for the erroneous one to prevent operating errors.
	*
	* @return nothing (class members initialized)
	*/
	protected function _setActiveConfig()
	{	if (!empty($this->params) && is_array($this->params))
			$this->config=$this->params;
		else $this->config=array();

		if (empty($this->config['operator']))	// config math operator select
			$this->config['operator']=self::OP_ALL;
		else
		{	$ops=$this->config['operator'];	// dereferenced local copy
			if (!is_numeric($ops) || $ops>self::OP_ALL)
			{	error_log('Invalid operator selection "'.$ops
						 .'" in '.__CLASS__.', using default OP_ALL');
				$this->config['operator']=self::OP_ALL;
			}
		}

		if (!isset($this->config['errMsg']))	// use default error message
			$this->config['errMsg']=self::ERR_MSG;
		// we must say something about wrong answers
		if (empty($this->config['errMsg']))
		{	if ($this->config['errMsg']!=='')
				error_log('Empty $errMsg passed to '.__CLASS__.', using bogus');
			$this->config['errMsg']=__CLASS__.' error';
		}
		else if (!is_string($this->config['errMsg']))
		{	error_log('Non-string $errMsg passed to '.__CLASS__.': '
					 .print_r($this->config['errMsg'],TRUE));
			$this->config['errMsg']='Wrong answer for '.__CLASS__;
		}

		if (empty($this->config['tag']))
			$tag=$this->config['tag']='';	// default is no tag
		else
		{	$tag=$this->config['tag'];
			if (!is_string($tag) || preg_match('/\W/',$tag))
			{	error_log('Ignoring invalid $tag passed to '.__CLASS__.': '
						 .print_r($tag,TRUE));
				$this->config['tag']='';
			}
		}
	}	// _setActiveConfig()

	/**
	* returns the numeric equation string used for the image or error reporting
	*
	* @return string, equation consisting of digits and an operator
	*/
	protected function _str()
	{	switch ($this->operator)	// build the text string for the image
		{	case 0:	$op='+';	break;
			case 1:	$op='x';	break;
			case 2:	$op='-';	break;
		}
		return $this->o1.' '.$op.' '.$this->o2;
	}	// _str()
}	// class HumanMathTest
//
// EOF: HumanMathTest.php
