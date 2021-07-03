//SETTING
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
}
var formatVND = function(money) {
    var vnd = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(money);
    vnd = vnd.replace('₫', 'VND');
    return vnd;
}
var datetimePicker = function() {
    jQuery.datetimepicker.setLocale('vi');
    $('#date-hours-combo-start').datetimepicker({
        format: 'd-m-Y H:i:s',
        step: 5,
        minDate: new Date(),
        minTime: moment().add(1, 'hours'),
    });
    $('#date-hours-combo-end').datetimepicker({
        format: 'd-m-Y H:i:s',
        step: 5,
        minDate: new Date(),
        minTime: moment().add(2, 'hours'),
    });
}
var showModal = function() {
    $(document).on('click', '.btn-pinks-alls.addProduct', function() {
        if ($('.big-modal-alls.addProduct').length > 0) {
            $('.big-modal-alls.addProduct').addClass('show');
            // $('body').css({'height':'100vh','overflow':'hidden'});
        }
        $('.bg-black').addClass('show');
        // $('body').css({'height':'100vh','overflow':'hidden'});
    })
}
var removeShow = function() {
    $('.bg-black').click(function() {
        if ($(this).hasClass('show')) {
            if ($('#modal_all_marketing').length > 0) {
                $('#modal_all_marketing').removeClass('show');
                $('body').css({ 'height': 'auto', 'overflow': 'auto' });
            }
            if ($('.big-modal-alls').length > 0) {
                $('.big-modal-alls').removeClass('show');
                $('body').css({ 'height': 'auto', 'overflow': 'auto' });
            }
            $(this).removeClass('show');
        }
    })
}
var hideModal = function() {
    $('.btn-greys-alls.close_modal_all').click(function(event) {
        event.preventDefault();
        $('.big-modal-alls').removeClass('show');
        $('.bg-black').removeClass('show');
        $('body').css({ 'height': 'auto', 'overflow': 'auto' });
    })
}
var timeCount = function(distance) {
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    return [days, hours, minutes, seconds];
}
var countdowns = function() {
    var countdowns = $('.time-countdowns');
    if (countdowns.length == 0) {
        return;
    }
    for (var i = 0; i < countdowns.length; i++) {
        var time = $(countdowns[i]).attr('data-end-time');
        var countDownStart = new Date($(countdowns[i]).attr('data-start-time')).getTime();
        var countDownEnd = new Date(time).getTime();
        window['interval' + i] = setInterval(function(element, countDownDate) {
            var now = new Date().getTime();
            var distance = countDownEnd - now;
            var distanceStart = countDownStart - now;
            if (distanceStart > 0) {
                var time = timeCount(distanceStart);
                $('.media-body').find('p.mb-3').html('Sự kiện sẽ bắt đầu sau')
                $(element).html((time[0] > 0 ? "<p><span>" + time[0] + "</span> Ngày</p>" : "") + '<p><span>' + time[1] + '</span> Giờ</p><p><span>' + time[2] + '</span> Phút</p>')
                $('.time_info').html((time[0] > 0 ? "Chương trình sẽ diễn ra trong " + time[0] + " ngày " : "Chương trình sẽ bắt đầu sau ") + time[1] + " giờ " + time[2] + " phút tới")
                $('.coudount-promotion').html((time[0] > 0 ? "Diễn ra sau " + time[0] + " ngày " : "Diễn ra sau ") + time[1] + " giờ " + time[2] + " phút tới")
            } else {
                var time = timeCount(distance);
                if (distance <= 0) {
                    clearInterval(window['interval' + i]);
                    $('.media-body p.mb-3').text('Sự kiện đã kết thúc')
                } else {
                    $('.media-body p.mb-3').text('Sự kiện sẽ kết thúc trong')
                    $(element).html((time[0] > 0 ? "<p><span>" + time[0] + "</span> Ngày</p>" : "") + '<p><span>' + time[1] + '</span> Giờ</p><p><span>' + time[2] + '</span> Phút</p>')
                    $('.time_info').html((time[0] > 0 ? "Chương trình sẽ kết thúc trong " + time[0] + " ngày " : "Chương trình sẽ kết thúc trong ") + time[1] + " giờ " + time[2] + " phút tới")
                    $('.coudount-promotion').html((time[0] > 0 ? "Kết thúc thời gian " + time[0] + " ngày " : "Kết thúc thời gian") + time[1] + " giờ " + time[2] + " phút tới")
                }
            }
        }, 1000, countdowns[i], countDownEnd);
    }
}
var selectAll = function() {
    $(document).ready(function() {
        $('.select-all-1 , .select-all-2 , .select-all-3 , .select-all-4').select2();
    });
};
var calendar = function() {
    if ($('.calendar').length == 0) return;
    $('.calendar').pignoseCalendar({
        lang: 'en',
        theme: 'light',
        format: 'DD-MM-YYYY',
        classOnDays: [],
        enabledDates: [],
        disabledDates: [],
        disabledWeekdays: [],
        disabledRanges: [],
        schedules: [],
        scheduleOptions: {
            colors: {}
        },
        week: 1,
        monthsLong: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
        weeks: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
        pickWeeks: false,
        initialize: true,
        multiple: false,
        toggle: false,
        buttons: false,
        reverse: false,
        modal: false,
        buttons: false,
        minDate: null,
        maxDate: null,
        select: function(a) {
            var date = convertDate(a[0]._d);
            $(this).closest('td').find('input[name="datetime"]').remove();
            $(this).closest('tr').find('td:first').prepend('<input name="datetime" hidden value="' + date + '">');
            $.ajax({
                    url: 'esystem/tim_khung_gio',
                    dataType: 'html',
                    type: 'POST',
                    data: {
                        time: date,
                    },
                })
                .done(function(json) {
                    $('.list-hour-prd-flash-check').html(json);
                })
        },
        selectOver: false,
        apply: function(a) {},
        click: null
    });
}

