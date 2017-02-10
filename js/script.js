$(document).ready(function () {

    var authorUrl = OC.generateUrl('/apps/spgverein/members');
    $.getJSON('members', function (data) {
        var orderByCity = new Map();

        $.each(data, function (i, member) {
            var groupedByAddress = orderByCity.get(member.city);
            if (groupedByAddress === undefined) {
                groupedByAddress = new Map();
                orderByCity.set(member.city, groupedByAddress);
            }

            var address = member.street + "\n" + member.zipcode + "\n" + member.city;

            var groupedMembers = groupedByAddress.get(address);
            if (groupedMembers === undefined) {
                groupedMembers = [];
            }
            groupedMembers.push(member);

            groupedByAddress.set(address, groupedMembers);
        });



        for (var [key, groupedByAddress] of orderByCity) {
            var city = $('<h1 class="city"/>').appendTo($('#members'));
            city.append(document.createTextNode(key));

            for (var [key, members] of groupedByAddress) {
                var address = $('<div/>').appendTo($('#members'));
                address.attr('class', "address");

                var lastname = $('<p/>').appendTo(address);
                var names = $('<p/>').appendTo(address);
                var street = $('<p/>').appendTo(address);
                var zipCodeAndCity = $('<p/>').appendTo(address);
                $.each(members, function (i, member) {
                    if (i === 0) {
                        lastname.append(document.createTextNode(member.lastname));
                        street.append(document.createTextNode(member.street));
                        zipCodeAndCity.append(document.createTextNode(member.zipcode + ' ' + member.city));
                    }

                    var prefix = "";
                    if (members.length > 1) {
                        if (i === members.length - 1) {
                            prefix = " und ";
                        } else if (i > 0) {
                            prefix = ", ";
                        }
                    }

                    var name = prefix + member.firstname;
                    names.append(document.createTextNode(name));
                });
            }
        }
    });
});