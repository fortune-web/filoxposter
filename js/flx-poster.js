jQuery(function($) {
    // cache form elements
    var $form = $('#flx-post-form');
    var $title = $('#post_title');
    var $submit = $('#flx-post-submit-button');
  
    // disable submit button on page load
    $submit.prop('disabled', true);
  
    // check if post title exists
    $title.on('input', function() {
      $.ajax({
        url: '/wp-json/flx/v1/post-title-exists/' + $(this).val(),
        method: 'GET'
      }).done(function(response) {
        if (response.exists) {
          alert('This post title already exists!');
          $submit.prop('disabled', true);
        } else {
          $submit.prop('disabled', false);
        }
      });
    });
  
    // check for empty fields
    $form.on('input', function() {
      if ($title.val() === '' || $form.val() === '') {
        $submit.prop('disabled', true);
      } else {
        $submit.prop('disabled', false);
      }
    });
  
    // submit form
    $form.on('submit', function(event) {
      event.preventDefault();
  
      $.ajax({
        url: '/wp-json/flx/v1/create-post',
        method: 'POST',
        data: {
          title: $title.val(),
          content: $form.val()
        }
      }).done(function(response) {
        if (response.success) {
          $form[0].reset();
          alert('Post created successfully! View your post at ' + response.link);
        } else {
          alert('There was an error creating your post. Please try again.');
        }
      });
    });
  });
  