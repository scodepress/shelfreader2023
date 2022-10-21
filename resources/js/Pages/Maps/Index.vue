<template>
    <div><layout></layout></div>
    <div id="container" style="margin-left: 50px;">

        <div style="margin-top: 30px; margin-right: 30px; margin-left: 30px;">

            <header class="ml-2 mr-2 bg-gray-800 rounded-lg">
                <div class="items-center px-4 py-3 md:flex md:justify-between">
                    <div class="text-xl text-white">{{ sortSchemeName }}</div>
                    <div class="block px-2 text-white text-semibold">
                        <a href="#" @click="emptyTables">Clear Shelf</a>
                    </div>
                    <div class="block px-2 text-white text-semibold">Corrections: {{ corrections }}</div>
                    <div class="block px-2 mb-3 text-semibold">
                        <form @submit.prevent="postBarcode">
                            <div>
                                <input
                                    class="block w-40 pl-1 mt-1 border-2 form-input rounded-md shadow-sm"
                                    v-model="form.barcode"
                                    autofocus
                                    placeholder="Scan Barcode"
				    ref="barcode"
                                />
                            </div>
                        </form>
                    </div>
                    <div>
                        <form @change="chooseSort">
                            <div class="block px-2 text-semibold">
                                <select
                                    class="block w-full mt-1 form-input rounded-md shadow-sm"
                                    v-model="form.sort"
                                >
			    	<option value="" selected disabled>
					Change Sort
			    	</option>
                                    <option
                                        v-for="(sort, index) in unloadedService"
                                        :key="index"
                                        :value="sort.sort_scheme_id"
                                    >
				    Sort Method: {{ sort.sort_scheme_name }}
                                    </option>

                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </header>

            
            
        </div>

	<div class="flex justify-center mt-6" v-if="status != 'Available'">
		
		<span class="text-3xl text-red-700">{{ statusAlert }}</span>
		
	</div>
    	<div class="flex justify-center mt-6" v-if="errors.barcode">
		
		<div v-for="error in errors">
			<span class="text-3xl text-red-700">{{ error }}</span>
		</div>
	</div>
	<div class="flex justify-center mt-6" v-if="status != 'Available'">
		
		<span class="text-3xl text-red-700">{{ statusAlert }}</span>
		
	</div>
	<div class="flex justify-center mt-6" v-if="$page.props.flash.message">
		<span class="text-3xl text-red-700">{{$page.props.flash.message}}</span>
	</div>
	
        <div
	    id="container"
	    ref="scrollToMe"
            style="
                width: 100%;
                height: 750px;
                overflow: auto;
                margin-left: 20px;
                margin-right: 20px;
                margin-top: 15px;
		padding-bottom:50px;
            "
        >
            <div
                class="flex-container"
                v-for="(draw, index) in drawer"
            >
                <!-- Up move -->
                <div
                    v-if="draw.shelf_position === mpos && mover > mpos"
                    key="pos4"
                    style="
                        background-image: none;
                        background-color: #00cc00;
                        height: 4px;
                    "
                ></div>
                <div
                    v-if="mover == draw.shelf_position"
                    key="pos3"
                    style="color: blue; font-weight: bold;"
                >
                    {{ index + 1 }}&nbsp;&nbsp;{{ draw.title.slice(0, 30) }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ draw.callnumber }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ draw.barcode }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   
                    <span v-if="mover > mpos" class="turnup">
                        ->
                    </span>
                    <span v-else class="turndown">
                        ->
                    </span>
                </div>

                <div v-else style="font-weight: bold;">
                    {{ index + 1 }} &nbsp;&nbsp;{{ draw.title.slice(0, 30) }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ draw.callnumber }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ draw.barcode }}
                   
                </div>
                <!-- Down move -->
                <div
                    v-if="draw.shelf_position === mpos && mover < mpos"
                    key="pos"
                    style="
                        background-image: none;
                        background-color: #00cc00;
                        height: 4px;
                    "
                ></div>
            </div>
            <br />
        </div>
    </div>
</template>

<script>
import Layout from "@/Layouts/Layout";
import { Link } from '@inertiajs/inertia-vue3';

export default {
    components: {
        Layout,
	Link,
    },
    props: {
        drawer: Object,
        mover: Number,
        mpos: Number,
        sortSchemeId: Number,
        loadedService: Number,
        scannedItem: Object,
        sortSchemes: Object,
        sortSchemeName: String,
        corrections: Number,
        status: String,
        nextMoverItem: Object,
        libraryApiServices: Object,
	errors: Object,
	unloadedService: Object,
    },
    data() {
        return {
            barcode: "",
            confirmingUserDeletion: false,
            currentItem: "",
            form: this.$inertia.form({
                barcode: this.scannedBarcode,
                sort: "",
            }),
        };
    },

    mounted() {

        this.nowFocusInput();
        this.scrollToElement();
    },

    methods: {
        postBarcode() {
            this.$inertia.post("process-barcode", {
                barcode: this.form.barcode,
                service: this.loadedService,
                sortSchemeId: this.sortSchemeId,
                onSuccess: (this.form.barcode = ""),
            });
        },

        makeCorrection() {
            this.$inertia.post("process-barcode", {
                barcode: this.form.barcode,
                service: this.loadedService,
                sortSchemeId: this.sortSchemeId,
                onSuccess: (this.form.barcode = ""),
            });
        },

        chooseService(service) {
            this.$inertia.post("set-service", {
                service: service,
            });
        },

        bookInfo(index) {
            this.confirmingUserDeletion = true;
            this.currentItem = this.shelf[index];
        },

        chooseSort() {
            this.$inertia.post("choose-sort", {
                sort: this.form.sort,
            });
        },
        focusInput() {
            this.nextTick(() => {
                this.$refs.barcode.focus();
            });
        },

        nowFocusInput() {
            this.$refs.barcode.focus();
        },

        scrollRight() {
            // Scroll right when shelf is full

            let content = this.$refs.shelfDig;

            content.scrollLeft += 59;
        },

        emptyTables() {
            this.$inertia.post("empty_tables", {
                sortSchemeId: this.sortSchemeId,
            });
        },

        scrollToElement() {
		const container = this.$refs.scrollToMe;
		container.scrollTop = container.scrollHeight - 500;
        },
    },

    computed: {
        currentDrawer() {
            return this.drawer;
        },
    },

    watch: {
        drawer: function () {
            this.scrollToElement();
        },
    },
};
</script>
<style>
.flex-container {
    display: flex;
    flex-direction: column;
}

.flex-container > div {
    display: flex-inline-block;
    border: solid 1px black;
    height: 50px;
    width: 850px;
    margin: auto;
    padding-bottom: 10px;
    padding-left: 8px;
    padding-top: 8px;
}
.turnup {
    display: inline-block;
    width: 5px;
    line-height: 5px;
    -ms-writing-mode: bt-lr;
    writing-mode: sideways-lr;
    -webkit-writing-mode: sideways-lr;
    -moz-writing-mode: sideways-lr;
    white-space: nowrap;
    font-size: 1.3em;
    font-weight: bold;
    color: green;
}

.turndown {
    display: inline-block;
    width: 5px;
    line-height: 5px;
    -ms-writing-mode: bt-rl;
    writing-mode: sideways-rl;
    -webkit-writing-mode: sideways-rl;
    -moz-writing-mode: sideways-rl;
    white-space: nowrap;
    font-size: 1.3em;
    font-weight: bold;
    color: green;
}
</style>
