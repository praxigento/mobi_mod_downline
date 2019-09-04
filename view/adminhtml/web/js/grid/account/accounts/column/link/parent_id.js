define([
    "Praxigento_Core/js/grid/column/link"
], function (Column) {
    "use strict";

    return Column.extend({
        defaults: {
            /* @see \Praxigento\Downline\Ui\DataProvider\Grid\Account\Query::A_PARENT_ID */
            idAttrName: "parentId",
            route: "/customer/index/edit/id/"
        }
    });
});
