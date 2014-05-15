<?php

use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Event\StepEvent;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;

class NavigationContext extends RawMinkContext implements TranslatedContextInterface {

	private function __assertNoCakeError() {
		$this->assertSession()->elementNotExists('css', '.cake-error');
	}

	private function __logCurrentUrl() {
		$address = $this->getSession()->getCurrentUrl();
		$this->getMainContext()->log('Current url is: ' . $address);
	}

/**
 * Opens homepage.
 *
 * @When /^(?:|I )go to (?:|the )homepage$/
 */
	public function iAmOnHomepage() {
		$this->getSession()->visit($this->locatePath('/'));
		$this->__logCurrentUrl();
		$this->__assertNoCakeError();
	}

/**
 * Opens specified page.
 *
 * @Given /^(?:|I )am on "(?P<page>[^"]+)"$/
 * @When /^(?:|I )go to "(?P<page>[^"]+)"$/
 */
	public function visit($page) {
		$this->getSession()->visit($this->locatePath($page));
		$this->__logCurrentUrl();
		$this->__assertNoCakeError();
	}

/**
 * Reloads current page.
 *
 * @When /^(?:|I )reload the page$/
 */
	public function reload() {
		$this->getSession()->reload();
		$this->__logCurrentUrl();
		$this->__assertNoCakeError();
	}

/**
 * Moves backward one page in history.
 *
 * @When /^(?:|I )move backward one page$/
 */
	public function back() {
		$this->getSession()->back();
		$this->__logCurrentUrl();
		$this->__assertNoCakeError();
	}

/**
 * Moves forward one page in history
 *
 * @When /^(?:|I )move forward one page$/
 */
	public function forward() {
		$this->getSession()->forward();
		$this->__logCurrentUrl();
		$this->__assertNoCakeError();
	}

/**
 * Presses button with specified id|name|title|alt|value.
 *
 * @When /^(?:|I )press "(?P<button>(?:[^"]|\\")*)"$/
 */
	public function pressButton($button) {
		$button = $this->_fixStepArgument($button);
		$this->getSession()->getPage()->pressButton($button);
		$this->__logCurrentUrl();
		$this->__assertNoCakeError();
	}

/**
 * Clicks link with specified id|title|alt|text.
 *
 * @When /^(?:|I )follow "(?P<link>(?:[^"]|\\")*)"$/
 */
	public function clickLink($link) {
		$link = $this->_fixStepArgument($link);
		$this->getSession()->getPage()->clickLink($link);
		$this->__logCurrentUrl();
		$this->__assertNoCakeError();
	}

/**
 * Fills in form field with specified id|name|label|value.
 *
 * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
 * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" with:$/
 * @When /^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
 */
	public function fillField($field, $value) {
		$field = $this->_fixStepArgument($field);
		$value = $this->_fixStepArgument($value);
		$this->getSession()->getPage()->fillField($field, $value);
	}

/**
 * Fills in form fields with provided table.
 *
 * @When /^(?:|I )fill in the following:$/
 */
	public function fillFields(TableNode $fields) {
		foreach ($fields->getRowsHash() as $field => $value) {
			$this->fillField($field, $value);
		}
	}

/**
 * Selects option in select field with specified id|name|label|value.
 *
 * @When /^(?:|I )select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
 */
	public function selectOption($select, $option) {
		$select = $this->_fixStepArgument($select);
		$option = $this->_fixStepArgument($option);
		$this->getSession()->getPage()->selectFieldOption($select, $option);
	}

/**
 * Selects additional option in select field with specified id|name|label|value.
 *
 * @When /^(?:|I )additionally select "(?P<option>(?:[^"]|\\")*)" from "(?P<select>(?:[^"]|\\")*)"$/
 */
	public function additionallySelectOption($select, $option) {
		$select = $this->_fixStepArgument($select);
		$option = $this->_fixStepArgument($option);
		$this->getSession()->getPage()->selectFieldOption($select, $option, true);
	}

/**
 * Checks checkbox with specified id|name|label|value.
 *
 * @When /^(?:|I )check "(?P<option>(?:[^"]|\\")*)"$/
 */
	public function checkOption($option) {
		$option = $this->_fixStepArgument($option);
		$this->getSession()->getPage()->checkField($option);
	}

/**
 * Unchecks checkbox with specified id|name|label|value.
 *
 * @When /^(?:|I )uncheck "(?P<option>(?:[^"]|\\")*)"$/
 */
	public function uncheckOption($option) {
		$option = $this->_fixStepArgument($option);
		$this->getSession()->getPage()->uncheckField($option);
	}

/**
 * Attaches file to field with specified id|name|label|value.
 *
 * @When /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)"$/
 */
	public function attachFileToField($field, $path) {
		$field = $this->_fixStepArgument($field);

		if ($this->getMinkParameter('files_path')) {
			$fullPath = rtrim(realpath($this->getMinkParameter('files_path')), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;
			if (is_file($fullPath)) {
				$path = $fullPath;
			}
		}

		$this->getSession()->getPage()->attachFileToField($field, $path);
	}

/**
 * Checks, that current page PATH is equal to specified.
 *
 * @Then /^(?:|I )should be on "(?P<page>[^"]+)"$/
 */
	public function assertPageAddress($page) {
		$this->assertSession()->addressEquals($this->locatePath($page));
	}

	public function assertHomepage() {
		// We have 2 pages that qualify as the home page
		$address = $this->getSession()->getCurrentUrl();

		$indexPagePath = $this->locatePath('/');
		$homePagePath = $this->locatePath('/pages/home');

		if ($address != $indexPagePath &&
			$address != $homePagePath ) {
			$message = sprintf('Current page is "%s", but should be either "%s" or "%s"', $address, $indexPagePath, $homePagePath);
			$this->getMainContext()->fail($message);
		}
	}

/**
 * Checks, that current page PATH matches regular expression.
 *
 * @Then /^the (?i)url(?-i) should match (?P<pattern>"([^"]|\\")*")$/
 */
	public function assertUrlRegExp($pattern) {
		$this->assertSession()->addressMatches($this->_fixStepArgument($pattern));
	}

/**
 * Checks, that current page response status is equal to specified.
 *
 * @Then /^the response status code should be (?P<code>\d+)$/
 */
	public function assertResponseStatus($code) {
		$this->assertSession()->statusCodeEquals($code);
	}

/**
 * Checks, that current page response status is not equal to specified.
 *
 * @Then /^the response status code should not be (?P<code>\d+)$/
 */
	public function assertResponseStatusIsNot($code) {
		$this->assertSession()->statusCodeNotEquals($code);
	}

/**
 * Checks, that page contains specified text.
 *
 * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)"$/
 */
	public function assertPageContainsText($text) {
		$this->assertSession()->pageTextContains($this->_fixStepArgument($text));
	}

/**
 * Checks, that page doesn't contain specified text.
 *
 * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)"$/
 */
	public function assertPageNotContainsText($text) {
		$this->assertSession()->pageTextNotContains($this->_fixStepArgument($text));
	}

/**
 * Checks, that page contains text matching specified pattern.
 *
 * @Then /^(?:|I )should see text matching (?P<pattern>"(?:[^"]|\\")*")$/
 */
	public function assertPageMatchesText($pattern) {
		$this->assertSession()->pageTextMatches($this->_fixStepArgument($pattern));
	}

/**
 * Checks, that page doesn't contain text matching specified pattern.
 *
 * @Then /^(?:|I )should not see text matching (?P<pattern>"(?:[^"]|\\")*")$/
 */
	public function assertPageNotMatchesText($pattern) {
		$this->assertSession()->pageTextNotMatches($this->_fixStepArgument($pattern));
	}

/**
 * Checks, that HTML response contains specified string.
 *
 * @Then /^the response should contain "(?P<text>(?:[^"]|\\")*)"$/
 */
	public function assertResponseContains($text) {
		$this->assertSession()->responseContains($this->_fixStepArgument($text));
	}

/**
 * Checks, that HTML response doesn't contain specified string.
 *
 * @Then /^the response should not contain "(?P<text>(?:[^"]|\\")*)"$/
 */
	public function assertResponseNotContains($text) {
		$this->assertSession()->responseNotContains($this->_fixStepArgument($text));
	}

/**
 * Checks, that element with specified CSS contains specified text.
 *
 * @Then /^(?:|I )should see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
 */
	public function assertElementContainsText($element, $text) {
		$this->assertSession()->elementTextContains('css', $element, $this->_fixStepArgument($text));
	}

/**
 * Checks, that element with specified CSS doesn't contain specified text.
 *
 * @Then /^(?:|I )should not see "(?P<text>(?:[^"]|\\")*)" in the "(?P<element>[^"]*)" element$/
 */
	public function assertElementNotContainsText($element, $text) {
		$this->assertSession()->elementTextNotContains('css', $element, $this->_fixStepArgument($text));
	}

/**
 * Checks, that element with specified CSS contains specified HTML.
 *
 * @Then /^the "(?P<element>[^"]*)" element should contain "(?P<value>(?:[^"]|\\")*)"$/
 */
	public function assertElementContains($element, $value) {
		$this->assertSession()->elementContains('css', $element, $this->_fixStepArgument($value));
	}

/**
 * Checks, that element with specified CSS doesn't contain specified HTML.
 *
 * @Then /^the "(?P<element>[^"]*)" element should not contain "(?P<value>(?:[^"]|\\")*)"$/
 */
	public function assertElementNotContains($element, $value) {
		$this->assertSession()->elementNotContains('css', $element, $this->_fixStepArgument($value));
	}

/**
 * Checks, that element with specified CSS exists on page.
 *
 * @Then /^(?:|I )should see an? "(?P<element>[^"]*)" element$/
 */
	public function assertElementOnPage($element) {
		$this->assertSession()->elementExists('css', $element);
	}

/**
 * Checks, that element with specified CSS doesn't exist on page.
 *
 * @Then /^(?:|I )should not see an? "(?P<element>[^"]*)" element$/
 */
	public function assertElementNotOnPage($element) {
		$this->assertSession()->elementNotExists('css', $element);
	}

/**
 * Checks, that form field with specified id|name|label|value has specified value.
 *
 * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should contain "(?P<value>(?:[^"]|\\")*)"$/
 */
	public function assertFieldContains($field, $value) {
		$field = $this->_fixStepArgument($field);
		$value = $this->_fixStepArgument($value);
		$this->assertSession()->fieldValueEquals($field, $value);
	}

/**
 * Checks, that form field with specified id|name|label|value doesn't have specified value.
 *
 * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should not contain "(?P<value>(?:[^"]|\\")*)"$/
 */
	public function assertFieldNotContains($field, $value) {
		$field = $this->_fixStepArgument($field);
		$value = $this->_fixStepArgument($value);
		$this->assertSession()->fieldValueNotEquals($field, $value);
	}

/**
 * Checks, that checkbox with specified in|name|label|value is checked.
 *
 * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should be checked$/
 * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" (?:is|should be) checked$/
 */
	public function assertCheckboxChecked($checkbox) {
		$this->assertSession()->checkboxChecked($this->_fixStepArgument($checkbox));
	}

/**
 * Checks, that checkbox with specified in|name|label|value is unchecked.
 *
 * @Then /^the "(?P<checkbox>(?:[^"]|\\")*)" checkbox should not be checked$/
 * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" should (?:be unchecked|not be checked)$/
 * @Then /^the checkbox "(?P<checkbox>(?:[^"]|\\")*)" is (?:unchecked|not checked)$/
 */
	public function assertCheckboxNotChecked($checkbox) {
		$this->assertSession()->checkboxNotChecked($this->_fixStepArgument($checkbox));
	}

/**
 * Checks, that (?P<num>\d+) CSS elements exist on the page
 *
 * @Then /^(?:|I )should see (?P<num>\d+) "(?P<element>[^"]*)" elements?$/
 */
	public function assertNumElements($num, $element) {
		$this->assertSession()->elementsCount('css', $element, intval($num));
	}

/**
 * Prints current URL to console.
 *
 * @Then /^print current URL$/
 */
	public function printCurrentUrl() {
		$this->printDebug($this->getSession()->getCurrentUrl());
	}

/**
 * Prints last response to console.
 *
 * @Then /^print last response$/
 */
	public function printLastResponse() {
		$this->printDebug(
			$this->getSession()->getCurrentUrl() . "\n\n" .
			$this->getSession()->getPage()->getContent()
		);
	}

/**
 * Returns list of definition translation resources paths.
 *
 * @return array
 */
	public function getTranslationResources() {
		return $this->getMinkTranslationResources();
	}

/**
 * Returns list of definition translation resources paths for this dictionary.
 *
 * @return array
 */
	public function getMinkTranslationResources() {
		return glob(__DIR__ . '/../../../../i18n/*.xliff');
	}

/**
 * Returns fixed step argument (with \\" replaced back to ").
 *
 * @param string $argument
 *
 * @return string
 */
	protected function _fixStepArgument($argument) {
		return str_replace('\\"', '"', $argument);
	}

}