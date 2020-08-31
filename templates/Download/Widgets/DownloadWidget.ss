<% require themedCSS('components/_team') %>
<div class="row">
    <% with $Downloads %>
            <div class="download__details typography border-top border-bottom">
                <h6><a class="" href="$File.Link" target="_blank"><i class="fal fa-arrow-to-bottom mr-2"></i> $Title.RAW</a></h6>
                <p class="download__details--size ml-4">(PDF, $File.Size)</p>
            </div>
    <% end_with %>
</div>
