<!DOCTYPE html>
<html lang="en" class="pages pages_blocks ">
<head>
    <?php $this->load->view('front/block/head') ?>
</head>
<body class="fixed-bottom-space landing-theme">
<div class="" style="font-size: 18px; font-family: Roboto;" >

    <!-- Main header -->
    <?php $this->load->view('front/block/header.php'); ?>

    <!-- Main content -->
    <?php $this->load->view($temp, $this->data); ?>
    <!--<a type="button" onclick="topFunction()" id="myBtn1" title="Go to top"><img src="<?php /*echo public_url('front') */?>/images/mascost.png"></a>
    <a type="button" onclick="topFunction()" id="myBtn" title="Go to top"><img src="<?php /*echo public_url('front') */?>/images/ic_to_top.png"></a>
-->
</div>

<script>
    // When the user scrolls down 20px from the top of the document, show the button
  /*  window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 500) {
            document.getElementById("myBtn").style.display = "block";
        } else {
            document.getElementById("myBtn").style.display = "none";
        }
    }
*/
    // When the user clicks on the button, scroll to the top of the document
  /*  function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }*/
</script>
<?php $this->load->view('front/block/footer.php') ?>
</body>
<script type="text/javascript">
    //<![CDATA[
    window.gon={};
    //]]>
</script>
</html>