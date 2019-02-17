<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-list-ol"></i>
                    <span>Danh mục</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/category/listing') ?>"><i class="fa fa-circle-o"></i> DS Danh mục</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-cube"></i> <span>Sản phẩm</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/record/listing') ?>"><i class="fa fa-circle-o"></i> DS Sản phẩm</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users"></i> <span>User</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/user/listing') ?>"><i class="fa fa-circle-o"></i> DS User</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-newspaper-o"></i> <span>Tin tức khuyến mãi</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/news/listing') ?>"><i class="fa fa-circle-o"></i> DS Tin tức khuyến mãi</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-picture-o"></i> <span>Banner quảng cáo</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/banner/listing') ?>"><i class="fa fa-circle-o"></i> DS Banner</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-shopping-cart"></i> <span>Đơn hàng</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/order/listing') ?>"><i class="fa fa-circle-o"></i> DS đơn hàng</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-tags"></i> <span>Voucher</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/voucher/listing') ?>"><i class="fa fa-circle-o"></i> DS voucher</a></li>
                </ul>
            </li>
            <li class="treeview hide">
                <a href="#">
                    <i class="fa fa-language"></i> <span>Dịch thuật</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/translation/listing') ?>"><i class="fa fa-circle-o"></i> DS dịch thuật</a></li>
                </ul>
            </li>
            <li class="treeview hide">
                <a href="#">
                    <i class="fa fa-bell-o"></i> <span>Gửi push</span>
                    <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?= base_url('_admin/notification/listing') ?>"><i class="fa fa-circle-o"></i> DS push notification</a></li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<script>
    /** add active class and stay opened when selected */
    var url = window.location;

    // for sidebar menu entirely but not cover treeview
    $('ul.sidebar-menu a').filter(function() {
        return this.href == url;
    }).parent().addClass('active');

    // for treeview
    $('ul.treeview-menu a').filter(function() {
        return this.href == url;
    }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
</script>