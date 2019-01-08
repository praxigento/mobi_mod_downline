define([
    "Praxigento_Core/js/grid/column/link"
], function (Column) {
    "use strict";

    return Column.extend({
        defaults: {
            /* @see \Praxigento\Downline\Ui\DataProvider\Grid\Account\Query::A_MLM_ID */
            idAttrName: "mlmId",
            route: "/customer/downline/index/mlmId/"
        }
    });
});
