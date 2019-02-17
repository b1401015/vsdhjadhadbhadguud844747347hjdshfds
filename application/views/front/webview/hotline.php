<?php $this->load->view('front/block/header_webview') ?>

<div id="content" class="container content-without-banner">
    <!-- PAGE CONTENT -->


    <style>
        .contact {
            display: inherit;
        }

        h2.card-title {
            font-family: "Roboto-Medium";
            text-transform: uppercase;
            color: #333;
            font-size: 2rem;
            line-height: 3rem;
            margin-bottom: 1rem;
        }

        h3.card-title {
            font-family: "Roboto-Medium";
            text-transform: uppercase;
            color: #111;
            font-size: 1.75rem;
            line-height: 2.5rem;
            margin-bottom: 1rem;
        }

        h4.card-title {
            color: #747781;
            font-size: 1.5rem;
            line-height: 2.5rem;
            text-transform: uppercase;
        }

        a.card-title {
            font-family: "Roboto-Medium";
            text-transform: uppercase;
            font-size: 2rem;
            line-height: 2.5rem;
        }
        .list-group {
            margin-bottom: 20px;
            padding-left: 0;
        }

        .list-group-item {
            position: relative;
            display: block;
            padding: 10px 15px;
            margin-bottom: -1px;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .list-group-item:first-child {
            border-top-right-radius: 4px;
            border-top-left-radius: 4px;
        }

        .list-group-item:last-child {
            margin-bottom: 0;
            border-bottom-right-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        a.list-group-item {
            color: #555;
        }

        a.list-group-item .list-group-item-heading {
            color: #333;
        }

        a.list-group-item:hover,
        a.list-group-item:focus {
            text-decoration: none;
            color: #555;
            background-color: #f5f5f5;
        }

        .list-group-item.disabled,
        .list-group-item.disabled:hover,
        .list-group-item.disabled:focus {
            background-color: #eeeeee;
            color: #777777;
            cursor: not-allowed;
        }

        .list-group-item.disabled .list-group-item-heading,
        .list-group-item.disabled:hover .list-group-item-heading,
        .list-group-item.disabled:focus .list-group-item-heading {
            color: inherit;
        }

        .list-group-item.disabled .list-group-item-text,
        .list-group-item.disabled:hover .list-group-item-text,
        .list-group-item.disabled:focus .list-group-item-text {
            color: #777777;
        }

        .list-group-item.active,
        .list-group-item.active:hover,
        .list-group-item.active:focus {
            z-index: 2;
            color: #fff;
            background-color: #337ab7;
            border-color: #337ab7;
        }

        .list-group-item.active .list-group-item-heading,
        .list-group-item.active:hover .list-group-item-heading,
        .list-group-item.active:focus .list-group-item-heading,
        .list-group-item.active .list-group-item-heading > small,
        .list-group-item.active:hover .list-group-item-heading > small,
        .list-group-item.active:focus .list-group-item-heading > small,
        .list-group-item.active .list-group-item-heading > .small,
        .list-group-item.active:hover .list-group-item-heading > .small,
        .list-group-item.active:focus .list-group-item-heading > .small {
            color: inherit;
        }

        .list-group-item.active .list-group-item-text,
        .list-group-item.active:hover .list-group-item-text,
        .list-group-item.active:focus .list-group-item-text {
            color: #c7ddef;
        }

    </style>

    <article>
        <div class="boxtag">
            <h1 class="titlearticle">Công ty ES Market</h1>
        </div>
        <div class="card-menu row contact-card">
            <div class="list-group">
                <div class="list-group-item">
                    <a>
                            <span class="cat-icon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                        Trụ sở chính
                        <span class="address">Số 72 Lê Thánh Tôn, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh, Việt Nam.</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
                <div class="list-group-item">
                    <a>
                            <span class="cat-icon">
                                <i class="fa fa-map-marker"></i>
                            </span>
                        Địa chỉ giao dịch
                        <span class="address">
                                <b>Tại thành phố Hồ Chí Minh:</b> Tầng B1, Vincom Mega Mall Thảo Điền, 159-161 Xa Lộ Hà Nội, Phường Thảo Điền, Quận 2, Thành Phố Hồ Chí Minh.
                                <p></p>
                                <b>Tại Hà Nội:</b> Tower 2, Times City, 458 Minh Khai, Phường Vĩnh Tuy, Quận Hai Bà Trưng, Thành Phố Hà Nội.
                            </span>

                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
                <div class="list-group-item">
                    <a href="tel:024-3975-9568">
                            <span class="cat-icon">
                                <i class="fa fa-phone"></i>
                            </span>
                        SĐT HN: <span class="contact">+84 24-3975.9568</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
                <div class="list-group-item">
                    <a href="tel:028-3975-9568">
                            <span class="cat-icon">
                                <i class="fa fa-phone"></i>
                            </span>
                        SĐT HCM: <span class="contact">+84 28-3975.9568</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
                <div class="list-group-item">
                    <a href="mailto:cskh@ES Market.com">
                            <span class="cat-icon">
                                <i class="fa fa-envelope-o "></i>
                            </span>
                        Email: <span class="contact">cskh@esm.vn</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </article>


</div>

<?php $this->load->view('front/block/footer_webview') ?>