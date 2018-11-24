<template>
    <div :id="member.id" class="member">
        <p v-for="name in member.fullnames">
            {{ name }}
        </p>
        <p>{{ member.street }}</p>
        <p>{{ member.zipcode }} {{ member.city }}</p>

        <br v-if="Object.keys(member.files).length > 0">
        <p class="docs" v-if="Object.keys(member.files).length > 0">
            <a class="button" :href="`/remote.php/webdav${member.files[index]}`" v-for="index in Object.keys(member.files)">
                <font-awesome-icon icon="file"/> {{ index }}
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
            }
        }
    }
</script>