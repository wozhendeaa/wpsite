window.$ = window.jQuery = require('jquery');
import 'slick-carousel'
import feather from 'feather-icons'
import bootstrap from 'bootstrap'

(function ($) {
    'use strict';

    let carouselType = $('#carousel-type').val();

    feather.replace();

    let tikTokFeedElement = $('.tik-tok-feed-carousel');
    let slickCarouselOptions = {
        draggable: true,
        centerMode: true,
        // centerPadding: '60px',
        rows: 1,
        dots: false,
        /* Just changed this to get the bottom dots navigation */
        infinite: true,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 1,
        // arrows: true,
        prevArrow: $('.arrows .left'),
        nextArrow: $('.arrows .right'),
        variableWidth: true,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    };

    if (carouselType === 'vertical') {
        slickCarouselOptions = {
            vertical: true,
            verticalSwiping: true,
            infinite: true,
            speed: 300,
            slidesToShow: 5,
            slidesToScroll: 1,
            prevArrow: $('.arrow .up'),
            nextArrow: $('.arrow .down'),
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
            ]
        };
    }

    tikTokFeedElement.slick(slickCarouselOptions);

    $('.img-slide').each(function() {
        $(this).on('click', function() {
            let idModal = $(this).attr('data-bs-target');
            let modalElement = $(idModal);
            let videoElement = modalElement.find('video');

            modalElement.on('shown.bs.modal', function (event) {
                videoElement.get(0).play();
            });

            modalElement.on('hidden.bs.modal', function (event) {
                videoElement.get(0).pause();
                videoElement.get(0).currentTime = 0;
            });
        });
    });

    $('.next-tik-tok-modal').on('click', function() {
        openNextTikTok($(this));
    });

    $('.prev-tik-tok-modal').on('click', function() {
        openPrevTikTok($(this));
    });

    function openNextTikTok(param) {
        let currentModalId = param.attr('data-modal-id');
        let currentModalElement = $('#' + currentModalId);
        let currentVideoElement = currentModalElement.find('video');

        let nextModalId = currentModalElement.next().attr('id');

        if (nextModalId === undefined) {
            return;
        } else if (nextModalId.indexOf('tik-tok-video-') < 0) {
            return;
        }

        let nextModalElement = $('#' + nextModalId);
        let nextVideoElement = nextModalElement.find('video');

        currentModalElement.modal('hide');
        currentVideoElement.get(0).pause();
        currentVideoElement.get(0).currentTime = 0;

        nextModalElement.modal('show');
        nextVideoElement.get(0).play();
    }

    function openPrevTikTok(param) {
        let currentModalId = param.attr('data-modal-id');
        let currentModalElement = $('#' + currentModalId);
        let currentVideoElement = currentModalElement.find('video');

        let prevModalId = currentModalElement.prev().attr('id');

        if (prevModalId === undefined) {
            return;
        } else if (prevModalId.indexOf('tik-tok-video-') < 0) {
            return;
        }

        let prevModalElement = $('#' + prevModalId);
        let prevVideoElement = prevModalElement.find('video');

        currentModalElement.modal('hide');
        currentVideoElement.get(0).pause();
        currentVideoElement.get(0).currentTime = 0;

        prevModalElement.modal('show');
        prevVideoElement.get(0).play();
    }

    // tikTokFeedElement.slick()

    // $('.tik-tok-video').find('.controls').each(function() {
    //     let movieContainer = $(this);
    //     let movie = movieContainer.find('video');
    //     let controls = movieContainer.find('figcaption');
    //     let playPause = controls.find('a');
    //
    //     movie.removeAttr('controls');
    //     controls.css('display', 'block');
    //
    //     playPause.on("click", function(e) {
    //         e.preventDefault()
    //
    //         if (movie.get(0).paused) {
    //             movie.trigger('play');
    //             playPause.text('◼');
    //         } else {
    //             movie.trigger('pause');
    //             playPause.text('►');
    //         }
    //     })
    // });
})(jQuery);