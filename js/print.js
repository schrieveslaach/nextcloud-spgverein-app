function printMembersOfCity(city) {
    var groupingOption = $(".grouping-option:checked").attr('id');
    $.getJSON(OC.generateUrl('/apps/spgverein/members/' + groupingOption), function (members) {

        var wrapper = new Members(members);
        wrapper.filter(function (member) {
            if (city !== undefined && member.city !== city) {
                return false;
            }

            if (!$('#member-' + member.id + ' .address-block-checkbox').is(':checked')) {
                return false;
            }

            return true;
        });

        var labels = $('<div/>');

        $('<link>')
                .attr('href', '/apps/spgverein/css/print.css')
                .attr('rel', 'stylesheet')
                .attr('type', 'text/css')
                .appendTo(labels);

        var pageDiv = function (i) {
            if (i % 12 === 0) {
                // create new page
                $('<div>')
                        .addClass('page')
                        .appendTo(labels);
            }

            return labels.find('.page').last();
        };

        var rowDiv = function (i) {
            var page = pageDiv(i);

            if (i % 2 === 0) {
                // create new row
                $('<div>')
                        .addClass('row')
                        .appendTo(page);
            }

            return page.find('.row').last();
        };

        $.each(wrapper.data, function (i, member) {

            var address = $('<div/>')
                    .addClass('address')
                    .appendTo(rowDiv(i));

            // TODO: make sure to make this value configurable
            $('<p/>').appendTo(address)
                    .append('TuS Mützenich 1921 e.V. • Am Nassenhof 4 • 52156 Monschau')
                    .addClass('sender');

            $.each(member.fullnames, function (j, fullname) {
                $('<p/>').appendTo(address)
                        .append(fullname);
            });
            $('<p/>').appendTo(address)
                    .append(member.street);
            $('<p/>').appendTo(address)
                    .append(member.zipcode + ' ' + member.city)
                    .addClass('city-line');
        });

        var printWindow = window.open();
        printWindow.document.write(labels.html());
        printWindow.document.close();
        printWindow.print();
    });
}