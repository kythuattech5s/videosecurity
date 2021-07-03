var _VH_CATE = {};
$(function() {

    _VH_CATE.initFilter();
    _VH_CATE.initCheckboxAll();

    _VH_CATE.initFncJquery();
    _VH_CATE.initChangeChooseSearch();
    _VH_CATE.initDelete();
    _VH_CATE.initReactWithParent();
    _VH_CATE.setUpAjaxEditable();

});
_VH_CATE.initFilter = function() {
    $('.listcontent .filter .advancefilter .btnfilter').click(function(event) {
        var setFilter = $(this).next();
        if (setFilter.is(':visible')) {
            setFilter.fadeOut(200);
        } else {
            setFilter.fadeIn(200);
        }
    });
    $('.listcontent .filter .advancefilter .btnadd').click(function(event) {
        var p = $(this).parent();
        var selectKeyChoose = p.find('select[name=keychoose]'); /*Select chọn loại điều kiện: id / slug/name...*/
        var key = selectKeyChoose.val();
        var text = p.find('select[name=keychoose] option:selected').text();
        var type = p.find('select[name=keychoose] option:selected').attr('dt-type'); /*Loại control Text, textarea, select...*/
        if (key != undefined && key != "-1") {
            /*Xóa input hidden đã có trong form tìm kiếm*/
            var ctform = $('.filter form input[name=' + key + ']');
            if (ctform != undefined && ctform.length > 0) {
                ctform.remove();
            }
            ctform = $('.filter form input[name=search-' + key + ']');
            if (ctform != undefined && ctform.length > 0) {
                ctform.remove();
            }
            ctform = $('.filter form input[name=type-' + key + ']');
            if (ctform != undefined && ctform.length > 0) {
                ctform.remove();
            }
            /*xóa item hiển thị cho người dùng biết đã chọn*/
            var liform = $('.listfilter ul li[data-field=' + key + ']');
            if (liform != undefined && liform.length > 0) {
                liform.remove();
            }
            /*Thêm item*/
            var groupValue = p.find('div.search-' + key);
            var valueCompare = "";
            var textCompare = "";
            var valueDest = "";
            var textDest = "";
            var isDate = false;
            if (groupValue.hasClass('search-date')) {
                var controlCompare = groupValue.find('input[name=from-' + key + ']');
                if (controlCompare != undefined && controlCompare.length > 0) {
                    valueCompare = controlCompare.val();
                    textCompare = valueCompare + " - ";
                }
                var controlDest = groupValue.find('[name=to-' + key + ']');
                if (controlDest != undefined && controlDest.length > 0) {
                    valueDest = controlDest.val();
                    textDest = valueDest;
                }
                isDate = true;
            } else {
                var controlCompare = groupValue.find('select[name*=-compare]');
                if (controlCompare != undefined && controlCompare.length > 0) {
                    valueCompare = controlCompare.val();
                    textCompare = controlCompare.find('option:selected').text();
                }
                var controlDest = groupValue.find('[name=' + key + ']');
                if (controlDest != undefined && controlDest.length > 0) {
                    if (controlDest.prop('tagName').toLowerCase() == 'select') {
                        valueDest = controlDest.val();
                        textDest = controlDest.find('option:selected').text();
                    } else if (controlDest.prop('tagName').toLowerCase() == 'input') {
                        textDest = controlDest.val();
                        valueDest = textDest;
                    }
                }
            }
            if (valueDest != "") {
                var text = '<li class="aclr" data-field="' + key + '"><a href="javascript:void(0)">' + text + ' : ' + textCompare + textDest + '</a><i class="fa fa-close"></i></li>';
                $('.listfilter ul').append(text);
                $('.filter form').append("<textarea style='display: none;' name='text-" + key + "' >" + text + "</textarea>");
                if (valueCompare.trim().length > 0) {
                    if (isDate) {
                        $('.filter form').append('<input name="from-' + key + '" type="hidden" value="' + valueCompare + '"/>');
                    }
                    $('.filter form').append('<input name="search-' + key + '" type="hidden" value="' + valueCompare + '"/>');
                } else {
                    $('.filter form').append('<input name="search-' + key + '" type="hidden" value="none"/>');
                }
                $('.filter form').append('<input name="type-' + key + '" type="hidden" value="' + type + '"/>');
                $('.filter form').append('<input name="' + (isDate ? 'to-' + key : key) + '" type="hidden" value="' + valueDest + '"/>');
                $.simplyToast("Đã thêm điều kiện tìm kiếm: <b>" + text + ":" + textCompare + textDest + "</b>", 'success');
            } else {
                $('.listcontent .filter .advancefilter .btnclose').click();
            }
        } else {
            $.simplyToast("Chưa chọn điều kiện lọc", "warning");
        }
    });
    $('.listcontent .filter .advancefilter .btnclose').click(function(event) {
        var mparent = $(this).parent();
        mparent.fadeOut(200);
    });
    $('.listcontent .listfilter').on('click', 'i.fa-close', function(event) {
        var p = $(this).parent();
        var name = p.attr('data-field');
        if (name != undefined) {
            $('.listcontent .filter form input[name=' + name + ']').remove();
            $('.listcontent .filter form input[name=search-' + name + ']').remove();
            $('.listcontent .filter form input[name=type-' + name + ']').remove();
            $('.listcontent .filter form input[name=from-' + name + ']').remove();
            $('.listcontent .filter form input[name=to-' + name + ']').remove();
            $('.listcontent .filter form input[name=text-' + name + ']').remove();
        }
        p.remove();
    });
}
_VH_CATE.showTableControl = function(checked) {
    if (checked) {
        $('.tablecontrol').fadeIn(200);
    } else {
        $('.tablecontrol').fadeOut(200);
    }
}
_VH_CATE.initCheckboxAll = function() {
    $(document).on('click', 'input#squaredTwoall', function(event) {
        var checked = $(this).is(':checked')
        if (!checked) {
            $('#no-more-tables table tr.active').removeClass('active');
        } else {
            $('#no-more-tables table tr').addClass('active');
        }
        $('input.one').prop('checked', checked);
        _VH_CATE.showTableControl(checked);
        _VH_CATE.getAllCheckboxOne();
    });
    $(document).on('click', 'input.one', function(event) {
        var checked = $(this).is(':checked')
        var parent = $(this).parent().parent().parent();
        if (!checked) {
            parent.removeClass('active');
            $('input#squaredTwoall').prop('checked', checked);
            var l = $('input.one:checked').length;
            if (l == 0) {
                _VH_CATE.showTableControl(false);
            } else {
                _VH_CATE.showTableControl(true);
            }
        } else {
            _VH_CATE.showTableControl(true);
            parent.addClass('active');
            var l = $('input.one:checked').length;
            var r = $('input.one').length;
            if (l == r) {
                $('input#squaredTwoall').prop('checked', checked);
            }
        }
        _VH_CATE.getAllCheckboxOne();
    });

}
_VH_CATE.getAllCheckboxOne = function() {
    var $arr = $('input.one:checked');
    var slt = [];
    for (var i = 0; i < $arr.length; i++) {
        slt.push($($arr[i]).attr('dt-id'));
    }
    $('input#squaredTwoall').attr('dt-id', JSON.stringify(slt));
}
_VH_CATE.initFncJquery = function() {
    jQuery.fn.extend({
        flex: function() {
            return this.each(function() {
                $(this).css({ 'display': 'flex' });
            });
        }
    });
}
_VH_CATE.initChangeChooseSearch = function() {
    $('select[name=keychoose]').select2()
        .on("change", function(e) {
            var parent = $(this).parent();
            var val = $(this).val();
            parent.find('.add div.search-item').hide();
            parent.find('.add div.search-' + val).flex();
        })
}
_VH_CATE.initDelete = function() {
        $(document).on('click', '._vh_delete', function(event) {
            event.preventDefault();
            var _this = this;
            bootbox.confirm("Bạn có thực sự muốn <strong style='font-size:20px'>xóa vĩnh viễn?</strong>", function(result) {
                if (result) {
                    var id = $(_this).parent().parent().find('input.one').attr('dt-id');
                    $.ajax({
                            url: $(_this).attr('href'),
                            type: 'POST',
                            data: { id: id },
                        })
                        .done(function(data) {
                            try {
                                var json = JSON.parse(data);
                                if (json.code == 200) {
                                    $(_this).parent().parent().remove();
                                }

                            } catch (ex) {}
                        });
                }
            });
        });
        $(document).on('click', '._vh_trash', function(event) {
            event.preventDefault();
            var _this = this;
            bootbox.confirm("Bạn có thực sự muốn đưa bản ghi này <strong  style='font-size:20px'>vào thùng rác ?</strong>", function(result) {
                if (result) {
                    var id = $(_this).parent().parent().find('input.one').attr('dt-id');
                    $.ajax({
                            url: $(_this).attr('href'),
                            type: 'POST',
                            data: { id: id },
                        })
                        .done(function(data) {
                            try {
                                var json = JSON.parse(data);
                                if (json.code == 200) {
                                    $(_this).parent().parent().remove();
                                }

                            } catch (ex) {}
                        })
                        .fail(function() {})
                        .always(function() {});
                }
            });
        });
        $(document).on('click', '._vh_backtrash', function(event) {
            event.preventDefault();
            var _this = this;
            bootbox.confirm("Bạn có thực sự muốn <strong  style='font-size:20px'>phục hồi</strong> bản ghi này?", function(result) {
                if (result) {
                    var id = $(_this).parent().parent().find('input.one').attr('dt-id');
                    $.ajax({
                            url: $(_this).attr('href'),
                            type: 'POST',
                            data: { id: id },
                        })
                        .done(function(data) {
                            try {
                                var json = JSON.parse(data);
                                if (json.code == 200) {
                                    $(_this).parent().parent().remove();
                                }

                            } catch (ex) {}
                        })
                        .fail(function() {})
                        .always(function() {});
                }
            });
        });
        $(document).on('click', '._vh_delete_all', function(event) {
            event.preventDefault();
            var _this = this;
            var id = $('#squaredTwoall').attr('dt-id');
            if (id.length <= 0) {
                return;
            }
            bootbox.confirm("Bạn có thực sự muốn xóa?", function(result) {
                if (result) {
                    $.ajax({
                            url: $(_this).attr('href'),
                            type: 'POST',
                            data: { id: id },
                        })
                        .done(function(data) {
                            try {
                                var json = JSON.parse(data);
                                if (json.code == 200) {
                                    window.location.reload();
                                }

                            } catch (ex) {}
                        })
                        .fail(function() {})
                        .always(function() {});
                }
            });


        });
    }
    /*Tương tác với parent : Thêm/xóa khỏi danh mục*/
