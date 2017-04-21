<?php

namespace Behat\Context;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Context\Traits\LibraryTrait;
use Behat\Context\Traits\Select2Trait;

/**
 * Select2 context
 *
 * @author Fabien Somnier <f.somnier@xeonys.com>
 */
class Select2Context extends RawMinkContext implements Context
{
    use KernelDictionary;
    use LibraryTrait;
    use Select2Trait;

    /**
     * Detect if a choice is displayed in Select2
     *
     * @When /^I should see "([^"]*)" in "([^"]*)" select2 element$/
     *
     * @param string $text     text to search
     * @param string $selector select2 css selector
     */
    public function iShouldSeeInSelectElement($text, $selector)
    {
        $this->getSelect2FromId($selector, true);
        $this->getElementFromText($text);
    }

    /**
     * Detect if a choice is not displayed in Select2
     *
     * @When /^I should not see "([^"]*)" in "([^"]*)" select2 element$/
     *
     * @param  string                    $text     text to search
     * @param  string                    $selector select2 css selector
     * @throws \InvalidArgumentException if displayed
     */
    public function iShouldNotSeeInSelectElement($text, $selector)
    {
        $this->getSelect2FromId($selector, true);
        try {
            $this->getElementFromText($text);
        } catch (\InvalidArgumentException $e) {
            // entry has not been found : test is ok
            return;
        }

        throw new \InvalidArgumentException(sprintf("%s found in %s", $text, $selector));
    }

    /**
     * Make a choice in Select2
     *
     * @When /^I choose "([^"]*)" in "([^"]*)" select2 element$/
     *
     * @param string $text     text to search
     * @param string $selector select2 css selector
     */
    public function iChooseInSelectElement($text, $selector)
    {
        $this->getSelect2FromId($selector, true);
        $this->getElementFromTextAndClick($text, 'select2-result-label');
    }

    /**
     * Check that Select2 element is enabled
     *
     * @When /^(?:|the )"([^"]*)" select2 element should be enabled$/
     *
     * @param string $selector select2 css selector
     */
    public function selectElementShouldBeEnabled($selector)
    {
        $element = $this->getSelect2FromId($selector, true);
        if ($element->getParent()->hasClass('select2-container-disabled')) {
            throw new \Exception("Select2 element is not enabled");
        }
    }

    /**
     * Check that Select2 element is disabled
     *
     * @When /^(?:|the )"([^"]*)" select2 element should be disabled$/
     *
     * @param string $selector select2 css selector
     */
    public function selectElementShouldBeDisabled($selector)
    {
        $element = $this->getSelect2FromId($selector, true);
        if (! $element->getParent()->hasClass('select2-container-disabled')) {
            throw new \Exception("Select2 element is not disabled");
        }
    }

    /**
     * Select a value in Select2
     *
     * @When /^(?:|I )select "(?P<entry>(?:[^"]|\\")*)" in select2 input "(?P<field>(?:[^"]|\\")*)"$/
     */
    public function iSelectInSelect2Input($value, $field)
    {
        $this->getSession()->executeScript(
            '$("'.$field.'").select2("open");'
        );
        $this->iWaitForJQuery();

        $results = $this->getPage()->findAll('css', '.select2-drop .select2-results li');
        foreach ($results as $result) {
            if ($result->getText() == $value) {
                $result->click();
                return;
            }
        }

        throw new \Exception(sprintf('Value "%s" not found in Select2 choices', $value));
    }
}
