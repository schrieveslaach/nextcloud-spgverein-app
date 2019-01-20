<template>
    <div style="width: 100%">
        <section class="city" v-for="city in cities">
            <div class="city-header">
                <h2> {{ city }} </h2>
                <a class="button" :href="getFileUrl(city)" target="_blank">
                    <font-awesome-icon icon="print"/>
                </a>
            </div>

            <div class="members">
                <member :club="club" v-bind:member="member" v-for="member in getMembersOf(city)"></member>
            </div>
        </section>
    </div>
</template>

<script>
    import Member from './member.vue';

    export default {
        components: {
            member: Member
        },

        props: {
            members: {
                type: Array
            },
            cities: {
                type: Array
            },
            club: {
                type: String
            }
        },

        methods: {
            getMembersOf(city) {
                return this.members.filter(member => member.city === city);
            },

            getFileUrl(city) {
                return OC.generateUrl(`/apps/spgverein/labels/${this.club}/${city}`);
            }
        }
    }
</script>