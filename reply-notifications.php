<?php
/*
Plugin Name: Reply Notifications
Plugin URI: https://github.com/yogasukmap/wp-reply-notifications
Description: Send Notifications to comment author when their comment get replied
Version: 1.0
Author: Yoga Sukma
Author URI: https://yogasukma.web.id
License: GPL2
*/

add_action( "wp_insert_comment", "ysu_new_comment_created", 99, 2 );

function ysu_new_comment_created( $comment_id, $comment ) {
	if ( $comment->comment_parent > 0 ) {
		ysu_send_email_notification( $comment );
	}
}

function ysu_send_email_notification( $comment ) {
	$post = get_post( $comment->comment_post_ID );

	add_filter( "wp_mail_content_type", "ysu_set_email_content_type" );

	wp_mail( get_comment_author_email( $comment ), ysu_get_email_subject( $post ), ysu_get_email_content( $post, $comment ) );

	remove_filter( "wp_mail_content_type", "ysu_set_email_content_type" );
}

function ysu_set_email_content_type( $contentType ) {
	return "text/html";
}

function ysu_get_comment_author_email( $comment ) {
	$replied_comment        = get_comment( $comment->comment_parent );
	$author_replied_comment = get_userdata( $replied_comment->user_id );

	return $author_replied_comment->user_email;
}

function ysu_get_email_subject( $post ) {
	return sprintf( __( "Seseorang membalas komentar anda pada artikel \"%s\"", "reply-notifications" ), $post->post_title );
}

function ysu_get_email_content( $post, $comment ) {
	return sprintf( __( "%s<br><br><a href='%s'>klik disini untuk membuka artikel</a>", "reply-notifications" ), $comment->comment_content, get_permalink( $post->ID ) );
}
