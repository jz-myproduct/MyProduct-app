$('.custom-file-input').on('change', function(event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);
});

$('#list_filter_state').change(function () {
    $('#feature_state_filter form').submit();
});

$("#list_filter_tags input").change(function() {
    setTimeout(function(){
        $('#feature_state_filter form').submit();
    }, 1500);
});

$("#roadmap_filter input").change(function() {
    setTimeout(function(){
        $('#feature_state_filter form').submit();
    }, 1500);
});

$('#list_filter_isNew').change(function () {
    $('#filters form').submit();
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

$('[data-toggle=confirmation]').confirmation({
    rootSelector: '[data-toggle=confirmation]',
});

$('.alert').alert();