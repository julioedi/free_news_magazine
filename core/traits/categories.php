<?php

/**
 *
 */
trait Categories
{
  public $categories = null;
  public $categories_structure = null;
  public $categories_obj = array();
  public $categories_taxonomy = null;
  public $categories_thumbs = array();

  public function get_categories(){
    if (!$this->categories) {
      $this->categories = get_categories();
    }
    return $this->categories;
  }
  public function get_category_thumb($id,$size = false){
    if (!is_numeric($id) || $id == 0 || $id == "0") {
      return wp_get_attachment_image(0);
    }
    $pre = is_string($size) ? $size : "full";
    if (!isset($categories_thumbs["$id"])) {
      $categories_thumbs["$id"] = array();
    }
    if (!isset($categories_thumbs["$id"][$pre])) {
      $categories_thumbs["$id"][$pre] = wp_get_attachment_image($id,$size);
    }

    return $categories_thumbs["$id"][$pre];

  }

  public function get_tax_permalink($term = null){
    if (!$term) {
      return "";
    }
    if (isset($this->categories_obj[$term->term_id])) {
      return $this->categories_obj[$term->term_id];
    }
    $taxonomy = $term->taxonomy;
    $slug = $term->slug;
    if (!$this->categories_structure) {
    	global $wp_rewrite;
      $this->categories_structure = $wp_rewrite->get_extra_permastruct( $taxonomy );
    }
    if (!$this->categories_taxonomy) {
      $this->categories_taxonomy = get_taxonomy( $taxonomy );
    }

    $termlink = apply_filters( 'pre_term_link', $this->categories_structure, $term );
    if ( empty( $termlink ) ){
      $termlink = '?cat=' . $term->term_id;
    }else{
      if ( ! empty( $this->categories_taxonomy->rewrite['hierarchical'] ) ) {
  			$hierarchical_slugs = array();
  			$ancestors          = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
  			foreach ( (array) $ancestors as $ancestor ) {
  				$ancestor_term        = get_term( $ancestor, $taxonomy );
  				$hierarchical_slugs[] = $ancestor_term->slug;
  			}
  			$hierarchical_slugs   = array_reverse( $hierarchical_slugs );
  			$hierarchical_slugs[] = $slug;
  			$termlink             = str_replace( "%$taxonomy%", implode( '/', $hierarchical_slugs ), $termlink );
  		} else {
  			$termlink = str_replace( "%$taxonomy%", $slug, $termlink );
  		}
  		$termlink = home_url( user_trailingslashit( $termlink, 'category' ) );
    }

    $termlink = apply_filters( 'category_link', $termlink, $term->term_id );
    $this->categories_obj[$term->term_id] = clear_home_url(apply_filters( 'term_link', $termlink, $term, $taxonomy ));
    return $this->categories_obj[$term->term_id];
  }
}

function get_temp_category(){
  global $storedData;
  return $storedData->get_categories();
}


function get_category_thumb($id = 0,$size = false){
  global $storedData;
  return $storedData->get_category_thumb($id,$size);
}
function get_tax_permalink($term = null){
    global $storedData;
    return $storedData->get_tax_permalink($term);
}