var convertDate = function(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth() + 1)).slice(-2),
        day = ("0" + date.getDate()).slice(-2);
    return [date.getFullYear(), mnth, day].join("-");
}

var checkboxAllModal = function() {
    $(document).on('click', '.addProduct .input-checked', function() {
        var name = 'product-choose-' + $(this).closest('.form-alls').find('form').data('type');
        var inputCheckAll = $('.checkbox-all');
        var inputNoChecked = $('.addProduct .input-checked').not(':checked');
        var inputChecked = $('.addProduct .input-checked:checked');
        var array_product_id = [];
        if ($(this).prop('checked') == false) {
            if (inputChecked.length == 0) {
                inputCheckAll.prop('checked', false)
                array_product_id = [];
            } else {
                inputCheckAll.prop('checked', true)
                $.each(inputChecked, function() {
                    array_product_id.push($(this).val());
                })
            }
        } else {
            inputCheckAll.prop('checked', true)
            $.each(inputChecked, function() {
                array_product_id.push($(this).val());
            })
        }
        sessionStorage.setItem(name, JSON.stringify(array_product_id))
    })
    $(document).on('click', '.checkbox-all', function() {
        var inputNoChecked = $('.addProduct .input-checked').not(':checked');
        var inputChecked = $('.addProduct .input-checked:checked');
        var name = 'product-choose-' + $(this).closest('.form-alls').find('form').data('type');
        var array_product_id = [];
        if (inputChecked.length == 0) {
            inputNoChecked.prop('checked', true)
            $(this).prop('checked', true)
            $.each(inputNoChecked, function() {
                $(this).prop('checked', true)
                array_product_id.push($(this).val());
            })
            $.each(inputChecked, function() {
                $(this).prop('checked', true)
                array_product_id.push($(this).val());
            })
        } else {
            if ($(this).prop('checked') == false) {
                if (inputNoChecked.length == 0) {
                    $(this).prop('checked', false)
                    inputChecked.prop('checked', false);
                    array_product_id = [];
                } else {
                    $(this).prop('checked', true);
                    inputNoChecked.prop('checked', true)
                    $.each(inputChecked, function() {
                        $(this).prop('checked', true);
                        array_product_id.push($(this).val());
                    })
                    $.each(inputNoChecked, function() {
                        $(this).prop('checked', true);
                        array_product_id.push($(this).val());
                    })
                }
            } else {
                inputNoChecked.prop('checked', true)
                $.each(inputNoChecked, function() {
                    $(this).prop('checked', true)
                    array_product_id.push($(this).val());
                })
            }
        }
        if (sessionStorage.getItem(name) !== null) {
            sessionStorage.setItem(name, JSON.stringify(array_product_id))
        } else {
            sessionStorage.setItem(name, JSON.stringify(array_product_id))
        }
    })
}
var readyRemoveSession = function() {
        $(document).ready(function() {
            if (sessionStorage.getItem('product-choose-voucher') !== null) {
                sessionStorage.removeItem('product-choose-voucher')
            }
        })
    }
    //Search
var ajaxSearchNameCode = function() {
    $(document).on('keyup', '#searchProductFlashSale', function() {
        var category = $('.form-alls').find('select[name="category"]').on('select2:select').val();
        var type = $('.form-alls').find('select[name="nameorcode"]').on('select2:select').val();
        var name = $(this).val();
        var id = $('.one.hidden').attr('dt-id');
        $.ajax({
                url: '/esystem/searchName',
                dataType: 'json',
                type: 'POST',
                data: {
                    name: name,
                    category: category,
                    type: type,
                    id: id,
                },
            })
            .done(function(data) {
                if (data.code == 200) {
                    $('.addProduct tbody').html(data.html);
                } else {
                    toastr['error'](data.message)
                }
            })
    })
};
var ajaxSearchProductPromotion = function() {
    $(document).on('keyup', '#searchProductPromotion', function() {
        var category = $('.form-alls').find('select[name="category"]').on('select2:select').val();
        var type = $('.form-alls').find('select[name="nameorcode"]').on('select2:select').val();
        var name = $(this).val();
        var id = $('.one.hidden').attr('dt-id');
        $.ajax({
                url: '/esystem/searchNamePromotion',
                dataType: 'json',
                type: 'POST',
                data: {
                    name: name,
                    category: category,
                    type: type,
                    id: id,
                },
            })
            .done(function(data) {
                if (data.code == 200) {
                    $('.addProduct tbody').html(data.html);
                } else {
                    toastr['error'](data.message)
                }
            })
    })
}
var ajaxSearchNameCodeCombo = function() {
        $(document).on('keyup', '#searchProductCombo', function() {
            var category = $('.form-alls').find('select[name="category"]').on('select2:select').val();
            var type = $('.form-alls').find('select[name="nameorcode"]').on('select2:select').val();
            var name = $(this).val();
            var id = $('.one.hidden').attr('dt-id');
            $.ajax({
                    url: '/esystem/searchNameCombo',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        name: name,
                        category: category,
                        type: type,
                        id: id,
                    },
                })
                .done(function(data) {
                    if (data.code == 200) {
                        $('.addProduct tbody').html(data.html);
                    } else {
                        toastr['error'](data.message)
                    }
                })
        })
    }
    //FlashSale
