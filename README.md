# BehatLibrary

Very useful libraries for project Behat/Travis tests

### Require the libraries

```
composer require --dev rezzza/rest-api-behat-extension:"*@dev"
composer require --dev novaway/common-contexts:"~2.0"
composer require --dev behatch/contexts:"*@dev"
composer require --dev alexandresalome/mailcatcher:"*@dev"
composer require --dev behat/mink-selenium2-driver:"~1.2"
composer require --dev behat/mink-browserkit-driver:"*@dev"
composer require --dev atoum/atoum:"~2.6"
composer require --dev elfafa/behat-library:"*@dev"
```

### Configure a `.env.behat`

An example is available [here](src/Behat/Resources/examples/.env.behat).

### Configure a `behat.yml`

An example is available [here](src/Behat/Resources/examples/behat.yml).

### Configure a `phantomjs.json`

An example is available [here](src/Behat/Resources/examples/phantomjs.json).

### Configure `.travis.yml`

An example is available [here](src/Behat/Resources/examples/.travis.yml).
