<% require themedCSS('components/_download') %>
<% require themedCSS('objects/_icons') %>
<% include TitleBanner %>
<div class="container mb-100">
    <div class="row">
        <% loop $DownloadCategories %>
            <div class="col-md-12 mb-40">
                <h3>$Up.Title</h3>
                <span class="h1">$Title</span>
            </div>
            <% include DownloadsList %>
        <% end_loop %>
    </div>
</div>
