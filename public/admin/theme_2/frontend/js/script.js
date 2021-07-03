var GUI = (function() {
    var win = $(window);
    var html = $('html,body');

    var tabless = function() {
        $(document).ready(function() {
            if ($('.table-compares').length == 0) return false;
            $('.table-compares').DataTable({
                paging: false,
                autoWidth: true,
                autoWidth: true,
                searching: false,
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 10001, targets: 4 },
                    { responsivePriority: 2, targets: -2 }
                ]
            });
        });

        $(document).ready(function() {
            if ($('.table-compares-prd-order').length == 0) return false;
            $('.table-compares-prd-order').DataTable({
                paging: false,
                autoWidth: true,
                autoWidth: true,
                searching: false,
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 10001, targets: 4 },
                    { responsivePriority: 2, targets: -2 }
                ]
            });
        });

    };
    var dateHour = function() {
        $(document).ready(function() {
            $('.date-hours').daterangepicker({
                timePicker: true,
                showDropdowns: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            $('.date-hours').val('');
            $('.date-hours').attr("placeholder", "Chọn thời gian bạn muốn tìm kiếm");
        });

        $(document).ready(function() {

            $('.date-hours-2').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });

        $(document).ready(function() {
            $('.date-right-prds').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'), 10)
            }, function(start, end, label) {
                var years = moment().diff(start, 'years');
                alert("You are " + years + " years old!");
            });

            $('.date-right-prds').val('');
            $('.date-right-prds').attr("placeholder", "");
        });

        $(document).ready(function() {
            $('.date-hours-combo').daterangepicker({
                timePicker: true,
                showDropdowns: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            $('.date-hours-combo').val('');
            $('.date-hours-combo').attr("placeholder", "09:00 29-09-2020");
        });

        $(document).ready(function() {
            $('.date-right-auctions').daterangepicker({
                timePicker: true,
                singleDatePicker: true,
                showDropdowns: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            $('.date-right-auctions').val('');
            $('.date-right-auctions').attr("placeholder", "09:00 29-09-2020");
        });

        $(document).ready(function() {
            $(document).ready(function() {
                $('.date-compare').daterangepicker({
                    "singleDatePicker": true,
                    "showDropdowns": true,
                    "autoApply": true,
                    "startDate": "09/27/2019",
                }, function(start, end, label) {
                    console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
                });
            });

        });

        $(document).ready(function() {
            $('.date-comparess').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });

        });
    };

    var windowOpenLink = function() {
        if ($('select[name="link-web"]')) {
            $('select[name="link-web"]').on('change', function(event) {
                event.preventDefault();
                if ($(this).val() != '') {
                    window.open($(this).val(), '_blank');
                }
            });
        }
    };

    var openOptionship = function() {
        $(".btn-option-in-shipping").click(function() {
            $(this).parents("li").removeClass("active");
            $(this).toggleClass("open-option-alls");
            $(this).parents(".items-in-option-shipping").find(".intros-in-option-shipping").slideToggle("low");
            $(".btn-option-in-shipping").not(this).parents(".items-in-option-shipping").find(".intros-in-option-shipping").slideUp("");
        });
    };

    var pbCalendar = function() {
        jQuery(document).ready(function() {

            var current_yyyymm_ = moment().format("YYYYMM");

            $("#pb-calendar").pb_calendar({
                schedule_list: function(callback_, yyyymm_) {
                    var temp_schedule_list_ = {};

                    temp_schedule_list_[current_yyyymm_ + "03"] = [
                        { 'ID': 1, style: "red" }
                    ];

                    temp_schedule_list_[current_yyyymm_ + "10"] = [
                        { 'ID': 2, style: "red" },
                        { 'ID': 3, style: "blue" },
                    ];

                    temp_schedule_list_[current_yyyymm_ + "20"] = [
                        { 'ID': 4, style: "red" },
                        { 'ID': 5, style: "blue" },
                        { 'ID': 6, style: "green" },
                    ];
                    callback_(temp_schedule_list_);
                },
                schedule_dot_item_render: function(dot_item_el_, schedule_data_) {
                    dot_item_el_.addClass(schedule_data_['style'], true);
                    return dot_item_el_;
                }
            });
        });
    };


    var selectAll = function() {
        $(document).ready(function() {
            $('.select-all-1 , .select-all-2 , .select-all-3 , .select-all-4').select2();
        });
    };


    var charts_pages = function() {
        $(document).ready(function() {
            var tooltipsLabel = ['Cty TNHH Vân Nam']
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['Nguồn 1', 'Nguồn 2', 'Nguồn 3', 'Nguồn 4', 'Nguồn 5'],
                    datasets: [{
                        label: '666',
                        backgroundColor: '#BB042D',
                        borderColor: '#BB042D',
                        data: [15, 10, 8, 5, 10, 0],
                    }]
                },

                // Configuration options go here
                options: {
                    legend: {
                        display: false
                            /*    align:'left',
                                position:'left'*/
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems, data) {
                                return tooltipsLabel;
                            },
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel) + " 000.000 " + "đ";
                            }
                        }
                    },
                }

            });
        });

        $(document).ready(function() {
            var tooltipsLabel = ['Ghế văn phòng']
            var ctx = document.getElementById('myChart2').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['Ghế v...', 'Ghế v...', 'Ghế v...', 'Ghế v...', 'Ghế v...'],
                    datasets: [{
                        label: '666',
                        backgroundColor: '#BB042D',
                        borderColor: '#BB042D',
                        data: [15, 10, 8, 5, 10, 0],
                    }]
                },

                // Configuration options go here
                options: {
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems, data) {
                                return tooltipsLabel;
                            },
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel) + " 000.000 " + "đ";
                            }
                        }
                    },
                }

            });
        });

        $(document).ready(function() {
            var tooltipsLabel = ['Mã ĐH: JAGER1234']
            var ctx = document.getElementById('myChart3').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['JGER1...', 'JGER1...', 'JGER1...', 'JGER1...', 'JGER1...'],
                    datasets: [{
                        label: '666',
                        backgroundColor: '#BB042D',
                        borderColor: '#BB042D',
                        data: [15, 10, 8, 5, 10, 0],
                    }]
                },

                // Configuration options go here
                options: {
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems, data) {
                                return tooltipsLabel;
                            },
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel) + " 000.000 " + "đ";
                            }
                        }
                    },
                }

            });
        });

        $(document).ready(function() {
            var tooltipsLabel = ['KH: Hạ Nhiên']
            var ctx = document.getElementById('myChart4').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['An Kh...', 'An Kh...', 'An Kh...', 'An Kh...', 'An Kh...'],
                    datasets: [{
                        label: '666',
                        backgroundColor: '#BB042D',
                        borderColor: '#BB042D',
                        data: [15, 10, 8, 5, 10, 0],
                    }]
                },

                // Configuration options go here
                options: {
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems, data) {
                                return tooltipsLabel;
                            },
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel) + " 000.000 " + "đ";
                            }
                        }
                    },
                }

            });
        });

        $(document).ready(function() {
            var tooltipsLabel = ['NV: Hạ Nhiên']
            var ctx = document.getElementById('myChart5').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['An Kh...', 'An Kh...', 'An Kh...', 'An Kh...', 'An Kh...'],
                    datasets: [{
                        label: '666',
                        backgroundColor: '#BB042D',
                        borderColor: '#BB042D',
                        data: [15, 10, 8, 5, 10, 0],
                    }]
                },

                // Configuration options go here
                options: {
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems, data) {
                                return tooltipsLabel;
                            },
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel) + " 000.000 " + "đ";
                            }
                        }
                    },
                }

            });
        });

        $(document).ready(function() {
            var tooltipsLabel = ['Ghế văn phòng Jager']
            var ctx = document.getElementById('myChart6').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['Bàn v...', 'Bàn v...', 'Bàn v...', 'Bàn v...', 'Bàn v...'],
                    datasets: [{
                        label: '666',
                        backgroundColor: '#BB042D',
                        borderColor: '#BB042D',
                        data: [15, 10, 8, 5, 10, 0],
                    }]
                },

                // Configuration options go here
                options: {
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems, data) {
                                return tooltipsLabel;
                            },
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel) + " 000.000 " + "đ";
                            }
                        }
                    },
                }

            });
        });

        $(document).ready(function() {
            var tooltipsLabel = ['Ghế văn phòng Jager']
            var ctx = document.getElementById('myChart7').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['Ghế v...', 'Ghế v...', 'Ghế v...', 'Ghế v...', 'Ghế v...'],
                    datasets: [{
                        label: '666',
                        backgroundColor: '#BB042D',
                        borderColor: '#BB042D',
                        data: [15, 10, 8, 5, 10, 0],
                    }]
                },

                // Configuration options go here
                options: {
                    legend: {
                        display: false
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems, data) {
                                return tooltipsLabel;
                            },
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel) + " 000.000 " + "đ";
                            }
                        }
                    },
                }

            });
        });

        $(document).ready(function() {
            var tooltipsLabel = ['Tháng 11 2019']
            var ctx = document.getElementById('compare-details-1').getContext('2d');
            ctx.height = 200;
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12', ],
                    datasets: [{
                        label: 'Doanh thu',
                        backgroundColor: '#BB042D',
                        borderColor: '#BB042D',
                        data: [5, 0.4, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1, 0.1],
                    }]
                },

                // Configuration options go here
                options: {
                    /*    
                        scales: {
                            xAxes: [{
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 90,
                                    minRotation: 90,
                                }
                            }]
                        },
                        */

                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: ""
                            },
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: "Doanh thu",
                                fontSize: 18,
                                fontColor: 'black',
                            },
                            ticks: {
                                fontSize: 16,
                                min: 0,
                                max: 5,
                                stepSize: 1,
                                suggestedMin: 0,
                                suggestedMax: 5,
                                callback: function(label, index, labels) {
                                    switch (label) {
                                        case 0:
                                            return '';
                                        case 1:
                                            return '20đ';
                                        case 2:
                                            return '40đ';
                                        case 3:
                                            return '60đ';
                                        case 4:
                                            return '80đ';
                                        case 5:
                                            return '100đ';
                                    }
                                }
                            },
                        }]
                    },
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        callbacks: {
                            title: function(tooltipItems, data) {
                                return tooltipsLabel;
                            },
                            label: function(tooltipItem, data) {
                                return Number(tooltipItem.yLabel) + ".000.000" + "đ";
                            }
                        }
                    },
                }

            });
        });


    };


    var backToTop = function() {
        if ($(".back-to-top").length > 0) {
            $(window).scroll(function() {
                var e = $(window).scrollTop();
                if (e > 300) {
                    $(".back-to-top").show();
                } else {
                    $(".back-to-top").hide();
                }
            });
            $(".back-to-top").click(function() {
                $('body,html').animate({
                    scrollTop: 0
                }, 500)
            })
        }
    };
    var initWowJs = function() {
        new WOW().init();
    };




    return {
        _: function() {
            dateHour();
            pbCalendar();
            selectAll();
            openOptionship();
            charts_pages();
            tabless();
            countdowns()
            clickShow()
                //windowOpenLink();
                //backToTop();H
                //initWowJs();
        }
    };
})();
$(document).ready(function() {
    // if (/Lighthouse/.test(navigator.userAgent)) {
    //     return;
    // }
    GUI._();
});