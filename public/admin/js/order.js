var ORDER = (function() {
    var showDetailOrder = function() {
        $('.showDetailOrder').click(function(event) {
            _this = $(this);
            event.preventDefault();
            var order_id = $(this).attr('data-order-id');
            _this.css('pointer-events', 'none');
            $.post({
                    url: "esystem/showOrderDetail",
                    data: { order_id: order_id }
                })
                .done(function(json) {
                    $('.modalOrder').html(json.html);
                    $('.modalOrder').addClass('show');
                    _this.css('pointer-events', 'all');
                    $('body').append(`<div class="bg-black"></div>`)
                    datetimeAlls();
                })
        })

    }

    var showDetailPreOder = function() {
        $('.showDetailPreOrder').click(function(event) {
            _this = $(this);
            event.preventDefault();
            var pre_order_id = $(this).attr('data-pre-order-id');
            _this.css('pointer-events', 'none');
            $.post({
                    url: "esystem/showPreOrderDetail",
                    data: { pre_order_id: pre_order_id }
                })
                .done(function(json) {
                    $('.modalOrder').html(json.html);
                    $('.modalOrder').addClass('show');
                    _this.css('pointer-events', 'all');
                    $('body').append(`<div class="bg-black"></div>`)
                })
        })
    }

    var sentEmailWithCustomer = function() {
        $(document).on('click', '.sent_has_product', function(event) {
            event.preventDefault();
            _this = $(this)
            $.ajax({
                type: 'POST',
                url: _this.attr('data-url'),
                data: {
                    pre_order_id: _this.attr('data-pre-order-id'),
                },
                dataType: 'json',
                beforeSend: function() {
                    _this.prop("disabled", true);
                },
            }).done(function(json) {
                if (json.code == 200) {
                    _this.removeClass('sent_has_product').addClass("order-success").css('pointer-events', 'none');
                    $.simplyToast(json.message, 'success');
                }
            });
        })
    }

    var closeDetailOrder = function() {
        $(document).on('click', '.closeOderDetail', function() {
            $(this).closest('.modalOrder').removeClass('show');
            $(this).closest('.modalOrder').html('');
            $('.bg-black').remove();
        })

        $(document).on('click', '.bg-black', function() {
            $('.modalOrder').removeClass('show');
            $('.modalOrder').html('');
            $(this).remove();
        })
    }

    var activeOrder = function() {
        $(document).on('click', '.active-order', function(event) {
            $.post({
                url: 'esystem/activeOrder',
                data: {
                    order_id: $(this).attr('dt-id'),
                }
            }).done(function(json) {
                if (json.code == 200) {
                    $('.closeOderDetail').trigger('click');
                    $.simplyToast(json.message, 'success');
                }

            })
        })
    }

    var tranferTechnician = function() {
        $(document).on('click', '.tranfer-technician', function(event) {
            $.post({
                url: 'esystem/tranferTechnician',
                data: {
                    order_id: $(this).attr('dt-id'),
                }
            }).done(function(json) {
                if (json.code == 200) {
                    $.simplyToast(json.message, 'success');
                } else {
                    $.simplyToast(json.message, 'danger');
                }
            })
        })
    }

    var showAgency = function() {
        $(document).on('click', '.show-modal-chooses-agency', function() {
            var _this = $(this);
            var order_id = _this.attr('dt-id');
            $('.agency_modal').addClass('show');
            $.post({
                url: 'esystem/showAgency',
                data: {
                    order_id: order_id,
                }
            }).done(function(json) {
                $('.agency_modal').html(json.html)
                $('.agency_modal').find('form').append(`<input type="hidden" value="${order_id}" name="order_id">`);
                select2();
            })
        });
    }

    var select2 = function() {
        $('.select2').select2();
    }

    var showTechnician = function() {
        $(document).on('click', '.show-modal-chooses-technician', function() {
            var _this = $(this);
            var order_id = _this.attr('dt-id');
            $('.technician_modal').addClass('show');
            $.post({
                url: 'esystem/showTechnician',
                data: {
                    order_id: order_id,
                }
            }).done(function(json) {
                $('.technician_modal').html(json.html)
                $('.technician_modal').find('form').append(`<input type="hidden" value="${order_id}" name="order_id">`);
            })
        });
    }

    var closeModalAgencyAndTechnician = function() {
        $(document).on('click', '.closeModal', function() {
            $('.agency_modal').removeClass('show');
            $('.agency_modal').html('');
            $('.technician_modal').removeClass('show');
            $('.technician_modal').html('');
        })
    }

    var confirmAgency = function() {
        $(document).on('submit', '#confirmAgency', function(event) {
            event.preventDefault();
            var _this = $(this);
            $.post({
                url: _this.attr('action'),
                data: _this.serialize()
            }).done(function(json) {
                if (json.code == 200) {
                    $('.closeModal').trigger('click');
                    $.simplyToast(json.message, 'success');
                } else {
                    $.simplyToast(json.message, 'danger');
                }

            })
        })
    }

    var confirmTechnician = function() {
        $(document).on('submit', '#confirmTechnician', function(event) {
            event.preventDefault();
            var _this = $(this);
            $.post({
                url: _this.attr('action'),
                data: _this.serialize()
            }).done(function(json) {
                if (json.code == 200) {
                    $.simplyToast(json.message, 'success');
                    $('.closeModal').trigger('click');
                } else {
                    $.simplyToast(json.message, 'danger');
                }

            })
        })
    }

    var searchAgency = function() {
        var timeOut;
        $(document).on('input', '#searchAgency', function() {
            var ms = 100;
            clearTimeout(timeOut);
            var text = $(this).val();
            timeOut = setTimeout(function() {
                filterAgencyTechnician(text, '.agency li');
            }, ms);
        })
    }


    var searchTechnician = function() {
        var timeOut;
        $(document).on('input', '#searchTechnician', function() {
            var ms = 100;
            clearTimeout(timeOut);
            var text = $(this).val();
            timeOut = setTimeout(function() {
                filterAgencyTechnician(text, '.technician li');
            }, ms);
        })
    }

    var filterAgencyTechnician = function(text, main) {
        var value = text.toLowerCase();
        $(main).filter(function() {
            $(this).toggle($(this).find('label').text().toLowerCase().indexOf(value) > -1)
        });
        countFilter(main);

    }

    var countFilter = function(main) {
        console.log();
        if ($(main).closest('ul').find('li:visible').length == 0) {
            $(main).closest('ul').parent().find('.no-resuft').remove();
            $(main).closest('ul').parent().append(`<p class="no-resuft">Không tìm thấy dữ liệu cần tìm !</p>`);
        }

        if ($(main).closest('ul').find('li:visible').length !== 0) {
            $(main).closest('ul').parent().find('.no-resuft').remove();
        }
    }

    var checkOption = function() {
        $(document).on('click', 'input[name="technician[]"]', function() {
            _this = $(this);
            if (!$(this).is(":checked")) {
                $(this).parent().siblings('select').find('option').first().prop('selected', true);
                $(this).parent().siblings('select').find('option').first().trigger('change');
            }
        })
        $(document).on('click', 'input[name="agency[]"]', function() {
            _this = $(this);
            if (!$(this).is(":checked")) {
                $(this).parent().siblings('select').find('option').first().prop('selected', true);
                $(this).parent().siblings('select').find('option').first().trigger('change');
            }
        })
    }

    var showRepAsk = function() {
        $(document).on('click', '.rep_ask', function() {
            $(this).siblings('form').slideToggle();
        })
    }

    var repComment = function() {
        $(document).on('submit', 'form.ask', function(event) {
            event.preventDefault();
            var _this = $(this);
            var _a = _this.siblings('a');
            var action = $(this).attr('action');
            $.post({
                url: action,
                data: $(this).serialize()
            }).done(function(json) {
                if (json.code == 200) {
                    $.simplyToast(json.message, 'success')
                    _this.remove();
                    _a.html('Đã trả lời').css('pointer-events', 'none').removeClass('rep_ask');
                } else {
                    $.simplyToast(json.message, 'danger');
                }
            })
        })
    }


    var datetimeAlls = function() {
        if ($('.single-date').length == 0) return;
        $('.single-date').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: "Áp dụng",
                cancelLabel: "Hủy",
                daysOfWeek: [
                    "CN",
                    "T2",
                    "T3",
                    "T4",
                    "T5",
                    "T6",
                    "T7"
                ],
                monthNames: [
                    "Tháng 1",
                    "Tháng 2",
                    "Tháng 3",
                    "Tháng 4",
                    "Tháng 5",
                    "Tháng 6",
                    "Tháng 7",
                    "Tháng 8",
                    "Tháng 9",
                    "Tháng 10",
                    "Tháng 11",
                    "Tháng 12"
                ],
                firstDay: 1
            },
            setValue: function() {
                $('.single-date').val();
            },
            startDate: $('.single-date').val()
        });

        $('.single-date').on('apply.daterangepicker', function(ev, picker) {
            if ($('.gallery-img').length !== 0) {
                $('.filter-acceptance').trigger('submit');
            }
        });
    };


    return {
        _: function() {
            datetimeAlls();
            sentEmailWithCustomer();
            showDetailPreOder();
            showDetailOrder();
            closeDetailOrder();
            showAgency();
            showTechnician();
            closeModalAgencyAndTechnician();
            confirmAgency();
            confirmTechnician();
            activeOrder();
            searchAgency();
            searchTechnician();
            tranferTechnician();
            checkOption();
            showRepAsk();
            repComment();
        }
    }
})();

