(function($) {
    $(function() {
        const radioValue = 'multisafepay/applePay';
        const radioSelector = 'input[type="radio"]';
        const fullSelector = radioSelector + '[value="' + radioValue + '"]';
        const onePageCheckoutSelector = '#onepagecheckout .payment-method-content';
        const configForObserver = {
            childList: true,
            subtree: true,
        };
        const hideElement = (element) => {
            element.style.display = 'none';
        };

        // This function is used to detect the type of checkout used
        function detectCheckoutType() {
            // Detect if a div with class 'panel-group' and id containing 'accordion' exists
            const accordionDiv = document.querySelector('div.panel-group[id*="accordion"]');
            const accordionFound = accordionDiv !== null;

            let journal3Found = false, onePageCheckoutFound = false;
            const currentScripts = document.getElementsByTagName('script');
            for (const script of currentScripts) {
                if (script.src.includes('journal3')) {
                    journal3Found = true;
                    break;
                }
                if (script.src.includes('onepagecheckout')) {
                    onePageCheckoutFound = true;
                    break;
                }
            }
            // To check the type of checkout used, uncomment the following line ...
            // console.log('journal3Found', journal3Found, 'onePageCheckoutFound', onePageCheckoutFound, 'accordionFound', accordionFound)
            return {journal3Found, onePageCheckoutFound, accordionFound};
        }

        // Creating the global variables to determine the type of checkout used
        const {journal3Found, onePageCheckoutFound, accordionFound} = detectCheckoutType();

        // This function is used to hide the Apple Pay option on those environments where is not available
        async function applePayCheckAndHide(fullSelector, radioValue) {
            return new Promise((resolve) => {
                const observer = new MutationObserver((mutations) => {
                    for (const mutation of mutations) {
                        if (mutation.addedNodes.length) {
                            const radioButton = document.querySelector(fullSelector);
                            // If the radio button for Apple Pay is found, it is returned and the observer is disconnected
                            if (radioButton && radioButton.value === radioValue) {
                                observer.disconnect();
                                resolve(radioButton);
                            }
                        }
                    }
                });
                observer.observe(document.body, configForObserver);

                // The observer disconnects after 10 seconds if the checkout process does "not" utilize accordion-style steps.
                // Adhering to this type of step-by-step approach (the accordion one), may require more time compared to
                // displaying all checkout information at once from the initial view.
                if (!accordionFound) {
                    setTimeout(() => observer.disconnect(), 10000);
                }
            });
        }

        // Observing the changes made by OnePageCheckout after the first DOM is loaded
        async function observeOnePageCheckoutChanges() {
            return new Promise((resolve) => {
                const targetNode = document.querySelector(onePageCheckoutSelector);
                const callback = function(mutationsList, observer) {
                    for (const mutation of mutationsList) {
                        if (mutation.type === 'childList') {
                            // Getting all the radio buttons loaded by OnePageCheckout
                            const radioButtons = document.querySelectorAll(onePageCheckoutSelector + ' ' + radioSelector);
                            for (const radioButton of radioButtons) {
                                // If the radio button for Apple Pay is found is hidden and the observer is disconnected
                                if (radioButton.value === radioValue) {
                                    hideElement(radioButton.parentElement.parentElement);
                                    observer.disconnect();
                                    resolve();
                                    break;
                                }
                            }
                        }
                    }
                };
                const observer = new MutationObserver(callback);
                observer.observe(targetNode, configForObserver);

                // Observer is disconnected after 10 seconds as OnePageCheckout launches more AJAX requests
                setTimeout(() => observer.disconnect(), 10000);
            });
        }

        applePayCheckAndHide(fullSelector, radioValue).then((radioButton) => {
            // If the Apple Pay is not allowed to be used by the Apple API in this combination of browser and device ...
            if (typeof(window.ApplePaySession && ApplePaySession.canMakePayments()) === 'undefined') {
                const divPosition = radioButton.closest('div div').parentElement;
                if (journal3Found) {
                    // If the 'section-body' class appeared, the native Journal3 theme was selected as the 'Active Checkout'
                    // option on the admin panel. Otherwise, the default OpenCart theme was used.
                    const targetElement = divPosition.parentElement.className.includes('section-body')
                        ? radioButton.closest('div div').parentElement
                        : radioButton.parentElement.parentElement;
                    hideElement(targetElement);
                } else if (onePageCheckoutFound && divPosition.className.includes('extpanel-body')) {
                    // Observing the AJAX requests made by OnePageCheckout
                    const originalOpen = XMLHttpRequest.prototype.open;
                    XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
                        // If the AJAX request is for the payment details [logged] or personal details [non-logged], the observer is launched
                        const patterns = ['payment_details', 'personal_details'];
                        if (patterns.some(pattern => url.includes(pattern))) {
                            (async() => {
                                try {
                                    await observeOnePageCheckoutChanges();
                                } catch (error) {
                                    console.error('Error:', error);
                                }
                            })();
                        }
                        originalOpen.apply(this, arguments);
                    };
                } else {
                    hideElement(radioButton.parentElement);
                }
            }
        });
    });
})(jQuery);