_VH_CATE.initReactWithParent = function() {
    $(document).on('click', '._vh_add_to_parent', function(event) {
        event.preventDefault();
        $('form.addtoparent').attr('action', $('form.addtoparent').attr('dt-add'));
    });
    $(document).on('click', '._vh_remove_from_parent', function(event) {
        event.preventDefault();
        $('form.addtoparent').attr('action', $('form.addtoparent').attr('dt-remove'));
    });
    $(document).on('submit', 'form.addtoparent', function(event) {
        event.preventDefault();
        var _this = this;
        var id = $('#squaredTwoall').attr('dt-id');
        if (id.length <= 0) {
            return;
        }
        var data = $(this).serialize();
        data += "&groupid=" + id;
        $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: data
            })
            .done(function(data) {
                console.log("success");
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });

    });
}
_VH_CATE.setUpAjaxEditable = function() {
    $(document).on('dblclick', 'input.editable', function(event) {
        event.preventDefault();
        $(this).prop("disabled", false);
        $(this).focus();
    });
    $(document).on('keypress', 'input.editable', function(event) {
        if (event.which == 13) {
            $(this).focusout();
        }

    });
    $(document).on('focusout', 'input.editable', function(event) {
        event.preventDefault();
        var disabled = $(this).prop("disabled");
        if (!disabled) {
            $(this).prop("disabled", true);
            var _id = $(this).parent().parent().find('input.one').attr('dt-id');
            _VH_CATE.submitEditable($(this).attr('name'), $(this).val(), _id, this);
        }
    });
    $(document).on('change', 'input.editable[type=checkbox]', function(event) {
        event.preventDefault();
        var _id = $(this).parent().parent().find('input.one').attr('dt-id');
        _VH_CATE.submitEditable($(this).attr('name'), $(this).is(':checked') ? 1 : 0, _id, this);
    });
    $(document).on('change', 'select.editable', function(event) {
        var _id = $(this).parent().parent().find('input.one').attr('dt-id');
        _VH_CATE.submitEditable($(this).attr('name'), $(this).val(), _id, this);
    });
}
_VH_CATE.submitEditable = function(control, value, _id, _this) {
    var _obj = {};
    _obj[control] = value;
    _obj['id'] = _id;
    _obj["prop"] = $(_this).attr("dt-prop");
    _obj["prop_id"] = $(_this).attr("dt-prop-id");
    $.ajax({
        url: $('#editableajax').attr('href'),
        type: 'POST',
        data: _obj,
    });

}