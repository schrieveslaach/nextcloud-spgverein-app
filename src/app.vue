<template>
	<Content app-name="spgverein">
		<AppNavigation>
			<ul id="app-spgverein-navigation">
				<AppNavigationItem
					title="Bestände"
					icon="icon-folder"
					:allow-collapse="false"
					:loading="isLoadingClubList">
					<template>
						<AppNavigationItem v-for="club in clubs"
							:key="club"
							:title="club"
							:icon="club === selectedClub ? 'icon-category-enabled' : null"
							:loading="club === selectedClub && isLoadingMembers"
							@click="selectedClub = club" />
						<!-- TODO: without the following tag there is nothing in the navigation bar -->
						<AppNavigationItem title="AppNavigationItemChild2" style="display: none" />
					</template>
				</AppNavigationItem>
			</ul>
		</AppNavigation>

		<AppContent :class="{ 'wait-for-data': isLoadingMembers }">
			<div v-if="hasMembers" class="action-buttons">
				<button @click="exportAsOdt">
					Export
				</button>
				<button @click="showPrinting = !showPrinting">
					Etiketten drucken
				</button>
			</div>

			<Members v-if="hasMembers" />
			<div v-else-if="isReady"
				class="no-data">
				Keine Vereinsdaten vorhanden
			</div>
		</AppContent>

		<Labels v-if="isReady && showPrinting"
			v-show="showPrinting"
			title="Etiketten drucken"
			@close="showPrinting=false" />
	</Content>
</template>

<style scoped>
.wait-for-data::after {
	z-index: 2;
	content: '';
	height: 28px;
	width: 28px;
	margin: -16px 0 0 -16px;
	position: absolute;
	top: 50%;
	left: 50%;
	border-radius: 100%;
	-webkit-animation: rotate 0.8s infinite linear;
	animation: rotate 0.8s infinite linear;
	-webkit-transform-origin: center;
	-ms-transform-origin: center;
	transform-origin: center;
	border: 2px solid var(--color-loading-light);
	border-top-color: var(--color-loading-dark);
}

.no-data {
	text-align: center;
	padding-top: 1rem;
}

.action-buttons {
	position: absolute;
	right: 0;
}
</style>

<script>
import { generateUrl } from '@nextcloud/router';
import AppContent from '@nextcloud/vue/dist/Components/AppContent';
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation';
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem';
import Content from '@nextcloud/vue/dist/Components/Content';
import Labels from './labels.vue';
import Members from './members.vue';
import { mapGetters, mapActions } from 'vuex';

export default {

	components: {
		AppContent,
		AppNavigation,
		AppNavigationItem,
		Content,
		Labels,
		Members,
	},
	data() {
		return {
			selectedClub: '',
			printAllLabels: false,
			showPrinting: false,
		};
	},

	computed: {
		...mapGetters(['clubs', 'members', 'cities']),

		isReady() {
			return !this.isLoadingClubList && !this.isLoadingMembers;
		},

		isLoadingClubList() {
			return this.clubs == null;
		},

		isLoadingMembers() {
			return this.members == null;
		},

		hasMembers() {
			return this.members != null && this.members.length > 0;
		},

		exportUrl() {
			return generateUrl(`/apps/spgverein/files/${this.selectedClub}.ods`);
		},
	},

	watch: {
		clubs(newClubs) {
			this.selectedClub = newClubs[0];
		},

		selectedClub(club) {
			if (club != null) {
				this.openClub(club);
			}
		},
	},

	mounted() {
		this.fetchClubs();
		OC.Search = new OCA.Search(this.filterByName, this.clearNameFilter);
	},

	methods: {
		...mapActions(['fetchClubs', 'openClub', 'filterByName', 'clearNameFilter']),

		exportAsOdt() {
			window.open(this.exportUrl);
		},
	},
};
</script>
