<?php
/* 
Template Name: PetsOnMe — Forgot Password Form Page
*/
get_header(); ?>
  
<body class="ios-page forgot-password-form-cf7">
    <?php include('page-header-has-icons.php') ?>
     <!-- main div start -->
    <div class="main">
        <?php include('components/forgot-password-form.php')?>
        <?php include('components/forgot-password-confirmation-modal.php')?>     
    </div>
     <!-- main div end -->
     <?php include('page-footer.php') ?>
<?php get_footer(); ?>