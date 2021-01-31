<template>
	<AppSidebar v-show="show" :title="title" @close="clearLabelPrintingFilter">
		<template v-if="!memberSelectionOnly">
			<input id="group-by-checkbox"
				type="checkbox"
				:checked="membersAreGroupedByRelatedMemberId"
				class="checkbox link-checkbox"
				@input="groupMembersInput">
			<label for="group-by-checkbox" class="link-checkbox-label">
				Gruppieren über Mitgliedsnummer
			</label>

			<br>

			<input id="resigned-members-checkbox"
				type="checkbox"
				:checked="includeResignedMembers"
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
					<select id="label-format-select"
						:value="selectedLabelFormat"
						class="text-box-input"
						@input="selectedLabelFormatInput">
						<option v-for="(item, index) in labelFormats"
							:key="item"
							:value="item.id"
							:selected="index === selectedLabelFormat">
							{{ index }} ({{ item.size }}, {{ item.rows }}&#215;{{ item.columns }})
						</option>
					</select>
				</div>
				<div style="width: 49%; float: right;">
					<label for="label-offset" class="text-box-label">
						Anfang leere Etiketten
					</label>
					<input id="label-offset"
						:value="labelOffset"
						type="number"
						min="0"
						:max="maxLabelOffset"
						step="1"
						class="text-box-input"
						@input="labelOffsetInput">
				</div>
			</div>
		</div>

		<object :data="printLabelsUrl" type="application/pdf" class="labels-preview">
			<embed :src="printLabelsUrl" type="application/pdf">
		</object>

		<p>
			<ActionLink icon="icon-download" :href="printLabelsUrl" target="_blank">
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
import debounce from 'debounce';
import AppSidebar from '@nextcloud/vue/dist/Components/AppSidebar';
import ActionLink from '@nextcloud/vue/dist/Components/ActionLink';
import { mapActions, mapGetters } from 'vuex';

export default {
	components: {
		ActionLink,
		AppSidebar,
	},

	computed: {
		...mapGetters(['printLabelsUrl',
			'addressLine',
			'labelFormats',
			'labelOffset',
			'maxLabelOffset',
			'membersAreGroupedByRelatedMemberId',
			'includeResignedMembers',
			'memberSelectionOnly']),

		title() {
			if (this.memberSelectionOnly) {
				return 'Einzelettiket drucken';
			} else {
				return 'Etiketten drucken';
			}
		},

		show() {
			return this.printLabelsUrl != null;
		},
	},

	methods: {
		...mapActions(['clearLabelPrintingFilter',
			'updateAddressLine',
			'updateLabelOffset',
			'groupMembersByRelatedMemberId',
			'filterResignedMembers',
			'updateSelectedLabelFormat']),

		addressLineInput: debounce(function(e) {
			this.updateAddressLine(e.target.value);
		}, 750),

		labelOffsetInput: debounce(function(e) {
			this.updateLabelOffset(e.target.value);
		}, 500),

		groupMembersInput: debounce(function(e) {
			const checked = e.target.checked;
			this.groupMembersByRelatedMemberId(checked != null && checked);
		}, 500),

		resignedMembersInput: debounce(function(e) {
			const checked = e.target.checked;
			this.filterResignedMembers(checked != null && checked);
		}, 500),

		selectedLabelFormatInput: debounce(function(e) {
			const formatId = Object.keys(this.labelFormats)[e.target.selectedIndex];
			this.updateSelectedLabelFormat(formatId);
		}, 500),
	},
};
</script>
