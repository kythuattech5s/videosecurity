var GUI = (function() {
    var win = $(window);
    var html = $('html,body');

    var menuMobile = function() {
        $(".button-phone").click(function() {
            $(".menu").toggleClass("opens-now");
            $(".remove-all").addClass("out-menu");
            $(".back-menu").addClass("show-back__menu");
        });

        $(".back-menu").click(function() {
            $(".menu").removeClass("opens-now");
            $(".remove-all").removeClass("out-menu");
            $(this).removeClass("show-back__menu");
        });

        $(".remove-all").click(function() {
            $(".menu").toggleClass("opens-now");
            $(this).removeClass("out-menu");
            $(".back-menu").removeClass("show-back__menu");
        });

        $(".btn-sub__mb").click(function() {
            $(this).parent(".sub-mobiles").children(".sub-mb__1").slideToggle();
            $(".btn-sub__mb").not(this).parent(".sub-mobiles").children(".sub-mb__1").slideUp();
            $(".btn-sub__mb").not(this).parents(".sub-mobiles").find(".sub-mb__2").slideUp();
        });

        $(".btn-link__footer").click(function() {
            $(this).parents(".web-link__footer").find(".list-wed__link").fadeToggle();
        });

        $(".btn-sub__mb2").click(function() {
            $(this).parent(".sub-mobiles").children(".sub-mb__2").slideToggle();
            $(".btn-sub__mb2").not(this).parent(".sub-mobiles").children(".sub-mb__2").slideUp();
        });

        $('.search-top__btn img').click(function(e) {
            $('.search-group__header').stop(true).toggleClass("active-tops");
        });
    };


    var filterMobileBox = function() {
        $(".btn-filter__mobiles").click(function() {
            $(".filter-all__mobile").addClass("show-fikter__alls");
            $(".remove-all").addClass("out-menu");
        });

        $(".remove-all").click(function() {
            $(".filter-all__mobile").removeClass("show-fikter__alls");
            $(this).removeClass("out-menu");
        });
    };

    var seeSkillTag = function() {
        $(".see-more-skill-tag").click(function() {
            $(this).parents(".tab-pane").find(".text-skill__prd").addClass("see-more-skill-opens");
            $(".see-more-skill-tag").not(this).parents(".tab-pane").find(".text-skill__prd").removeClass("see-more-skill-opens");
            $(this).css("display", "none");
        });
    };

    var slideMainMobile = function() {
        if ($('.sl-mains__mobile').length > 0) {
            var slider = tns({
                container: ".sl-mains__mobile",
                items: 1,
                speed: 800,
                nav: true,
                loop: true,
                slideBy: "page",
                arrowKeys: false,
                controls: false,
                swipeAngle: true,
                mouseDrag: true,
                autoplay: false,
                autoplayTimeout: 5000,
                autoHeight: false,
                autoplayHoverPause: false,
                autoplayButtonOutput: false,
                responsive: {
                    "992": {
                        "speed": 800
                    },
                    "1": {
                        "speed": 400
                    }
                }
            });

        }
    };


    var showMobileDetails = function() {
        if ($('.big-media-shows-mobile').length > 0) {
            var sliderFirst = tns({
                container: '.big-media-shows-mobile',
                navContainer: '.small-media-shows-mobile',
                items: 1,
                speed: 800,
                nav: true,
                gutter: 0,
                loop: false,
                slideBy: "1",
                controls: true,
                controlsText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                swipeAngle: true,
                swipeAngle: 30,
                mouseDrag: true,
                autoplay: false,
                autoplayTimeout: 5000,
                autoHeight: false,
                autoplayHoverPause: false,
                autoplayButtonOutput: false,
                responsive: {
                    640: {
                        axis: "horizontal",
                    },
                    700: {
                        axis: "horizontal",
                    },
                    991: {
                        axis: "horizontal",
                    }
                }
            });

        }

        if ($('.small-media-shows-mobile').length > 0) {
            var sliderLast = tns({
                items: 4,
                container: '.small-media-shows-mobile',
                navContainer: '.big-media-shows-mobile',
                mouseDrag: true,
                gutter: 10,
                slideBy: "2",
                navAsThumbnails: true,
                loop: false,
                swipeAngle: 30,
                controls: false,
                controlsText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                nav: true,
            });
        }

    };

    var numberUpDownMobile = function() {

        $(".up-btns__mobile").click(function() {
            var value = parseInt(document.getElementById('number').value, 10);
            value = isNaN(value) ? 0 : value;
            value++;
            document.getElementById('number').value = value;
        });

        $(".down-btns__mobile").click(function() {
            var value = parseInt(document.getElementById('number').value, 10);
            value = isNaN(value) ? 0 : value;
            value < 1 ? value = 1 : '';
            value--;
            document.getElementById('number').value = value;
        });
    };

    var slideSeviceMobile = function() {
        if ($('.sl-sevice__mobile').length > 0) {
            var slider = tns({
                container: ".sl-sevice__mobile",
                items: 2,
                speed: 800,
                nav: true,
                loop: true,
                gutter: 15,
                slideBy: "page",
                arrowKeys: false,
                controls: false,
                controlsText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                swipeAngle: true,
                swipeAngle: 30,
                autoWidth: true,
                mouseDrag: true,
                autoplay: false,
                autoplayTimeout: 5000,
                autoHeight: false,
                autoplayHoverPause: false,
                autoplayButtonOutput: false,
                responsive: {
                    "1200": {
                        "speed": 800,
                        items: 2,
                    },
                    "992": {
                        "speed": 800,
                        items: 2,
                    },
                    "768": {
                        "speed": 800,
                        items: 2,
                    },
                    "576": {
                        "speed": 800,
                        items: 2,
                    },
                    "1": {
                        "speed": 400,
                        items: 2,
                    }
                }
            });
        }
    };

    var slideFlashMobile = function() {
        if ($('.sl-flash__mobile').length > 0) {
            var slider = tns({
                container: ".sl-flash__mobile",
                items: 25,
                speed: 800,
                autoWidth: true,
                nav: false,
                loop: true,
                gutter: 5,
                slideBy: "page",
                arrowKeys: false,
                controls: false,
                controlsText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                swipeAngle: true,
                swipeAngle: 30,
                mouseDrag: true,
                autoplay: false,
                autoplayTimeout: 5000,
                autoHeight: false,
                autoplayHoverPause: false,
                autoplayButtonOutput: false,
                responsive: {
                    "1200": {
                        "speed": 800,
                        items: 2,
                    },
                    "992": {
                        "speed": 800,
                        items: 2,
                    },
                    "768": {
                        "speed": 800,
                        items: 2,
                    },
                    "576": {
                        "speed": 800,
                        items: 2,
                    },
                    "1": {
                        "speed": 400,
                        items: 2,
                    }
                }
            });
        }
    };

    var slideBannerBottom = function() {
        if ($('.sl-banner__bottoms').length > 0) {
            var slider = tns({
                container: ".sl-banner__bottoms",
                items: 1,
                speed: 800,
                nav: false,
                loop: true,
                gutter: 0,
                slideBy: "page",
                arrowKeys: false,
                controls: false,
                controlsText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                swipeAngle: true,
                swipeAngle: 30,
                mouseDrag: true,
                autoplay: false,
                autoplayTimeout: 5000,
                autoHeight: false,
                autoplayHoverPause: false,
                autoplayButtonOutput: false,
                responsive: {
                    "1200": {
                        "speed": 800,
                        items: 1,
                    },
                    "992": {
                        "speed": 800,
                        items: 1,
                    },
                    "768": {
                        "speed": 800,
                        items: 1,
                    },
                    "576": {
                        "speed": 800,
                        items: 1,
                    },
                    "1": {
                        "speed": 400,
                        items: 1,
                    }
                }
            });
        }
    };


    function tinySliders(id, timePlay) {
        var container = $('.sl-page-mobile-' + id);
        if (container.length > 0) {
            var slider = tns({
                container: ".sl-page-mobile-" + id,
                speed: 800,
                nav: false,
                loop: true,
                gutter: 5,
                slideBy: "page",
                autoWidth: true,
                arrowKeys: false,
                controls: false,
                controlsText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
                swipeAngle: true,
                swipeAngle: 30,
                mouseDrag: true,
                autoplay: false,
                autoplayTimeout: timePlay,
                autoHeight: false,
                autoplayHoverPause: false,
                autoplayButtonOutput: false,
                responsive: {
                    "1200": {
                        "speed": 800,
                        items: 5,
                    },
                    "992": {
                        "speed": 800,
                        items: 5,
                    },
                    "768": {
                        "speed": 800,
                        items: 3,
                    },
                    "576": {
                        "speed": 800,
                        items: 3,
                    },
                    "1": {
                        "speed": 400,
                        items: 2,
                    }
                }
            });
        }
    };

    var slideBottomPrdMobile = function() {
        if ($('.prd-mobile__bottom').length > 0) {
            var timePlay = 5000;
            $('.prd-mobile__bottom').each(function(index, el) {
                var id = $(this).find('.products-bottom__mobile').data('id');
                if (typeof id !== undefined) {
                    timePlay = timePlay + 1000;
                    tinySliders(id, timePlay);
                }
            });
        }
    }

    var equalHeightsMobile = function() {
        $('.sl-sevice__mobile').each(function() {
            var highestBox = 0;

            $(this).find('.items-sevice__mobile .name-sevice__mobile').each(function() {
                if ($(this).height() > highestBox) {
                    highestBox = $(this).height();
                }
            })

            $(this).find('.items-sevice__mobile .name-sevice__mobile').height(highestBox);
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

    var openTariff = function() {
        $(".top-type__sevice").click(function() {
            $(this).parents(".item-type__sevice").find(".content-type__sevice").slideToggle();
            $(".top-type__sevice").not(this).parents(".item-type__sevice").find(".content-type__sevice").slideUp();
        });

        $(".title-btn__ports").click(function() {
            $(this).parents(".item-port__code").find(".contents-port__core").slideToggle();
            $(".title-btn__ports").not(this).parents(".item-port__code").find(".contents-port__core").slideUp();
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
            //menuMobile();
            slideMainMobile();
            slideSeviceMobile();
            equalHeightsMobile();
            slideFlashMobile();
            slideBannerBottom();
            slideBottomPrdMobile();
            filterMobileBox();
            showMobileDetails();
            numberUpDownMobile();
            seeSkillTag();
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