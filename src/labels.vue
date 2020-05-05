<template>
	<AppSidebar title="Etiketten drucken" @close="close">
		<template #primary-actions>
			<input id="group-by-checkbox"
				type="checkbox"
				:checked="groupMembers"
				class="checkbox link-checkbox"
				@input="groupMembersInput">
			<label for="group-by-checkbox" class="link-checkbox-label">
				Gruppieren über Mitgliedsnummer
			</label>

			<input id="resigned-members-checkbox"
				type="checkbox"
				:checked="resignedMembers"
				class="checkbox link-checkbox"
				@input="resignedMembersInput">
			<label for="resigned-members-checkbox" class="link-checkbox-label">
				Ausgetretene Mitglieder
			</label>
		</template>

		<div class="label-parameters">
			<div>
				<label for="addressline-input" class="text-box-label">
					Adresszeile
				</label>
				<input id="addressline-input"
					placeholder="Adresszeile"
					:value="addressLine"
					class="text-box-input"
					@input="addressLineInput">
			</div>

			<div>
				<div style="width: 49%; float: left;">
					<label for="label-format-select" class="text-box-label">
						Etikettenformat
					</label>
					<select id="label-format-select" v-model="selectedLabelFormat" class="text-box-input">
						<option v-for="(item, index) in labelFormats"
							:key="item"
							:value="item.id"
							:selected="index === 0">
							{{ item.id }} ({{ item.size }}, {{ item.rows }}&#215;{{ item.columns }})
						</option>
					</select>
				</div>
				<div style="width: 49%; float: right;">
					<label for="label-offset" class="text-box-label">
						Anfang leere Etiketten
					</label>
					<input id="label-offset"
						v-model="labelOffset"
						type="number"
						min="0"
						:max="maxLabelOffset"
						step="1"
						class="text-box-input">
				</div>
			</div>
		</div>

		<object :data="labelsUrl" type="application/pdf" class="labels-preview">
			<embed :src="labelsUrl" type="application/pdf">
		</object>

		<p>
			<ActionLink icon="icon-download" :href="labelsUrl" target="_blank">
				Download
			</ActionLink>
		</p>
	</AppSidebar>
</template>

<style scoped>

.labels-preview {
    margin-top: 25px;
	margin-left: 10px;
	margin-right: 10px;
    min-height: 250px;
	width: 95%;
}

.label-parameters {
    display: flex;
    flex-direction: column;
	padding-top: 1.5rem;
	padding-right: 0.3rem;
	padding-left: 0.3rem;
}

.text-box-label {
    display: block;
}

.text-box-input {
    width: 100%;
}
</style>

<script>
import { generateUrl } from '@nextcloud/router';
import debounce from 'debounce';
import ActionLink from '@nextcloud/vue/dist/Components/ActionLink';
import AppSidebar from '@nextcloud/vue/dist/Components/AppSidebar';
import { mapGetters } from 'vuex';

export default {

	components: {
		ActionLink,
		AppSidebar,
	},

	data() {
		return {
			addressLine: '',
			groupMembers: false,
			resignedMembers: false,
			labelFormatData: {},
			selectedLabelFormat: null,
			labelOffset: 0,
		};
	},

	computed: {
		...mapGetters(['club', 'cities', 'selectedCities']),

		maxLabelOffset() {
			if (this.labelFormatData == null || this.selectedLabelFormat == null) {
				return 0;
			}

			const format = this.labelFormatData[this.selectedLabelFormat];
			return format.columns * format.rows - 1;
		},

		labelFormats() {
			return Object.keys(this.labelFormatData)
				.map(id => ({ id, ...this.labelFormatData[id] }));
		},

		labelsUrl() {
			const params = {};
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

			params.cities = this.selectedCities
				.map(v => encodeURIComponent(v))
				.join(',');

			const query = Object.keys(params)
				.map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
				.join('&');

			return generateUrl(`/apps/spgverein/labels/${this.club}?${query}`);
		},
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
		},
	},

	mounted() {
		if (localStorage.addressLine) {
			this.addressLine = localStorage.addressLine;
		}
		if (localStorage.groupMembers) {
			this.groupMembers = localStorage.groupMembers;
		}

		fetch(generateUrl('/apps/spgverein/labels/formats'))
			.then(response => response.json())
			.then(formats => {
				this.labelFormatData = formats;

				if (localStorage.selectedLabelFormat != null) {
					this.selectedLabelFormat = localStorage.selectedLabelFormat;
				}
			});
	},

	methods: {
		close() {
			this.$emit('close');
		},

		addressLineInput: debounce(function(e) {
			this.addressLine = e.target.value;
		}, 750),

		groupMembersInput: debounce(function(e) {
			const checked = e.target.checked;
			this.groupMembers = checked != null && checked;
		}, 750),

		resignedMembersInput: debounce(function(e) {
			const checked = e.target.checked;
			this.resignedMembers = checked != null && checked;
		}, 750),
	},
};
</script>
