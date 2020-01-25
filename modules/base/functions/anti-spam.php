<?php
function nc_url_spamcheck($approved, $commentdata)
{
    return (strlen($commentdata['comment_author_url']) > 50) ? 'spam' : $approved;
}
add_filter('pre_comment_approved', 'nc_url_spamcheck', 99, 2);

function nc_comment_post($incoming_comment)
{
    $pattern = '/[一-龥]/u';
    // 禁止全英文评论
    if (!preg_match($pattern, $incoming_comment['comment_content'])) {
        wp_die("您的评论中必须包含汉字!");
    }
    $pattern = '/[あ-んア-ン]/u';
    // 禁止日文评论
    if (preg_match($pattern, $incoming_comment['comment_content'])) {
        wp_die("评论禁止包含日文!");
    }
    return($incoming_comment);
}
add_filter('preprocess_comment', 'nc_comment_post');
