<template>
    <modal @close="close()">
        <template slot="header">
            Etiketten<span v-if="cities.length === 1">– {{ cities[0] }}</span>
        </template>

        <template slot="body">
            <div class="label-parameters">
                <div>
                    <label for="group-by-checkbox" style="word-wrap:break-word">
                        <input id="group-by-checkbox" type="checkbox" @input="groupMembersInput"
                               :checked="groupMembers" style="vertical-align: middle;">
                        Gruppieren über Mitgliedsnummer
                    </label>
					<label for="resigned-members-checkbox" style="word-wrap:break-word">
						<input id="resigned-members-checkbox" type="checkbox" @input="resignedMembersInput"
							   :checked="resignedMembers" style="vertical-align: middle;">
						Ausgetretene Mitglieder
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
</template>

<script>
    import debounce from 'debounce';

    export default {
        data() {
            return {
                addressLine: '',
                groupMembers: false,
				resignedMembers: false,
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

            labelsUrl() {
                let params = {};
                if (this.addressLine.length > 0) {
                    params.addressLine = this.addressLine;
                }
                if (this.groupMembers) {
                    params.groupMembers = this.groupMembers;
                }
                if (this.resignedMembers) {
					params.resignedMembers = this.resignedMembers;
				}
                if (this.selectedLabelFormat != null) {
                    params.format = this.selectedLabelFormat;
                }
                if (this.labelOffset > 0) {
                    params.offset = this.labelOffset;
                }
                if (this.cities.length > 0) {
                    params.cities = this.cities
                        .map(v => encodeURIComponent(v))
                        .join(',');
                }

                const query = Object.keys(params)
                    .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
                    .join('&');

                return OC.generateUrl(`/apps/spgverein/labels/${this.club}?${query}`);
            }
        },

        props: {
            cities: {
                type: Array
            },
            club: {
                type: String
            }
        },

        methods: {
            close() {
                this.$emit('close')
            },

            addressLineInput: debounce(function (e) {
                this.addressLine = e.target.value;
            }, 750),

            groupMembersInput: debounce(function (e) {
                const checked = e.target.checked;
                this.groupMembers = checked != null && checked;
            }, 750),

			resignedMembersInput: debounce(function (e) {
				const checked = e.target.checked;
				this.resignedMembers = checked != null && checked;
			}, 750)
        },

        watch: {
            addressLine(newAddressLine) {
                localStorage.addressLine = newAddressLine;
            },

            groupMembers(newGroupMembers) {
                localStorage.groupMembers = newGroupMembers;
            },

			resignedMembers(newResignedMembers) {
				localStorage.resignedMembers = newResignedMembers;
			},

            selectedLabelFormat(newSelectedLabelFormat) {
                localStorage.selectedLabelFormat = newSelectedLabelFormat;
            }
        }
    }
</script>
