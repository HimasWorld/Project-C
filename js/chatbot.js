jQuery(document).ready(function($) {
    $('#chatbot-input').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            const message = $(this).val();
            $('#chatbot-messages').append(`<div class="user-message">${message}</div>`);
            $(this).val('');

            // Send message to API
            $.post(chatbotData.apiUrl, {
                message: message,
                _ajax_nonce: chatbotData.nonce
            }, function(response) {
                $('#chatbot-messages').append(`<div class="bot-message">${response}</div>`);
            });
        }
    });
});