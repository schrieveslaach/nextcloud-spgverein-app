function generatePdf(city) {
    var groupingOption = $(".grouping-option:checked").attr('id');
    $.getJSON(OC.generateUrl('/apps/spgverein/members/' + groupingOption), function (members) {
        var doc = new jsPDF('p', 'pt', 'letter');
        var fontSize = 9;
        doc.setFontSize(fontSize);

        var row = 0;
        var column = 0;

        $.each(members, function (i, member) {
            if (member.city !== city) {
                return;
            }

            var margin = 25;
            var pad = 2;
            var width = 160;

            doc.text(member.lastname,
                    margin + column * width,
                    margin + row * 100);

            doc.text(member.firstname,
                    margin + column * width,
                    margin + pad + fontSize + row * 100);

            doc.text(member.street,
                    margin + column * width,
                    margin + 2 * (pad + fontSize) + row * 100);

            doc.text(member.zipcode + ' ' + member.city,
                    margin + column * width,
                    margin + 3 * (pad + fontSize) + row * 100);

            ++column;
            if (column % 3 === 0) {
                column = 0;
                ++row;
            }

            if ((row + 1) % 9 === 0) {
                row = 0;
                doc.addPage();
            }
        });

        doc.save(city + '.pdf');
    });
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

            $('<div/>').appendTo(districtWithMembers)
                    .addClass("w100")
                    .append($('<a/>')
                            .addClass('pdf-link')
                            .append($('<i class="fa fa-file-pdf-o" aria-hidden="true"></i> '))
                            .append(document.createTextNode(t('spgverein', 'Download as PDF')))
                            .click(function () {
                                generatePdf(city);
                            }));
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