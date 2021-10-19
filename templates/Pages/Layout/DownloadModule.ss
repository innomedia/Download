<% require themedCSS('components/_download') %>
<% require themedCSS('objects/_icons') %>
<% include TitleBanner %>
<div class="container mb-100">
    <div class="row">
        <% loop $DownloadCategories %>
            <% if $DownloadSubCategories %>
                <div class="col-md-12 typography">
                    <h3>$Up.Title</h3>
                    <span class="h1">$Title</span>
                    <% if $Content %>
                        $Content
                    <% end_if %>
                </div>
                <% loop $DownloadSubCategories %>
                    <div class="col-md-12 mb-40">
                        <div class="category_container">
                            <span class="category">$Title</span>
                        </div>
                    </div>
                    <div class="mb-40">
                        <% include DownloadsList %>
                    </div>
                <% end_loop %>
            <% else %>
                <div class="col-md-12 mb-40">
                    <h3>$Up.Title</h3>
                    <span class="h1">$Title</span>
                </div>
                <% include DownloadsList %>
            <% end_if %>
        <% end_loop %>
    </div>
</div>
