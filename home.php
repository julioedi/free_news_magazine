<?php
$templates = [
  "categories",
];
add_filter(theme_domain ."/frontend/default_styles",function($styles){
  $styles = array_merge(
    $styles,
    array(
      "cats_slider",
    )
  );
  return $styles;
});

get_header();
foreach ($templates as $value) {
  get_template_part("/templates/home/$value");
}
get_footer();
