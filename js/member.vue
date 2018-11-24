<template>
    <div :id="member.id" class="member">
        <p v-for="name in member.fullnames">
            {{ name }}
        </p>
        <p>{{ member.street }}</p>
        <p>{{ member.zipcode }} {{ member.city }}</p>

        <br v-if="Object.keys(member.files).length > 0">
        <p class="docs" v-if="Object.keys(member.files).length > 0">
            <a class="button" :href="getFileUrl(file)" v-for="file in member.files">
                <font-awesome-icon icon="file"/> {{ file }}
            </a>
        </p>
    </div>
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