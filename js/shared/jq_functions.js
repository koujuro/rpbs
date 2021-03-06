(function ($) {
    $(document).ready(function () {
        searchFilters({class: 'clients', searchBox: 'clientSearch'});
        searchFilters({class: 'objects', searchBox: 'objectSearch'});
        searchFilters({class: 'types', searchBox: 'typeSearch'});
    });

    function searchFilters(params) {
        let $userName = $('.' + params.class);
        let $search = $('.' + params.searchBox);

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