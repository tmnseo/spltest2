<script language="javascript">
    (function(_,$){
    $(document).on("click", "a.cp-np__switch-from-block", function(){
        if ($('#theme_editor').length == 0) {
            var ca_target = $(this).attr('data-ca-target-id');
            var a_href = $(this).prop('href');
            if (a_href.length > 0) {
                var new_url = $.attachToUrl(a_href, 'cp_np_this_product=1');
                $.ceAjax('request', new_url, {
                    result_ids: ca_target,
                    full_render: true,
                    save_history: false,
                    caching: false,
                    scroll: '.tygh-content',
                });
            }
            event.preventDefault();
        }
    });
    $(document).on("click", ".cp-np-mosts__2nd-line_more a", function(){
        if ($('#theme_editor').length == 0) {
             
            var res_ids = 'cp_np_other_block_pagination*';
            var a_href = $(this).prop('href');
            if (a_href.length > 0) {
                var new_url = $.attachToUrl(a_href, 'cp_np_pagination=1');
                $.ceAjax('request', new_url, {
                    result_ids: res_ids,
                    full_render: true,
                    save_history: false,
                    caching: false,
                    callback: function (data) {
                        if (data && data.more_list && data.more_list.length > 0) {
                            var more_data= data.more_list;
                            $('#cp_np_other_block_pagination').after(data.more_list).html();
                            var pu_cont = $('#cp_np_other_block_pagination');
                            $('#cp_np_other_block_pagination').remove();
                            $('.cp-np-mosts__2nd-line_items tbody').append(pu_cont);
                        }
                    }
                });
            }
            event.preventDefault();
        }
    });
    $(document).on("click", ".cp-np__filter-block a", function(){
        if ($('#theme_editor').length == 0) {
            var tar_blank = $(this).attr('target');
            var ca_target = $(this).attr('data-ca-target-id');
            var a_href = $(this).prop('href');
            if (a_href.length > 0 && tar_blank != '_blank') {
                var fet_hash = fn_cp_np_get_url_param('features_hash', a_href);
                var save_fh = $.attachToUrl(a_href, 'cp_is_filter_run=2');
                var new_url = a_href.replace('features_hash=' + fet_hash, '');
                new_url = new_url.replace('?&', '?');
                new_url = new_url.replace('&&', '&');
                $.ceAjax('request', save_fh, {
                    result_ids: '',
                    full_render: false,
                    save_history: false,
                    hidden: true,
                    caching: false,
                    callback: function (response) {
                        $.ceAjax('request', new_url, {
                            result_ids: ca_target,
                            full_render: true,
                            save_history: true,
                            caching: false,
                            scroll: '.ty-mainbox-title',
                        });
                    }
                });
            }
            event.preventDefault();
        }
    });
    $.ceEvent('on', 'ce.ajaxdone', function (elms, scripts, params, responseData, responseText) {
        if (responseData && responseData.cp_is_new_link) {
            var new_url = responseData.cp_is_new_link;
            window.history.replaceState('', null, new_url);
        }
        if (responseData && responseData.cp_new_meta_data) {
            var m_data = responseData.cp_new_meta_data;
            if (m_data.description) {
                $('head meta[name="description"]').attr('content', m_data.description);
            } else {
                $('head meta[name="description"]').attr('content', '');
            }
            if (m_data.keywords) {
                $('head meta[name="keywords"]').attr('content', m_data.keywords);
            } else {
                $('head meta[name="keywords"]').attr('content', '');
            }
            if (m_data.canonical) {
                $('head link[rel="canonical"]').attr('href', m_data.canonical);
            } else {
                $('head link[rel="canonical"]').attr('href', '');
            }
        }
    });
    })(Tygh,Tygh.$);
    $(document).ready(function () {
        var has_new_url = "{$cp_is_new_link2}";
        if (has_new_url) {
            window.history.replaceState('', null, has_new_url);
        }
    });
    function fn_cp_np_get_url_param(param, link) {
        var url = link.slice(link.indexOf('?') + 1).split('&');
        for (var i = 0; i < url.length; i++) {
            var urlparam = url[i].split('=');
            if (urlparam[0] == param) {
                return urlparam[1];
            }
        }
    }
</script>