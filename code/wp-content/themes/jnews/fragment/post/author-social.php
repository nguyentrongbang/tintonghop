<?php
	if(is_single()) {
		$author_id = get_post_field( 'post_author', get_the_ID() );
	} else {
		$author_id = get_queried_object()->ID;
	}
?>

<?php if( get_the_author_meta( "url", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("url", $author_id); ?>" class="url"><i class="fa fa-globe"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "facebook", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("facebook", $author_id); ?>" class="facebook"><i class="fa fa-facebook-official"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "twitter", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("twitter", $author_id); ?>" class="twitter"><i class="fa fa-twitter"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "googleplus", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("googleplus", $author_id); ?>" class="googleplus removed"><i class="fa fa-google-plus"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "linkedin", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("linkedin", $author_id); ?>" class="linkedin"><i class="fa fa-linkedin"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "pinterest", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("pinterest", $author_id); ?>" class="pinterest"><i class="fa fa-pinterest"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "behance", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("behance", $author_id); ?>" class="behance"><i class="fa fa-behance"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "github", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("github", $author_id); ?>" class="github"><i class="fa fa-github"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "flickr", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("flickr", $author_id); ?>" class="flickr"><i class="fa fa-flickr"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "tumblr", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("tumblr", $author_id); ?>" class="tumblr"><i class="fa fa-tumblr"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "dribbble", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("dribbble", $author_id); ?>" class="dribbble"><i class="fa fa-dribbble"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "soundcloud", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("soundcloud", $author_id); ?>" class="soundcloud"><i class="fa fa-soundcloud"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "instagram", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("instagram", $author_id); ?>" class="instagram"><i class="fa fa-instagram"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "vimeo", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("vimeo", $author_id); ?>" class="vimeo"><i class="fa fa-vimeo"></i> </a>
<?php endif; ?>

<?php if( get_the_author_meta( "youtube", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("youtube", $author_id); ?>" class="youtube"><i class="fa fa-youtube-play"></i></a>
<?php endif; ?>

<?php if( get_the_author_meta( "vk", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("vk", $author_id); ?>" class="vk"><i class="fa fa-vk"></i></a>
<?php endif; ?>

<?php if( get_the_author_meta( "reddit", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("reddit", $author_id); ?>" class="reddit"><i class="fa fa-reddit"></i></a>
<?php endif; ?>

<?php if( get_the_author_meta( "weibo", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("weibo", $author_id); ?>" class="weibo"><i class="fa fa-weibo"></i></a>
<?php endif; ?>

<?php if( get_the_author_meta( "rss", $author_id ) ) : ?>
    <a target="_blank" href="<?php the_author_meta("rss", $author_id); ?>" class="rss"><i class="fa fa-rss"></i></a>
<?php endif; ?>
