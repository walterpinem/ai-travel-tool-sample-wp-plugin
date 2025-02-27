/**
 * AI Travel Tool JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // DOM elements
        const $generateBtn = $('#aitraveltool-generate-btn');
        const $destination = $('#aitraveltool-destination');
        const $tripType = $('#aitraveltool-trip-type');
        const $apiService = $('#aitraveltool-api-service');
        const $loading = $('#aitraveltool-loading');
        const $result = $('#aitraveltool-result');

        // Event listener for generate button
        $generateBtn.on('click', function(e) {
            e.preventDefault();
            generateItinerary();
        });

        // Function to generate travel itinerary
        function generateItinerary() {
            // Get form values
            const destination = $destination.val().trim();
            const tripType = $tripType.val();
            const apiService = $apiService.val();

            // Validate inputs
            if (!destination) {
                showError('Please enter a destination.');
                $destination.focus();
                return;
            }

            if (!tripType) {
                showError('Please select a trip type.');
                $tripType.focus();
                return;
            }

            // Show loading indicator and hide results
            $result.hide();
            $loading.show();
            $generateBtn.prop('disabled', true);

            // AJAX request to generate itinerary
            $.ajax({
                url: aitraveltool_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'aitraveltool_generate_itinerary',
                    nonce: aitraveltool_params.nonce,
                    destination: destination,
                    trip_type: tripType,
                    api_service: apiService
                },
                success: function(response) {
                    $loading.hide();
                    $generateBtn.prop('disabled', false);

                    if (response.success) {
                        displayResult(destination, tripType, response.data.itinerary);
                    } else {
                        showError(response.data.message || aitraveltool_params.error);
                    }
                },
                error: function() {
                    $loading.hide();
                    $generateBtn.prop('disabled', false);
                    showError(aitraveltool_params.error);
                }
            });
        }

        // Function to display the generated itinerary
        function displayResult(destination, tripType, itinerary) {
            // Format the itinerary with Markdown conversion
            const formattedItinerary = formatContent(itinerary);

            // Create the HTML content
            const html = `
                <h3>${tripType} Trip to ${destination}</h3>
                <div class="aitraveltool-itinerary-content">
                    ${formattedItinerary}
                </div>
            `;

            $result.html(html).show();

            // Scroll to result
            $('html, body').animate({
                scrollTop: $result.offset().top - 50
            }, 500);
        }

        // Function to format the content with basic Markdown support
        function formatContent(content) {
            if (!content) return '';

            // Convert line breaks to <br>
            let formatted = content.replace(/\n/g, '<br>');

            // Convert Markdown headers (# Header -> <h4>Header</h4>)
            formatted = formatted.replace(/#{1,6}\s+(.*?)(?:<br>|$)/g, function(match, p1) {
                return '<h4>' + p1 + '</h4>';
            });

            // Convert Markdown bold (**text** -> <strong>text</strong>)
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            // Convert Markdown italic (*text* -> <em>text</em>)
            formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');

            // Convert Markdown lists (- item -> <li>item</li>)
            formatted = formatted.replace(/<br>- (.*?)(?:<br>|$)/g, '<br><ul><li>$1</li></ul>');
            formatted = formatted.replace(/<\/ul><br><ul>/g, '');

            return formatted;
        }

        // Function to show error message
        function showError(message) {
            $result.html(`<div class="aitraveltool-error">${message}</div>`).show();
        }
    });
})(jQuery);
