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

class MultiSafePayAdminTests extends MultisafepayTestSuiteForOpenCart {

    public function testTranslationAdminFilesHaveIdenticalNumberOfKeys() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.1' || $oc_version == '2.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl', 'de-de');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'admin/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            $keys[$language] = count($_);
        }
        $this->assertEquals($keys['en-gb'], $keys['es']);
        $this->assertEquals($keys['en-gb'], $keys['it-it']);
        $this->assertEquals($keys['en-gb'], $keys['nl-nl']);
        $this->assertEquals($keys['en-gb'], $keys['de-de']);
        $this->assertEquals($keys['es'], $keys['it-it']);
        $this->assertEquals($keys['es'], $keys['nl-nl']);
        $this->assertEquals($keys['es'], $keys['de-de']);
        $this->assertEquals($keys['it-it'], $keys['nl-nl']);
        $this->assertEquals($keys['it-it'], $keys['de-de']);
        $this->assertEquals($keys['nl-nl'], $keys['de-de']);
    }

    public function testTranslationCatalogFilesHaveIdenticalNumberOfKeys() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.1' || $oc_version == '2.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl', 'de-de');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'catalog/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            $keys[$language] = count($_);
        }
        $this->assertEquals($keys['en-gb'], $keys['es']);
        $this->assertEquals($keys['en-gb'], $keys['it-it']);
        $this->assertEquals($keys['en-gb'], $keys['nl-nl']);
        $this->assertEquals($keys['en-gb'], $keys['de-de']);
        $this->assertEquals($keys['es'], $keys['it-it']);
        $this->assertEquals($keys['es'], $keys['nl-nl']);
        $this->assertEquals($keys['es'], $keys['de-de']);
        $this->assertEquals($keys['it-it'], $keys['nl-nl']);
        $this->assertEquals($keys['it-it'], $keys['de-de']);
        $this->assertEquals($keys['nl-nl'], $keys['de-de']);
    }

    public function testTranslationAdminFilesHaveIdenticalKeys() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.1' || $oc_version == '2.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl', 'de-de');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'admin/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            $keys[$language] = array_keys($_);
        }
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['es']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['it-it']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['de-de']));
        $this->assertCount(0, array_diff($keys['es'], $keys['it-it']));
        $this->assertCount(0, array_diff($keys['es'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['es'], $keys['de-de']));
        $this->assertCount(0, array_diff($keys['it-it'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['it-it'], $keys['de-de']));
        $this->assertCount(0, array_diff($keys['nl-nl'], $keys['de-de']));
    }

    public function testTranslationCatalogFilesHaveIdenticalKeys() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.1' || $oc_version == '2.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl', 'de-de');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'catalog/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            $keys[$language] = array_keys($_);
        }
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['es']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['it-it']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['de-de']));
        $this->assertCount(0, array_diff($keys['es'], $keys['it-it']));
        $this->assertCount(0, array_diff($keys['es'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['es'], $keys['de-de']));
        $this->assertCount(0, array_diff($keys['it-it'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['it-it'], $keys['de-de']));
        $this->assertCount(0, array_diff($keys['nl-nl'], $keys['de-de']));
    }



    public function testTranslationAdminFilesHaveIdenticalNumberOfKeysLower22() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('english', 'spanish', 'italian', 'dutch', 'deutsch');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'admin/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            $keys[$language] = count($_);
        }
        $this->assertEquals($keys['english'], $keys['spanish']);
        $this->assertEquals($keys['english'], $keys['italian']);
        $this->assertEquals($keys['english'], $keys['dutch']);
        $this->assertEquals($keys['english'], $keys['deutsch']);
        $this->assertEquals($keys['spanish'], $keys['italian']);
        $this->assertEquals($keys['spanish'], $keys['dutch']);
        $this->assertEquals($keys['spanish'], $keys['deutsch']);
        $this->assertEquals($keys['italian'], $keys['dutch']);
        $this->assertEquals($keys['italian'], $keys['deutsch']);
        $this->assertEquals($keys['dutch'], $keys['deutsch']);
    }

    public function testTranslationCatalogFilesHaveIdenticalNumberOfKeysLower22() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('english', 'spanish', 'italian', 'dutch', 'deutsch');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'catalog/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            $keys[$language] = count($_);
        }
        $this->assertEquals($keys['english'], $keys['spanish']);
        $this->assertEquals($keys['english'], $keys['italian']);
        $this->assertEquals($keys['english'], $keys['dutch']);
        $this->assertEquals($keys['english'], $keys['deutsch']);
        $this->assertEquals($keys['spanish'], $keys['italian']);
        $this->assertEquals($keys['spanish'], $keys['dutch']);
        $this->assertEquals($keys['spanish'], $keys['deutsch']);
        $this->assertEquals($keys['italian'], $keys['dutch']);
        $this->assertEquals($keys['italian'], $keys['deutsch']);
        $this->assertEquals($keys['dutch'], $keys['deutsch']);
    }

    public function testTranslationAdminFilesHaveIdenticalKeysLower22() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('english', 'spanish', 'italian', 'dutch', 'deutsch');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'admin/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            $keys[$language] = array_keys($_);
        }
        $this->assertCount(0, array_diff($keys['english'], $keys['spanish']));
        $this->assertCount(0, array_diff($keys['english'], $keys['italian']));
        $this->assertCount(0, array_diff($keys['english'], $keys['dutch']));
        $this->assertCount(0, array_diff($keys['english'], $keys['deutsch']));
        $this->assertCount(0, array_diff($keys['spanish'], $keys['italian']));
        $this->assertCount(0, array_diff($keys['spanish'], $keys['dutch']));
        $this->assertCount(0, array_diff($keys['spanish'], $keys['deutsch']));
        $this->assertCount(0, array_diff($keys['italian'], $keys['dutch']));
        $this->assertCount(0, array_diff($keys['italian'], $keys['deutsch']));
        $this->assertCount(0, array_diff($keys['dutch'], $keys['deutsch']));
    }

    public function testTranslationCatalogFilesHaveIdenticalKeysLower22() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('english', 'spanish', 'italian', 'dutch', 'deutsch');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'catalog/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            $keys[$language] = array_keys($_);
        }
        $this->assertCount(0, array_diff($keys['english'], $keys['spanish']));
        $this->assertCount(0, array_diff($keys['english'], $keys['italian']));
        $this->assertCount(0, array_diff($keys['english'], $keys['dutch']));
        $this->assertCount(0, array_diff($keys['english'], $keys['deutsch']));
        $this->assertCount(0, array_diff($keys['spanish'], $keys['italian']));
        $this->assertCount(0, array_diff($keys['spanish'], $keys['dutch']));
        $this->assertCount(0, array_diff($keys['spanish'], $keys['deutsch']));
        $this->assertCount(0, array_diff($keys['italian'], $keys['dutch']));
        $this->assertCount(0, array_diff($keys['italian'], $keys['deutsch']));
        $this->assertCount(0, array_diff($keys['dutch'], $keys['deutsch']));
    }


    public function testCataloglenghtPaymentMethodsTitle() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.1' || $oc_version == '2.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl', 'de-de');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'catalog/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            foreach ($_ as $key => $value) {
                if(strpos($key, 'text_title') !== false) {
                    $length = mb_strlen($value, 'utf8');
                    $this->assertLessThan(128, $length);
                }
            }
        }
    }


    public function testAdminlenghtPaymentMethodsTitle() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.1' || $oc_version == '2.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl', 'de-de');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'admin/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            foreach ($_ as $key => $value) {
                if(strpos($key, 'text_title') !== false) {
                    $length = mb_strlen($value, 'utf8');
                    $this->assertLessThan(128, $length);
                }
            }
        }
    }


    public function testCataloglenghtPaymentMethodsTitleLower22() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('english', 'spanish', 'italian', 'dutch', 'deutsch');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'catalog/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            foreach ($_ as $key => $value) {
                if(strpos($key, 'text_title') !== false) {
                    $length = mb_strlen($value, 'utf8');
                    $this->assertLessThan(128, $length);
                }
            }
        }
    }


    public function testAdminlenghtPaymentMethodsTitleLower22() {
        $oc_version = $this->multisafepay_version_control->getOcVersion();
        if($oc_version == '2.2' || $oc_version == '2.3' || $oc_version == '3.0') {
            $this->markTestSkipped('This test has been skipped.');
        }
        $languages = array('english', 'spanish', 'italian', 'dutch', 'deutsch');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require(getenv('OC_ROOT') . 'admin/language/' . $language . '/'. $this->multisafepay_version_control->getExtensionRoute() . '.php');
            foreach ($_ as $key => $value) {
                if(strpos($key, 'text_title') !== false) {
                    $length = mb_strlen($value, 'utf8');
                    $this->assertLessThan(128, $length);
                }
            }
        }
    }

}