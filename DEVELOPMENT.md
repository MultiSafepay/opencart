# Notes for developers
This extension has been developed by MultiSafepay following the development guidelines of OpenCart.

## About the local environment for development
* Considering the MVC-L architecture of OpenCart, this extension has been developed using a local environment following a custom directory structure that will be explained in the following lines. 
* It use [modman](https://github.com/colinmollenhour/modman); a script which allows you to mix all the extension files throughout the core code directories of the OpenCart application, and therefore separate the extension files from core files of the OpenCart application, which will allow keep the extension under version control.
* It also uses a custom script located in "bin/hardlinks.sh" that allows you to create an internal structure of hard links and kept synchronize for some duplicate files (such as language directories, or controllers or models that need to be located in different directories of according to the version of OpenCart you are using). In this way is possible to work only in the latest version of OpenCart, kept the sync between them, and keep the extension under version control.
* It also use [Composer](https://getcomposer.org/)

## Set a local environment for development
Following this procedure you will get a local environment in which be possible to work in a clean way.

* Set the following directory structure:
```
  * PARENT-FOLDER
    * "FOLDER-WITH-YOUR-OPENCART-APPLICATION"
    * CREATE A FOLDER FOR "MULTISAFEPAY-OPENCART-EXTENSION"
 ```     

* Change directory to "MULTISAFEPAY-OPENCART-EXTENSION", and clone our GitHub repository: 
```
cd "MULTISAFEPAY-OPENCART-EXTENSION"
git clone git@github.com:MultiSafepay/Opencart.git ./
```

* Change directory to "MULTISAFEPAY-OPENCART-EXTENSION" and execute composer install passing as argument the major version of the PHP to get all dependencies in place:
```
cd "MULTISAFEPAY-OPENCART-EXTENSION"
bash bin/composer-install.sh 8
```

* Set hardlinks inside the local environment to avoid the necessity of edit duplicate files. 
```
cd "MULTISAFEPAY-OPENCART-EXTENSION" 
bash 

* [Install modman](https://github.com/colinmollenhour/modman#installation).

* Change directory to the root of your application "FOLDER-WITH-YOUR-OPENCART-APPLICATION" and initialize modman and link to the repo:
```
cd "FOLDER-WITH-YOUR-OPENCART-APPLICATION" 
modman init
modman link ../MULTISAFEPAY-OPENCART-EXTENSION/
```

bin/hardlinks.sh
```


* That`s it. 
  * Now you can make changes in the extension editing the files in "MULTISAFEPAY-OPENCART-EXTENSION" folder. This changes will immediately pass and take place inside the "FOLDER-WITH-YOUR-OPENCART-APPLICATION". 
  * You only need to touch the files in the exte


## Execute PHPUnit test in a local environment for development

* To be able to run PHPUnit test, move to the extension folder

```
cd "MULTISAFEPAY-OPENCART-EXTENSION"
``` 

* Rename phpunit.xml.dist to phpunit.xml and edit the file to add the env variables (API_KEY, OC_VERSION, OC_ROOT, TEST_ROOT, HTTP_SERVER, TEST_CONFIG, CURRENCY_CODE, LANGUAGE_CODE, OC_ADMIN_USERNAME, OC_ADMIN_PASSWORD)
* Login to the OpenCart application and enable and setup the extension properly.
* Then you should be able to run PHPUnit test suites with: 

```
composer run-script system-tests
composer run-script language-tests
composer run-script catalog-tests
composer run-script admin-tests
```


## Creating a new pull request
1) The changes should be documented in `CHANGELOG.md` by following the [KeepAChangelog](https://keepachangelog.com/en/1.0.0/) standard:
    - Changes are documented as `Added`, `Changed`, `Removed` or `Fixed`
    - A section `## Unreleased` is kept in the top at all times.
    - Each change commited, should be added via one line in the `## Unreleased` section, with a brief and concise commit message
2) Make your pull request
