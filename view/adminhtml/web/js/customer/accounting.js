define([
    'jquery',
    'ko',
    'uiComponent',
    'uiRegistry',
    'Magento_Ui/js/modal/modal',
    'mage/template',
    'text!Praxigento_Downline/templates/customer/accounting/slider.html'
], function ($, ko, Component, uiRegistry, Modal, mageTemplate, innerHtml) {

    /**
     * Collect working data into the local context (scope).
     */
    /* component constants */
    const TYPE_DIRECT = 'direct';
    const ID_SEARCH = 'prxgtCustomerSearch';
    const ID_SELECT = 'prxgtCustomerSearchOptions';
    /* pin globals into UI component scope */
    const baseUrl = BASE_URL;
    const formKey = '?form_key=' + FORM_KEY;
    /* define local working data */
    const urlAdminBase = baseUrl.replace('/customer/', '/');
    const urlAssetGet = urlAdminBase + 'account/asset/get/' + formKey;
    const urlCustomerGet = urlAdminBase + 'prxgt/customer/get_byId/' + formKey;
    const urlCustomerSearch = urlAdminBase + 'prxgt/customer/search_byKey/' + formKey;
    const urlTransfer = urlAdminBase + 'account/asset/transfer/' + formKey;

    /* slider itself */
    let popup;
    /* View Model for slider */
    let viewModel = {
        amount: ko.observable(0),
        assets: undefined,
        comment: ko.observable(""),
        counterparty: ko.observable(),
        customer: undefined,
        displaySearchOpts: ko.observable(false),
        lastInputWasValid: ko.observable(true),
        operationId: ko.observable(0),
        selectedAsset: ko.observable(),
        selectedCounterparty: undefined,
        transAmount: ko.observable(0),
        transferType: ko.observable(TYPE_DIRECT),
        warnAmount: undefined,
        warnDiffCountries: ko.observable(false),
        warnOutOfDwnl: ko.observable(false)
    };

    /**
     * Get current customer ID data from uiRegistry (loaded asynchronously).
     *
     * @returns {number|string}
     */
    function getCustomerId() {
        /* */
        const customerDs = uiRegistry.get('customer_form.customer_form_data_source');
        const customerData = customerDs.data;
        const customer = customerData.customer;
        return customer.entity_id;
    }

    /**
     * Compute conditions for transaction amount warning (transfer amount is greater then asset balance).
     *
     * 'this' - is a viewModel object.
     *
     * @returns {boolean}
     */
    function fnWarnOnTransferAmount() {
        let result = false;
        let amount = this.amount();
        let selected = this.selectedAsset();
        if (selected !== undefined) {
            let balance = selected.acc_balance;
            if (balance === undefined) balance = 0;
            if (amount > balance) result = true;
        } else {
            /* skip amount validation if asset is not chosen */
        }
        return result;
    }

    /**
     * Front-back communication functions
     */
    /* get initial data to fill in slider (customer info, assets balances, etc.) */
    let fnAjaxGetInitData = function () {
        /* flags for ajax requests - 'true' means that requests is done. */
        let isAjaxGetCustDone = false;
        let isAjaxGetAssetsDone = false;
        /* containers for customer & assets data loaded by AJAX */
        let customer, assets;

        /**
         * Definitions.
         */
        let options = {
            type: 'slide',
            responsive: true,
            innerScroll: true,
            clickableOverlay: true,
            title: $.mage.__('Accounting'),
            buttons: [{
                text: $.mage.__('Cancel'),
                class: '',
                click: function () {
                    this.closeModal();
                }
            }, {
                text: $.mage.__('Process'),
                class: '',
                click: fnAjaxProcessData
            }]
        };
        /* populate template with initial data then open slider */
        let fnModalOpen = function () {
            // VARS
            let elSearch, elSelect; // search key input & select elements to search counterparty
            let idTimeoutLoad; // timeout id for waiting to load suggestions from the back
            let suggestById;

            // MAIN
            /* switch off loader */
            $('body').trigger('processStop');
            /* set parsed HTML as content for modal placeholder create modal slider */
            $('#modal_panel_placeholder').html(innerHtml);
            popup = Modal(options, $('#modal_panel_placeholder'));

            /* open modal slider, populate knockout view-model and bind it to template */
            popup.openModal();
            viewModel.amount = ko.observable(0);
            viewModel.assets = assets;
            viewModel.comment = ko.observable("");
            viewModel.counterparty = ko.observable();
            viewModel.customer = customer;
            viewModel.displaySearchOpts = ko.observable(false);
            viewModel.error = ko.observable("");
            viewModel.operationId = ko.observable(0);
            viewModel.transAmount = ko.observable(0);
            viewModel.transferType = ko.observable(TYPE_DIRECT);
            viewModel.warnAmount = ko.computed(fnWarnOnTransferAmount, viewModel);
            let elm = document.getElementById('modal_panel_placeholder');
            ko.cleanNode(elm);
            ko.applyBindings(viewModel, elm);

            /*
             * Counterparty suggestions.
             */
            elSearch = document.getElementById(ID_SEARCH);
            elSelect = document.getElementById(ID_SELECT);
            elSearch.value = ''; // clean previous values
            elSearch.addEventListener('input', () => {
                const term = elSearch.value;
                if ((typeof term === 'string') && (term.length >= 2)) {
                    // wait 1 sec. then load data from the server
                    if (idTimeoutLoad) clearTimeout(idTimeoutLoad);
                    idTimeoutLoad = setTimeout(() => {
                        // FUNCS
                        /**
                         * Callback to parse loaded data and populate selector's options.
                         * @param {array} res
                         */
                        function fnResponse(res) {
                            const opts = elSelect.options;
                            opts.length = 0;
                            suggestById = [];
                            for (const one of res) {
                                opts[opts.length] = new Option(one.label, one.value);
                                suggestById[one.value] = one.data;
                            }
                            /* switch off ajax loader */
                            $('body').trigger('processStop');
                        }

                        // MAIN
                        viewModel.displaySearchOpts(true);
                        /* switch on ajax loader (will be switched off on response processing). */
                        $('body').trigger('processStart');
                        fnAjaxCustomerSearch({term}, fnResponse);
                    }, 1000);
                }
            });
            // analyze selected option and populate viewModel with data
            elSelect.addEventListener('change', () => {
                const id = Number.parseInt(elSelect.value);
                const data = suggestById[id]
                if (data) {
                    elSearch.value = data.label;
                    const country = data.country;
                    const path = data.path_full;
                    const custCountry = viewModel.customer.country;
                    const custPath = viewModel.customer.path_full;
                    const isTheSameCountry = (custCountry === country);
                    const isInDownline = path.startsWith(custPath);
                    /* set model attributes */
                    viewModel.selectedCounterparty = data.id;
                    viewModel.warnDiffCountries(!isTheSameCountry);
                    viewModel.warnOutOfDwnl(!isInDownline);
                }
            });
        };
        /* success responses handlers for 2 requests */
        let fnGetCustSuccess = function (response) {
            customer = response.data;
            isAjaxGetCustDone = true;
            if (isAjaxGetCustDone && isAjaxGetAssetsDone) {
                /* open slider */
                fnModalOpen();
            }
        };
        let fnGetAssetsSuccess = function (response) {
            assets = response.data.items;
            isAjaxGetAssetsDone = true;
            if (isAjaxGetCustDone && isAjaxGetAssetsDone) {
                /* open slider */
                fnModalOpen();
            }
        };

        /**
         * Processing
         */
        /* switch on ajax loader */
        $('body').trigger('processStart');

        /* compose common parts for async requests */
        const customerId = getCustomerId();
        let request = {data: {customerId: customerId}};
        let json = JSON.stringify(request);
        let opts = {
            data: json,
            contentType: 'application/json',
            type: 'post'
        };
        /* perform ajax request to get customer data */
        opts.success = fnGetCustSuccess;
        $.ajax(urlCustomerGet, opts);
        /* perform ajax request to get assets data */
        opts.success = fnGetAssetsSuccess;
        $.ajax(urlAssetGet, opts);
    };

    /**
     * Search counterparty customers on the server and prepare data for UI.
     *
     * @param request
     * @param response
     */
    let fnAjaxCustomerSearch = function (request, response) {
        /* switch off warnings */
        viewModel.warnDiffCountries(false);
        viewModel.warnOutOfDwnl(false);
        /* send request to server and get found users */
        let data = {data: {search_key: request.term}};
        let json = JSON.stringify(data);
        $.ajax({
            url: urlCustomerSearch,
            data: json,
            contentType: 'application/json',
            type: 'post',
            success: function (resp) {
                /* convert API data into JQuery widget data */
                let data = resp.data;
                let found = [];
                for (let i = 0; i < data.items.length; i++) {
                    const one = data.items[i];
                    const nameFirst = one.name_first;
                    const nameLast = one.name_last;
                    const mlmId = one.mlm_id;
                    const value = one.id;
                    one.label = nameFirst + ' ' + nameLast + ' / ' + mlmId;
                    const item = {label: one.label, value, data: one};
                    found.push(item);
                }
                response(found);
            }
        });
    };

    let fnAjaxProcessData = function () {
        let asset = viewModel.selectedAsset();
        let assetId = asset.asset_id;
        let amount = viewModel.amount();
        let comment = viewModel.comment();
        let customerId = viewModel.customer.id;
        let counterPartyId = viewModel.selectedCounterparty;
        let type = viewModel.transferType();
        let isDirect = (type === TYPE_DIRECT);

        viewModel.displaySearchOpts(false);

        /* see: \Praxigento\Accounting\Controller\Adminhtml\Asset\Transfer */
        let data = {
            amount: amount,
            comment: comment,
            assetId: assetId,
            counterPartyId: counterPartyId,
            customerId: customerId,
            isDirect: isDirect,
        };
        let json = JSON.stringify({data: data});

        /* Function to process assets transfer response from server: create modal slider and populate with data */
        let fnSuccess = function (response) {

            /* clean up UI */
            viewModel.error("");
            viewModel.operationId(0);
            viewModel.transAmount(0);
            viewModel.warnDiffCountries(false);
            viewModel.warnOutOfDwnl(false);

            /* switch off ajax loader */
            $('body').trigger('processStop');

            /* analyze response */
            let respRes = response.result;
            let respData = response.data;
            if (!respData) {
                /* display error message */
                let msg = respRes.text;
                viewModel.error = ko.observable(msg);
            } else {
                /* display operation details */
                let amount = respData.amount;
                viewModel.transAmount(amount.toFixed(2));
                viewModel.operationId = ko.observable(respData.oper_id);
                // viewModel.operationId.valueHasMutated();
            }

            /* refresh UI */
            /* "ko i18n" statements are duplicated on cleanNode/applyBindings */
            let elm = document.getElementById('modal_panel_placeholder');
            ko.cleanNode(elm);
            ko.applyBindings(viewModel, elm);

            /* wait 3 sec. & close modal */
            setTimeout(function () {
                popup.closeModal();
            }, 3000);
        };

        /* Send request to the server to process asset transfer. */
        let opts = {
            data: json,
            contentType: 'application/json',
            type: 'post',
            success: fnSuccess
        };
        $.ajax(urlTransfer, opts);

        /* switch on ajax loader (will be switched off on response processing). */
        $('body').trigger('processStart');
    };

    /* bind modal opening to 'Accounting' button on the form */
    /* (see \Praxigento\Accounting\Block\Customer\Adminhtml\Edit\AccountingButton) */
    $('#customer-edit-prxgt-accounting').on('click', fnAjaxGetInitData);

    /* this is required return to prevent Magento parsing errors */
    return Component.extend({});
});
