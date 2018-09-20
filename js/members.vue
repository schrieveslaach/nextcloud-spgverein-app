<template>
    <div style="width: 100%">
        <section class="city" v-for="city in cities">
            <div class="city-header">
                <h2> {{ city }} </h2>
                <a class="button" href="#" @click="printMembersOf(city)">
                    <font-awesome-icon icon="print"/>
                </a>
            </div>

            <div class="members">
                <member v-bind:member="member" v-for="member in getMembersOf(city)"></member>
            </div>
        </section>
    </div>
</template>

<script>
    import Member from './member.vue';

    export default {
        data() {
            return {};
        },

        model: {
            event: 'print'
        },

        components: {
            member: Member
        },

        props: {
            members: {
                type: Array
            },
            cities: {
                type: Array
            }
        },

        methods: {
            getMembersOf(city) {
                return this.members.filter(member => member.city === city);
            },

            printMembersOf(city) {
                this.$emit('print', {city});
            }
        }
    }
</script>