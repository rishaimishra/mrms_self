jQuery(document).ready(function () {
    jQuery(".add-more").on("click", function () {

        var objClone = jQuery(this).closest(".col-lg-12").clone(true, true);
        var num = 1;
        var i = $(".collection-point").size() + 1;
        if (i > 5) {
            alert("You Can Only Add Max 5 Custom Field");
        } else {
            var hasid = objClone.find("input:text").attr("id");

            objClone.find("input:text").val("");
            objClone.find("input:text").prop("id", "Clone" + num);
            objClone.find(".add-more").remove();
            objClone.find(".remove-more").show();

            jQuery(this).closest(".col-lg-12").after(objClone);
        }
    });
    jQuery(".remove-more").on("click", function () {
        jQuery(this).closest(".col-lg-12").remove();
    });

    jQuery("a#media-popup").on("click", function () {
        var url = BASE_URL + "/back-admin/media/popup";

        var media_input = jQuery(this).attr("media-input")
            ? jQuery(this).attr("media-input")
            : "media_id";
        var media_destination = jQuery(this).attr("media-destination")
            ? jQuery(this).attr("media-destination")
            : "media-dest";

        jQuery.ajax({
            url: url,
            method: "post",
            data:
                "_token=" +
                CSRF_TOKEN +
                "&media_input=" +
                media_input +
                "&media_destination=" +
                media_destination,
            success: function (html) {
                jQuery("#response-block").html(html);
            },
        });
        return false;
    });
});
