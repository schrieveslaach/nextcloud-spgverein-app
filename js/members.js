function Members(data) {
    this.data = data;

    if (data instanceof Array) {
        this.data.sort(function (m1, m2) {
            var cmp = m1.city.localeCompare(m2.city);
            if (cmp === 0) {
                cmp = m1.fullnames[0].localeCompare(m2.fullnames[0]);
            }
            return cmp;
        });
    }
}

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

