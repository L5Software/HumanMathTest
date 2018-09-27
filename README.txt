HumanMathTest implements a math test 'bot deterrent PHP class for use in online forms
Copyright (c) 2018 by Fred Koschara
Released under the terms of the BSD-3-clause license
https://opensource.org/licenses/BSD-3-Clause

This class is used to create an image to be included in an online form that
shows a simple math test the visitor must solve when submitting the form. The
operands and operation are stored in the $_SESSION data for the page. After
submitting the form, the visitor's answer is checked by calling the verify()
method to compare their entry vs. the session data. If an error is found, the
form submission should be rejected.

Used in isolation, this test is far from adequate protection on a form where
anything more than minimal security is needed:  There is a statistically high
possibility of getting a correct answer from a random selection.

This class, as written, is intended to reduce noise submissions on a survey
form, not to preventing someone from biasing the outcome.

Original file source: https://FKEinternet.net/FOSS/PHP/HumanMathTest/HumanMathTest.zip
Documentation: https://FKEinternet.net/FOSS/PHP/HumanMathTest/

Please consider supporting my work:
  http://wfredk.com/donate.php
  https://RaceToSpaceProject.com/shopping/bookpreorder.php

Email: foss (at) L5Software (dot) com

v0.9.0.1, 2018/09/27, added *Limitations and Scope* section
v0.9, 2018/09/23, Initial release