var addItemFlashSale = function() {
    $('.add-item-flash-sale').click(function(event) {
        event.preventDefault();
        flash_sale_id = $(this).data('id');
        $('.select-all-itemss .input-checked:checked').val();
        var inputChecked = $('.select-all-itemss .input-checked:checked');
        var array_product_id = [];
        $.each(inputChecked, function() {
            array_product_id.push($(this).val());
        })
        $.ajax({
                url: '/esystem/them-san-pham-flash-sale',
                dataType: 'json',
                type: 'POST',
                data: {
                    array_product_id: array_product_id,
                    flash_sale_id: flash_sale_id,
                },
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    window.location.href = json.url;
                } else {
                    toastr['error'](json.message);
                }
            })
    })
}
var changeActMarketing = function() {
    $(document).on('change', '.table_flash_sale .toggle_action_flash_sale', function() {
        _this = $(this);
        $.ajax({
                url: '/esystem/thay-doi-trang-thai-flash-sale',
                dataType: 'json',
                type: 'POST',
                data: {
                    id: _this.val(),
                    flash_sale_id: $('.one.hidden').attr('dt-id'),
                    product_id: $(_this).closest('tr').attr('id'),
                    price: _this.closest('tr').find('.price_sale input').val(),
                    qty: _this.closest('tr').find('.qty input').val(),
                    limit: _this.closest('tr').find('.limit input').val(),
                },
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                } else {
                    $(_this).prop('checked', false);
                    toastr['error'](json.message);
                }
            })
    })
    $(document).on('change', '.table_promotion .toggle_action_flash_sale', function() {
        _this = $(this);
        $.post({
                url: '/esystem/change-act-promotion',
                data: {
                    id: _this.val(),
                    promotion_id: $('.one.hidden').attr('dt-id'),
                    product_id: $(_this).closest('tr').attr('id'),
                    price: $(_this).closest('tr').find('.price_sale input').val(),
                    limit: $(_this).closest('tr').find('.limit input').val(),
                },
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                } else {
                    toastr['error'](json.message);
                    $(_this).prop('checked', false)
                }
            })
    })
}
var changeInputValue = function() {
    $(document).on('click', '.table_flash_sale .editProduct', function() {
        var _this = $(this);
        var item = $('.flash_sale_detail tbody tr');
        var array_id = [];
        var array_price = [];
        var array_qty = [];
        var array_limit = [];
        if ($(this).text() == "Chỉnh sửa sản phẩm") {
            $(this).text('Lưu lại');
            $.each(item, function() {
                var price = $(this).find('.price_sale');
                var qty = $(this).find('.qty');
                var limit = $(this).find('.limit');
                price.html(`<input name="price" type="number" min="1" value="${price.data('value')}" placeholder="Giá giảm">`)
                qty.html(`<input name="qty" type="number" min="1" value="${qty.data('value')}" placeholder="Số lượng bán ra">`)
                limit.html(`<input name="limit" type="number" min="1" value="${limit.data('value')}" placeholder="Số lượng tối đa">`)
            })
        } else {
            $.each(item, function() {
                var price = $(this).find('input[name="price"]').val();
                var qty = $(this).find('input[name="qty"]').val();
                var limit = $(this).find('input[name="limit"]').val();
                array_id.push(Number($(this).attr('id')));
                array_price.push(Number(price));
                array_qty.push(Number(qty))
                array_limit.push(Number(limit));
            })
            var data = { array_id: array_id, array_price: array_price, array_qty: array_qty, array_limit: array_limit, flash_sale_id: $('.one.hidden').attr('dt-id') }
            $.post({
                    url: '/esystem/sua-san-pham-flash-sale',
                    data: data,
                    beforeSend: function() {
                        _this.prop('disabled', true);
                        _this.html(`<div class="loader"><div>`)
                    }
                })
                .done(function(json) {
                    if (json.code === 200) {
                        $.each(item, function() {
                            var price = $(this).find('input[name="price"]').val();
                            var qty = $(this).find('input[name="qty"]').val();
                            var limit = $(this).find('input[name="limit"]').val();
                            $(this).find('.price_sale').data('value', price);
                            $(this).find('.qty').data('value', qty);
                            $(this).find('.limit').data('limit', limit);
                            $(this).find('.price_sale').html(formatVND(Number(price)));
                            $(this).find('.qty').html(qty);
                            $(this).find('.limit').html(limit);
                        })
                        toastr['success'](json.message)
                        _this.text('Chỉnh sửa sản phẩm');
                    } else {
                        toastr['error'](json.message);
                        _this.text('Lưu lại');
                    }
                    _this.prop('disabled', false);
                })
        }
    })
    $(document).on('click', '.table_promotion .editProduct', function() {
        var _this = $(this);
        var item = $('.flash_sale_detail tbody tr');
        var array_id = [];
        var array_price = [];
        var array_limit = [];
        if ($(this).text() == "Chỉnh sửa sản phẩm") {
            $(this).text('Lưu lại');
            $.each(item, function() {
                var price = $(this).find('.price_sale');
                var limit = $(this).find('.limit');
                price.html(`<input name="price" type="number" min="1" value="${price.data('value')}" placeholder="Giá giảm">`)
                limit.html(`<input name="limit" type="number" min="1" value="${limit.data('value')}" placeholder="Số lượng tối đa">`)
            })
        } else {
            $.each(item, function() {
                var price = $(this).find('input[name="price"]').val();
                var qty = $(this).find('input[name="qty"]').val();
                var limit = $(this).find('input[name="limit"]').val();
                array_id.push(Number($(this).attr('id')));
                array_price.push(Number(price));
                array_limit.push(Number(limit));
            })
            var data = { array_id: array_id, array_price: array_price, array_limit: array_limit, promotion_id: $('.one.hidden').attr('dt-id') }
            $.post({
                    url: '/esystem/edit-item-promotion',
                    data: data,
                    beforeSend: function() {
                        _this.prop('disabled', true);
                        _this.html(`<div class="loader"><div>`)
                    }
                })
                .done(function(json) {
                    if (json.code === 200) {
                        $.each(item, function() {
                            var price = $(this).find('input[name="price"]').val();
                            var qty = $(this).find('input[name="qty"]').val();
                            var limit = $(this).find('input[name="limit"]').val();
                            $(this).find('.price_sale').data('value', price);
                            $(this).find('.qty').data('value', qty);
                            $(this).find('.limit').data('limit', limit);
                            $(this).find('.price_sale').html(formatVND(Number(price)));
                            $(this).find('.qty').html(qty);
                            $(this).find('.limit').html(limit);
                        })
                        toastr['success'](json.message)
                        _this.text('Chỉnh sửa sản phẩm');
                    } else {
                        toastr['error'](json.message);
                        _this.text('Lưu lại');
                    }
                    _this.prop('disabled', false);
                })
        }
    })
    $(document).on('change', '.flash_sale_detail input[name="price"]', function() {
        var parent = $(this).closest('tr');
        var input = parent.find('.percen');
        var price_old = Number(parent.find('.price_old').data('value'));
        var price_sale = Number(parent.find('.price_sale').data('value'));
        var price_change = $(this).val();
        if (price_change < 1000) {
            parent.find('.error_price').remove();
            parent.find('.price_sale').append(`<p class="error_price">Giá giảm không thể nhỏ hơn 1000 ₫</p>`)
            return false;
        } else {
            parent.find('.error_price').remove();
        }
        if (price_change >= price_old) {
            input.closest('td').find('.error_percen').remove();
            input.closest('td').append(`<p class="error_percen">Giá giảm không thể lớn hơn hoặc bằng giá gốc</p>`)
        } else {
            var percen = Math.round((price_old - price_change) / price_old * 100);
            if (percen > 90) {
                input.closest('td').find('.error_percen').remove();
                input.html(percen)
                input.closest('td').append(`<p class="error_percen">Giá giảm lớn hơn 90%</p>`)
            } else {
                input.closest('td').find('.error_percen').remove();
                input.html(percen)
            }
        }
    })
    $(document).on('change', '.flash_sale_detail input[name="qty"]', function() {
        var parent = $(this).closest('tr');
        var inputLimit = parent.find('.limit');
        var qty = Number($(this).val());
        var limit = Number(parent.find('input[name="limit"]').val());
        if (qty < limit) {
            inputLimit.closest('td').find('.error_limit').remove();
            inputLimit.closest('td').append(`<p class="error_limit">Giới hạn bán ra không thể lớn hơn số lượng bán ra</p>`)
        } else {
            inputLimit.closest('td').find('.error_limit').remove();
        }
    })
    $(document).on('change', '.flash_sale_detail input[name="limit"]', function() {
        var parent = $(this).closest('tr');
        var inputLimit = parent.find('.limit');
        var limit = Number($(this).val());
        var qty = Number(parent.find('input[name="qty"]').val());
        if (qty < limit) {
            inputLimit.closest('td').find('.error_limit').remove();
            inputLimit.closest('td').append(`<p class="error_limit">Giới hạn bán ra không thể lớn hơn số lượng bán ra</p>`)
        } else {
            inputLimit.closest('td').find('.error_limit').remove();
        }
    })
}
var showCreateFlashSale = function() {
    $('.showModalCreateFlashSale').click(function() {
        if (!$('.big-modal-alls.createFlashSale').hasClass('show')) {
            $('.big-modal-alls.createFlashSale').addClass('show')
            $('.bg-black').addClass('show')
        }
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        var date = yyyy + '-' + mm + '-' + dd;
        $.ajax({
                url: '/esystem/tim_khung_gio',
                dataType: 'html',
                type: 'POST',
                data: {
                    time: date,
                },
            })
            .done(function(json) {
                $('.list-hour-prd-flash-check').html(json);
            })
    })
}
var getTimeSlotStart = function() {
    $(document).ready(function() {
        if ($('.table-allss input[name="datetime"]').length == 0) return;
        date = $('.table-allss input[name="datetime"]').val();
        $.ajax({
                url: '/esystem/tim_khung_gio',
                dataType: 'html',
                type: 'POST',
                data: {
                    time: date,
                },
            })
            .done(function(json) {
                $('.list-hour-prd-flash-check').html(json);
            })
    })
}
var createFlashSale = function() {
    $('button.createFlashSale').click(function(event) {
        event.preventDefault();
        var time = $('.calendar').closest('tr').find('input[name="datetime"]').val();
        var slot_time = $('.table-check-flashsale').find('input[name="slot_time"]:checked').val();
        $.ajax({
                url: '/esystem/tao-flash-sale',
                dataType: 'json',
                type: 'POST',
                data: {
                    time: time,
                    slot_time: slot_time,
                },
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    window.location.href = json.url;
                } else {
                    toastr['error'](json.message);
                }
            })
    })
}
var deleteProductMarketing = function() {
    $('.checkbox_delele_all').click(function() {
        var button = $('.editProduct');
        var buttonChange = $('.deleteAllItem');
        var inputNoChecked = $('.flash_sale_detail .checkbox_delete_item').not(':checked');
        var inputChecked = $('.flash_sale_detail .checkbox_delete_item:checked');
        if (inputChecked.length === 0) {
            inputNoChecked.prop('checked', true)
            button.removeClass('editProduct').addClass('deleteAllItem')
            button.text('Xóa sản phẩm');
            if ($('main').hasClass('table_combo')) {
                $('.table_combo .header_button_right').find('.deleteItems').remove();
                $('.table_combo .header_button_right').append(`<button class="btn-pinks-alls deleteItems">Xóa tất cả</button>`);
            }
        } else {
            if (inputNoChecked.length == 0) {
                $(this).prop('checked', false)
                inputChecked.prop('checked', false)
                buttonChange.removeClass('deleteAllItem').addClass('editProduct')
                buttonChange.text('Chỉnh sửa sản phẩm');
                if ($('main').hasClass('table_combo')) {
                    $('.table_combo .header_button_right').find('.deleteItems').remove();
                }
            } else {
                $(this).prop('checked', true)
                inputNoChecked.prop('checked', true)
                button.removeClass('editProduct').addClass('deleteAllItem')
                button.text('Xóa sản phẩm');
                if ($('main').hasClass('table_combo')) {
                    $('.table_combo .header_button_right').find('.deleteItems').remove();
                    $('.table_combo .header_button_right').append(`<button class="btn-pinks-alls deleteItems">Xóa tất cả</button>`);
                }
            }
        }
    })
    $('.checkbox_delete_item').click(function() {
        var button = $('.editProduct');
        var buttonChange = $('.deleteAllItem');
        var inputChecked = $('.flash_sale_detail .checkbox_delete_item:checked');
        if ($(this).prop('checked') == true) {
            $('.checkbox_delele_all').prop('checked', true)
            button.removeClass('editProduct').addClass('deleteAllItem')
            button.text('Xóa sản phẩm');
            if ($('main').hasClass('table_combo')) {
                $('.table_combo .header_button_right').find('.deleteItems').remove();
                $('.table_combo .header_button_right').append(`<button class="btn-pinks-alls deleteItems">Xóa tất cả</button>`);
            }
        } else {
            if (inputChecked.length == 0) {
                $('.checkbox_delele_all').prop('checked', false)
                buttonChange.removeClass('deleteAllItem').addClass('editProduct')
                buttonChange.text('Chỉnh sửa sản phẩm');
                if ($('main').hasClass('table_combo')) {
                    $('.table_combo .header_button_right').find('.deleteItems').remove();
                }
            } else {
                $('.checkbox_delele_all').prop('checked', true)
                button.removeClass('editProduct').addClass('deleteAllItem')
                button.text('Xóa sản phẩm');
                if ($('main').hasClass('table_combo')) {
                    $('.table_combo .header_button_right').find('.deleteItems').remove();
                    $('.table_combo .header_button_right').append(`<button class="btn-pinks-alls deleteItems">Xóa tất cả</button>`);
                }
            }
        }
    })
    $(document).on('click', '.table_flash_sale .deleteAllItem', function() {
        _this = $(this)
        var inputChecked = $('.flash_sale_detail .checkbox_delete_item:checked');
        var array_product_id = [];
        var flash_sale_id = $('.one.hidden').attr('dt-id');
        $.each(inputChecked, function() {
            array_product_id.push($(this).closest('tr').attr('id'));
        })
        $.post({
                url: '/esystem/xoa-san-pham-flash-sale',
                data: {
                    array_product_id: array_product_id,
                    flash_sale_id: flash_sale_id,
                },
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    inputChecked.closest('tr').remove();
                    _this.removeClass('deleteAllItem').addClass('editProduct');
                    _this.html('Chỉnh sửa sản phẩm');
                    $('.count_product_item').html(json.count);
                    $('.checkbox_delele_all').prop('checked', false);
                    if (json.count == 0) {
                        $('.header_table').find('.header_button_right').remove();
                        $('.flash_sale_detail').html(``);
                        $('.flash_sale_detail').html(`
                        <div class="box-choose-flashsale">
                           <div class="btn-modal-choose-flashsale">
                                <button type="button" class="btn-pinks-alls addProduct">
                                     Thêm sản phẩm
                                </button>
                                <p>Hãy thêm và kích hoạt từng sản phẩm trong khu vực Flash Sale</p>
                           </div>
                        </div>
                    `);
                    }
                } else {
                    toastr['error'](json.message);
                }
            })
    })
    $(document).on('click', '.table_promotion .deleteAllItem', function() {
        _this = $(this)
        var inputChecked = $('.flash_sale_detail .checkbox_delete_item:checked');
        var array_product_id = [];
        var promotion_id = $('.one.hidden').attr('dt-id');
        $.each(inputChecked, function() {
            array_product_id.push($(this).closest('tr').attr('id'));
        })
        $.post({
                url: '/esystem/delete-item-promotion',
                data: {
                    array_product_id: array_product_id,
                    promotion_id: promotion_id,
                },
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    inputChecked.closest('tr').remove();
                    _this.removeClass('deleteAllItem').addClass('editProduct');
                    _this.html('Chỉnh sửa sản phẩm');
                    $('.count_product_item').html(json.count);
                    $('.checkbox_delele_all').prop('checked', false);
                    if (json.count == 0) {
                        $('.box-alls').remove();
                        $('.show_no_product').append(`
                        <div class="box-choose-sale-event">
                            <img src="/admin/theme_2/frontend/images/box-icon-1.png">
                            <button type="button" class="btn-pinks-alls addProduct">
                                Thêm sản phẩm
                            </button>
                        </div>
                    `);
                    }
                } else {
                    toastr['error'](json.message);
                }
            })
    })
}
var editFlashSaleTimeSlot = function() {
        $(document).on('click', '.edit-timeFlashSale', function(event) {
            event.preventDefault();
            var flash_sale_id = $('.one.hidden').attr('dt-id');
            var time = $('.calendar').closest('tr').find('input[name="datetime"]').val();
            var slot_time = $('.table-check-flashsale').find('input[name="slot_time"]:checked').val();
            $.ajax({
                    url: '/esystem/edit-flash-sale',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        time: time,
                        slot_time: slot_time,
                        flash_sale_id: flash_sale_id,
                    },
                })
                .done(function(json) {
                    if (json.code == 200) {
                        toastr['success'](json.message)
                        location.reload();
                    } else {
                        toastr['error'](json.message);
                    }
                })
        })
    }
    //COMBO
