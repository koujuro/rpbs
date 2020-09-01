(function ($) {
    $(document).ready(function () {
        searchDevices();
    });

    function searchDevices() {
        let $userName = $('.filter-option');
        let $search = $('.search');

        $userName.each(function () {
            $(this).attr('data-search-term', $(this).text().toLowerCase());
        });

        $search.on('keyup', function () {
            let searchTerm = $(this).val().toLowerCase();

            $userName.each(function () {
                if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }
})(jQuery);