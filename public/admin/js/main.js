var _VH_MAIN = {};
$(function() {
    _VH_MAIN.initOption();
    _VH_MAIN.initScrollbar();
    _VH_MAIN.initFixedMenu();
    _VH_MAIN.initSelect2();
    _VH_MAIN.setupAjaxDone();
    _VH_MAIN.initCheckbox();

});
_VH_MAIN.initScrollbar = function() {
    $.mCustomScrollbar.defaults.scrollButtons.enable = true;
    $('.scrollbar').mCustomScrollbar();
}
_VH_MAIN.initOption = function() {
    $('div[data-option]').each(function(index, el) {
        var op = $(el).attr('data-option');
        var v = localStorage.getItem('_admin_menu_' + op);
        if (v != undefined && v == '1') {
            $(el).hide();
        }
    });
    $('div[data-option] i.close').click(function(event) {
        var parent = $(this).parent();
        var op = parent.attr('data-option');
        var v = localStorage.setItem('_admin_menu_' + op, 1);
        parent.fadeOut(500);
    });
}
_VH_MAIN.initFixedMenu = function() {
    $(window).scroll(function(event) {
        var t = $(window).scrollTop();
        var h = $('.top_menu').height();
        var m = $('.main-menu').height();
        if (t > h + 0.1) {
            $('.main-menu').addClass('fix_menu');
            $('.right_header_admin .r_h_t_admin').addClass('fix_menu');
            $('.main_admin').css({
                'margin-top': m + 15 + 'px',
            })
        } else {
            $('.main-menu').removeClass('fix_menu');

            $('.right_header_admin .r_h_t_admin').removeClass('fix_menu');

            $('.main_admin').css({
                'margin-top': '0px',
            })
        }
    });
}
_VH_MAIN.initSelect2 = function() {
    $('.select2').select2();
    $('.simpleselect2').select2({ minimumResultsForSearch: -1 });
}
_VH_MAIN.showToast = function(title, msg, icon) {
    /*informational, warning, errors and success*/
    $.toast({
        text: msg,
        heading: title,
        icon: 'informational',
        showHideTransition: 'fade',
        allowToastClose: true,
        hideAfter: 3000,
        stack: 5,
        position: 'bottom-right',



        textAlign: 'left',
        loader: false,
        loaderBg: '#9EC600',
        beforeShow: function() {},
        afterShown: function() {},
        beforeHide: function() {},
        afterHidden: function() {}
    });
}
_VH_MAIN.setupAjaxDone = function() {
    $.ajaxSetup({
        data: { _token: $('meta[name=_token]').attr('content') },
    });

    $(document).ajaxSuccess(function(event, xhr, settings) {
        var text = xhr.responseText;
        try {
            var json = JSON.parse(text);
            if (json.code == REDIRECT) {
                window.location.href = window.location.href;
            } else {
                if (json.hasOwnProperty('noti') && json.noti == 1) {
                    $.simplyToast(json.message, json.code == SUCCESS ? 'success' : 'danger');
                }
            }

        } catch (ex) {}
    });
    $(document).ajaxError(function(event, xhr, settings, thrownError) {
        $.simplyToast('Xảy ra lỗi trong quá trình xử lý', 'danger');
    });
}
_VH_MAIN.initCheckbox = function() {
    $('input.ccb[type=checkbox]').checkboxpicker();
}
var FORM = (function() {
    var ajax = function(_this, type) {
        var data = $(_this).serialize();
        var typeModal = 'product-choose-' + $(_this).attr('data-type');
        if ($(_this).attr('attach') == 'true') {
            data += '&choose_storage=' + sessionStorage.getItem(typeModal);
        }
        $.ajax({
                url: $(_this).attr('action'),
                type: $(_this).attr('method'),
                dataType: 'json',
                data: data,
                global: false,
            })
            .done(function(json) {
                if (json.code == 200) {
                    switch (type) {
                        case 'create-promotion':
                        case 'create-deal':
                        case 'edit-deal':
                            window.location.href = json.redirect;
                            break;
                        case 'search-product-modal':
                            $('.choose-product-modal tbody').html(json.html);
                            break;
                        case 'choose-product-modal':
                            sessionStorage.removeItem(typeModal);
                            window.location.reload();
                            break;
                        default:
                            $.simplyToast(json.message, 'success');
                    }
                } else {
                    $.simplyToast(json.message, 'danger');
                }
            })
    }
    var chooseProductModal = function() {
        $('.check-all-product').change(function(event) {
            var formParent = $(this).closest('form.choose-product-modal');
            var typeModal = "product-choose-" + formParent.attr('data-type');
            var chooses = sessionStorage.getItem(typeModal);
            chooses = Array.isArray(chooses) ? chooses : [];
            if ($(this).is(':checked')) {
                $('.check-single-product').prop('checked', true);
                $('.check-single-product').each(function(index, el) {
                    var id = $(el).closest('tr').attr('data-id');
                    if (chooses.indexOf(id) == -1) {
                        chooses.push(id);
                    }
                });
            } else {
                $('.check-single-product').prop('checked', false);
                $('.check-single-product').each(function(index, el) {
                    var id = $(el).closest('tr').attr('data-id');
                    if (chooses.indexOf(id) != -1) {
                        chooses = chooses.filter(
                            item => item != id
                        );
                    }
                });
            }
            sessionStorage.setItem(typeModal, JSON.stringify(chooses));
        });
        $(document).on('change', '.check-single-product', function(event) {
            event.preventDefault();
            var formParent = $(this).closest('form.choose-product-modal');
            var typeModal = "product-choose-" + formParent.attr('data-type');
            var id = $(this).closest('tr').attr('data-id');
            var chooses = sessionStorage.getItem(typeModal);
            chooses = chooses != null ? JSON.parse(chooses) : [];
            if ($(this).is(':checked')) {
                if (chooses.indexOf(id) == -1) {
                    chooses.push(id);
                }
            } else {
                if (chooses.indexOf(id) != -1) {
                    chooses = chooses.filter(
                        item => item != id
                    );
                }
            }
            sessionStorage.setItem(typeModal, JSON.stringify(chooses));
        });
        $('.choose-product-modal').closest('.modal').on('hidden.bs.modal', function() {
            var formParent = $(this).closest('form.choose-product-modal');
            var typeModal = "product-choose-" + formParent.attr('data-type');
            sessionStorage.removeItem(typeModal);
        })
    }
    var notify = function() {
        if (typeNotify != '' && messageNotify != '') {
            $.simplyToast(messageNotify, typeNotify);
        }
    }

    return {
        _: function() {
            chooseProductModal();
            // notify();
        },
        ajax: function(_this, type = '') {
            return ajax(_this, type);
        },
    };
})();
$(function() {
    FORM._();
});