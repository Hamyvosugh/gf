/**
 * Admin JavaScript for Farakhor Data Manager
 * js/admin.js
 */

jQuery(document).ready(function($) {
    // Initialize Select2 for categories and tags
    if ($.fn.select2) {
        $('.categories-select, .tags-select').select2({
            tags: true,
            tokenSeparators: [','],
            placeholder: 'Select or type to add new'
        });
    }

    // Initialize any datepickers
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    }

    // Add media uploader for images
    $('.upload-image-button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var targetId = button.data('target');
        var inputField = $('#' + targetId);
        var previewDiv = $('#' + targetId + '-preview');

        var mediaUploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
            previewDiv.html('<img src="' + attachment.url + '" alt="Preview">');
        });

        mediaUploader.open();
    });

    // Create Form Submission
    $('#create-farakhor-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        
        // Get selected categories and tags
        var categories = $('#categories').select2('data').map(item => item.text).join(',');
        var tags = $('#tag').select2('data').map(item => item.text).join(',');
        
        // Create FormData object
        var formData = new FormData(this);
        formData.set('categories', categories);
        formData.set('tag', tags);
        
        // Disable submit button
        submitButton.prop('disabled', true);
        
        $.ajax({
            url: farakhorAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    window.location.href = farakhorAjax.admin_url + 'admin.php?page=farakhor-data';
                } else {
                    alert('Error saving data: ' + response.data);
                    submitButton.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                alert('Error saving data. Please try again.');
                submitButton.prop('disabled', false);
            }
        });
    });

    // Edit Form Submission
    $('#edit-farakhor-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        
        // Get selected categories and tags
        var categories = $('#categories').select2('data').map(item => item.text).join(',');
        var tags = $('#tag').select2('data').map(item => item.text).join(',');
        
        // Create FormData object
        var formData = new FormData(this);
        formData.set('categories', categories);
        formData.set('tag', tags);
        
        // Disable submit button
        submitButton.prop('disabled', true);
        
        $.ajax({
            url: farakhorAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Data saved successfully!');
                    window.location.href = farakhorAjax.ajaxurl.replace('admin-ajax.php', 'admin.php?page=farakhor-data');
                } else {
                    alert('Error saving data: ' + response.data);
                    submitButton.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                alert('Error saving data. Please try again.');
                submitButton.prop('disabled', false);
            }
        });
    });

    // Handle delete button clicks
    $('.delete-item').on('click', function(e) {
        e.preventDefault();
        
        if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            var itemId = $(this).data('id');
            
            $.ajax({
                url: farakhorAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_farakhor_data',
                    id: itemId,
                    nonce: farakhorAjax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('Error occurred during deletion. Please try again.');
                }
            });
        }
    });
});