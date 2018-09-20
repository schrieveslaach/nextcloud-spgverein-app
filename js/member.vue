<template>
    <div :id="member.id" class="member">
        <p v-for="name in member.fullnames">
            {{ name }}
        </p>
        <p>{{ member.street }}</p>
        <p>{{ member.zipcode }} {{ member.city }}</p>
        <!--<input value="1983010" checked="checked" class="address-block-checkbox" type="checkbox">-->
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