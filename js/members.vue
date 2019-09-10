<template>
    <div style="width: 100%">
        <template v-for="city in cities">
            <section class="city" v-if="getMembersOf(city).length > 0">
                <div class="city-header">
                    <h2> {{ city }} </h2>
                    <a class="button" v-on:click="openLabelsDialog(city)">
                        <font-awesome-icon icon="print"/>
                    </a>
                </div>

                <table>
                    <colgroup>
                        <col style="width:25%">
                        <col style="width:20%">
                        <col style="width:5%">
                        <col style="width:20%">
                        <col style="width:30%">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Straße</th>
                        <th>Postleitzahl</th>
                        <th>Ort</th>
                        <th>Datümer</th>
                        <th>Anhänge</th>
                    </tr>
                    </thead>
                    <tbody>
                    <member :club="club" v-bind:member="member" v-for="member in getMembersOf(city)" :key="member.id"/>
                    </tbody>
                </table>
            </section>
        </template>

        <labels-modal :club="club" :cities="[selectedCityForLabels]" v-if="showModal" @close="closeLabelsDialog" />
    </div>
</template>

<script>
    import Member from './member.vue';
    import LabelsModal from './labels-modal.vue';

    export default {
        data() {
            return {
                selectedCityForLabels: null,
            }
        },

        components: {
            Member,
            LabelsModal
        },

        computed: {
            showModal() {
                return this.selectedCityForLabels != null;
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
            }
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