<?php if ( have_comments() ) : ?>
	<h4 id="comments"><?php comments_number('No Comments', 'One Comment', '% Comments' );?></h4>
	<ul class="comment-list comment-header">
	<?php wp_list_comments();?>
	</ul>
	<?php paginate_comments_links() ?>
<?php endif; ?>

<?php
	$technews_comments_args = array(
		'title_reply'=>'Post a Comment',
		'comment_notes_after' => '',
        'label_submit'=>'SUBMIT COMMENT'
        );
	comment_form($technews_comments_args);
?>