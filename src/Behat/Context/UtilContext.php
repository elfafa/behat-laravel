<?php

namespace Behat\Context;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Context\Traits\LibraryTrait;

/**
 * Util context
 *
 * @author Fabien Somnier <f.somnier@xeonys.com>
 */
class UtilContext extends MinkContext implements Context
{
    use LibraryTrait;

    /**
     * Check that an element exists
     *
     * @When /^I should find the element "([^"]*)"$/
     *
     * @param string $selector css selector
     */
    public function iShouldFindTheElement($selector)
    {
        $this->getElementFromCss($selector);
    }

    /**
     * Check that an element doesn't exist
     *
     * @When /^I should not find the element "([^"]*)"$/
     *
     * @param string $selector css selector
     */
    public function iShouldNotFindTheElement($selector)
    {
        try {
            $this->getElementFromCss($selector);
        } catch (\InvalidArgumentException $e) {
            // entry has not been found : test is ok
            return;
        }

        throw new \InvalidArgumentException(sprintf("Found element %s", $selector));
    }

    /**
     * Click on an Element
     *
     * @When /^I click on the element "([^"]*)"$/
     *
     * @param string $selector css selector
     */
    public function iClickOnTheElement($selector)
    {
        $element = $this->getElementFromCss($selector);
        $element->click();
    }

    /**
     * Click on a Link element
     *
     * @When /^I click on the link "([^"]*)"$/
     *
     * @param string $selector text selector
     */
    public function iClickOnTheLink($text)
    {
        $element = $this->getLinkFromText($text);
        $element->click();
    }

    /**
     * Click on a Link element, inside a specific container
     *
     * @When /^I click on the link "([^"]*)" in "([^"]*)"$/
     *
     * @param string $link     Link css selector
     * @param string $selector container css selector
     */
    public function iClickOnTheLinkIn($link, $selector)
    {
        $container = $this->getElementFromCss($selector, true);
        $element   = $this->getLinkFromCss($link, $container);
        $element->click();
    }

    /**
     * Click on an Element
     *
     * @When /^I click on the text "([^"]*)"$/
     *
     * @param string $text text selector
     */
    public function iClickOnTheText($text)
    {
        $this->getElementFromTextAndClick($text);
    }

    /**
     * Click on a Link element, inside a specific container
     *
     * @When /^I click on the text "([^"]*)" in "([^"]*)"$/
     *
     * @param string $text     text selector
     * @param string $selector container css selector
     */
    public function iClickOnTheTextIn($text, $selector)
    {
        $container = $this->getElementFromCss($selector, true);
        $this->getElementFromTextAndClick($text, null, $container);
    }

    /**
     * Detect if a text is displayed exactly in given quantity
     *
     * @Then /^I should see "([^"]*)" exactly (?P<nth>\d+) times$/
     *
     * @param string  $text  text to search
     * @param integer $times how many times we expect it
     */
    public function iShouldSeeExactlyTimes($text, $times)
    {
        $pageContent = $this->getSession()->getPage()->getText();
        $founds      = substr_count($pageContent, $text);
        if ($times != $founds) {
            throw new \Exception('Found '.$founds.' occurences of "'.$text.'" when expecting '.$times);
        }
    }

    /**
     * Make a choice in a radio select
     *
     * @When /^I check "([^"]*)" radio button$/
     *
     * @param  string $label radio to search
     */
    public function iCheckRadioButton($label)
    {
        $element = $this->getInputFromTypeAndLabel('radio', $label);
        $this->fillField(
            $element->find('css', 'input[type="radio"]')->getAttribute('name'),
            $element->find('css', 'input[type="radio"]')->getAttribute('value')
        );
    }

    /**
     * Check if a choice is selected in Select
     *
     * @Then /^"([^"]*)" should be selected in "([^"]*)"$/
     *
     * @param  string     $text     text to search
     * @param  string     $selector css selector
     * @throws \Exception if choice is not selected
     */
    public function shouldBeSelectedIn($text, $selector)
    {
        $element = $this->getElementFromCss($selector.' option[selected="selected"]', true);
        if ($text === $element->getText()) {
            return;
        }
        throw new \Exception(sprintf("%s is not selected in %s select box", $text, $selector));
    }

    /**
     * Check if text if inside an Element
     *
     * @Then /^I should see "([^"]*)" in "([^"]*)"$/
     *
     * @param  string     $text     text to search
     * @param  string     $selector Element css selector
     * @throws \Exception if text is not inside
     */
    public function iShouldSeeInElement($text, $selector)
    {
        $element = $this->getElementFromCss($selector, true);
        if (stripos($element->getText(), (string) $text) === false) {
            throw new \Exception(sprintf("%s not found in %s", $text, $selector));
        }
    }

    /**
     * Check if text if inside some of css Element
     *
     * @Then /^I should see "([^"]*)" in a "([^"]*)" element$/
     *
     * @param  string     $text     text to search
     * @param  string     $selector Element css selector
     * @throws \Exception if text is not found
     */
    public function iShouldSeeInAElement($text, $selector)
    {
        $elements = $this->getElementsFromCss($selector);
        foreach ($elements as $element) {
            if (stripos($element->getText(), (string) $text) !== false) {
                return;
            }
        }

        throw new \Exception(sprintf("'%s' not found in any %s element", $text, $selector));
    }

