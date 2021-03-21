<template>
	<tr :id="member.id">
		<td v-for="name in member.fullnames" :key="name" class="name-and-address">
			<div class="name">
				<span>{{ name }}</span>
				<br>
				<span class="address-line">
					{{ member.street }}, {{ member.zipcode }} {{ member.city }}
				</span>
			</div>

			<Actions>
				<ActionButton icon="icon-filetype-file"
					:close-after-click="true"
					@click="selectMembersToPrint([ member.id ])">
					Einzeletikett drucken
				</ActionButton>
			</Actions>
		</td>

		<td>
			<ul>
				<li>Geburtsdatum: {{ member.birth | date }}</li>
				<li>Eintrittsdatum: {{ member.admissionDate | date }}</li>
				<li v-if="member.resignationDate">
					Austrittsdatum: {{ member.resignationDate | date }}
				</li>
			</ul>
		</td>

		<td>
			<template v-if="Object.keys(member.files).length > 0">
				<a v-for="file in member.files"
					:key="file"
					class="attachment-link"
					:href="getFileUrl(file)">
					<div class="icon-file" />
					{{ file }}
				</a>
			</template>
			<!-- eslint-disable-next-line vue/no-v-html -->
			<span v-else v-html="'&nbsp;'" />
		</td>
	</tr>
</template>

<style scoped>
.name-and-address {
	display: flex;
}

.name {
	flex-grow: 6;
}

span.address-line {
	color: var(--color-text-maxcontrast);
}

.attachment-link {
    display: flex;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}
.attachment-link > .icon-file {
	margin-right: 0.2rem;
}

@media only screen and (max-width: 760px) {

	td, tr {
        display: block;
    }

	tr {
		padding-bottom: 0.5rem;
	}

    td {
        border: none;
        position: relative;
        padding-left: 25%;
    }

    td:before {
        position: absolute;
        top: 6px;
        left: 6px;
        width: 20%;
        padding-right: 10px;
        white-space: nowrap;
    }

    td:nth-of-type(1):before {
		color: var(--color-text-maxcontrast);
        content: "Name & Adresse";
    }

    td:nth-of-type(2):before {
		color: var(--color-text-maxcontrast);
        content: "Daten";
    }

    td:nth-of-type(3):before {
		color: var(--color-text-maxcontrast);
        content: "Anhänge";
    }

}
@media only screen and (min-width: 761px) {
	td:nth-of-type(1) {
		padding-left: 10px;
	}

	tr {
		border-bottom: 1px solid var(--color-border);
	}
}
</style>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton';
import Actions from '@nextcloud/vue/dist/Components/Actions';
import { generateUrl } from '@nextcloud/router';
import dayjs from 'dayjs';
import { mapActions } from 'vuex';

export default {
	components: {
		ActionButton,
		Actions,
	},
	filters: {
		date(value) {
			if (!value) return '';
			return dayjs(value).format('L');
		},
	},
	props: {
		member: {
			type: Object,
			required: true,
		},
		club: {
			type: String,
			required: true,
		},
	},
	data() {
		return {};
	},

	methods: {
		...mapActions(['selectMembersToPrint']),

		getFileUrl(file) {
			return generateUrl(`/apps/spgverein/files/${this.club}/${this.member.id}/${file}`);
		},
	},
};
</script>
