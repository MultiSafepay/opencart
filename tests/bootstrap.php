<?php

/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the MultiSafepay plugin
 * to newer versions in the future. If you wish to customize the plugin for your
 * needs please document your changes and make backups before you update.
 *
 * @category    MultiSafepay
 * @package     Connect
 * @author      TechSupport <integration@multisafepay.com>
 * @copyright   Copyright (c) MultiSafepay, Inc. (https://www.multisafepay.com)
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

// Enviroment variable OC_ROOT exist in phpunit.xml
if (!getenv('OC_ROOT')) {
    echo "\033[0;31mYou need to setup up enviroment variables in phpunit.xml file\033[0;m" . PHP_EOL;
    exit( 1 );
}

// OC_ROOT path must have a / at the end
if (substr(getenv('OC_ROOT'), -1) !== '/') {
    echo "\033[0;31mOC_ROOT must end with / character\033[0;m" . PHP_EOL;
    exit( 1 );
}

// Check for dependencies
if (file_exists(getenv('OC_ROOT') . 'system/library/multisafepay/vendor/autoload.php')) {
    require_once(getenv('OC_ROOT') . 'system/library/multisafepay/vendor/autoload.php');
} else {
    echo "\033[0;31mIt seems dependencies are lost. Did you run `composer install`?\033[0;m" . PHP_EOL;
    exit( 1 );
}

// Load Fixtures
$fixture_class = array('CustomersTest', 'OrdersTest', 'SessionTest', 'CouponsTest', 'GeoZonesTest', 'TaxClassesTest', 'TaxRatesTest', 'Helper');
foreach ($fixture_class as $fixture) {
    if (file_exists(getenv('TEST_ROOT') . 'Fixtures/'.$fixture.'.php')) {
        require_once(getenv('TEST_ROOT') . 'Fixtures/'.$fixture.'.php');
    } else {
        echo "\033[0;31mIt seems dependencies are lost. Fixtures ".$fixture." files are required\033[0;m" . PHP_EOL;
        exit( 1 );
    }
}


// Load OpenCartMultiSafepayTest that extends from PHPUnit TestCase
if (file_exists(getenv('TEST_ROOT') . 'unit/OpenCartMultiSafepayTest.php')) {
    require_once(getenv('TEST_ROOT') . 'unit/OpenCartMultiSafepayTest.php');
} else {
    echo "\033[0;31mIt seems dependencies are lost. OpenCartMultiSafepayTest file is required\033[0;m" . PHP_EOL;
    exit( 1 );
}