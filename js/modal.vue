<script>
    export default {
        name: 'modal',
        methods: {
            close() {
                this.$emit('close');
            },
        },
    };
</script>
<template>
    <transition name="modal-fade">
        <div class="modal-backdrop">
            <div class="modal"
                 role="dialog"
                 aria-labelledby="modalTitle"
                 aria-describedby="modalDescription">
                <header class="modal-header"
                        id="modalTitle">
                    <slot name="header">
                        This is the default tile!
                    </slot>
                    <button type="button"
                            class="btn-close"
                            @click="close"
                            aria-label="Close modal">
                        <font-awesome-icon icon="times"/>
                    </button>
                </header>
                <section class="modal-body"
                         id="modalDescription">
                    <slot name="body">
                        I'm the default body!
                    </slot>
                </section>
            </div>
        </div>
    </transition>
</template>
