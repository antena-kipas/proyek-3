<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    
    <!--====== Title ======-->
    <title>SDN LEBAKSIUH</title>
    
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/png">
        
        
    <!--====== Animate CSS ======-->
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
        
    <!--====== Nice Select CSS ======-->
    <link rel="stylesheet" href="{{ asset('css/nice-select.css') }}">
        
        
    <!--====== Line Icons CSS ======-->
    <link rel="stylesheet" href="{{ asset('css/LineIcons.2.0.css') }}">
        
    <!--====== Bootstrap CSS ======-->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.4.5.2.min.css') }}">
    
    <!--====== Default CSS ======-->
    <link rel="stylesheet" href="{{ asset('css/default.css') }}">
    
    <!--====== Style CSS ======-->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
</head>

<body>
    <!--[if IE]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->

    <!--====== PRELOADER PART START ======-->

    <div class="preloader">
        <div class="loader">
            <div class="ytp-spinner">
                <div class="ytp-spinner-container">
                    <div class="ytp-spinner-rotator">
                        <div class="ytp-spinner-left">
                            <div class="ytp-spinner-circle"></div>
                        </div>
                        <div class="ytp-spinner-right">
                            <div class="ytp-spinner-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--====== PRELOADER PART ENDS ======-->

    <!--====== HEADER PART START ======-->

    <section class="header_area">
        <div class="header_navbar">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand" href="index.html">
                                <img src="{{ asset('images/logo.svg') }}" alt="Logo">
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                <ul id="nav" class="navbar-nav ml-auto">
                                    <li class="nav-item active">
                                        <a class="page-scroll" href="#home">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="page-scroll" href="#about">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="page-scroll" href="#services">Fasilitas</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="page-scroll" href="#gallery">Gallery</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#">Login</a>
                                    </li>
                                </ul>
                            </div> <!-- navbar collapse -->
                        </nav> <!-- navbar -->
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
        </div> <!-- header navbar -->

        <div id="home" class="header_slider">
            <div class="single_slider bg_cover d-flex align-items-center" style="background-image: url({{ asset('images/hero-area.jpg') }})">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-md-8">
                            <div class="slider_content">
                                <h2 class="slider_title wow fadeInLeftBig" data-wow-duration="1.3s" data-wow-delay="0.2s">Lorem ipsum dolor sit<span> Lorem ipsum</span></h2>
                                <p class="wow fadeInLeftBig" data-wow-duration="1.3s" data-wow-delay="0.5s">Lorem ipsum dolor amet, consetetur sadipscing elitr sed diam nonumy eirmod tempor invidun dolore.</p>
                            </div> <!-- slider content -->
                        </div>
                    </div> <!-- row -->
                </div> <!-- container -->
            </div> <!-- single slider -->
        </div> <!-- header slider -->
    </section>

    <!--====== HEADER PART ENDS ======-->
    
    <!--====== ABOUT PART START ======-->

    <section id="about" class="about_area pt-130 pb-130">
        <div class="about_wrapper">
            <div class="about_image bg_cover" style="background-image: url({{ asset('images/about_image.jpg') }})"></div> <!-- about image -->
            <div class="container">
                <div class="row justify-content-end">
                    <div class="col-lg-6">
                        <div class="about_content">
                            <div class="section_title">
                                <h3 class="title">About Us <br> TraveLand in <span>Numbers</span></h3>
                                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed dianum eirmod tempor invidunt ut labore et dolore magna aliqyam era sed diam voluptua. At vero eos et accusam etjusto duo dolres etea reb. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.</p>
                            </div> <!-- section title -->
                            <a href="javascript:void(0)" class="main-btn">Read More</a>
                        </div> <!-- about content -->
                        <div class="about_counter d-flex flex-wrap">
                            <div class="single_counter counter_1 d-flex justify-content-center align-items-center wow fadeInUpBig" data-wow-duration="1.3s" data-wow-delay="0.2s">
                                <div class="counter_wrapper">
                                    <span class="counter">0</span>
                                    <p>Lorem</p>
                                </div> <!-- single wrapper -->
                            </div> <!-- single counter -->
                            
                            <div class="single_counter counter_2 d-flex justify-content-center align-items-center wow fadeInUpBig" data-wow-duration="1.3s" data-wow-delay="0.5s">
                                <div class="counter_wrapper">
                                    <span class="counter">0</span>
                                    <p>Lorem</p>
                                </div> <!-- single wrapper -->
                            </div> <!-- single counter -->
                            
                            <div class="single_counter counter_2 d-flex justify-content-center align-items-center wow fadeInUpBig" data-wow-duration="1.3s" data-wow-delay="0.8s">
                                <div class="counter_wrapper">
                                    <span class="counter">0</span>
                                    <p>Lorem</p>
                                </div> <!-- single wrapper -->
                            </div> <!-- single counter -->
                            
                            <div class="single_counter counter_1 d-flex justify-content-center align-items-center wow fadeInUpBig" data-wow-duration="1.3s" data-wow-delay="1.1s">
                                <div class="counter_wrapper">
                                    <span class="counter">0</span>
                                    <p>Lorem</p>
                                </div> <!-- single wrapper -->
                            </div> <!-- single counter -->
                        </div> <!-- about_counter -->
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
        </div> <!-- about wrapper -->
    </section>

    <!--====== ABOUT PART ENDS ======-->
    
    
    <!--====== SERVICES PART START ======-->

    <section id="services" class="services_area pt-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="section_title text-center pb-25">
                        <h3 class="title">Fasilitas We Provide</h3>
                        <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed dianum eirmod tempor invidunt ut labore et dolore magna </p>
                    </div> <!-- section title -->
                </div>
            </div> <!-- row -->
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="single_service mt-30 text-center wow fadeInUpBig" data-wow-duration="1.3s" data-wow-delay="0.2s">
                        <div class="services_icon">
                            <i class="lni lni-apartment"></i>
                        </div>
                        <div class="services_content">
                            <h4 class="title">lorem</h4>
                            <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed dianum eirmod.</p>
                        </div>
                    </div> <!-- single service -->
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="single_service mt-30 text-center wow fadeInUpBig" data-wow-duration="1.3s" data-wow-delay="0.4s">
                        <div class="services_icon">
                            <i class="lni lni-plane"></i>
                        </div>
                        <div class="services_content">
                            <h4 class="title">lorem</h4>
                            <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed dianum eirmod.</p>
                        </div>
                    </div> <!-- single service -->
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="single_service mt-30 text-center wow fadeInUpBig" data-wow-duration="1.3s" data-wow-delay="0.6s">
                        <div class="services_icon">
                            <i class="lni lni-ship"></i>
                        </div>
                        <div class="services_content">
                            <h4 class="title">lorem</h4>
                            <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed dianum eirmod.</p>
                        </div>
                    </div> <!-- single service -->
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="single_service mt-30 text-center wow fadeInUpBig" data-wow-duration="1.3s" data-wow-delay="0.8s">
                        <div class="services_icon">
                            <i class="lni lni-car-alt"></i>
                        </div>
                        <div class="services_content">
                            <h4 class="title">lorem</h4>
                            <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed dianum eirmod.</p>
                        </div>
                    </div> <!-- single service -->
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </section>

    <!--====== SERVICES PART ENDS ======-->
    

 
    
    <!--====== TESTIMONIAL PART START ======-->

    <section id="testimonial" class="testimonial_area pt-120">
    </section>

    <!--====== TESTIMONIAL PART ENDS ======-->
    
    <!--====== BLOG PART START ======-->

    <section id="gallery" class="blog_area pt-120 pb-130">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="section_title text-center pb-25">
                        <h3 class="title">Recent Blog Post</h3>
                        <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed dianum eirmod tempor invidunt ut labore et dolore magna </p>
                    </div> <!-- section title -->
                </div>
            </div> <!-- row -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="single_blog mt-30">
                        <div class="blog_image">
                            <img src="{{ asset('images/blog-1.jpg') }}" alt="blog">
                        </div>
                        <div class="blog_content">
                            <div class="blog_meta">
                                <ul>
                                    <li>Posted by: <a href="javascript:void(0)">Michle Smith</a></li>
                                    <li>14 June 2023</li>
                                </ul>
                            </div>
                            <h4 class="blog_title"><a href="javascript:void(0)">Tonumy eirmod tempor invidunt ut labore et dolore magn aaliq yam erat, sed diam voluptua.</a></h4>
                            <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magn aaliq yam erat, sed diam voluptua. At vero eos et accusam et justo dolores  ea rebum Stet clita kasd gubergren sea.</p>
                        </div>
                    </div> <!-- single blog -->
                </div>
                <div class="col-lg-6">
                    <div class="single_blog d-sm-flex mt-30">
                        <div class="blog_image">
                            <img src="{{ asset('images/blog-2.jpg') }}" alt="blog">
                        </div>
                        <div class="blog_content blog_content_2 media-body">
                            <h4 class="blog_title"><a href="javascript:void(0)">Tonumy eirmod temporinvi unt ut labore et dolore</a></h4>
                            <p>Lorem ipsum dolor sit ametcon setetur sadipscing elitr, sed diam nonumy eirmod tempor</p>
                            <a href="javascript:void(0)" class="more">Read More</a>
                        </div>
                    </div> <!-- single blog -->
                    
                    <div class="single_blog d-sm-flex mt-30">
                        <div class="blog_image">
                            <img src="{{ asset('images/blog-3.jpg') }}" alt="blog">
                        </div>
                        <div class="blog_content blog_content_2 media-body">
                            <h4 class="blog_title"><a href="javascript:void(0)">Tonumy eirmod temporinvi unt ut labore et dolore</a></h4>
                            <p>Lorem ipsum dolor sit ametcon setetur sadipscing elitr, sed diam nonumy eirmod tempor</p>
                            <a href="javascript:void(0)" class="more">Read More</a>
                        </div>
                    </div> <!-- single blog -->
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </section>

    <!--====== BLOG PART ENDS ======-->
    
    
   
    
    <!--====== FOOTER PART START ======-->

    <section id="footer" class="footer_area">
        <div class="footer_widget pt-80 pb-130">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 order-md-1 order-lg-1">
                        <div class="footer_about mt-50">
                            <a href="#"><img src="{{ asset('images/logo.svg') }}" alt="logo"></a>
                            <p>Lorem ipsum dolor sifsddt amet, conse tetur sadipscing elitr, sed diam onumy eirmod tempor invidunt ut labore et dolore magna aliquya.</p>
                            <ul class="social">
                                <li><a class="social_1" href="javascript:void(0)"><i class="lni lni-facebook-original"></i></a></li>
                                <li><a class="social_2" href="javascript:void(0)"><i class="lni lni-twitter-original"></i></a></li>
                                <li><a class="social_3" href="javascript:void(0)"><i class="lni lni-instagram-original"></i></a></li>
                                <li><a class="social_4" href="javascript:void(0)"><i class="lni lni-linkedin-original"></i></a></li>
                            </ul>
                        </div> <!-- footer about -->
                    </div>
                    <div class="col-lg-4 col-md-12 order-md-3 order-lg-2">
                        <div class="footer_link_wrapper d-flex flex-wrap">
                            <div class="footer_link mt-45">
                                <h4 class="footer_title">Quick Link</h4>
                                <ul class="link">
                                    <li><a href="javascript:void(0)">Home</a></li>
                                    <li><a href="javascript:void(0)">About</a></li>
                                    <li><a href="javascript:void(0)">Trip Pacakge</a></li>
                                    <li><a href="javascript:void(0)">Services</a></li>
                                    <li><a href="javascript:void(0)">Gallery</a></li>
                                </ul>
                            </div> <!-- footer link -->
                            <div class="footer_link mt-45">
                                <h4 class="footer_title">Support</h4>
                                <ul class="link">
                                    <li><a href="javascript:void(0)">Customer Support</a></li>
                                    <li><a href="javascript:void(0)">Privacy & Policy</a></li>
                                    <li><a href="javascript:void(0)">Terms & Condition</a></li>
                                    <li><a href="javascript:void(0)">Forum</a></li>
                                    <li><a href="javascript:void(0)">Tour Guide</a></li>
                                </ul>
                            </div> <!-- footer link -->
                        </div> <!-- footer link wrapper -->
                    </div>
                    <div class="col-lg-4 col-md-6 order-md-2 order-lg-3">
                        <div class="footer_subscribe mt-45">
                            <h4 class="footer_title">Subscribe Newsletter</h4>
                            <p>Lorem ipsum dolor sit amet, consetetur sadip scing elitr, sed diam.</p>
                            <div class="subscribe_form">
                                <form action="#">
                                    <input type="text" placeholder="Enter email">
                                    <button><i class="lni lni-arrow-right"></i></button>
                                </form>
                            </div>
                        </div> <!-- footer subscribe -->
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
            <p class="text-center mt-90">Designed and Developed by <a href="#">UIdeck</a></p>
        </div> <!-- footer widget -->
    </section>

    <!--====== FOOTER PART ENDS ======-->
    
    <!--====== BACK TOP TOP PART START ======-->

    <a href="#" class="back-to-top"><i class="lni lni-chevron-up"></i></a>

    <!--====== BACK TOP TOP PART ENDS ======-->
    
    <!--====== PART START ======-->

<!--
    <section class="">
        <div class="container">
            <div class="row">
                <div class="col-lg-">
                    
                </div>
            </div>
        </div>
    </section>
-->

    <!--====== PART ENDS ======-->





    <!--====== Jquery js ======-->
    <script src="{{ asset('js/vendor/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('js/vendor/modernizr-3.7.1.min.js') }}"></script>
    
    <!--====== Bootstrap js ======-->
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.4.5.2.min.js') }}"></script>

    
    <!--====== Scrolling Nav js ======-->
    <script src="{{ asset('js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/scrolling-nav.js') }}"></script>
    
    <!--====== Counter Up js ======-->
    <script src="{{ asset('js/waypoints.min.js') }}"></script>
    <script src="{{ asset('js/jquery.counterup.min.js') }}"></script>
    
    <!--====== Nice Select js ======-->
    <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
    
    <!--====== WOW js ======-->
    <script src="{{ asset('js/wow.min.js') }}"></script>
    
    <!--====== Main js ======-->
    <script src="{{ asset('js/main.js') }}"></script>
    
</body>

</html>