<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if IE 7]>
<html class="ie ie7" xmlns="http://www.w3.org/1999/xhtml" lang="vi">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8 not-ie7" xmlns="http://www.w3.org/1999/xhtml" lang="vi">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]>
<html class="not-ie7" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#" lang="vi">
<!--<![endif]-->

<head>
    
    <title><?php echo isset($data['meta_title']) ? $data['meta_title'] : ''; ?>
    </title>

    <meta http-equiv="Content-Language" content="vi" />
    <meta name="robots" content="noodp,index,follow" />
    <meta name="revisit-after" content="1 days" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <base href="<?php echo CMS_URL; ?>" />
        <link href="<?php echo isset($this->system['icon']) ? htmlspecialchars($this->system['icon']) : NULL; ?>" rel="shortcut icon" type="image/x-icon" />
        <link rel="icon" type="image/png" href="<?php echo isset($this->system['icon']) ? htmlspecialchars($this->system['icon']) : NULL; ?>" />
        <link rel="publisher" href="https://plus.google.com/111452226439609690293" />


        <link rel="canonical" href="<?php echo isset($data['canonical']) ? $data['canonical'] : ''; ?>"/>
        <?php echo (isset($data['rel_prev']) && !empty($data['rel_prev'])) ? '<link rel="prev" href="' . $data['rel_prev'] . '" />' : ''; ?>
        <?php echo (isset($data['rel_next']) && !empty($data['rel_next'])) ? '<link rel="next" href="' . $data['rel_next'] . '" />' : ''; ?>
        <meta name="description" content="<?php echo isset($data['meta_description']) ? $data['meta_description'] : ''; ?>" />
        <meta name="keywords" content="<?php echo isset($data['meta_keywords']) ? $data['meta_keywords'] : ''; ?>" />
        <link rel="author" href="https://plus.google.com/111452226439609690293" />


        <meta property="og:title" content="<?php echo isset($data['meta_title']) ? $data['meta_title'] : ''; ?>" />
        <meta property="og:image" content="<?php echo isset($data['image']) ? $data['image'] : ''; ?>" />
        <meta property="og:description" content="<?php echo isset($data['meta_description']) ? $data['meta_description'] : ''; ?>" />
        <meta property="og:site_name" content="Tranh68.com" />
        <meta property="og:url" content="<?php echo isset($data['canonical']) ? $data['canonical'] : ''; ?>" />
        <meta property="fb:app_id" content="842241945811199" />
        <meta property="fb:admins" content="100005010036691"/>
        <!--?php
        $this->minify->css(array('style.css', 'linkfont.css', 'responsive.css'));
        $this->minify->js(array('jquery-1.7.2.min.js', 'jquery.lazyload.js'));
        echo $this->minify->deploy_css(TRUE);
        echo $this->minify->deploy_js();
        ?-->
        <!-- <link href="template/frontend/fonts/font-awesome-4.1.0/css/font-awesome1.min.css" type="text/css" rel="stylesheet" /> -->

      <!--<script src="template/frontend/plugin/flexslider/jquery.flexslider.js" type="text/javascript"></script>
        <link href="template/frontend/plugin/flexslider/flexslider.css" type="text/css" rel="stylesheet" />-->
       <!-- <link href="template/frontend/styles1.min.css" type="text/css" rel="stylesheet" /> -->
    <!-- <link href="template/frontend/css/linkfont.css" type="text/css" rel="stylesheet" /> -->
    <?php 
    $arr = array('template/frontend/fonts/font-awesome-4.1.0/css/font-awesome1.min.css','template/frontend/styles1.min.css','template/frontend/css/linkfont1.css');
    $buffer = '';
    foreach ($arr as $key => $f) {
        $f = FCPATH.$f;
        if(file_exists($f)){
            
            $buffer .= file_get_contents($f);
        }
        
    }
    echo '<style>'. $buffer."</style>";
    ?>
<script src="template/frontend/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="template/frontend/scripts.min.js" defer type="text/javascript"></script>
<script> var qazy_image = "http://qnimate.com/blank.gif";  </script>
<script src="template/frontend/js/qazy.js" defer type="text/javascript"></script>

<script>
(function (){
    var els = [	'section', 'article', 'hgroup', 'header', 'footer', 'nav', 'aside', 
	'figure', 'mark', 'time', 'ruby', 'rt', 'rp' ];
    for (var i=0; i<els.length; i++){
        document.createElement(els[i]);
        }
})();
</script>

        <style type="text/css">
            .main-content ul li:nth-child(3n){
                margin-right:0px;
            }
section.main-content ul li:first-child + li + li{

                margin-right:0px;
            }

        </style>
</head>
<body>

    <?php $this->load->view('frontend/header'); ?>

    <?php
    $data = isset($data) ? $data : NULL;
    $this->load->view($template, $data);
    ?>
    <?php $this->load->view('frontend/nav-content'); ?>


    <?php $this->load->view('frontend/footer'); ?>
    <script type="text/javascript">
        $(window).scroll(function () {
            if ($(this).scrollTop() > 40) {
                $('.header').css({top: '0px', position: 'fixed', });
            } else {
                $('.header').css({position: 'relative', left: '0px', });
            }
        });
    </script>

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/563ec92b0717f59310499365/default';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!--End of Tawk.to Script-->

</body>
</html>