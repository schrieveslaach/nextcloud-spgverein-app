function printAsPdf(event) {
    var doc = new jsPDF();
    event.data.find('.address').each(function (row, d) {
        $(d).find('p').each(function (j, p) {
            doc.text($(p).text(),
                    10,
                    10 + 10 * j + (row % 6) * 50);
        });

        if (row % 6 === 0) {
            doc.addPage();
        }
    });

    doc.save(event.data.find('h1').text() + '.pdf');
}

function loadCities() {
    $.getJSON(OC.generateUrl('/apps/spgverein/cities'), function (cities) {
        $.each(cities, function (i, city) {
            // Add to navigation
            var cityShortCut = $('<li/>').appendTo($('#navigation-districts'));
            $('<a/>').appendTo(cityShortCut)
                    .addClass('district')
                    .attr('href', '#' + city)
                    .text(city);

            // Create container holding all member of that district
            var districtWithMembers = $('<div/>').appendTo($('#members'));
            $('<h1/>').appendTo(districtWithMembers)
                    .addClass("city")
                    .attr('id', city)
                    .append(document.createTextNode(city));
        });
    });
}

function loadMembers() {
    var groupingOption = $(".grouping-option:checked").attr('id');

    $.getJSON(OC.generateUrl('/apps/spgverein/members/' + groupingOption), function (members) {

        // Remove old elements
        $('.address').remove();

        $.each(members, function (i, member) {

            var address = $('<div/>').appendTo($('h1[id="' + member.city + '"]').parent());
            address.attr('class', "address");

            $('<p/>').appendTo(address)
                    .append(member.lastname);
            $('<p/>').appendTo(address)
                    .append(member.firstname);
            $('<p/>').appendTo(address)
                    .append(member.street);
            $('<p/>').appendTo(address)
                    .append(member.zipcode + ' ' + member.city);
        });
    });
}

$(document).ready(function () {

    $(".grouping-option").change(function () {
        $(".grouping-option").prop('checked', false);
        $(this).prop('checked', true);

        loadMembers();
    });

    loadCities();
    loadMembers();
});