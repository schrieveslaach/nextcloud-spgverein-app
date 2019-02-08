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
                    <div>
                        <label for="group-by-checkbox" style="word-wrap:break-word">
                            <input id="group-by-checkbox" type="checkbox" @input="groupMembersInput"
                                   :checked="groupMembers" style="vertical-align: middle;">
                            Gruppieren über Mitgliedsnummer
                        </label>
                    </div>

                    <div>
                        <label for="addressline-input" class="text-box-label">
                            Adresszeile
                        </label>
                        <input placeholder="Adresszeile" id="addressline-input" @input="addressLineInput"
                               :value="addressLine" class="text-box-input">
                    </div>

                    <div>
                        <div style="width: 49%; float: left;">
                            <label for="label-format-select" class="text-box-label">
                                Etikettenformat
                            </label>
                            <select id="label-format-select" v-model="selectedLabelFormat" class="text-box-input">
                                <option v-for="(item, index) in labelFormats" :value="item.id" :selected="index === 0">
                                    {{item.id}} ({{item.size}}, {{item.rows}}&#215;{{item.columns}})
                                </option>
                            </select>
                        </div>
                        <div style="width: 49%; float: right;">
                            <label for="label-offset" class="text-box-label">
                                Anfang leere Etiketten
                            </label>
                            <input type="number" min="0" v-model="labelOffset" :max="maxLabelOffset" step="1"
                                   id="label-offset" class="text-box-input">
                        </div>
                    </div>
                </div>

                <object :data="labelsUrl" type="application/pdf" class="labels">
                    <embed :src="labelsUrl" type="application/pdf"/>
                </object>

                <p style="position: absolute; top: 0; right: 0;">
                    <a class="button" :href="labelsUrl" target="_blank" style="float: right">
                        <font-awesome-icon icon="download"/>
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
                groupMembers: false,
                labelFormatData: {},
                selectedLabelFormat: null,
                labelOffset: 0
            }
        },

        mounted() {
            if (localStorage.addressLine) {
                this.addressLine = localStorage.addressLine;
            }
            if (localStorage.groupMembers) {
                this.groupMembers = localStorage.groupMembers;
            }

            fetch(OC.generateUrl(`/apps/spgverein/labels/formats`))
                .then(response => response.json())
                .then(formats => {
                    this.labelFormatData = formats;

                    if (localStorage.selectedLabelFormat != null) {
                        this.selectedLabelFormat = localStorage.selectedLabelFormat;
                    }
                });
        },

        components: {
            member: Member
        },

        computed: {
            maxLabelOffset() {
                if (this.labelFormatData == null || this.selectedLabelFormat == null) {
                    return 0;
                }

                const format = this.labelFormatData[this.selectedLabelFormat];
                return format.columns * format.rows - 1;
            },

            labelFormats() {
                return Object.keys(this.labelFormatData)
                    .map(id => ({id, ...this.labelFormatData[id]}))
            },

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
                if (this.selectedLabelFormat != null) {
                    params.format = this.selectedLabelFormat;
                }
                if (this.labelOffset > 0) {
                    params.offset = this.labelOffset;
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
            },

            selectedLabelFormat(newSelectedLabelFormat) {
                localStorage.selectedLabelFormat = newSelectedLabelFormat;
            }
        }
    }
</script>