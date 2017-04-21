<?php

namespace Behat\Context;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Context\Traits\LibraryTrait;

/**
 * Feature context
 *
 * @author Fabien Somnier <f.somnier@xeonys.com>
 */
class FeatureContext extends RawMinkContext implements Context
{
    use KernelDictionary;
    use LibraryTrait;

    /**
     * Treatment before each scenario
     * @BeforeScenario @javascript
     */
    public function beforeScenario()
    {
        $this->isJavascript = true;
        $this->getSession()->getDriver()->maximizeWindow();
    }

    /**
     * Treatment after each step with javascript
     *
     * !! combined @javascript annotation is not currently supported by Behat3 !!
     * !! issue : https://github.com/Behat/Behat/issues/653                    !!
     *
     * @AfterStep
     */
    public function afterStep()
    {
        $this->iWaitForJQuery();
    }
}