var addItemProductCombo = function() {
    $(document).on('click', '.add-item-combo', function(event) {
        event.preventDefault();
        _this = $(this);
        var inputChecked = $('.input-checked:checked');
        var array_product_id = [];
        var combo_id = $('.one.hidden').attr('dt-id');
        $.each(inputChecked, function() {
            array_product_id.push($(this).val());
        })
        $.post({
                url: '/esystem/them-san-pham-combo',
                data: {
                    array_product_id: array_product_id,
                    combo_id: combo_id
                },
                beforeSend: function() {
                    _this.prop('disabled', true)
                }
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    window.location.href = json.url
                } else {
                    toastr['error'](json.message);
                }
                _this.prop('disabled', false)
            })
    })
}
var backCombo = function() {
    $('.backCombo').on('click', function(event) {
        event.preventDefault()
        window.location.href = '/esystem/view/combos';
    })
}
var createCombo = function() {
    $(document).on('submit', '#comboEvents', function(event) {
        form = $(this);
        action = form.attr('action');
        event.preventDefault()
        $.ajax({
                url: action,
                dataType: 'json',
                type: 'POST',
                data: form.serialize(),
                beforeSend: function() {
                    form.find('button').prop('disabled', true);
                }
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    window.location.href = json.url
                } else {
                    toastr['error'](json.message);
                }
                form.find('button').prop('disabled', false);
            })
    })
}
var deleteItemProductCombo = function() {
        $('.table_combo .del_item').click(function() {
            _this = $(this);
            var inputChecked = $('.checkbox_delete_item:checked');
            var combo_id = $('.one.hidden').attr('dt-id');
            var array_product_id = [];
            array_product_id.push(_this.closest('tr').attr('id'));
            $.ajax({
                    url: '/esystem/xoa-san-pham-combo',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        array_product_id: array_product_id,
                        combo_id: combo_id,
                    },
                })
                .done(function(json) {
                    if (json.code == 200) {
                        _this.closest('tr').remove();
                        $('.count_product_item').html(json.count);
                        toastr['success'](json.message)
                        if (inputChecked.length == 1) {
                            $('.checkbox_delele_all').prop('checked', false)
                        }
                    } else {
                        toastr['error'](json.message);
                    }
                })
        })
        $('.table_flash_sale .del_item').click(function() {
            _this = $(this);
            var inputChecked = $('.checkbox_delete_item:checked');
            var flash_sale_id = $('.one.hidden').attr('dt-id');
            var array_product_id = [];
            array_product_id.push(_this.closest('tr').attr('id'));
            $.post({
                    url: '/esystem/xoa-san-pham-flash-sale',
                    data: {
                        array_product_id: array_product_id,
                        flash_sale_id: flash_sale_id,
                    },
                })
                .done(function(json) {
                    if (json.code == 200) {
                        _this.closest('tr').remove();
                        $('.count_product_item').html(json.count);
                        toastr['success'](json.message)
                        if (inputChecked.length == 1) {
                            $('.checkbox_delele_all').prop('checked', false)
                        }
                        if (json.count == 0) {
                            $('.header_table').find('.header_button_right').remove();
                            $('.flash_sale_detail').html(``);
                            $('.flash_sale_detail').html(`
                            <div class="box-choose-flashsale">
                               <div class="btn-modal-choose-flashsale">
                                    <button type="button" class="btn-pinks-alls addProduct">
                                         Thêm sản phẩm
                                    </button>
                                    <p>Hãy thêm và kích hoạt từng sản phẩm trong khu vực Flash Sale</p>
                               </div>
                            </div>
                        `);
                        }
                    } else {
                        toastr['error'](json.message);
                    }
                })
        })
        $(document).on('click', '.table_combo .deleteItems', function() {
            _this = $(this)
            var inputChecked = $('.flash_sale_detail .checkbox_delete_item:checked');
            console.log(inputChecked);
            var array_product_id = [];
            var combo_id = $('.one.hidden').attr('dt-id');
            $.each(inputChecked, function() {
                array_product_id.push($(this).closest('tr').attr('id'));
            })
            $.post({
                    url: '/esystem/xoa-san-pham-combo',
                    data: {
                        array_product_id: array_product_id,
                        combo_id: combo_id,
                    },
                })
                .done(function(json) {
                    if (json.code == 200) {
                        toastr['success'](json.message)
                        inputChecked.closest('tr').remove();
                        $('.count_product_item').html(json.count);
                        $('.checkbox_delele_all').prop('checked', false);
                        _this.remove();
                    } else {
                        toastr['error'](json.message);
                    }
                })
        })
    }
    //Promotion
