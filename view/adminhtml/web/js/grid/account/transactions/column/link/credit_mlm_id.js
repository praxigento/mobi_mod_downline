define([
    "Praxigento_Core/js/grid/column/link"
], function (Column) {
    "use strict";

    return Column.extend({
        defaults: {
            idAttrName: "mlmIdCredit",
            route: "/customer/downline/index/mlmId/"
        }
    });
});
