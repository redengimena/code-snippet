<?php
/* 
Template Name: PetsOnMe — Pet Insurance Page
*/
get_header(); ?>
  
<body class="ios-page">
    <?php include('page-header-has-icons.php') ?>
     <!-- main div start -->
    <div class="main">
        
        <?php include('components/hero-text.php')?>
        <?php include('components/grid-content-type-one.php')?>
        <?php include('components/full-carousel.php')?>
        <?php include('components/grid-content-type-two.php')?>
        <?php include('components/two-box-column-with-icon.php')?>
        <?php include('components/members.php')?>
        <?php include('components/two-box-column.php')?>
        <?php include('components/two-column-image-with-button-reverse.php')?>
    </div>
     <!-- main div end -->
     <?php include('page-footer.php') ?>
<?php get_footer(); ?>
        