<template>
    <div style="width: 100%">
        <modal v-show="isModalVisible" @close="closePrintDialog()">
            <template slot="header">
                <a class="button" href="#" @click="openPdfInWindow()">
                    <font-awesome-icon icon="file-pdf"/>
                </a>
            </template>
            <template slot="body">
                <canvas ref="pdfLabelsCanvas"></canvas>
            </template>
        </modal>

        <section class="city" v-for="city in cities">
            <div class="city-header">
                <h2> {{ city }} </h2>
                <a class="button" href="#" @click="showPrintablePdfForCity(city)">
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
            return {
                cityToPrint: null,
                base64EncodedPdf: null
            };
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

            showPrintablePdfForCity(city) {
                this.cityToPrint = city;

                jQuery.CreateTemplate("inches",
                    8.2677165354, // 210mm
                    11.692913386, // 297mm
                    0.03, 0.03,
                    4.1338582677, // 105mm
                    1.8897637795, // 48mm
                    6, 2,
                    0.0, 0.0);

                this.getMembersOf(city).forEach(member => {
                    jQuery.CreateLabel();
                    jQuery.AddText(0.8, 0.8, member.street, 12);
                    //jQuery.AddText(0.9, 0.60, 'USD$99.90', 10);
                });

                jQuery.DrawPDF(base64EncodedPdf => this.base64EncodedPdf = base64EncodedPdf);
            },

            closePrintDialog() {
                this.cityToPrint = null;
                this.base64EncodedPdf = null;
            },

            openPdfInWindow() {
                document.location = `data:application/pdf;base64,${this.base64EncodedPdf}`;
            }
        },

        computed: {
            isModalVisible() {
                return this.cityToPrint != null;
            }
        },

        watch: {
            base64EncodedPdf(newBase64EncodedPdf) {
                if (newBase64EncodedPdf == null) {
                    return;
                }

                const vm = this;

                PDFJS.getDocument({data: atob(newBase64EncodedPdf)}).then(pdf => {
                    pdf.getPage(1).then(page => {
                        const scale = 0.5;
                        const viewport = page.getViewport(scale);

                        const canvas = vm.$refs.pdfLabelsCanvas;
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        page.render({
                            canvasContext: context,
                            viewport: viewport
                        });
                    })
                });
            }
        }
    }
</script>