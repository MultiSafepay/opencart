<?php

class AdminControllerExtensionPaymentMultiSafePayTest extends MultisafepayTestSuiteForOpenCart {

    public function testIndex() {
        $this->login($this->getAdminUserName(), $this->getAdminPassword());
        $response = $this->dispatchAction($this->multisafepay_version_control->getExtensionRoute());
        $this->assertRegExp('/Edit the MultiSafepay Configuration/', $response->getOutput());
        $this->logout();
    }

}