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
				<col style="width:50%">
				<col style="width:25%">
				<col style="width:25%">
			</colgroup>
			<thead>
				<tr>
					<th>Name & Adresse</th>
					<th>Daten</th>
					<th>Anhänge</th>
				</tr>
			</thead>
			<transition-group tag="tbody" name="table-row">
				<Member v-for="member in members"
					:key="member.id"
					:ref="member.id"
					:highlighted="member.id == highlightedMember"
					:club="club"
					:member="member" />
			</transition-group>
		</table>
	</div>
</template>

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
		...mapGetters(['members', 'cities', 'club', 'highlightedMember']),
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

		highlightedMember(newHighlightedMember) {
			if (newHighlightedMember != null) {
				const el = this.$refs[newHighlightedMember][0];
				this.$nextTick(() => {
					el.$el.scrollIntoView({
						behavior: 'smooth',
						block: 'center',
					});
				});
			}
		},
	},

	mounted() {
		if (this.highlightedMember != null) {
			const el = this.$refs[this.highlightedMember][0];
			this.$nextTick(() => {
				el.$el.scrollIntoView({
					behavior: 'smooth',
					block: 'center',
				});
			});
		}
	},

	methods: {
		...mapActions(['filterMembersByCities']),
	},
};
</script>

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
