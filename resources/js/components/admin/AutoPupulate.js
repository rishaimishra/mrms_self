import axios from 'axios';
import jQuery from 'jquery';
import loader from './../Loader';


class AutoPupulate {

    constructor() {
        this.getConstituency();
        /*this.getSection();
        this.getChiefdom();
        this.getDistrict();
        this.getProvince();*/

    }

    getConstituency() {
        jQuery("select#ward").on('change', function () {

            loader.show();

            var ward = jQuery(this).val();

            var url = '/get/constituency';

            var data = 'ward=' + ward;

            axios.post(url, data, {dataType: 'json', accept: 'application/json'}).then(({data}) => {

                var options = '';

                jQuery.each(data, function (key, value) {
                    options += '<option value="'+ key +'">'+ value +'</option>';
                });

                if(options != '')
                {
                    jQuery("body select#constituency").find('option').remove();
                    jQuery("body select#constituency").append(options);
                }

                loader.hide();

            }).catch(({response}) => {

            });

        });
    }


}

new AutoPupulate();