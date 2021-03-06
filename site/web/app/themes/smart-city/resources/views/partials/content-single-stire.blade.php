<script src="https://cdn.jsdelivr.net/algoliasearch.helper/2/algoliasearch.helper.min.js"></script>

<article @php(post_class())>
  <header>
    @include('partials/page-header', ['title' => pll__('Știri')])
  </header>
  <div class="container-fluid article-container">
    <div class="container">
      <div class="dropdown show small-menu smart-dropdown" style="text-align: center">
        <a
          class="btn btn-secondary dropdown-toggle d-lg-none"
          href="#"
          role="button"
          id="dropdownMenuLink"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false">
          {{ pll__('Vezi alte categorii') }}
        </a>

        <div class="dropdown-menu algolia-facets" aria-labelledby="dropdownMenuLink">
        </div>
      </div>

      <div class="row">
        <div class="col-lg-8 col-md-12">
          <h1 class="entry-title">{!! get_the_title() !!}</h1>
          @include('partials/entry-meta')
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 col-md-12">
          <div class="entry-content">
            @php(the_content())
          </div>

          <div class="section social-media">
            <div class="content">
              <h4>{{ pll__('Distribuie articol') }}</h4>
              <div class="icons">
                <div>
                  <a
                    href="https://www.facebook.com/sharer/sharer.php?u={{ get_the_permalink() }}"
                    target="popup"
                    onclick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ get_the_permalink() }}','popup','width=600,height=600'); return false;">
                    <i class="fab fa-facebook-square"></i>
                  </a>
                </div>
                <div>
                  <a
                    href="https://twitter.com/home?status={{ get_the_permalink() }}"
                    target="popup"
                    onclick="window.open('https://twitter.com/home?status={{ get_the_permalink() }}','popup','width=600,height=600'); return false;">
                    <i class="fab fa-twitter-square"></i>
                  </a>
                </div>
                <div>
                  <a href="mailto:?body={{ get_the_permalink() }}">
                    <i class="fas fa-envelope"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="section">
            <div class="content">
              <h4>{{ pll__('Articole populare') }}</h4>
            </div>
          </div>

          <div class="row justify-content-center articles">
            @foreach (News::related() as $article)
              <div class="col-lg-5 col-md-8 col-sm-12">
                @include('partials/components/article-detailed-box', ['articol' => $article])
              </div>
            @endforeach
          </div>

          <div class="comments" style="margin-top: 100px; margin-bottom: 120px;">
            @php(comments_template('/partials/comments.blade.php'))
          </div>
        </div>

        <div class="col-lg-4 col-md-12 sidebar d-none d-lg-block">
          <h3>{{ pll__('Categorii') }}</h3>
          <ul class="algolia-facets"></ul>
        </div>

        <script type="text/html" id="category-template">
          <li>
            <a href="<%= href %>" class="category-link">
              <span class="category <%= is_current ? 'current' : '' %>">
                <%= label %>
                <span class="count"><%= count %></span>
              </span>
            </a>
          </li>
        </script>

      </div>
    </div>
  </div>
</article>
<script id="dsq-count-scr" src="//albaiuliasmartcity.disqus.com/count.js" async></script>

<script type="text/javascript">
  jQuery(function() {
    const currentCat = '{{ get_the_category() ? get_the_category()[0]->name : '' }}';
    const parentURL = "{{ get_permalink(pll_get_post(get_page_by_title('stiri')->ID)) }}";

    const client = algoliasearch(
      algolia.application_id,
      algolia.search_api_key
    );

    const helper = algoliasearchHelper(client, algolia.indices.posts_stire.name, {
      disjunctiveFacets: ['category'],
      facetingAfterDistinct: true,
      filters: 'locale:"' + current_locale + '"',
    });

    helper.on('result', async function(data) {
      const facets = data.getFacetValues('category', {sortBy: ['count:desc']});
      const template = _.template(document.getElementById('category-template').innerHTML);

      facets.map((facet) => {
        const queryString = `?refinementList[category][0]=${facet.name}`;
        const html = template({
          'label': facet.name,
          'count': facet.count,
          'is_current': facet.name === currentCat,
          'href': parentURL + queryString,
        });

        jQuery(".algolia-facets").append(html);
      });
    });

    helper.search();
  });

  var disqus_config = function () {
    this.language = "{{ pll_current_language() }}";
  };
</script>