    /**
     * Check if text if inside some of css Element
     *
     * @Then /^I should see "([^"]*)" in the (\d+st|\d+nd|\d+rd|\d+th) "([^"]*)" element$/
     *
     * @param  string     $text     text to search
     * @param  string     $position which position of element
     * @param  string     $selector Element css selector
     * @throws \Exception if text is not found
     */
    public function iShouldSeeInTheElement($text, $position, $selector)
    {
        $elements = $this->getElementsFromCss($selector);
        $count    = 1;
        foreach ($elements as $element) {
            if (
                $count++ == (int) $position
                && (
                    stripos($element->getText(), (string) $text) !== false
                    || stripos(strip_tags($element->getHtml()), (string) $text) !== false
                )
            ) {
                return;
            } elseif ($count > (int) $position) {
                break;
            }
        }

        throw new \Exception(sprintf("'%s' not found in the %s element number %d", $text, $selector, $position));
    }

    /**
     * Check if text if not inside an Element
     *
     * @Then /^I should not see "([^"]*)" in "([^"]*)"$/
     *
     * @param  string     $text     text to search
     * @param  string     $selector Element css selector
     * @throws \Exception if text is inside
     */
    public function iShouldNotSeeIn($text, $selector)
    {
        $element = $this->getElementFromCss($selector, true);
        if (stripos($element->getText(), (string) $text) !== false) {
            throw new \Exception(sprintf("'%s' found in %s", $text, $selector));
        }
    }

    /**
     * Run an external command
     *
     * @When /^I should have downloaded "([^"]*)" file$/
     *
     * @param string $cmdName name of command
     */
    public function iShouldHaveDownloadedFile($fileName)
    {
        $path = 'app/behat/';
        if (! file_exists($path.$fileName)) {
            throw new \Exception(sprintf("%s file not found in %s", $fileName, $path));
        }
    }

    /**
     * Wait that a modal is displayed
     *
     * @Then /^I wait for modal "([^"]*)"$/
     *
     * @param string $modal css identifier of modal
     */
    public function iWaitForModal($modal)
    {
        $this->spin(function ($modal) {
            $return = $this->getSession()->evaluateScript(
                'return $("#'.$modal.'").length && $("#'.$modal.'").css("display") == "block";'
            );

            return (bool) $return;
        }, 10, $modal);
    }

    /**
     * Wait that a text is displayed
     *
     * @Then /^I wait for text "([^"]*)"$/
     * @Then /^I wait for text "([^"]*)" during (\d+) seconds$/
     *
     * @param string  $text    text to wait
     * @param integer $seconds timeout
     */
    public function iWaitForTextDuringSeconds($text, $seconds = 10)
    {
        $this->spin(function ($text) {
            $return = (int) $this->getSession()->evaluateScript(
                'return document.documentElement.innerHTML.indexOf("'.$text.'");'
            );

            return (0 <= $return);
        }, $seconds, $text);
    }

    /**
     * Fill date field with given datetime
     *
     * @When /^(?:|I )fill in "([^"]*)" date field with "([^"]*)"$/
     *
     * @param  string   $field    date field css selector
     * @param  string   $datetime datetime
     */
    public function iFillInDateFieldWith($field, $datetime)
    {
        $this->fillField($field, date('d/m/Y', strtotime($datetime)));
    }

    /**
     * Fill field with given text
     *
     * @When /^(?:|I )fill in "([^"]*)" css element with "([^"]*)"$/
     *
     * @param string $selector field css selector
     * @param string $text     text
     */
    public function iFillInCssElementWith($selector, $text)
    {
        $element = $this->getElementFromCss($selector);
        $element->setValue($text);
    }

    /**
     * Check that element contains specific text
     *
     * @When /^(?:|the )"([^"]*)" css element should contain "([^"]*)"([^"]*)$/
     *
     * @param string $selector field css selector
     * @param string $text     text
     * @param string $exactly
     */
    public function cssElementShouldContain($selector, $text, $exactly = '')
    {
        $value = (string) $this->getElementFromCss($selector)->getValue();
        $text  = (string) $text;
        if (trim($exactly) == 'exactly' && strtolower($value) != strtolower($text)) {
            throw new \Exception(sprintf('Element contains "%s" and not "%s" as expected', $value, $text));
        } elseif (trim($exactly) != 'exactly' && false === stripos($value, $text)) {
            throw new \Exception(sprintf('Element contains "%s", but not "%s" as expected', $value, $text));
        }
    }

    /**
     * Check that element doesn't contain specific text
     *
     * @When /^(?:|the )"([^"]*)" css element should not contain "([^"]*)"([^"]*)$/
     *
     * @param string $selector field css selector
     * @param string $text     text
     */
    public function cssElementShouldNotContain($selector, $text, $exactly = '')
    {
        $value = (string) $this->getElementFromCss($selector)->getValue();
        $text  = (string) $text;
        if (trim($exactly) == 'exactly' && strtolower($value) == strtolower($text)) {
            throw new \Exception(sprintf('Element contains "%s" exactly', $text));
        } elseif (trim($exactly) != 'exactly' && false !== stripos($value, $text)) {
            throw new \Exception(sprintf('Element contains "%s"', $text));
        }
    }

    /**
     * Display a screenshot of current page
     *
     * @Then /^show (?:|me )a screenshot$/
     */
    public function showAScreenshot()
    {
        $this->openScreenshot();
    }

    /**
     * Close current window
     *
     * @Then /^I close window "([^"]*)"$/
     */
    public function iCloseWindow($name)
    {
        $this->getSession()->switchToWindow($name);
        $this->getSession()->executeScript(
            'window.close();'
        );
        $this->iSwitchToWindow();
    }

    /**
     * Close current window
     *
     * @Then /^I switch to main window$/
     * @Then /^I switch to window "([^"]*)"$/
     */
    public function iSwitchToWindow($name = '')
    {
        if (empty($name)) {
            $name = $this->getSession()->getWindowNames()[0];
        }
        $this->getSession()->switchToWindow($name);
    }
}
