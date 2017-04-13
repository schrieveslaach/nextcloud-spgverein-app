function generatePdf(city) {
    var groupingOption = $(".grouping-option:checked").attr('id');
    $.getJSON(OC.generateUrl('/apps/spgverein/members/' + groupingOption), function (members) {
        var doc = new jsPDF('p', 'pt', 'letter');
        var fontSize = 14;
        doc.setFontSize(fontSize);

        var row = 0;
        var column = 0;

        $.each(members, function (i, member) {
            if (member.city !== city) {
                return;
            }

            var margin = 50;
            var pad = 4;
            var width = 320;

            doc.text(member.fullnames,
                    margin + column * width,
                    margin + row * 100);

            doc.text(member.street,
                    margin + column * width,
                    margin + (member.fullnames.length) * (pad + fontSize) + row * 100);

            doc.text(member.zipcode + ' ' + member.city,
                    margin + column * width,
                    margin + (member.fullnames.length + 1) * (pad + fontSize) + row * 100);

            ++column;
            if (column % 2 === 0) {
                column = 0;
                ++row;
            }

            if ((row + 1) % 7 === 0) {
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
                            .append(document.createTextNode(' ' + t('spgverein', 'Download as PDF')))
                            .click(function () {
                                generatePdf(city);
                            }));
        });

        loadMembers();
    });
}

function loadMembers() {
    var groupingOption = $(".grouping-option:checked").attr('id');

    $.getJSON(OC.generateUrl('/apps/spgverein/members/' + groupingOption), function (data) {

        // Remove old elements
        $('.ui-grid-a').remove();

        var numberOfColumns = function () {
            if (window.matchMedia('(min-width: 992px)').matches) {
                return 4;
            }

            if (window.matchMedia('(min-width: 768px)').matches) {
                return 3;
            }

            if (window.matchMedia('(min-width: 576px)').matches) {
                return 2;
            }

            return 1;
        };

        var row = function (city, j, membersCount) {
            if (j % numberOfColumns() === 0) {
                return $('<div/>')
                        .addClass('ui-grid-a')
                        .appendTo($('h1[id="' + city + '"]').parent());
            } else {
                return $('h1[id="' + city + '"]')
                        .parent()
                        .find('.ui-grid-a')
                        .last();
            }
        };

        var members = new Members(data);
        $.each(members.getCities(), function (i, city) {
            var cityMembers = members.getMembersOf(city);

            $.each(cityMembers, function (j, member) {
                var address = $('<div/>')
                        .addClass('ui-block-a');

                $.each(member.fullnames, function (j, fullname) {
                    $('<p/>').appendTo(address)
                            .append(fullname);
                });
                $('<p/>').appendTo(address)
                        .append(member.street);
                $('<p/>').appendTo(address)
                        .append(member.zipcode + ' ' + member.city);

                row(city, j, cityMembers.length).append(address);
            });


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
});