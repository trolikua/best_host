<?php
/*
Template Name: Template for "Rating" post type
Template Post Type: sm_rating
*/

get_header(); ?>

<?php
$ratings = carbon_get_post_meta(get_the_ID(), 'rating_items');

echo "<header class='entry-header'>
		<h1 class='entry-title'>".get_the_title()."</h1></header><div class='entry-content' >";

$post = get_post( get_the_ID() );
echo "<div class='content'>".$post->post_content."</div><div class='rating-wrapper'>";?>
<div class="rating-header">
    <div class="rating-provider-title">VPN Provider</div>
    <div class="rating-feature-title">Features</div>
    <div class="rating-score-title">Our Score</div>
</div>
<?php foreach ($ratings as $rating) :?>
<div class="rating-container">
	<div class="hosting-logo">
		<img src="<?=$rating['hosting_logo']?>">
	<?php if ($rating['hosting_badge']): ?>
        <div class="badge"><?=$rating['hosting_badge']?></div>
	<?php endif; ?>
	<?php if ($rating['stars_rating']): ?>
        <div class="hosting-stars"><?php echo hosting_stars($rating['stars_rating']); ?></div>
	<?php endif; ?>
	</div>
	<div class="main-info">
		<div class="short-description"><?=$rating['hosting_description']?></div>
        <?php if ($rating['features']): ?>
		<div class="rating-features">
			<?php foreach ($rating['features'] as $feature) :?>
				<div class="rating-feature"><i class="far fa-check-circle"></i><?=$feature['hosting_features']?></div>
			<?php endforeach; ?>
		</div>
    <?php endif; ?>
	<?php if ($rating['available_on']): ?>
		<div class="rating-availableon">
			<?php foreach ($rating['available_on'] as $available) :?>
                <div class="tooltip"><i class="fab <?=$available?> fa-2x"></i>
                    <span class="tooltiptext"><?=$available_on[$available]?></span>
                </div>

			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	</div>
    <div class="rating-score-wrap">
        <div class="rating-score"><?=$rating['hosting_score']?></div>
    </div>
	<div class="visit-link"><a href="<?=$rating['hosting_url']?>">Visit site</a></div>
</div>

<?php endforeach; ?>
    </div>
    </div>
<?php get_footer(); ?>