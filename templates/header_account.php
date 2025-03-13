<?php
if (!is_user_logged_in()) {
  return;
}
global $storedData;
$user = $storedData->get_current_user();
$account .= '<div id="header_account">';
$account .= "<pre>" . print_r($user,true) . "</pre>";
// $account .= get_avatar_url($user->ID);
$account .= '</div>';
