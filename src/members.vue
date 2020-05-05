<template>
	<div style="width: 100%">
		<div class="city-filter">
			<Multiselect
				v-model="selectedCities"
				style="width: 100%"
				:options="cities"
				:multiple="true"
				placeholder="Ort" />
		</div>
		<table>
			<colgroup>
				<col style="width:30%">
				<col style="width:25%">
				<col style="width:20%">
				<col style="width:25%">
			</colgroup>
			<thead>
				<tr>
					<th>Name</th>
					<th>Adresse</th>
					<th>Datümer</th>
					<th>Anhänge</th>
				</tr>
			</thead>
			<transition-group tag="tbody" name="table-row">
				<Member v-for="member in members"
					:key="member.id"
					:club="club"
					:member="member" />
			</transition-group>
		</table>
	</div>
</template>

<style scoped>
.table-row-enter-active, .table-row-leave-active {
  transition: opacity .5s;
}
.table-row-enter, .table-row-leave-to {
  opacity: 0;
}

.city-filter {
	margin-top: 5rem;
}
table {
    width: 100%;
    border-collapse: collapse;
	margin-top: 1rem;
}

@media only screen and (min-width: 761px) {
	th:nth-of-type(1) {
		padding-left: 10px;
	}

	th {
		color: var(--color-text-maxcontrast);
		border-bottom: 1px solid var(--color-border);
	}
}

@media only screen and (max-width: 760px) {

    /* Force table to not be like tables anymore */
    table, thead, tbody, th, tr {
        display: block;
    }

    /* Hide table headers (but not display: none;, for accessibility) */
    thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    tr {
        border: 1px solid var(--color-background-darker);
    }
}
</style>

<script>
import Member from './member.vue';
import Multiselect from '@nextcloud/vue/dist/Components/Multiselect';
import { mapGetters, mapActions } from 'vuex';

export default {

	components: {
		Member,
		Multiselect,
	},

	data() {
		return {
			selectedCities: [],
			nameFilter: null,
		};
	},

	computed: {
		...mapGetters(['members', 'cities', 'club']),
	},

	watch: {
		selectedCities(selectedCities) {
			this.filterMembersByCities(selectedCities);
		},

		addressLine(newAddressLine) {
			localStorage.addressLine = newAddressLine;
		},

		groupMembers(newGroupMembers) {
			localStorage.groupMembers = newGroupMembers;
		},

		selectedLabelFormat(newSelectedLabelFormat) {
			localStorage.selectedLabelFormat = newSelectedLabelFormat;
		},
	},

	methods: {
		...mapActions(['filterMembersByCities']),
	},
};
</script>
