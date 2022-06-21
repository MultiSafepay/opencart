class MultiSafepayPaymentComponent {

    payment_component = null;

    constructor(config, gateway) {
        this.payment_component = null;
        this.config = config;
        this.gateway = gateway;
        this.initializePaymentComponent();
    }

    getPaymentComponent() {
        if (!this.paymentComponent) {
            this.paymentComponent = this.getNewPaymentComponent();
        }
        return this.paymentComponent;
    };

    getNewPaymentComponent() {
        return new MultiSafepay(
            {
                env: this.config.env,
                apiToken: this.config.apiToken,
                order: this.config.orderData
            }
        );
    };

    insertPayload(payload) {
        $('#multisafepay-form input[name="payload"]').val(payload);
    };

    removePayload() {
        $('#multisafepay-form input[name="payload"]').val();
    };

    initializePaymentComponent() {
        this.getPaymentComponent().init('payment', {
            container: '#multisafepay-payment',
            gateway: this.gateway,
            onLoad: state => {
                this.logger('onLoad');
            },
            onError: state => {
                this.logger('onError');
            }
        });
    };

    onSubmitCheckoutForm(event) {
        this.removePayload();
        if (this.getPaymentComponent().hasErrors()) {
            this.logger(this.getPaymentComponent().getErrors());
            $('#button-confirm').prop('disabled', true);
            event.preventDefault();
            event.stopPropagation();
            return;
        }
        const payload = this.getPaymentComponent().getPaymentData().payload;
        this.insertPayload(payload);
        $('#multisafepay-form').unbind('submit').submit();
    };

    logger(argument) {
        if (this.config.debug) {
            console.log(argument);
        }
    };
};
