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
        comment: ko.observable(""),
        assets: undefined,
        counterparty: ko.observable(),
        customer: undefined,
        operationId: 0,
        selectedAsset: ko.observable(),
        selectedCounterparty: undefined,
        transferType: ko.observable(TYPE_DIRECT),
        warnDiffCountries: ko.observable(false),
        warnOutOfDwnl: ko.observable(false),
        lastInputWasValid: ko.observable(true),
        warnAmount: undefined
    };

    /**
     * Get current customer ID data from uiRegistry (loaded asynchronously).
     *
     * @returns {*|Integer|string}
     */
    function getCustomerId() {
        /* */
        const customerDs = uiRegistry.get('customer_form.customer_form_data_source');
        const customerData = customerDs.data;
        const customer = customerData.customer;
        const result = customer.entity_id;
        return result;
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
        if (selected != undefined) {
            let balance = selected.acc_balance;
            if (balance == undefined) balance = 0;
            if (amount > balance) {
                result = true;
            }
        } else {
            /* skip amount validation if asset is not chosen */
        }
        return result;
    };

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
            /* switch off loader */
            $('body').trigger('processStop');
            /* set parsed HTML as content for modal placeholder create modal slider */
            $('#modal_panel_placeholder').html(innerHtml);
            popup = Modal(options, $('#modal_panel_placeholder'));

            /* open modal slider, populate knockout view-model and bind it to template */
            popup.openModal();
            viewModel.amount = ko.observable(0);
            viewModel.comment = ko.observable("");
            viewModel.assets = assets;
            viewModel.counterparty = ko.observable();
            viewModel.customer = customer;
            viewModel.operationId = 0;
            viewModel.transferType = ko.observable(TYPE_DIRECT);
            viewModel.warnAmount = ko.computed(fnWarnOnTransferAmount, viewModel);
            let elm = document.getElementById('modal_panel_placeholder');
            ko.cleanNode(elm);
            ko.applyBindings(viewModel, elm);

            /* add JQuery auto complete widget to the slider */
            $('#prxgtCustomerSearch').autocomplete({
                source: fnAjaxCustomerSearch,
                select: fnAutocompleteSelected,
                minLength: 2,
                /* disable auto-complete helper text */
                messages: {
                    noResults: '',
                    results: function () {
                    }
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
                    let one = data.items[i];
                    let nameFirst = one.name_first;
                    let nameLast = one.name_last;
                    let email = one.email;
                    let mlmId = one.mlm_id;
                    let label = nameFirst + ' ' + nameLast + ' <' + email + '> / ' + mlmId;
                    let foundOne = {
                        label: label,
                        value: label,
                        data: one
                    };
                    found.push(foundOne);
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
        let isDirect = (type == TYPE_DIRECT);

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

        /* process response from server: create modal slider and populate with data */
        let fnSuccess = function (response) {
            /* switch off ajax loader */
            $('body').trigger('processStop');
            viewModel.operationId = response.data.oper_id;
            let elm = document.getElementById('modal_panel_placeholder');
            ko.cleanNode(elm);
            ko.applyBindings(viewModel, elm);
            /* wait 3 sec. & close modal */
            setTimeout(function () {
                popup.closeModal();
            }, 3000);
        };

        let opts = {
            data: json,
            contentType: 'application/json',
            type: 'post',
            success: fnSuccess
        };

        $.ajax(urlTransfer, opts);
        /* switch on ajax loader */
        $('body').trigger('processStart');
    };

    /**
     * Function is fired when user selects transfer counterparty from the suggested list.
     *
     * @param event
     * @param ui
     */
    let fnAutocompleteSelected = function (event, ui) {
        const country = ui.item.data.country;
        const path = ui.item.data.path_full;
        const custCountry = viewModel.customer.country;
        const custPath = viewModel.customer.path_full;
        const isTheSameCountry = (custCountry == country);
        const isInDownline = path.startsWith(custPath);
        /* set model attributes */
        viewModel.selectedCounterparty = ui.item.data.id;
        viewModel.warnDiffCountries(!isTheSameCountry);
        viewModel.warnOutOfDwnl(!isInDownline);
    };

    /* bind modal opening to 'Accounting' button on the form */
    /* (see \Praxigento\Accounting\Block\Customer\Adminhtml\Edit\AccountingButton) */
    $('#customer-edit-prxgt-accounting').on('click', fnAjaxGetInitData);

    /* this is required return to prevent Magento parsing errors */
    let result = Component.extend({});
    return result;
});