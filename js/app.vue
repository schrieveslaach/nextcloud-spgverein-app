<template>
    <div id="app-content-wrapper">
        <div style="width: 100%; margin-bottom: 75px">
            <section class="club-selection" v-if="clubs.length > 1">
                <h3>
                    Bestand
                </h3>

                <select v-model="selectedClub">
                    <option :value="club" v-for="club in clubs">{{ club }}</option>
                </select>
            </section>

            <div class="wait-for-data" v-if="isLoading">
                <font-awesome-icon icon="spinner" size="lg" spin />
                Vereinsdaten werden geladen…
            </div>
            <members v-else v-bind:members="membersByFilter" v-bind:cities="cities" :club="selectedClub"></members>
        </div>

        <footer>
            <a class="button" :href="exportUrl" download>
                <font-awesome-icon icon="file-excel"/>
                Export
            </a>
            <a class="button" @click="showPrintAllMembers()">
                <font-awesome-icon icon="print"/>
                Etiketten aller Mitglieder drucken
            </a>
        </footer>

        <labels-modal :club="selectedClub" :cities="cities" v-if="printAllLabels" @close="closePrintAllMembers()" />
    </div>
</template>

<script>
    import Members from './members.vue';
    import LabelsModal from './labels-modal.vue';

    export default {
        data() {
            return {
                clubs: [],
                cities: [],
                members: [],
                selectedClub: '',
                nameFilter: null,
                printAllLabels: false
            };
        },

        computed: {
            isLoading() {
                return this.clubs.length === 0 || this.cities.length === 0 || this.members.length === 0;
            },

            membersByFilter() {
                if (this.nameFilter == null) {
                    return this.members;
                }

                const filter = this.nameFilter;
                return this.members.filter(member => {
                    return member.fullnames.filter(name => name.toLowerCase().indexOf(filter) !== -1).length > 0;
                })
            },

            exportUrl() {
                return OC.generateUrl(`/apps/spgverein/files/${this.selectedClub}.ods`);
            }
        },

        components: {
            Members,
            LabelsModal
        },

        methods: {
            showPrintAllMembers() {
                this.printAllLabels = true;
            },
            closePrintAllMembers() {
                this.printAllLabels = false;
            },

            filter(nameFilter) {
                this.nameFilter = nameFilter;
            },
            cleanSearch() {
                this.nameFilter = null;
            },

            fetchMembers() {
                fetch(OC.generateUrl(`/apps/spgverein/members/${this.selectedClub}`))
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
            fetch(OC.generateUrl('/apps/spgverein/clubs'))
                .then(response => response.json())
                .then(clubs => clubs.sort())
                .then(clubs => this.clubs = clubs);
        },

        mounted() {
            OC.Search = new OCA.Search(this.filter, this.cleanSearch);
            this.fetchMembers();
        },

        watch: {
            clubs(newClubs) {
                this.selectedClub = newClubs[0];
            },

            selectedClub(newSelectedClub) {
                fetch(OC.generateUrl(`/apps/spgverein/cities/${newSelectedClub}`))
                    .then(response => response.json())
                    .then(cities => this.cities = cities)
                    .then(() => this.fetchMembers());
            }
        }
    }
</script>