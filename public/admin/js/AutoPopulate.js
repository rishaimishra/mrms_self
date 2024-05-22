$( document ).ready(function() {


    $("select#ward").on('change', function () {

        //alert("dfdf");

        var ward = jQuery(this).val();

        var url = autoPopulateAjaxUrl;

        //var data = 'ward=' + ward;


        var posting = $.get( url, { ward: ward,type:'constituency' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#constituency").find('option').remove();
                jQuery("body select#constituency").append(options);
            }
            $('#constituency').selectpicker('refresh');

        });


        var posting = $.get( url, { ward: ward,type:'section' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#section").find('option').remove();
                jQuery("body select#section").append(options);
            }
            $('#section').selectpicker('refresh');

        });


        var posting = $.get( url, { ward: ward,type:'chiefdom' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#chiefdom").find('option').remove();
                jQuery("body select#chiefdom").append(options);
            }
            $('#chiefdom').selectpicker('refresh');

        });


        var posting = $.get( url, { ward: ward,type:'district' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#district").find('option').remove();
                jQuery("body select#district").append(options);
            }
            $('#district').selectpicker('refresh');

        });


        var posting = $.get( url, { ward: ward,type:'province' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#province").find('option').remove();
                jQuery("body select#province").append(options);
            }
            $('#province').selectpicker('refresh');

        });





    });


    $("select#ward_1").on('change', function () {

        //alert("dfdf");

        var ward = jQuery(this).val();

        var url = autoPopulateAjaxUrl;

        //var data = 'ward=' + ward;


        var posting = $.get( url, { ward: ward,type:'constituency' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#constituency_1").find('option').remove();
                jQuery("body select#constituency_1").append(options);
            }
            $('#constituency_1').selectpicker('refresh');

        });


        var posting = $.get( url, { ward: ward,type:'section' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#section_1").find('option').remove();
                jQuery("body select#section_1").append(options);
            }
            $('#section_1').selectpicker('refresh');

        });


        var posting = $.get( url, { ward: ward,type:'chiefdom' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#chiefdom_1").find('option').remove();
                jQuery("body select#chiefdom_1").append(options);
            }
            $('#chiefdom_1').selectpicker('refresh');

        });


        var posting = $.get( url, { ward: ward,type:'district' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#district_1").find('option').remove();
                jQuery("body select#district_1").append(options);
            }
            $('#district_1').selectpicker('refresh');

        });


        var posting = $.get( url, { ward: ward,type:'province' } );

        // Put the results in a div
        posting.done(function( data ) {

            var options = '';

            jQuery.each(data, function (key, value) {
                options += '<option value="'+ key +'">'+ value +'</option>';
            });

            if(options != '')
            {
                jQuery("body select#province_1").find('option').remove();
                jQuery("body select#province_1").append(options);
            }
            $('#province_1').selectpicker('refresh');

        });





    });
});

