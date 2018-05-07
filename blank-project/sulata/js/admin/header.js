//Show hide search
function toggleSearch(arg) {
    if (arg == 'show') {
        if ($('#search-click')) {
            $('#search-click').hide();
        }
        if ($('#search-area')) {
            $('#search-area').show('slow');
        }
    } else {
        if ($('#search-click')) {
            $('#search-click').show('slow');
        }
        if ($('#search-area')) {
            $('#search-area').hide();
        }
    }
}