# HumanMathTest

This class is used to create an image to be included in an online form that shows a
simple math test the visitor must solve when submitting the form. The operands and
operation are stored in the $_SESSION data for the page. After submitting the form,
the visitor's answer is checked by calling the verify() method to compare their entry
vs. the session data. If an error is found, the form submission should be rejected.

[Documentation](https://FKEinternet.net/FOSS/PHP/HumanMathTest/)

## Limitations and Scope

One problem with this sort of form protection is that the math problems need
to be very simple for average users to succeed. Answers are limited to integers
in the 0-99 range so that users can calculate them. Even without using optical
character recognition to parse the image and calculate the correct answer, a bot
attempting numerous trials will succeed with a small but harmful percentage once
it knows the field requires numeric input and the valid range. This is a start
but the challenge is to make it harder for bots to defeat while keeping it easy
enough for humans of almost all capabilities to succeed.

Used in isolation, this test is far from adequate protection on a form where
anything more than minimal security is needed because of the statistically high
possibility of getting a correct answer from a random selection.

This class, as written, is intended to reduce noise submissions on a survey
form, not to preventing someone from biasing the outcome.

## Released Versions

* 0.9.0.1, 27 September 2018, added *Limitations and Scope* section
* 0.9, 23 September 2018

## Code Style

* [PHP Code Style Guide](http://mitsloan.mit.edu/shared/content/PHP_Code_Style_Guide.php)

## License

This project is licensed under the [BSD-3-clause license](https://opensource.org/licenses/BSD-3-Clause)

## Author

* **Fred Koschara** - *Initial work* - [L5Software](https://github.com/L5Software)

### Please consider supporting my work:

* [Race To Space](https://RaceToSpaceProject.com/shopping/bookpreorder.php)
* [Donations accepted](http://wfredk.com/donate.php)
