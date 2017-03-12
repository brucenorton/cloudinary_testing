<?php 
//for wordpress
 require_once("wp-blog-header.php");
  header('HTTP/1.1 200 OK');
//for cloudinary
	require 'cloudinary/src/Cloudinary.php';
	require 'cloudinary/src/Uploader.php';
	require 'cloudinary/src/Api.php';

if (file_exists('load_settings.php')) {
  include 'load_settings.php';
}

$args = array(
	"posts_per_page"   => 10,//set to -1 to get all
);

$posts = get_posts($args); 
echo('<ul>');
foreach($posts as $post) : setup_postdata( $post ); ?>
<?php
	$tagArray = array();
	echo "<li>" . $post->ID . ': ' . $post->post_title;
	//context for cloudinary to put post title in metadata
	$context = array("caption" => $post->post_title);
	
	$imgArgs = array( 
			'post_type' => 'attachment', 
			'post_mime_type' => 'image',
			'numberposts' => -1, 
			'post_status' => null, 
			'post_parent' => $post->ID 
	); 
	//get images
	$attached_images = get_posts( $imgArgs );

	echo('<ul>');

	//get tags
	$tags = wp_get_post_tags($post->ID);

	foreach($tags as $tag){
		echo('<li>'. $tag->{'name'}.'</li>');
		$tagArray[] = $tag->{'name'};
	}

	//loop through the images and upload / transfer to cloudinary with both tags and post title in meta data
	foreach($attached_images as $image){
		echo('<li>'.$image->{'guid'}.'</li>');
		$img = $image->{'guid'};
		//cloudinary stuff
		//$context = array("caption" => "some caption", "alt" => "alternative");
		cloudinaryUpload($img, $context, $tagArray);
		
	}
	
	echo( '</ul></li>');

wp_reset_postdata();
endforeach;

//function for uploading / transferring to 
function cloudinaryUpload($imgSrc, $context, $tagArray){
\Cloudinary\Uploader::upload($imgSrc, array(
		"use_filename" => TRUE, 
		"tags" => $tagArray,
		"context" => $context,
		//Maor says this doesn't work... so try "notification_url"
		//"callback" => "http://deicinginnovations.com/training/load_callback.php"
	));
}



?>