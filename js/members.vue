<template>
    <div style="width: 100%">
        <section class="city" v-for="city in cities">
            <div class="city-header">
                <h2> {{ city }} </h2>
                <a class="button" v-on:click="openLabelsDialog(city)">
                    <font-awesome-icon icon="print"/>
                </a>
            </div>

            <div class="members">
                <member :club="club" v-bind:member="member" v-for="member in getMembersOf(city)"></member>
            </div>
        </section>

        <modal v-if="showModal" v-on:close="closeLabelsDialog()">
            <template slot="header">
                Etiketten – {{ selectedCityForLabels }}
            </template>

            <template slot="body">
                <div class="label-parameters">
                    <div style="white-space:nowrap">
                        <input class="marker-checkbox" id="group-by-checkbox" type="checkbox" @input="groupMembersInput"
                               :checked="groupMembers">
                        <label class="marker-label" for="group-by-checkbox">
                            Gruppieren über Mitgliedsnummer
                        </label>
                    </div>

                    <br>
                    <input placeholder="Adresszeile" @input="addressLineInput" :value="addressLine">
                </div>

                <p>
                    &nbsp;
                </p>

                <object :data="labelsUrl" type="application/pdf" class="labels">
                    <embed :src="labelsUrl" type="application/pdf"/>
                </object>

                <p>
                    &nbsp;
                </p>

                <p>
                    <a class="button" :href="labelsUrl" target="_blank" style="float: right">
                        <font-awesome-icon icon="file"/>
                        Download
                    </a>
                </p>
            </template>
        </modal>
    </div>
</template>

<script>
    import Member from './member.vue';
    import debounce from 'debounce';

    export default {
        data() {
            return {
                selectedCityForLabels: null,
                addressLine: '',
                groupMembers: false
            }
        },

        mounted() {
            if (localStorage.addressLine) {
                this.addressLine = localStorage.addressLine;
            }
            if (localStorage.groupMembers) {
                this.groupMembers = localStorage.groupMembers;
            }
        },

        components: {
            member: Member
        },

        computed: {
            showModal() {
                return this.selectedCityForLabels != null;
            },

            labelsUrl() {
                let params = {};
                if (this.addressLine.length > 0) {
                    params.addressLine = this.addressLine;
                }
                if (this.groupMembers) {
                    params.groupMembers = this.groupMembers;
                }

                const query = Object.keys(params)
                    .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
                    .join('&');

                return OC.generateUrl(`/apps/spgverein/labels/${this.club}/${this.selectedCityForLabels}?${query}`);
            }
        },

        props: {
            members: {
                type: Array
            },
            cities: {
                type: Array
            },
            club: {
                type: String
            }
        },

        methods: {
            getMembersOf(city) {
                return this.members.filter(member => member.city === city);
            },

            openLabelsDialog(city) {
                this.selectedCityForLabels = city;
            },
            closeLabelsDialog() {
                this.selectedCityForLabels = null;
            },

            addressLineInput: debounce(function (e) {
                this.addressLine = e.target.value;
            }, 750),

            groupMembersInput: debounce(function (e) {
                const checked = e.target.checked;
                this.groupMembers = checked != null && checked;
            }, 750)
        },

        watch: {
            addressLine(newAddressLine) {
                localStorage.addressLine = newAddressLine;
            },

            groupMembers(newGroupMembers) {
                localStorage.groupMembers = newGroupMembers;
            }
        }
    }
</script>