define([
    "Praxigento_Core/js/grid/column/link"
], function (Column) {
    "use strict";

    return Column.extend({
        defaults: {
            /* see \Praxigento\Downline\Ui\DataProvider\Grid\Customer\Group\Changes\Query::A_CUST_MLM_ID */
            idAttrName: "custMlmId",
            route: "/customer/downline/index/mlmId/"
        }
    });
});
