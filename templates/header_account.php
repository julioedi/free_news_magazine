<?php
if (!is_user_logged_in()) {
  return;
}
global $storedData;
$user = $storedData->get_current_user();
$account .= '<div id="header_account">';
$picture = $storedData->get_render_userPicture($user->data->user_login,$user->ID);
$account .= "<div class=\"profile_image\">$picture</div>";

$name = ucfirst($user->data->display_name);
$account .= "<div class=\"name\"><span>$name</span><i class=\"fa-solid fa-chevron-down expand\"></i></div>";

$list = $storedData->render_accountLinks();
$account .= "<div class=\"container\">$list</div>";

$account .= '</div>';

$account .= "<div class=\"bookmarks icon_btn\"><i class=\"fa-regular fa-bookmark\"></i></div>";