ORDER._();

var disableButtonWhenSubmit = function(objButton) {
    var text_from = objButton.text();
    var text_to = '...';
    /*objButton.css('pointer-events', 'none');*/
    objButton.text(text_to);
    objButton.attr('action-text', text_from);
}

var enableButtonWhenSubmit = function(objButton) {
    var text_from = objButton.attr('action-text');
    /*objButton.css('pointer-events', 'all');*/
    objButton.text(text_from);
    objButton.removeAttr('action-text');
}
var FORM = (function() {
    var validate = function(_this) {
        var allow = true;
        var reqs = $(_this).find('input[type="text"].req, input[type="password"].req, select.req, textarea.req');
        reqs.each(function(index, el) {
            if ($(el).val().trim() == '') {
                $.simplyToast($(el).data('req'), 'danger');
                return allow = false;
            }
        });
        return allow;
    }

    var ajax = function(_this, type = '', button = null) {
        if (!FORM.validate(_this)) return false;

        if (button == null) {
            button = _this.find('button[type="submit"]');
        }

        var data = $(_this).serialize();

        $.ajax({
                url: $(_this).attr('action'),
                type: $(_this).attr('method'),
                dataType: 'json',
                data: data,
                beforeSend: function() {
                    disableButtonWhenSubmit(button);
                }
            })
            .done(function(json) {
                if (json.code == 200) {
                    switch (type) {
                        case 'shoot-to-technician':
                            _this.closest('.modalOrder').removeClass('show');
                            _this.closest('.modalOrder').html('');
                            $('.bg-black').remove();
                            break;
                        case 'shoot-to-carrier':
                            break;
                    }
                    $.simplyToast(json.message, 'success');
                } else {
                    $.simplyToast(json.message, 'danger');
                }
                enableButtonWhenSubmit(button);
            })
    }

    return {
        _: function() {

        },

        ajax: function(_this, type = '', button = null) {
            return ajax(_this, type, button);
        },

        validate: function(_this) {
            return validate(_this);
        }
    };
})();
$(function() {
    FORM._();
});

