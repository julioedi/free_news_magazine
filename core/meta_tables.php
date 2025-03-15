<?php

$articles = json_decode(file_get_contents(theme_dir ."/sample_jsons/articles.json"),true);
$categories = json_decode(file_get_contents(theme_dir ."/sample_jsons/categories.json"),true);
$categoriesIDS = [];
$tagsIDS = [];
foreach ($categories as $key => $value) {
  $slug = sanitize_title($value["title"]);
  $cat = get_term_by('slug', $slug, 'category');
  if (!$cat) {
    $cat = wp_insert_term(
      $value["title"],
      "category",
    );
  }
  $categories[$key] = (array) $cat;

}
// foreach ($categories as $key => $value) {
//   // code...
// }

echo "<pre>";
foreach ($articles as &$value) {
  $id = $value["id"] + 10;
  $content = implode("\n\n",$value["content"]);
  if (!empty($value["categories"])) {
    foreach ($value["categories"] as $key => $catID) {
      $value["categories"][$key] = $categories[$catID - 1]["term_id"];
    }
  }
  $my_post = array(
    'post_title'    => wp_strip_all_tags( $value["title"] ),
    'post_content'  =>  $content,
    'post_status'   => 'publish',
    'post_author'   => 1,
    'post_category' => $value["categories"],
    "tags_input" => $value["tags"]
  );
  // $value = $my_post;
  // wp_insert_post($my_post);
}
