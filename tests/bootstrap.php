<?php
// Enviroment variable OC_ROOT exist in phpunit.xml
if (!getenv('API_KEY')) {
    echo "\033[0;31mYou need to setup up the MultiSafepay API_KEY variable in phpunit.xml file\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (!getenv('OC_VERSION')) {
    echo "\033[0;31mYou need to setup up the OC_VERSION variable in phpunit.xml file\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (!getenv('OC_ROOT')) {
    echo "\033[0;31mYou need to setup up the OC_ROOT variable in phpunit.xml file. This should be the absolute path to the application folder\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (!getenv('TEST_ROOT')) {
    echo "\033[0;31mYou need to setup up the TEST_ROOT variable in phpunit.xml file. This should be the absolute path to the test folder, inside the extension folder\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (!getenv('HTTP_SERVER')) {
    echo "\033[0;31mYou need to setup up the HTTP_SERVER variable in phpunit.xml file. This is the public URL of the OpenCart application\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (!getenv('TEST_CONFIG')) {
    echo "\033[0;31mYou need to setup up the TEST_CONFIG variable in phpunit.xml file. Accepted values are test-config-3-0 or test-config-2-3.\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (!getenv('CURRENCY_CODE')) {
    echo "\033[0;31mYou need to setup up the CURRENCY_CODE variable in phpunit.xml file. Accepted value for now is just EUR\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (!getenv('OC_ADMIN_USERNAME')) {
    echo "\033[0;31mYou need to setup up the OC_ADMIN_USERNAME variables in phpunit.xml file\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (!getenv('OC_ADMIN_PASSWORD')) {
    echo "\033[0;31mYou need to setup up the OC_ADMIN_PASSWORD variables in phpunit.xml file\033[0;m" . PHP_EOL;
    exit( 1 );
}

// OC_ROOT, TEST_ROOT, HTTP_SERVER path must have a / at the end
if (substr(getenv('OC_ROOT'), -1) !== '/') {
    echo "\033[0;31mOC_ROOT must end with / character\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (substr(getenv('TEST_ROOT'), -1) !== '/') {
    echo "\033[0;31mTEST_ROOT must end with / character\033[0;m" . PHP_EOL;
    exit( 1 );
}
if (substr(getenv('HTTP_SERVER'), -1) !== '/') {
    echo "\033[0;31mHTTP_SERVER must end with / character\033[0;m" . PHP_EOL;
    exit( 1 );
}


// Check Composer dependencies
if (file_exists(getenv('OC_ROOT') . 'system/library/multisafepay/vendor/autoload.php')) {
    require_once(getenv('OC_ROOT') . 'system/library/multisafepay/vendor/autoload.php');
} else {
    echo "\033[0;31mIt seems dependencies are lost. Did you run `composer install`?\033[0;m" . PHP_EOL;
    exit( 1 );
}

// Load Fixtures
$fixture_class = array('customers', 'orders', 'session', 'coupons', 'geo_zones', 'tax_classes', 'tax_rates');
foreach ($fixture_class as $fixture) {
    if (file_exists(getenv('TEST_ROOT') . 'multisafepay-test-suite/fixtures/'.$fixture.'.php')) {
        require_once(getenv('TEST_ROOT') . 'multisafepay-test-suite/fixtures/'.$fixture.'.php');
    } else {
        echo "\033[0;31mIt seems dependencies are lost. Fixtures ".$fixture." files are required\033[0;m" . PHP_EOL;
        exit( 1 );
    }
}

// Load Helper Class
foreach ($fixture_class as $fixture) {
    if (file_exists(getenv('TEST_ROOT') . 'multisafepay-test-suite/helper/helper.php')) {
        require_once(getenv('TEST_ROOT') . 'multisafepay-test-suite/helper/helper.php');
    } else {
        echo "\033[0;31mIt seems dependencies are lost. The file helper.php class is required\033[0;m" . PHP_EOL;
        exit( 1 );
    }
}

// Load Specific TestCase
$oc_version = getenv('OC_VERSION');
if((version_compare($oc_version, '3.0.0.0', '>=') && version_compare($oc_version, '3.0.3.8', '<='))) {
    if (file_exists(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-3.0.php')) {
        require_once(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-3.0.php');
    } else {
        echo "\033[0;31mIt seems dependencies are lost. The file multisafepay-test-suite-for-opencart-3.0.php is required\033[0;m" . PHP_EOL;
        exit( 1 );
    }
}

if((version_compare($oc_version, '2.3.0.0', '>=') && version_compare($oc_version, '2.3.0.2', '<='))) {
    if (file_exists(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-2.3.php')) {
        require_once(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-2.3.php');
    } else {
        echo "\033[0;31mIt seems dependencies are lost. The file multisafepay-test-suite-for-opencart-2.3.php is required\033[0;m" . PHP_EOL;
        exit( 1 );
    }
}
if(version_compare($oc_version, '2.2.0.0', '==')) {
    if (file_exists(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-2.2.php')) {
        require_once(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-2.2.php');
    } else {
        echo "\033[0;31mIt seems dependencies are lost. The file multisafepay-test-suite-for-opencart-2.2.php is required\033[0;m" . PHP_EOL;
        exit( 1 );
    }
}
if((version_compare($oc_version, '2.1.0.0', '>=') && version_compare($oc_version, '2.1.0.2', '<='))) {
    if (file_exists(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-2.1.php')) {
        require_once(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-2.1.php');
    } else {
        echo "\033[0;31mIt seems dependencies are lost. The file multisafepay-test-suite-for-opencart-2.1.php is required\033[0;m" . PHP_EOL;
        exit( 1 );
    }
}
if((version_compare($oc_version, '2.0.0.0', '>=') && version_compare($oc_version, '2.0.3.1', '<='))) {
    if (file_exists(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-2.0.php')) {
        require_once(getenv('TEST_ROOT') . 'multisafepay-test-suite/multisafepay-test-suite-for-opencart-2.0.php');
    } else {
        echo "\033[0;31mIt seems dependencies are lost. The file multisafepay-test-suite-for-opencart-2.0.php is required\033[0;m" . PHP_EOL;
        exit( 1 );
    }
}

