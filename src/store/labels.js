import { generateUrl } from '@nextcloud/router';

export const getters = {
	addressLine: state => state.addressLine,
	labelFormats: state => state.labelFormats,
	labelOffset: state => state.labelOffset,
	selectedLabelFormat: state => state.selectedLabelFormat,

	membersAreGroupedByRelatedMemberId: state => state.membersAreGroupedByRelatedMemberId,
	includeResignedMembers: state => state.includeResignedMembers,

	maxLabelOffset: state => {
		if (state.labelFormat == null || state.selectedLabelFormat == null) {
			return 0;
		}

		const format = state.labelFormat[state.selectedLabelFormat];
		return format.columns * format.rows - 1;
	},

	memberSelectionOnly: state => state.memberIds.length > 0,

	printLabelsUrl: (state, getters) => {
		if (state.memberIds.length === 0 && !state.printSelectedCities) {
			return null;
		}

		const params = {};
		if (state.addressLine.length > 0) {
			params.addressLine = state.addressLine;
		}

		if (state.selectedLabelFormat != null) {
			params.format = state.selectedLabelFormat;
		}

		if (state.labelOffset > 0) {
			params.offset = state.labelOffset;
		}

		if (getters.memberSelectionOnly) {
			params.members = state.memberIds
				.map(v => encodeURIComponent(v))
				.join(',');
		} else {
			if (state.membersAreGroupedByRelatedMemberId) {
				params.groupMembers = state.membersAreGroupedByRelatedMemberId;
			}

			if (state.includeResignedMembers) {
				params.resignedMembers = state.includeResignedMembers;
			}

			params.cities = getters.selectedCities
				.map(v => encodeURIComponent(v))
				.join(',');
		}

		const query = Object.keys(params)
			.map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
			.join('&');

		return generateUrl(`/apps/spgverein/labels/${getters.club.id}?${query}`);
	},
};

export const mutations = {

	updateLabelPrintFilter(state, { memberIds }) {
		state.memberIds = memberIds;
		state.printSelectedCities = false;
	},

	clearLabelPrintFilter(state) {
		state.memberIds = [];
		state.printSelectedCities = false;
	},

	setAddressLine(state, addressLine) {
		state.addressLine = addressLine;
	},

	setLabelOffset(state, labelOffset) {
		state.labelOffset = labelOffset;
	},

	setMembersAreGroupedByRelatedMemberId(state, membersAreGroupedByRelatedMemberId) {
		state.membersAreGroupedByRelatedMemberId = membersAreGroupedByRelatedMemberId;
	},

	setIncludeResignedMembers(state, includeResignedMembers) {
		state.includeResignedMembers = includeResignedMembers;
	},

	setPrintSelectedCities(state, printSelectedCities) {
		state.printSelectedCities = printSelectedCities;
		state.memberIds = [];
	},

	setSelectedLabelFormat(state, selectedLabelFormat) {
		if (Object.keys(state.labelFormats).indexOf(selectedLabelFormat) >= 0) {
			state.selectedLabelFormat = selectedLabelFormat;
		}
	},
};

export const actions = {
	selectMembersToPrint({ commit }, memberIds) {
		commit('updateLabelPrintFilter', { memberIds });
	},

	printSelectedCities({ commit }) {
		commit('setPrintSelectedCities', true);
	},

	clearLabelPrintingFilter({ commit }) {
		commit('clearLabelPrintFilter');
	},

	updateAddressLine({ commit }, addressLine) {
		commit('setAddressLine', addressLine);
	},

	updateLabelOffset({ commit }, labelOffset) {
		commit('setLabelOffset', labelOffset);
	},

	updateSelectedLabelFormat({ commit }, selectedLabelFormat) {
		commit('setSelectedLabelFormat', selectedLabelFormat);
	},

	groupMembersByRelatedMemberId({ commit }, membersAreGroupedByRelatedMemberId) {
		commit('setMembersAreGroupedByRelatedMemberId', membersAreGroupedByRelatedMemberId);
	},

	filterResignedMembers({ commit }, includeResignedMembers) {
		commit('setIncludeResignedMembers', includeResignedMembers);
	},
};

export default async function createLabelsStore() {
	const labelFormats = await fetch(generateUrl('/apps/spgverein/labels/formats'))
		.then(response => response.json());

	return {
		state: {
			memberIds: [],
			printSelectedCities: false,
			addressLine: '',
			labelFormats,
			selectedLabelFormat: null,
			labelOffset: 0,
			membersAreGroupedByRelatedMemberId: false,
			includeResignedMembers: false,
		},
		getters,
		mutations,
		actions,
	};
}