var createPromotion = function() {
    $('.create-promotion').on('submit', function(event) {
        event.preventDefault();
        form = $(this);
        action = form.attr('action');
        $.ajax({
                url: action,
                dataType: 'json',
                type: 'POST',
                data: form.serialize(),
                beforeSend: function() {
                    form.find('.btn-pinks-alls').prop('disabled', true)
                }
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    window.location.href = json.url
                } else {
                    toastr['error'](json.message);
                }
                form.find('.btn-pinks-alls').prop('disabled', false)
            })
    })
}
var addItemProductPromotion = function() {
    $('.add-item-promotion').click(function(event) {
        event.preventDefault();
        var action = $(this).data('action');
        var inputChecked = $('.addProduct .input-checked:checked');
        var promotion_id = $('.one.hidden').attr('dt-id');
        var array_product_id = [];
        $.each(inputChecked, function() {
            array_product_id.push($(this).val());
        })
        $.post({
                url: '/esystem/add-item-promotion',
                data: {
                    array_product_id: array_product_id,
                    promotion_id: promotion_id,
                }
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    window.location.href = json.url
                } else {
                    toastr['error'](json.message);
                }
            })
            .fail(function(json) {})
    })
}
var editItemProductPromotion = function() {
    $('.edit-item-modal-promotion').click(function() {
        event.preventDefault();
        var action = $(this).data('action');
        var val = $('.editProduct .input-checked:checked');
        var id = $('.one.hidden').attr('dt-id');
        var arrayId = [];
        var arrayPrice = [];
        var arrayLimit = [];
        $.each(val, function() {
            var All = $(this).closest('tr');
            arrayLimit.push(All.find('input[name="limit"]').val());
            arrayPrice.push(All.find('input[name="price"]').val());
            arrayId.push($(this).val());
        })
        $.ajax({
                url: action,
                dataType: 'json',
                type: 'POST',
                data: {
                    arrayLimit: arrayLimit,
                    arrayPrice: arrayPrice,
                    arrayId: arrayId,
                    id: id,
                }
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message)
                    window.location.href = json.url
                } else {
                    toastr['error'](json.message);
                }
            })
    })
}
var deletePromotion = function() {
        $('.btn-clear-sale-event').click(function(event) {
            event.preventDefault();
            var promotion_id = $('.one.hidden').attr('dt-id');
            $.post({
                    url: '/esystem/delete-promotion',
                    data: {
                        promotion_id: promotion_id
                    }
                })
                .done(function(json) {
                    if (json.code == 200) {
                        toastr['success'](json.message)
                        window.location.href = json.url;
                    }
                })
        })
    }
    //Voucher
