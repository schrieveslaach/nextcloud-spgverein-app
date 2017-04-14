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
                            .append($('<i class="fa fa-print" aria-hidden="true"></i> '))
                            .append(document.createTextNode(' ' + t('spgverein', 'Print')))
                            .click(function () {
                                printMembersOfCity(city);
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