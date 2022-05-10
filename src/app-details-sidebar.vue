<template>
	<div class="details">
		<h2>{{ selectedMember.fullnames[ 0 ] }}</h2>
		<template v-if="selectedMember.bankAccount != null">
			<h3>Bankdaten</h3>
			<p>IBAN: {{ selectedMember.bankAccount.iban }}</p>
		</template>
	</div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
	computed: {
		...mapGetters('details', ['selectedMember']),
	},
};
</script>

<style scoped>
.details {
   padding: 10px;
}
</style>
