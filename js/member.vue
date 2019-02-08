<template>
    <tr :id="member.id">
        <td v-for="name in member.fullnames">
            {{ name }}
        </td>
        <td>{{ member.street }}</td>
        <td>{{ member.zipcode }}</td>
        <td>{{ member.city }}</td>

        <td>
            <template v-if="Object.keys(member.files).length > 0">
                <a class="attachment-link" :href="getFileUrl(file)" v-for="file in member.files">
                    <font-awesome-icon icon="file"/>
                    {{ file }}
                </a>
            </template>
            <template v-else>
                <span>&nbsp;</span>
            </template>
        </td>
    </tr>
</template>

<script>


    export default {
        data() {
            return {};
        },

        props: {
            member: {
                type: Object
            },
            club: {
                type: String
            }
        },

        methods: {
            getFileUrl(file) {
                return OC.generateUrl(`/apps/spgverein/files/${this.club}/${this.member.id}/${file}`);
            }
        }
    }
</script>