var selectTypeVoucher = function() {
    $('input[name="type_voucher"]').on('click', function() {
        if ($(this).val() == 1) {
            $('.changeTypeDuction').html('');
            $('.changeTypeDuction').append(`
                <div class="col-lg-5">
                    <div class="group-select-alls">
                        <select name="type_discount" class="control-alls-form input-alls-form">
                            <option value="1">Theo số tiền</option>
                            <option value="2">Theo phần trăm</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="combo-sale-searchs"><input type="text" name="discount" class="control-alls-form input-alls-form" placeholder="Số tiền"> <button class="btn-add-search-left text-grey">vnđ</button></div>
                </div>
            `);
        } else {
            $('.changeTypeDuction').html('');
            $('.changeTypeDuction').append(`
                <div class="col-lg-5">
                    <div class="group-select-alls">
                        <select name="type_discount" class="control-alls-form input-alls-form">
                            <option value="1">Theo phần trăm</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="combo-sale-searchs"><input type="text" name="discount" class="control-alls-form input-alls-form" placeholder="Phần trăm"> <span class="btn-add-search-left text-grey">% Jcoin</span></div>
                </div>
            `);
        }
    })
}
var selectTypeDiscount = function() {
    $(document).on('change', 'select[name="type_discount"]', function() {
        if ($(this).val() == 2) {
            $('.combo-sale-searchs').html('');
            $('.combo-sale-searchs').append('<input type="text" name="discount" class="control-alls-form input-alls-form" placeholder="Phần trăm"> <span class="btn-add-search-left text-grey">% GIẢM</span>')
        } else {
            $('.combo-sale-searchs').html('');
            $('.combo-sale-searchs').append('<input type="text" name="discount" class="control-alls-form input-alls-form" placeholder="Số tiền"> <span class="btn-add-search-left text-grey">VND</span>')
        }
    })
}
var selectTypeCoupon = function() {
    $('input[name="type_code"]').on('click', function() {
        if ($(this).val() == 2) {
            $('.group-check-create-combo.act').html('');
            $('.allOrChoose').html('')
            $('.group-check-create-combo.act').append(`
                    <div class="check-shipss"><input type="radio" class="form-check-input input-checked" value="1" id="exampleCheck112" name="is_public">
                        <span class="checkmark"></span>
                        <label for="exampleRadios112"> Công khai
                            <div class="answer-form-alls ml-2">
                                <img src="/admin/theme_2/frontend/images/answer-icon-1.png">
                            </div>
                        </label>
                    </div>
                    <div class="check-shipss"><input type="radio" class="form-check-input input-checked" value="0" id="exampleCheck112" name="is_public">
                        <span class="checkmark"></span>
                        <label for="exampleRadios112"> Không công khai
                            <div class="answer-form-alls ml-2">
                                <img src="/admin/theme_2/frontend/images/answer-icon-1.png">
                            </div>
                        </label>
                    </div>
                `);
            $('.allOrChoose').append('<a class="btn-pinks-alls addProduct">Thêm sản phẩm</a>')
        } else {
            $('.group-check-create-combo.act').html('');
            $('.allOrChoose').html('')
            $('.group-check-create-combo.act').append(`
                <div class="check-shipss">
                    <input type="radio" class="form-check-input input-checked" value="1" id="exampleCheck112" name="is_public">
                    <span class="checkmark"> </span>
                    <label for="exampleRadios112"> Công khai
                        <div class="answer-form-alls ml-2">
                            <img src="/admin/theme_2/frontend/images/answer-icon-1.png">
                        </div>
                    </label>
                </div>
                <div class="check-shipss">
                    <input type="radio" class="form-check-input input-checked" value="0" id="exampleCheck112" name="is_public">
                    <span class="checkmark"></span>
                    <label for="exampleRadios112"> Không công khai
                        <div class="answer-form-alls ml-2">
                            <img src="/admin/theme_2/frontend/images/answer-icon-1.png">
                        </div>
                    </label>
                </div>
                `);
            $('.allOrChoose').append('Tất cả sản phẩm')
        }
    })
}
var createVoucher = function() {
    $('#createAjax').on('submit', function(event) {
        event.preventDefault();
        action = $(this).attr('action')
        $.ajax({
                url: action,
                dataType: 'json',
                type: 'POST',
                contentType: false,
                cache: false,
                processData: false,
                data: new FormData(this),
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message);
                    window.location.href = json.url;
                } else {
                    toastr['error'](json.message);
                }
            })
    })
}

