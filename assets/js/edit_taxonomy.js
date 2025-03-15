jQuery(document).ready(function($){
  var mediaUploader;
  const $preview = $("#preview_cover");
  const $inputVal = $('#thumbnail_id');
  const deletebtn = '<div class="delete_cover"><div class="icon_button"><i class="fa-solid fa-trash"></i></div></div>';
  $(document).on("click","#cover_no_image .theme_btn, #preview_cover img",() =>{
    // If the uploader object exists, reopen the dialog
    if (mediaUploader) {
        mediaUploader.open();
        return;
    }
    // Otherwise, create a new media uploader
     mediaUploader = wp.media.frames.file_frame = wp.media({
         title: 'Select or Upload an Image',
         button: {
             text: 'Use this image'
         },
         multiple: false
     });
    mediaUploader.on('select', function() {
        // Get the selected image data
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        $preview.html(`<img src="${attachment.url}" data-id="${attachment.id}">${deletebtn}`);

        $inputVal.attr("value",attachment.id);
    });

    mediaUploader.open();
  });
  $(document).on("click","#preview_cover .delete_cover",() =>{
    $preview.html("");
    $inputVal.attr("value","0");
  })
});
