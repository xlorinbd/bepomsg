/*=========================================================================================
	File Name: api-documentation.js
	Description: show hide div for api documentation.
------------------------------------------------------------------------------
    Item Name: Ultimate SMS - Bulk SMS Application For Marketing
    Author: Codeglen
    Author URL: https://codecanyon.net/user/codeglen
==========================================================================================*/

$(document).ready(function () {

        let featureDescription = $('.features_description .title');
        featureDescription.hide();

        if ($("#contacts-api-div").length > 0) {
            $("#contacts-api-div").show();
        } else {
            featureDescription.first().show();
        }

        function setFeature(feature) {
            featureDescription.each(function () {
                if (this !== feature)
                    $(this).hide();
            });
            $('#' + feature).toggle();
        }

        $("#features li").click(function (e) {
            e.preventDefault();
            setFeature(this.id + '-div')
        });
    }
)
