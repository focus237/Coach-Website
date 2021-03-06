$(function(){var screenWidth=$(window).width()
    var screenHeight=$(window).height()
    window.sr=ScrollReveal({reset:!1})
    var documentResized=function(){$('header#header').css('height',screenHeight)
        if(screenWidth<=1080){$('header').removeClass('desktop').addClass('mobile')
            $('div#container').addClass('mobile')
            $('div.blog div.posts div.posts-row div.post').css('width','100%')
            $('div.post-img img').css('height','20%')}else{$('header').removeClass('mobile').addClass('desktop')
            $('div#container').removeClass('mobile')
            $('div.blog div.posts div.posts-row div.post').css('width','27%')
            $('div.post-img img').css('height','30%')}}
    if(screenWidth<=1080){$('div.author-login h2').css('font-size','11rem')
        $('div.author-login h6').css('font-size','5rem')
        $('div.author-login form').css('width','100%')
        $('div.author-login form label, div.author-login form button').css('font-size','4rem').css('margin-top','3rem')
        $('div.author-login form input, div.author-login form button').css('font-size','4rem').css('height','10rem')}
    documentResized()
    $(window).resize(function(){screenWidth=window.innerWidth
        documentResized()})
    $('a#menu-btn').click(function(){$('nav').slideDown(300)
        $('body').css('overflow-y','hidden')})
    $('li#back-btn a').click(function(){$('nav').slideUp(300)
        $('body').css('overflow-y','auto')})
    $('header#header').parallax({imageSrc:'/assets/img/bg.jpg',speed:0.5})
    var slides1=["/assets/img/sliders/1.jpg","/assets/img/sliders/2.jpg","/assets/img/sliders/3.jpg"]
    var slides2=["/assets/img/sliders/4.jpg","/assets/img/sliders/5.jpg","/assets/img/sliders/6.jpg"]
    var slides3=["/assets/img/sliders/7.jpg","/assets/img/sliders/8.jpg","/assets/img/sliders/9.jpg"]
    $('div#company-section div#main').slidesbg({dataSlide:slides3,autoplay:!0,nav:!1,pagination:!1,delay:3000})
    $('div#adult-section div#main').slidesbg({dataSlide:slides2,autoplay:!0,nav:!1,pagination:!1,delay:3000})
    $('div#teen-section div#main').slidesbg({dataSlide:slides1,autoplay:!0,nav:!1,pagination:!1,delay:3000})
    $('.slider').slick({autoplay:!0,arrows:!1,centerMode:!0,fade:!0,focusOnSelect:!1,mobileFirst:!0,pauseOnFocus:!1,pauseOnHover:!1,adaptiveHeight:!0,})
    sr.reveal('div.right',{origin:'right',duration:1000,delay:200,distance:'50%',mobile:!0})
    sr.reveal('div#side12',{origin:'top',duration:1000,delay:200,distance:'50%',mobile:!0})
    sr.reveal('div#top h5',{origin:'bottom',duration:1000,delay:200,distance:'50%',mobile:!0})
    sr.reveal('div#top h6',{origin:'top',duration:1000,delay:200,distance:'50%',mobile:!0})
    sr.reveal('div#main div.left',{origin:'left',duration:1000,delay:200,distance:'50%',mobile:!0})
    sr.reveal('button#submit, div#side11',{origin:'bottom',duration:1000,delay:200,distance:'50%',mobile:!0})
    sr.reveal('div.post',{origin:'bottom',duration:100,delay:100,distance:'10%',mobile:!0})
    sr.reveal('div.container div.main-page',{origin:'bottom',duration:1000,delay:500,distance:'150%',mobile:!0})})