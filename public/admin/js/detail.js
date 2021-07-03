$(function() {

    $('.datepicker').datetimepicker({
        format: 'd/m/Y H:i:s',
        formatTime: 'H:i:s',
        formatDate: 'd/m/Y',
        onClose: function(time, input, event) {
            var val = $(input).val();
            if (val != undefined && val != "") {
                var p = val.split(' ');
                var d = p[0].split('/');
                var t = p[1].split(':');
                var ret = d[2] + '-' + d[1] + '-' + d[0] + ' ' + p[1];
                if ($(input).attr("dt-type") == 'INTDATETIME') {
                    var date = new Date(d[2], parseInt(d[1]) - 1, d[0], t[0], t[1], t[2]);
                    $(input).next().val(date.getTime() / 1000);
                } else {
                    $(input).next().val(ret);
                }

            }
        }
    });
    $('.boxtitle .btnshow').click(function(event) {
        var boxedit = $(this).closest('.boxedit');
        var boxhide = boxedit.find('.boxhide');
        if (boxhide.is(":visible")) {
            boxhide.slideUp();
        } else {
            boxhide.slideDown();
        }
    });

    $('._vh_update').click(function(event) {
        event.preventDefault();
        if (!validateForm('#frmUpdate')) {
            return;
        }
        try {
            tinyMCE.triggerSave();
        } catch (e) {}
        $('#frmUpdate').attr('action', $('#frmUpdate').attr('dt-ajax'));
        var frmClone = $('#frmUpdate').clone();
        frmClone.ajaxForm();
        frmClone.submit();
        frmClone.remove();
    });
    $('._vh_save').click(function(event) {
        event.preventDefault();
        if (!validateForm('#frmUpdate')) {
            return;
        }
        try {
            tinyMCE.triggerSave();
        } catch (e) {}
        $('#frmUpdate').attr('action', $('#frmUpdate').attr('dt-normal'));
        $('#frmUpdate').submit();
    });
    $(function() {
        $(".iframe-btn").fancybox({
            'width': '75%',
            'height': '75%',
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe'
        });
    });

    $(function() {
        $(".fancybox").fancybox();
    });

    function validateForm(frm) {
        var arr = $(frm).find('input[required],select[required],textarea[required]');
        for (var i = 0; i < arr.length; i++) {
            var item = $(arr[i]);
            var val = item.val();
            if (val == undefined || val == "") {
                item.focus();
                $.simplyToast("Vui lòng nhập " + $(item).attr('placeholder'), 'danger');
                return false;
            }
        }
        return true;
    }
    if ($('select[name="province_id"]').length > 0 && $('select[name="district_id"]').length > 0) {
        $('select[name="district_id"]').attr('disabled', true);
        $(document).on('change', 'select[name="province_id"]', function(e) {
            var _this = $(this);
            _this.attr('disabled', true);
            $('select[name="district_id"]').attr('disabled', true);
            var id = $(this).val();
            $.ajax({
                    url: 'esystem/get-district-by-province',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        province_id: id
                    },
                })
                .done(function(html) {
                    _this.attr('disabled', false);
                    $('select[name="district_id"]').attr('disabled', false);
                    $('select[name="district_id"]').html(html)
                })
        });
    }

});