<?php
$cats = get_temp_category();

$cathtml = '<section id="cats_slider"><div class="mws">';
$cathtml .= '<div class="slider-content theme_horizontal_slider">';
foreach ($cats as $key => $term) {
  $tax = (array) $term;
  // if ($key > 10) break;
  $thumb = $tax["_thumbnail_id"];
  $cathtml .= '<a class="cat-item slider_item" href="' . get_tax_permalink($term) . '">';
  $cathtml .= get_category_thumb($thumb ,"thumbnail");
  $cathtml .= "<span>{$tax["name"]}</span>";
  $cathtml .= '</a>';
}
$cathtml . "</div></div></section>";
enqueue_horizontal_slider();
echo $cathtml;
