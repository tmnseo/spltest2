<div class="geo-list hidden cm-popup-box" id="geo_list_{$block.block_id}">
    <div class="geo-list__search">
        {$nothing_found=__("nothing_found")}
        {$title_search_input=__("where_are_you_from")}
        <input type="text" value=""  id="cp_geo_search_finder"  class="geo-list__search-input cm-hint" title="{$title_search_input}"/>
        <script type="text/javascript">
            $("#cp_geo_search_finder").autocomplete({
                source: function( request, response ) {
                    var type = "";
                    getRusCitiesCp(type, request, response);
                    if (request.term.length > 1) {
                        $('#cpBuildCitiesList').css('display', 'block');
                        $('.geo-list-cities_popular').css('display', 'none');
                    }else{
                        $('#cpBuildCitiesList').css('display', 'none');
                        $('.geo-list-cities_popular').css('display', '');
                    }
                },
                change: function(event, ui) {
                    var inputVal = $(event.target).val();
                    if (inputVal === '{$title_search_input}') {
                        $('#cpBuildCitiesList').css('display', 'none');
                        $('.geo-list-cities_popular').css('display', '');
                    }
                },
                minLength: 1
            });

            function getRusCitiesCp(type, request, response) {
                $.ceAjax('request', fn_url('cp_city.autocomplete_city?q=' + encodeURIComponent(request.term)), {
                    callback: function(data) {
                        //response(data.autocomplete);
                        var myObject = JSON.parse(data.autocomplete);
                        
                      // alert(myObject);
                        $('#cpBuildCitiesList').html('');
                        if (myObject.length) {
                            myObject.forEach(function(data, index) {
                                onclic_str = 'onclick="cpSetCit('+data.code+');"';

                                $('#cpBuildCitiesList').append('<li class="geo-list-cities__item"><span '+onclic_str+'>'+data.label+'</span></li>');
                            });
                        } else {
                            $('#cpBuildCitiesList').append('<li class="geo-list-cities__item geo-list-cities__item_nothing-found"><span>{$nothing_found}</span></li>');
                        }
                    }
                });
            }


            function cpSetupLocation(city_id) {

                $('#cp_city_id_value').val(city_id);

                $('form[name="cp_geo_maxm"]').submit();
                //cpSetupLocation("'+data.code+'");
            }

            function cpSetCit(code){
                $('#cp_city_id_value').val(code);
                //$('#cp_geo_maxm_form').submit();
                $('#cp_button_setup_location').click();

            }
            </script>

        {*$city_state*}
        <span class="icon-spl-search"></span>
    </div>
    <ul class="geo-list-cities" id="cpBuildCitiesList">
    </ul>
    <ul class="geo-list-cities_popular">
        {foreach from=$cp_top_cities item="city"}
            <li class="geo-list-cities__item"><span onclick="cpSetCit({$city.rus_cities_city_id});">{$city.city}</span></li>
        {/foreach}
    </ul>
</div>

<div style="display: none">
    <form action="{""|fn_url}" id="cp_geo_maxm_form" method="POST" class="" name="cp_geo_maxm">    {*cm-ajax cm-post cm-ajax-full-render*}
        <input type="hidden" name="result_ids" value="cp_geo_maps_location_block_*" />
        <input type="hidden" name="return_url" value="{$smarty.request.return_url|default:$config.current_url}" />
        <input type="hidden" name="cp_city_id" id="cp_city_id_value" value="0"/>
        <button class="ty-btn-go" id="cp_button_setup_location" type="submit" name="dispatch[cp_city.setup_location]" title="{__("go")}"></button>
    </form>
</div>
