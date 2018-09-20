import jsPDF from 'jspdf';

import Members from './members.vue';

import {library} from '@fortawesome/fontawesome-svg-core'
import {faPrint} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

library.add(faPrint);

const Vue = require('./node_modules/vue/dist/vue.js');
Vue.component('font-awesome-icon', FontAwesomeIcon)

new Vue({
    el: '#app-content-wrapper',
    data: {
        cities: [],
        members: [],
        groupingOption: 'none'
    },
    components: {
        'members': Members
    },
    template: '<div id="app-content-wrapper"><members v-bind:members="members" v-bind:cities="cities" v-on:print="print"></members></div>',
    methods: {
        print(e) {
            const members = this.members.filter(member => {
                if (!e.city) {
                    return true;
                }
                return member.city === e.city;
            });

            generatePdf(members);
        },

        fetchMembers() {
            fetch(OC.generateUrl(`/apps/spgverein/members/${this.groupingOption}`,))
                .then(response => response.json())
                .then(members => {
                    const regex = /(.*)\s+((\d+)\s*([a-z])?)/;

                    return members.sort((m1, m2) => {
                        let cmp = m1.city.localeCompare(m2.city);
                        if (cmp === 0) {

                            const str1 = m1.street.match(regex);
                            const str2 = m2.street.match(regex);

                            cmp = str1[1].localeCompare(str2[1]);
                            if (cmp === 0) {
                                const a = parseInt(str1[3]);
                                const b = parseInt(str2[3]);

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
                })
                .then(members => this.members = members);
        }
    },

    created() {
        fetch(OC.generateUrl('/apps/spgverein/cities'))
            .then(response => response.json())
            .then(cities => this.cities = cities);

        this.fetchMembers();
    },

    mounted() {
        const self = this;
        document.getElementById("groupmembers-checkbox").onchange = e => {
            if (!e.target.checked) {
                self.groupingOption = 'none';
            } else {
                self.groupingOption = 'related-id';
            }

            self.fetchMembers();
        };
    },


});

const labelFormat = {
    width: 105,
    height: 48.5,
    columns: 2,
    rows: 6
};

function generatePdf(members) {
    const fontSize = 10;

    const doc = new jsPDF();
    doc.setFontSize(fontSize);

    let column = 0;
    let row = 0;

    members.forEach(member => {
        let rowOffset = 0.5 * fontSize;

        member.fullnames.forEach(name => {
            doc.text(name,
                column * labelFormat.width + 1,
                row * labelFormat.height + rowOffset);
            rowOffset += fontSize;
        });

        doc.text(member.street,
            column * labelFormat.width + 1,
            row * labelFormat.height + rowOffset);
        rowOffset += fontSize;

        doc.text(member.zipcode + ' ' + member.city,
            column * labelFormat.width + 1,
            row * labelFormat.height + rowOffset);
        rowOffset += fontSize;

        if (++column % labelFormat.columns === 0) {
            column = 0;
            ++row;
        }

        if (row > 0 && row % labelFormat.rows === 0) {
            row = 0;
            doc.addPage();
        }
    });

    doc.save('labels.pdf');
}