<?php

namespace Behat\Context\Traits;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Element\Element;

/**
 * Select2 trait
 *
 * @author Fabien Somnier <f.somnier@xeonys.com>
 */
trait Select2Trait
{
    /**
     * Get Select2 element from its id
     *
     * @param  string  $selector css identifier to search
     * @param  boolean $andOpen  do we open the select2 after finding it
     * @return Element
     */
    private function getSelect2FromId($selector, $andOpen = true)
    {
        $element = $this->getElementFromId($selector);
        $classes = explode(' ', $element->getParent()->getAttribute("class"));
        if ($andOpen && ! in_array('select2-dropdown-open', $classes)) {
            // open on it only if it's not already opened
            $element->click();
            $this->getSession()->wait(2000); // to give time for jQuery.active++ event
            $this->iWaitForJQuery();
        }

        return $element;
    }
}