var addItemProductVoucher = function() {
    $(document).on('click', '.add-item-voucher', function(event) {
        event.preventDefault();
        var data = $('.addProduct .input-checked:checked');
        var item = [];
        var arrayId = [];
        $.each(data, function() {
            var price_origin = Number($(this).closest('tr').find('.origin').data('origin'));
            var price_old = Number($(this).closest('tr').find('del').data('price'));
            item.push({
                id: $(this).val(),
                name: $(this).closest('tr').find('.name').text(),
                image: $(this).closest('tr').find('img').attr('src'),
                price: price_origin > 0 ? formatVND(price_origin) : formatVND(price_old),
                qty: Number($(this).closest('tr').find('td').eq(3).text())
            })
            arrayId.push($(this).val());
        })
        var countItem = item.length;
        var tbody = [];
        if (countItem > 0) {
            $('.allOrChoose').html('');
            $('.allOrChoose').append(`
               <div class="d-flex" style="justify-content: space-between;align-items: center;">
               <p style="font-size:16px;"><span style="font-weight:bold;" class="count-choose" >${countItem}</span> Sản phẩm được chọn</p>
               <a class="btn-pinks-alls addProduct">Thêm sản phẩm</a>
               </div>
               <table width="100%" style="margin-top:15px;border:1px solid black">
               <thead>
                    <tr>
                         <th>Sản phẩm</th>
                         <th>Giá gốc</th>
                         <th>Số lượng hàng</th>
                         <th>Hành động</th>
                    </tr>
               </thead>
               </table>
               `)
            item.map(function(v) {
                tbody.push(`
                         <tr id="${v.id}">
                              <td>
                                   <div>
                                   <img style="width:150px" src="${v.image}">
                                   <span>${v.name}</span>
                                   </div>
                              </td>
                              <td>${v.price}</td>
                              <td>${v.qty}</td>
                              <td><a class="del_item"><img src="/admin/theme_2/frontend/images/trash-icon-1.png"></a></td>
                         </tr>
                         `);
            })
            sessionStorage.setItem('product-choose-voucher', JSON.stringify(arrayId));
            $('.allOrChoose table').append(`<tbody>${tbody}</tbody>`);
            $('.allOrChoose').append(`<input name="id" value='${JSON.stringify(arrayId)}' hidden>`)
            $('.addProduct').removeClass('show');
            $('.bg-black').removeClass('show');
        } else {
            toastr['error']('Bạn chưa chọn sản phẩm nào')
        }
    })
}
var editVoucher = function() {
    $('#editVoucher').on('submit', function(event) {
        event.preventDefault();
        var action = $(this).attr('action');
        $.ajax({
                url: action,
                dataType: 'json',
                type: 'POST',
                contentType: false,
                cache: false,
                processData: false,
                data: new FormData(this),
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message);
                    window.location.href = json.url;
                } else {
                    toastr['error'](json.message);
                    if (json.data) {
                        $('#date-hours-combo-start').val(json.data.start_at);
                        $('#date-hours-combo-end').val(json.data.expired_at);
                    }
                }
            })
    })
}
var deleteItemProductVoucher = function() {
    $(document).on('click', '.table_voucher .del_item', function(event) {
        event.preventDefault();
        $.ajax({
                url: '/esystem/deleteItemProductVoucher',
                dataType: 'json',
                type: 'POST',
                data: {
                    id: $('.one.hidden').attr('dt-id'),
                    product_id: $(this).closest('tr').attr('id'),
                }
            })
            .done(function(json) {
                if (json.code == 200) {
                    toastr['success'](json.message);
                } else {
                    toastr['error'](json.message);
                }
            })
        $(this).closest('tr').remove();
        if ($('.allOrChoose tbody tr').length == 0) {
            $('.allOrChoose').html('');
            $('.allOrChoose').append(`<a class="btn-pinks-alls addProduct">Thêm sản phẩm</a>`);
            $('.allOrChoose input[name="id[]"]').remove();
        } else {
            $('.allOrChoose .count-choose').text($('.allOrChoose tbody tr').length)
            itemId = [];
            var val = $('.allOrChoose tbody tr');
            $.each(val, function() {
                itemId.push($(this).attr('id'));
            })
            $('.allOrChoose input[name="id"]').attr('value', JSON.stringify(itemId));
        }
    })
}
var modalItem = function() {
    $('.search-product-modal').on('submit', function(event) {
        _this = $(this);
        event.preventDefault();
        $.ajax({
                url: _this.attr('action'),
                type: 'GET',
                dataType: json,
                data: _this.serialize()
            })
            .done(function(json) {
                $('.addProduct tbody').html(json.html);
            })
    })
}
var modalAddClose = function() {
    $('.big-modal-alls').find(".modal-content").each(function() {
        if ($(this).find(".close").length <= 0) {
            $(this).parents(".modal-dialog").prepend('<button type="button" class="close-modal___alls" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        }
    });
    $(".close-modal___alls ").click(function() {
        $(this).parents(".modal").removeClass("show");
        $(".bg-black").removeClass("show");
    });

    $(document).on('click', '.close-modal-add-flash-sale', function(event) {
        event.preventDefault();
        $('.bg-black').trigger('click');
    })
}

var sendVoucher = function() {
    $('.send-voucher').click(function(event) {
        event.preventDefault();
        $.post({
            url: '/esystem/send-notification-voucher',
            data: {
                voucher_id: $(this).attr('data-id')
            }
        }).done(function() {
            toastr['success'](json.message);
        })
    })
}

var sendVoucher = function() {
    $('.send-voucher').click(function(event) {
        event.preventDefault();
        $.post({
            url: '/esystem/send-notification-voucher',
            data: {
                voucher_id: $(this).attr('data-id')
            }
        }).done(function() {
            toastr['success'](json.message);
        })
    })
}
$(function() {
    //Modal
    modalAddClose();
    //Seting
    countdowns();
    hideModal();
    datetimePicker();
    selectAll();
    ajaxSearchNameCode();
    showModal();
    checkboxAllModal();
    getTimeSlotStart();
    calendar();
    removeShow()
    deleteProductMarketing();
    changeActMarketing();
    //FlashSale
    addItemFlashSale();
    showCreateFlashSale();
    createFlashSale();
    changeInputValue();
    editFlashSaleTimeSlot();
    //Combo
    createCombo();
    addItemProductCombo();
    deleteItemProductCombo();
    ajaxSearchNameCodeCombo();
    backCombo();
    //Promotion
    createPromotion();
    addItemProductPromotion();
    editItemProductPromotion();
    ajaxSearchProductPromotion();
    deletePromotion();
    //Voucher
    selectTypeCoupon();
    selectTypeDiscount();
    createVoucher();
    addItemProductVoucher();
    deleteItemProductVoucher();
    selectTypeVoucher();
    editVoucher();
    sendVoucher();
})