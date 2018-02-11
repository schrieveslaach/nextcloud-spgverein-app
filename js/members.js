function Members(data) {
    this.data = data;

    if (this.data instanceof Array) {
        this.data.sort(function (m1, m2) {
            var regex = /(.*)\s+((\d+)\s*([a-z])?)/;

            var cmp = m1.city.localeCompare(m2.city);
            if (cmp === 0) {

                var str1 = m1.street.match(regex);
                var str2 = m2.street.match(regex);

                cmp = str1[1].localeCompare(str2[1]);
                if (cmp === 0) {
                    var a = parseInt(str1[3]);
                    var b = parseInt(str2[3]);

                    if (a < b)
                        cmp = -1;
                    else if (a > b)
                        cmp = 1;
                    else
                        cmp = 0;
                }
            }
            return cmp;
        });
    }
}

Members.prototype.filter = function (filterFunction) {
    this.data = $.grep(this.data, function (member, i) {
        return filterFunction(member);
    });
};

Members.prototype.getMembersOf = function (city) {
    var members = [];
    $.each(this.data, function (i, member) {
        if (member.city === city) {
            members.push(member);
        }
    });
    return members;
};

Members.prototype.getCities = function () {
    var cities = new Set();

    $.each(this.data, function (i, member) {
        cities.add(member.city);
    });

    return Array.from(cities);
};

