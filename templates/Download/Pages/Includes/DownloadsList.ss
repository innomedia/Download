<% loop $SortedDownloads %>

  <% if $Up.Style = "Style A" %>
  <div class="col-md-3 mb-40">
    <div class="bg_lightblue full-height pa-50">
      <a class="download-overlay mb-4" href="$File.Link">
        <span class="download-overlay-center text-center">
          <i class="far fa-file-pdf"></i><br />
          $Up.Title <%t Downloads.DOWNLOAD "herunterladen" %>
        </span>
        <img class="img-fluid" src="$PreviewThumbnail.Fit(300,600).Link" />
      </a>
      <span class="h2">$Title</span>
      <span class="font_blue font_bold d-block"><%t Downloads.FileSize "Größe" %>: $File.Size</span>
      <span class="d-block"><%t Downloads.FORMAT "Format" %>: <span class="text-upper">$File.Extension</span></span>
    </div>
  </div>
  <% else %>
    <div class="col-md-12 mb-20">
      <div class="bg_lightblue pa-30">
        <a href="$File.Link" class="rounded_icon quad_50 bg_blue ml-4 right">
          <i class="far fa-file-pdf"></i>
        </a>
      </div>
    </div>
  <% end_if %>
<% end_loop %>