var ajaxClickBox = function(element) {
    _this = $(element);
    bootbox.confirm({
        title: _this.data('title'),
        message: _this.data('message'),
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i> ' + _this.data('refuse')
            },
            confirm: {
                label: '<i class="fa fa-check"></i> ' + _this.data('agree')
            }
        },
        callback: function(result) {
            if (result == true) {
                order_id = _this.data('order-id');
                type = _this.data('type');
                $.post({
                        url: _this.data('url'),
                        data: {
                            order_id: order_id,
                            type: type
                        }
                    })
                    .done(function(json) {
                        if (json.code == 200) {
                            $.simplyToast(json.message, 'success');
                            $('.closeOderDetail').trigger('click');
                        } else {
                            $.simplyToast(json.message, 'danger');
                        }
                    })
            }
        }
    });
}

var CALLBACK_AJAX = (function() {

    var callBackFilterImgs = function(json) {
        if (json.code == 200) {
            $.simplyToast(json.message, 'success');
            $('.gallery-img').html(json.html);
        } else {
            $.simplyToast(json.message, 'danger');
        }
    }

    var callBack = function(json) {
        if (json.code == 200) {
            $.simplyToast(json.message, 'success');
        } else {
            $.simplyToast(json.message, 'danger');
        }
    }


    return {
        _: function() {},
        callBackFilterImgs: function(json) {
            callBackFilterImgs(json);
        },

        callBack: function(json) {
            callBack(json);
        },

    }
})();