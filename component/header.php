<header class="main-header header-style-three">


    <div class="header-top-style3">
        <div class="container">
            <div class="outer-box clearfix">

                <div class="header-top-style3_left pull-left">
                    <div class="header-contact-info-two">
                        <ul>
                            <li><span class="flaticon-wall-clock"></span>Mon - Sun
                            </li>
                            <li><span class="flaticon-envelope-2"></span><a href="mailto:#">Email:
                                    admin@7stardigitizing.com</a></li>
                        </ul>
                    </div>
                </div>
                <div class="header-top-style3_right pull-right">
                    <div class="header-social-link-1">
                        <div class="social-link">
                            <ul class="clearfix">
                                <li><a href="https://www.facebook.com/7stardigitizing/" target="_blank">
                                        <span class="fab fa-facebook-f"></span></a>
                                </li>
                                <li><a href="https://www.linkedin.com/">
                                        <span class="fab fa-linkedin-in"></span></a>
                                </li>
                                <li><a href="https://twitter.com/">
                                        <span class="fab fa-twitter"></span></a>
                                </li>
                                <li><a href="https://www.instagram.com/">
                                        <span class="fab fa-instagram" style="font-weight: 800"></span></a>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Start Header-->
    <div class="header">
        <div class="container">
            <div class="outer-box clearfix">
                <div class="header-left clearfix pull-left">
                    <div class="logo">
                        <a href="<?php echo './index.php'; ?>" title="lebulid"><img
                                src="wp-content/uploads/2021/05/logo1.png" alt="logo"
                                style="width: 160px ; height: auto;" /></a>
                    </div>
                </div>
                <div class="header-right pull-right" style="margin: 9px 0px;">
                    <div class="header-contact-info2 clearfix">
                        <ul class="clearfix">
                            <li>
                                <div class="icon">
                                    <span class="flaticon-phone-call-1 clr1"></span>
                                </div>
                                <div class="text">
                                    <p>24/7 Phone Services</p>
                                    <h4><a href="tel:#">+92 000 000 0000</a></h4>
                                </div>
                            </li>
                            <li>
                                <div class="icon">
                                    <span class="flaticon-placeholder-1 clr1"></span>
                                </div>
                                <div class="text">
                                    <p>Visit Our Place</p>
                                    <h4>Karachi, Sindh, Pakistan</h4>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--End header-->

    <!--Start Header-->
    <div class="header-bottom">
        <div class="container">
            <div class="outer-box clearfix">

                <div class="header-bottom_left pull-left">
                    <div class="nav-outer style1 clearfix">
                        <!--Mobile Navigation Toggler-->
                        <div class="mobile-nav-toggler">
                            <div class="inner">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </div>
                        </div>
                        <!-- Main Menu -->
                        <nav class="main-menu style1 navbar-expand-md navbar-light">
                            <div class="collapse navbar-collapse show clearfix" id="navbarSupportedContent">
                                <ul class="navigation clearfix">
                                    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

                                    <li class="menu-item">
                                        <a href="<?php echo './index.php'; ?>" class="hvr-underline-from-left1"
                                            style="<?= ($currentPage == 'index.php') ? 'color: #e74901;' : '' ?>">Home</a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="<?php echo './about-us.php'; ?>" class="hvr-underline-from-left1"
                                            style="<?= ($currentPage == 'about-us.php') ? 'color: #e74901;' : '' ?>">About
                                            Us</a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="<?php echo './price.php'; ?>" class="hvr-underline-from-left1"
                                            style="<?= ($currentPage == 'price.php') ? 'color: #e74901;' : '' ?>">Price</a>
                                    </li>

                                    <?php if (isset($_SESSION['user_id']) && !isset($_SESSION['is_admin'])): ?>
                                        <li class="menu-item">
                                            <a href="<?php echo './dashboard/index.php'; ?>"
                                                class="hvr-underline-from-left1"
                                                style="<?= ($currentPage == 'index.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'dashboard') ? 'color: #e74901;' : '' ?>">Dashboard</a>
                                        </li>
                                    <?php else: ?>
                                        <li class="menu-item">
                                            <a href="<?php echo './login.php'; ?>" class="hvr-underline-from-left1"
                                                style="<?= ($currentPage == 'login.php') ? 'color: #e74901;' : '' ?>">Login
                                                For Order</a>
                                        </li>
                                    <?php endif; ?>

                                    <li class="menu-item">
                                        <a href="<?php echo './contact-us.php'; ?>" class="hvr-underline-from-left1"
                                            style="<?= ($currentPage == 'contact-us.php') ? 'color: #e74901;' : '' ?>">Contact
                                            Us</a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="<?php echo './terms-and-condition.php'; ?>"
                                            class="hvr-underline-from-left1"
                                            style="<?= ($currentPage == 'terms-and-condition.php') ? 'color: #e74901;' : '' ?>">Terms
                                            & Conditions</a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="<?php echo './privacy-and-policy.php'; ?>"
                                            class="hvr-underline-from-left1"
                                            style="<?= ($currentPage == 'privacy-and-policy.php') ? 'color: #e74901;' : '' ?>">Privacy
                                            Policy</a>
                                    </li>
                                </ul>


                            </div>
                        </nav>
                        <!-- Main Menu End-->
                    </div>
                </div>

                <div class="header-bottom_right pull-right">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Show Dashboard if user is logged in -->
                        <div class="header-bottom_right__btn_style2">
                            <a href="<?php echo './dashboard/index.php'; ?>">Order Now<span
                                    class="flaticon-right-arrow-1 right-arrow"></span></a>
                        </div>
                    <?php else: ?>
                        <div class="header-bottom_right__btn_style2">
                            <a href="<?php echo './login.php'; ?>">Login For Order<span
                                    class="flaticon-right-arrow-1 right-arrow"></span></a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
    <!--End header-->


    <!--Sticky Header-->
    <div class="sticky-header">
        <div class="container">
            <div class="clearfix">
                <!--Logo-->
                <div class="logo float-left" style="padding: 0px">
                    <div class="img-responsive">
                        <a href="<?php echo './index.php'; ?>" title="lebulid"><img
                                src="wp-content/uploads/2021/05/logo1.png" alt="logo"
                                style="width: 160px ; height: auto;" /></a>
                    </div>
                </div>
                <!--Right Col-->
                <div class="right-col float-right">
                    <!-- Main Menu -->
                    <nav class="main-menu clearfix">
                        <!--Keep This Empty / Menu will come through Javascript-->
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!--End Sticky Header-->

    <!-- Mobile Menu  -->
    <div class="mobile-menu">
        <div class="menu-backdrop"></div>
        <div class="close-btn"><span class="icon fa fa-times-circle"></span></div>

        <nav class="menu-box">
            <div class="nav-logo">
                <a href="<?php echo './index.php'; ?>" title="lebulid"><img src="wp-content/uploads/2021/05/logo1.png"
                        alt="logo" style="width: 160px ; height: auto;" /></a>
            </div>
            <div class="menu-outer">
                <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
            </div>
            <div class="social-links">
                <ul class="clearfix">
                    <li><a href=" https://www.facebook.com/7stardigitizing/" target="_blank"><span
                                class="fab fa-facebook-f"></span></a></li>
                    <li><a href="https://www.linkedin.com/"><span class="fab fa-linkedin-in"></span></a></li>
                    <li><a href="https://www.pinterest.com/"><span class="fab fa-pinterest-p"></span></a></li>
                    <li><a href="https://twitter.com/"><span class="fab fa-twitter"></span></a></li>
                </ul>
            </div>
        </nav>
    </div>
    <!-- End Mobile Menu -->

</header>