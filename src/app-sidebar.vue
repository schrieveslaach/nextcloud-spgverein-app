<template>
	<AppSidebar v-show="show" :title="title" @close="clearSelection()">
		<AppLabelSidebar v-if="printLabelsUrl != null " />
		<AppDetailsSidebar v-if="selectedMember != null" />
	</AppSidebar>
</template>

<script>
import AppSidebar from '@nextcloud/vue/dist/Components/AppSidebar';
import AppLabelSidebar from './app-label-sidebar.vue';
import AppDetailsSidebar from './app-details-sidebar.vue';
import { mapActions, mapGetters } from 'vuex';

export default {
	components: {
		AppLabelSidebar,
		AppSidebar,
		AppDetailsSidebar,
	},

	computed: {
		...mapGetters('labels', ['printLabelsUrl', 'memberSelectionOnly']),
		...mapGetters('details', ['selectedMember']),

		title() {
			if (this.selectedMember) {
				return 'Details';
			}
			if (this.memberSelectionOnly) {
				return 'Einzelettiket drucken';
			}
			return 'Etiketten drucken';
		},

		show() {
			return this.printLabelsUrl != null || this.selectedMember != null;
		},
	},
	methods: {
		...mapActions(['clearSelection']),
	},
};
</script>
