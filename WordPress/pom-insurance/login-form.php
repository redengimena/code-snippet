<?php
/* 
Template Name: PetsOnMe — Login Form Page
*/
get_header(); ?>
  
<body class="ios-page login-form-cf7">
    <?php include('page-header-has-icons.php') ?>
     <!-- main div start -->
    <div class="main">
        <?php include('components/login.php')?>
        <?php include('components/login-confirmation-modal.php')?>     
    </div>
     <!-- main div end -->
     <?php include('page-footer.php') ?>
<?php get_footer(); ?>