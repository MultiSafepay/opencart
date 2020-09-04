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

class MultiSafePayLanguageTest extends OpenCartMultiSafepayTest {

    public function testTranslationAdminFilesHaveIdenticalNumberOfKeys() {
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require_once(getenv('OC_ROOT') . 'admin/language/' . $language . '/extension/payment/multisafepay.php');
            $keys[$language] = count($_);
        }
        $this->assertEquals($keys['en-gb'], $keys['es']);
        $this->assertEquals($keys['en-gb'], $keys['it-it']);
        $this->assertEquals($keys['en-gb'], $keys['nl-nl']);
        $this->assertEquals($keys['es'], $keys['it-it']);
        $this->assertEquals($keys['es'], $keys['nl-nl']);
        $this->assertEquals($keys['it-it'], $keys['nl-nl']);
    }

    public function testTranslationCatalogFilesHaveIdenticalNumberOfKeys() {
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require_once(getenv('OC_ROOT') . 'catalog/language/' . $language . '/extension/payment/multisafepay.php');
            $keys[$language] = count($_);
        }
        $this->assertEquals($keys['en-gb'], $keys['es']);
        $this->assertEquals($keys['en-gb'], $keys['it-it']);
        $this->assertEquals($keys['en-gb'], $keys['nl-nl']);
        $this->assertEquals($keys['es'], $keys['it-it']);
        $this->assertEquals($keys['es'], $keys['nl-nl']);
        $this->assertEquals($keys['it-it'], $keys['nl-nl']);
    }

    public function testTranslationAdminFilesHaveIdenticalKeys() {
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require_once(getenv('OC_ROOT') . 'admin/language/' . $language . '/extension/payment/multisafepay.php');
            $keys[$language] = array_keys($_);
        }
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['es']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['it-it']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['es'], $keys['it-it']));
        $this->assertCount(0, array_diff($keys['es'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['it-it'], $keys['nl-nl']));
    }

    public function testTranslationCatalogFilesHaveIdenticalKeys() {
        $languages = array('en-gb', 'es', 'it-it', 'nl-nl');
        $keys = array();
        foreach ($languages as $language) {
            $_ = array();
            require_once(getenv('OC_ROOT') . 'catalog/language/' . $language . '/extension/payment/multisafepay.php');
            $keys[$language] = array_keys($_);
        }
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['es']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['it-it']));
        $this->assertCount(0, array_diff($keys['en-gb'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['es'], $keys['it-it']));
        $this->assertCount(0, array_diff($keys['es'], $keys['nl-nl']));
        $this->assertCount(0, array_diff($keys['it-it'], $keys['nl-nl']));
    }

}