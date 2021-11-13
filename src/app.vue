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
							:key="club.id"
							:title="club.name"
							:to="{ name: 'Clubs', params: { id: club.id } }"
							:icon="club === selectedClub ? 'icon-category-enabled' : null"
							:loading="club === selectedClub && isLoadingMembers"
							:force-menu="true"
							@click="selectedClub = club">
							<template slot="actions">
								<ActionLink
									title="In Dateien anzeigen"
									icon="icon-folder"
									:href="club.link" />
							</template>
						</AppNavigationItem>
						<!-- TODO: without the following tag there is nothing in the navigation bar -->
						<AppNavigationItem title="AppNavigationItemChild2" style="display: none" />
					</template>
				</AppNavigationItem>
			</ul>
		</AppNavigation>

		<AppContent :class="{ 'wait-for-data': isLoadingMembers }">
			<div v-if="hasMembers && exportUrl != null" class="action-buttons">
				<button @click="exportAsOdt">
					Export
				</button>
				<button @click="printSelectedCities">
					Etiketten drucken
				</button>
			</div>

			<Members v-if="hasMembers" />
			<div v-else-if="isReady"
				class="no-data">
				<template v-if="error">
					<h1 class="error">
						Konnte Vereinsdaten nicht laden
					</h1>
					<p>{{ error }}</p>
				</template>
				<template v-else>
					<h1>Keine Vereinsdaten vorhanden</h1>
					<p>
						Synchronisieren Sie die Daten Ihres Programms <a class="external-link" target="_blank" href="https://spg-direkt.de/">SPG-Verein</a>
						mit <a class="external-link" target="_blank" href="https://nextcloud.com/clients/">Nextcloud Desktop Client</a>
						und besuchen Sie diese Seite erneut.
					</p>
					<p>
						Aktuell wird Version 3 und 4 von <a class="external-link" target="_blank" href="https://spg-direkt.de/">SPG-Verein</a>
						unterstützt. Synchronisieren Sie für Version 3 aus dem Installationsverzeichnis die Ordner, die Dateien beinhalten, die mit
						<i>mitgl.dat</i> enden. Für Version 4 synchronisieren Sie alle Dateien mit dem Namensscheme <i>spg_verein_*.mdf</i>.
					</p>
				</template>
			</div>
		</AppContent>

		<AppSidebar />
	</Content>
</template>

<script>
import { generateUrl } from '@nextcloud/router';
import ActionLink from '@nextcloud/vue/dist/Components/ActionLink';
import AppContent from '@nextcloud/vue/dist/Components/AppContent';
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation';
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem';
import AppSidebar from './app-sidebar.vue';
import Content from '@nextcloud/vue/dist/Components/Content';
import Members from './members.vue';
import { mapGetters, mapActions } from 'vuex';

export default {

	components: {
		ActionLink,
		AppSidebar,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		Content,
		Members,
	},
	data() {
		return {
			selectedClub: null,
		};
	},

	computed: {
		...mapGetters(['clubs', 'members', 'cities', 'error']),

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
			if (this.selectedClub == null) {
				return null;
			}
			return generateUrl(`/apps/spgverein/files/${this.selectedClub.id}.ods`);
		},
	},

	watch: {
		clubs(newClubs) {
			if (this.$route.name == null) {
				this.$router.push({ name: 'Clubs', params: { id: newClubs[0].id } });
			} else if (this.$route.name === 'Clubs') {
				this.selectedClub = newClubs.find(club => `${club.id}` === `${this.$route.params.id}`);
				this.highlightMember(this.$route.query.member);
			}
		},

		selectedClub(club) {
			if (club != null) {
				this.openClub(club);
			}
		},

		$route(to) {
			if (to.name === 'Clubs') {
				this.selectedClub = this.clubs.find(club => club.id === to.params.id);
				this.highlightMember(to.query.member);
			}
		},
	},

	mounted() {
		this.fetchClubs();
	},

	methods: {
		...mapActions(['fetchClubs', 'openClub', 'printSelectedCities', 'highlightMember']),

		exportAsOdt() {
			window.open(this.exportUrl);
		},
	},
};
</script>

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

.no-data > h1 {
	font-size: 130%;
	padding-bottom: 1rem;
}

.no-data > h1.error {
	color: var(--color-error);
}

.no-data > p {
	padding-bottom: 0.25rem;
}

.action-buttons {
	position: absolute;
	right: 0;
}

a.external-link {
	text-decoration: underline;
}
</style>
