<% include Breadcrumbs %>
<% require themedCSS('components/_download') %>
<% require themedCSS('objects/_icons') %>
<div class="subDownload mb-4 text-nowrap">
  <a href="$File.Link" class="rounded_icon quad_50 bg_blue mr-4">
    <i class="far fa-file-pdf"></i>
  </a>
  <div class="subDownload_data d-iblock typography text-wrap">
    <span class="h3 font-black">$Title</span>
    <span class="font_blue font_bold"><%t Downloads.FileSize "Größe" %>: $File.Size</span> /
    <span><%t Downloads.FORMAT "Format" %>: <span class="text-upper">$File.Extension</span></span>
  </div>
</div>
