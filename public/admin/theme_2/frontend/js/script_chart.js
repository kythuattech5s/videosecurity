var formatVND = function(money) {
    var vnd = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(money);
    return vnd;
}

var TIMEPICKER = (function() {
    var chooseTime = function() {
        if ($('.date-comparess').length == 0) { return; }
        var item = $('.date-comparess');
        item.daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            startDate: item.attr('start-date'),
            endDate: item.attr('end-date'),
            maxDate: item.attr('max-date'),
        }, function(start, end, label) {
            var url = item.attr('url');
            var name = item.attr('name');
            url += '&' + name + '=' + start.format('DD/MM/YYYY') + '-' + end.format('DD/MM/YYYY');
            window.location.href = url;
        });
    }
    return {
        _: function() {
            chooseTime();
        },
    };
})();

var CHART = (function() {
    var revenueOverTime = function() {
        if ($('#revenue-over-time').length == 0) { return; }
        var ctx = $('#revenue-over-time');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['0', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'],
                datasets: [{
                        label: ctx.attr('label-yesterday'),
                        data: JSON.parse(ctx.attr('data-yesterday')),
                        fill: false,
                        borderColor: '#B7B7C7',
                        backgroundColor: '#B7B7C7',
                        borderWidth: 1,
                        lineTension: 0
                    },
                    {
                        label: ctx.attr('label-today'),
                        data: JSON.parse(ctx.attr('data-today')),
                        fill: false,
                        borderColor: '#fa4410',
                        backgroundColor: '#fa4410',
                        borderWidth: 1,
                        lineTension: 0
                    }
                ]
            },
            options: {
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return formatVND(tooltipItem.value);
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            callback: function(label, index, labels) {
                                return formatVND(label);
                            },
                        },
                    }]
                },
            }
        });
    }
    var detailRevenueOverTime = function() {
        if ($('#detail-revenue-over-time').length == 0) { return; }
        var ctx = $('#detail-revenue-over-time');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: JSON.parse(ctx.attr('range-month')),
                datasets: [{
                    label: 'Doanh thu',
                    backgroundColor: '#fa4410',
                    borderColor: '#fa4410',
                    data: JSON.parse(ctx.attr('data-month')),
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            fontSize: 18,
                            fontColor: 'black',
                        },
                        ticks: {
                            callback: function(label, index, labels) {
                                return formatVND(label);
                            },
                        },
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return formatVND(tooltipItem.value);
                        }
                    }
                },
            }
        });
    }
    var sellingProduct = function() {
        if ($('#selling-products').length !== 0) {
            const array_id = ['#selling-products'];
            $.each(array_id, function(index, key) {
                var main = document.querySelector(key);
                var productName = $(main).data('labels');
                var arrayProduct = $(main).data('value');
                var arrayPrice = $(main).data('price');
                var barChartData = {
                    labels: productName,
                    datasets: [{
                        label: 'Doanh thu',
                        backgroundColor: '#fa4410',
                        data: arrayProduct
                    }],
                };
                var chart = 'chart' + [index + 1];
                chart = new Chart(main, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        elements: {
                            rectangle: {
                                borderWidth: 2,
                                borderColor: "#fa4410",
                                borderSkipped: 'top'
                            }
                        },
                        animation: {
                            easing: 'easeInOutQuad',
                            duration: 520
                        },
                        responsive: true,
                        maintainAspectRatio: true,
                        title: {
                            display: false,
                        },
                        tooltips: {
                            callbacks: {

                                label: function(tooltipItem, data) {

                                    return formatVND(arrayPrice[tooltipItem.index]);
                                }
                            },
                            titleFontFamily: 'Open Sans',
                            caretSize: 5,
                            cornerRadius: 2,
                            backgroundColor: 'black',
                            titleFontSize: 12,
                            titleFontColor: 'white',
                            bodyFontColor: 'white',
                            bodyFontSize: 14,
                            bodyAlign: 'center',
                            displayColors: false,
                            xPadding: 24,
                            yAlign: 'bottom',
                            xAlign: 'center',
                        },
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    display: false, //remove grid line
                                    drawBorder: false //remove main line
                                }
                            }],
                            yAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Số lượng',
                                },
                                stacked: true,
                                ticks: {
                                    beginAtZero: true,
                                    callback: function(label, index, labels) {
                                        return label;
                                    },
                                    stepSize: 5

                                },
                                type: 'linear',
                                position: 'left',
                                id: 'y-axis-0'
                            }]
                        }
                    }
                });
            })
        }
    }
    var revenueWithOrder = function() {
        if ($('#revenue-with-order').length == 0) { return; }
        var ctx = $('#revenue-with-order');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: JSON.parse(ctx.attr('data-labels')),
                datasets: [{
                    label: 'Doanh thu',
                    backgroundColor: '#fa4410',
                    borderColor: '#fa4410',
                    data: JSON.parse(ctx.attr('data-price')),
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            fontSize: 18,
                            fontColor: 'black',
                        },
                        ticks: {
                            callback: function(label, index, labels) {
                                return formatVND(label);
                            },
                        },
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return formatVND(tooltipItem.value);
                        }
                    }
                },
            }
        });
    }
    var revenueWithUser = function() {
        if ($('#revenue-with-user').length == 0) { return; }
        var ctx = $('#revenue-with-user');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: JSON.parse(ctx.attr('data-labels')),
                datasets: [{
                    label: 'Doanh thu',
                    backgroundColor: '#fa4410',
                    borderColor: '#fa4410',
                    data: JSON.parse(ctx.attr('data-price')),
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            fontSize: 18,
                            fontColor: 'black',
                        },
                        ticks: {
                            callback: function(label, index, labels) {
                                return formatVND(label);
                            },
                        },
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return formatVND(tooltipItem.value);
                        }
                    }
                },
            }
        });
    }
    var refundWithProduct = function() {
        if ($('#refund-with-product').length == 0) { return; }
        var ctx = $('#refund-with-product');
        var arrayPrice = JSON.parse(ctx.attr('data-price'));
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: JSON.parse(ctx.attr('data-labels')),
                datasets: [{
                    label: 'Số tiền',
                    backgroundColor: '#fa4410',
                    borderColor: '#fa4410',
                    data: JSON.parse(ctx.attr('data-value')),
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Số lượng',
                        },
                        stacked: true,
                        ticks: {
                            beginAtZero: true,
                            callback: function(label, index, labels) {
                                return label;
                            },
                            stepSize: 1,
                        },
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return formatVND(arrayPrice[tooltipItem.index]);
                        }
                    }
                },
            }
        });
    }
    var refundWithOrder = function() {
        if ($('#refund-with-order').length == 0) { return; }
        var ctx = $('#refund-with-order');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: JSON.parse(ctx.attr('data-labels')),
                datasets: [{
                    label: 'Số tiền',
                    backgroundColor: '#fa4410',
                    borderColor: '#fa4410',
                    data: JSON.parse(ctx.attr('data-price')),
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            fontSize: 18,
                            fontColor: 'black',
                        },
                        ticks: {
                            callback: function(label, index, labels) {
                                return formatVND(label);
                            },
                        },
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return formatVND(tooltipItem.value);
                        }
                    }
                },
            }
        });
    }
    return {
        _: function() {
            revenueOverTime();
            detailRevenueOverTime();
            sellingProduct();
            revenueWithOrder();
            revenueWithUser();
            refundWithProduct();
            refundWithOrder();
        },
    };
})();
$(function() {
    CHART._();
    TIMEPICKER._();
});