<?php

namespace Behat\Context\Traits;

use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\Element;

/**
 * Library trait
 *
 * @author Fabien Somnier <f.somnier@xeonys.com>
 */
trait LibraryTrait
{
    private $isJavascript = false;

    /**
     * Treatment before each scenario
     * @BeforeScenario @javascript
     */
    public function beforeScenario()
    {
        $this->isJavascript = true;
    }

    /**
     * Get Mink page element
     *
     * @return DocumentElement
     */
    protected function getPage()
    {
        return $this->getSession()->getPage();
    }

    /**
     * Get Element from a css selector
     *
     * @param  string                    $selector css to search
     * @param  boolean                   $isId     is given $selector should be an identifier
     * @return Element
     * @throws \InvalidArgumentException if not found
     */
    protected function getElementFromCss($selector, $isId = false)
    {
        if ($isId) {
            $selector = (strpos($selector, '#') === 0) ? $selector : '#'.$selector;
        }
        $element = $this->getPage()->find(
            'xpath',
            $this->getSession()->getSelectorsHandler()->selectorToXpath('css', $selector)
        );
        if ($element === null) {
            throw new \InvalidArgumentException(sprintf("Not Found element %s", $selector));
        }

        return $element;
    }

    /**
     * Get Elements from a css selector
     *
     * @param  string                    $selector css to search
     * @return Element[]
     * @throws \InvalidArgumentException if not found
     */
    protected function getElementsFromCss($selector)
    {
        $elements = $this->getPage()->findAll(
            'xpath',
            $this->getSession()->getSelectorsHandler()->selectorToXpath('css', $selector)
        );
        if (! count($elements)) {
            throw new \InvalidArgumentException(sprintf("Not Found any elements %s", $selector));
        }

        return $elements;
    }

    /**
     * Get Link element from a css selector
     *
     * @param  string                    $selector  css to search
     * @param  Element                   $container eventual element where to search link
     * @return Element
     * @throws \InvalidArgumentException if not found
     */
    protected function getLinkFromCss($selector, $container = null)
    {
        $element = $container ? $container->findLink($selector) : $this->getPage()->findLink('css', $selector);
        if ($element === null) {
            throw new \InvalidArgumentException(sprintf("Not Found link element %s", $selector));
        }

        return $element;
    }

    /**
     * Get Element from a text selector
     * note : element must be visible, and 'span', 'label', 'div', 'a', 'li', or 'button' type
     *
     * @param  string                    $selector text to search
     * @param  string                    $class
     * @param  Element                   $container eventual element where to search link
     * @return Element
     * @throws \InvalidArgumentException if not found
     */
    protected function getElementFromText($selector, $class = null, $container = null)
    {
        $xpath     = '(//span|//label|//div|//button|//a|//a/*|//li|//b)[contains(text(),"'.$selector.'")]';
        $container = $container ?: $this->getPage();
        $elements  = $container->findAll(
            'xpath',
            $this->getSession()->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
        );
        if ($elements) {
            foreach ($elements as $key => $element) {
                if (! $element->isVisible()) {
                    unset($elements[$key]);
                } elseif ($class && ! $element->hasClass($class)) {
                    unset($elements[$key]);
                }
            }
            $element = count($elements) ? reset($elements) : null;
        } else {
            $element = null;
        }
        if ($element === null) {
            throw new \InvalidArgumentException(sprintf("Not Found element from text %s", $selector));
        }

        return $element;
    }

    /**
     * Get Link element from a text selector
     *
     * @param  string                    $selector text to search
     * @return Element
     * @throws \InvalidArgumentException if not found
     */
    protected function getLinkFromText($selector)
    {
        $element = $this->getPage()->findLink($selector);
        if ($element === null) {
            throw new \InvalidArgumentException(sprintf("Not Found link element %s", $selector));
        }

        return $element;
    }

    /**
     * Get Element from its id
     *
     * @param  string                    $selector css identifier to search
     * @return Element
     * @throws \InvalidArgumentException if not found
     */
    protected function getElementFromId($selector)
    {
        $xpath   = $this->getSession()->getSelectorsHandler()->selectorToXpath('xpath', '//*[@id="'.$selector.'"]/a');
        $element = $this->getPage()->find('xpath', $xpath);
        if ($element === null) {
            throw new \InvalidArgumentException(sprintf("Not Found element from identifier %s", $selector));
        }

        return $element;
    }

    /**
     * Get Input element from its label and type
     *
     * @param  string                    $type  input type to search (radio, ...)
     * @param  string                    $label text to search in input
     * @return Element
     * @throws \InvalidArgumentException if not found
     */
    protected function getInputFromTypeAndLabel($type, $label)
    {
        foreach ($this->getPage()->findAll('css', 'label') as $element) {
            if ($label === $element->getText() && $element->has('css', 'input[type="'.$type.'"]')) {
                return $element;
            }
        }

        throw new \InvalidArgumentException(sprintf("Not found element type %s from label %s", $type, $label));
    }

    /**
     * Get element from a text and click
     *
     * @param  string                    $text
     * @param  string                    $class
     * @param  Element                   $container eventual element where to search link
     * @throws \InvalidArgumentException if not found
     */
    protected function getElementFromTextAndClick($text, $class = null, $container = null)
    {
        $element = $this->getElementFromText($text, $class, $container);
        $element->click();
    }

    /**
     * Open screenshot
     */
    protected function openScreenshot()
    {
        file_put_contents('/tmp/behat_screenshot.jpg', $this->getSession()->getScreenshot());
        if (PHP_OS === "Darwin" && PHP_SAPI === "cli") {
            exec('open -a "Preview.app" /tmp/behat_screenshot.jpg');
        }
    }

    /**
     * Enable to wait for a function ($lambda) to return true (useful to wait for an element in page)
     *
     * @param  function   $lambda function for test
     * @param  integer    $wait   allowed waiting time in second
     * @param  array      $param  function $lambda parameters
     * @return true
     * @throws \Exception if exception or allowed waiting time exceeded
     */
    protected function spin($lambda, $wait = 30, $param = null)
    {
        for ($i = 0; $i < $wait * 2; $i++) {
            try {
                if ($lambda($param)) {
                    return true;
                }
            } catch (\Exception $e) {
                throw $e;
            }
            $this->getSession()->wait(500);
        }
        $backtrace = debug_backtrace();

        throw new \Exception("Timeout thrown by ".$backtrace[1]['class']."::".$backtrace[1]['function']."()\n");
    }

    /**
     * Check that jQuery is available, and none ajax call is current
     */
    protected function iWaitForJQuery()
    {
        if (! $this->isJavascript) {
            return;
        }
        $this->getSession()->wait(500);
        try {
            $this->spin(function () {
                $return = $this->getSession()->evaluateScript(
                    'return typeof window.jQuery !== "undefined" && 0 === window.jQuery.active && (typeof Pace == "undefined" || false === Pace.running);'
                );

                return (bool) $return;
            }, 15);
        } catch (\Exception $e) {
            if ('test' != $this->getKernel()->getEnvironment()) {
                echo $e->getMessage();
            }
        }
    }
}
