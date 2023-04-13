<?php
/*
Template Name: Twitter-like Feed
*/

get_header(); ?>

<main id="primary" class="site-main">
    <div id="twitter-like-feed" class="content"></div>
    <button id="load-more" class="load-more">Load More</button>
</main><!-- #primary -->

<?php
get_sidebar();
get_footer();
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    const feedContainer = document.getElementById('twitter-like-feed');
    const loadMoreButton = document.getElementById('load-more');

    function fetchFeedItems() {
        loadMoreButton.disabled = true;
        loadMoreButton.textContent = 'Loading...';

        const authorName = '低级黑小明';
        const apiUrl = '<?php echo esc_url_raw(rest_url('wp/v2/question')); ?>';
        const authorApiUrl = '<?php echo esc_url_raw(rest_url('wp/v2/users')); ?>';

        fetch(`${authorApiUrl}?search=${authorName}`)
            .then(response => response.json())
            .then(users => {
                if (users.length > 0) {
                    const authorId = users[0].id;
                    const queryParams = new URLSearchParams({
                        author: authorId,
                        per_page: 10,
                        page: currentPage
                    });

                    fetch(`${apiUrl}?${queryParams.toString()}`)
                        .then(response => response.json())
                        .then(posts => {
                            if (posts.length > 0) {
                                posts.forEach(post => {
                                    const postDiv = document.createElement('div');
                                    postDiv.classList.add('post');
                                    postDiv.innerHTML = post.content.rendered;
                                    feedContainer.appendChild(postDiv);
                                });

                                currentPage++;
                                loadMoreButton.disabled = false;
                                loadMoreButton.textContent = 'Load More';
                            } else {
                                loadMoreButton.disabled = true;
                                loadMoreButton.textContent = 'No More Items';
                            }
                        });
                } else {
                    feedContainer.innerHTML = `Author '${authorName}' not found.`;
                }
            });
    }

    loadMoreButton.addEventListener('click', fetchFeedItems);

    // Load the initial set of feed items
    fetchFeedItems();
});
</script>
