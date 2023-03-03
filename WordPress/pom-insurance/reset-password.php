<?php
/* 
Template Name: PetsOnMe — Reset Password Form Page
*/
get_header(); ?>
  
<body class="ios-page reset-password-form-cf7">
    <?php include('page-header-has-icons.php') ?>
     <!-- main div start -->
    <div class="main">
        <?php include('components/reset-password-form.php')?>
        <?php include('components/reset-password-confirmation-modal.php')?>     
    </div>
     <!-- main div end -->
     <?php include('page-footer.php') ?>
<?php get_footer(); ?>