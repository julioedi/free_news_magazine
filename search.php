<?php get_header(); ?>

<main>
    <h1>Search Results for: <?php echo get_search_query(); ?></h1>

    <?php if (have_posts()) : ?>
        <ul>
            <?php while (have_posts()) : the_post(); ?>
                <li>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <p><?php the_excerpt(); ?></p>
                    <?php echo wp_get_attachment_image(0,"thumbnail") ?>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- Pagination -->
        <div class="pagination">
            <?php echo paginate_links(); ?>
        </div>

    <?php else : ?>
        <p>No results found. Try a different search.</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
