<template>
    <div>
        <layout></layout>
    </div>
    <br />

    <jet-confirmation-modal
        :show="confirmingUserDeletion"
        @close="confirmingUserDeletion = false"
    >
        <template #title>
            <h3><b>Item Information</b></h3>
        </template>

        <template #content>
            <div><b>Title:</b> {{ currentItem.title }}</div>
            <div><b>Barcode:</b> {{ currentItem.barcode }}</div>
            <div><b>Call Number:</b> {{ currentItem.callnumber }}</div>
            <div>
                <b>Effective Shelving Order:</b>
                {{ currentItem.effective_shelving_order }}
            </div>
            <div>
                <b>Shelving Location:</b> {{ shelf[0].effective_location_name }}
            </div>

            <div
                class="mt-4 text-indigo-900"
                v-if="
                    currentShelf.length === currentItemNumber &&
                    corrections === 0
                "
            >
                <button @click="deleteItem(currentItem.barcode)" type="button">
                    Remove this Item From the Shelf
                </button>
            </div>
        </template>

        <template #footer>
            <jet-secondary-button
                @click.native="confirmingUserDeletion = false"
            >
                Close
            </jet-secondary-button>
        </template>
    </jet-confirmation-modal>

    

    <div style="width: 100%; height: 100%">
        <header class="ml-2 mr-2 bg-gray-800 rounded-lg">
            <div class="items-center px-4 py-3 md:flex md:justify-between">
                <div class="text-xl text-white">{{ sortSchemeName }}</div>
                <div class="text-xl text-white">
                    Location: {{ initialLocationName }}
                </div>
                <div class="block px-2 text-xl text-white text-semibold">
                    <a href="#" @click="emptyTables">Clear Shelf</a>
                </div>
                <div class="block px-2 text-xl text-white text-semibold">
                    Corrections: {{ corrections }}
                </div>
                <div v-if="$page.props.user.privs === 1">
                    <form @change="postBarcode">
                        <div class="block px-2 text-semibold">
                            <select
                                class="block w-40 mt-1 form-input rounded-md shadow-sm"
                                v-model="form.barcode"
                            >
                                <option
                                    v-for="(demo, index) in mains"
                                    :key="index"
                                    :value="demo.barcode"
                                >
                                   {{ demo.id }}.  {{ demo.title }}
                                </option>
                            </select>
                        </div>
                    </form>
		
                </div>
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
        id="sbar"
        style="
            width: 100%;
            height: 750px;
            overflow: auto;
            margin-left: 20px;
            margin-right: 0px;
            margin-top: 15px;
        "
        ref="shelfdig"
    >
        <div style="width: 100%">
            <div
                class="corntainer"
                style="overflow-x: scroll; height: 750px; width: 100%"
            >
                <ul class="flex w-full pt-6 h-max">
                    <li
                        class="flex text-center"
                        v-if="mpos > mover"
                        v-for="(book, index) in shelf"
                        :key="1"
                    >
                        <div
                            v-if="mover === book.shelf_position"
                            class="font-bold text-blue-800 turn"
                        >
                            <span class="mb-2 text-sm font-bold unturn">{{ index + 1 }}</span>{{ book.title.slice(0, 25) }}
                        </div>
                        <div v-else class="turn">
                            <span class="mb-2 text-sm font-bold unturn">{{ index + 1 }}</span>{{ book.title.slice(0, 25) }}
                        </div>
                        <div
                            v-if="index + 1 === mpos"
                            class="h-2 bg-green-700 turn"
                            style="background-image: none; width: 7px"
                        ></div>
                    </li>
                    <li
                        class="flex"
                        v-if="mpos < mover"
                        v-for="(book, index) in shelf"
                        :key="1"
                    >
                        <div
                            v-if="index + 1 === mpos"
                            class="h-2 bg-green-800 turn"
                            style="background-image: none; width: 7px"
                        ></div>
                        <div
                            v-if="mover === book.shelf_position"
                            class="font-bold text-blue-800 turn"
                        ><span class="mb-2 text-sm font-bold unturn">{{ index + 1 }}</span>
                            <a @click="bookInfo(index)" href="#">{{
                                book.title.slice(0, 25)
                            }}</a>
                        </div>
                        <div v-else class="flex turn"><span class="mb-2 text-sm font-bold unturn">h4{{ index+1 }}</span>
                            <a @click="bookInfo(index)" href="#">{{
                                book.title.slice(0, 25)
                            }}</a>
                            <span class="text-green-800 border-1">{{
                                index + 1
                            }}</span>
                        </div>
                    </li>
                    <li
                        v-if="mpos === 0"
                        v-for="(book, index) in shelf"
                        :key="1"
                    >
                        <div class="turn"><span class="mb-2 text-sm font-bold unturn">{{ index + 1 }}</span>
                            <a @click="bookInfo(index)" href="#">{{
                                book.title.slice(0, 25)
                            }}</a>
                        </div>
                    </li>
                </ul>
                <ul class="flex w-full h-max">
                    <li v-for="(book, index) in shelf" :key="index">
                        <div class="callnum">{{ book.callnumber }}</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from "@/Layouts/Layout";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
export default {
    components: {
        Layout,
        JetConfirmationModal,
    },
    props: [
        "shelf",
        "offshelf",
        "offset",
        "lis",
        "movers",
        "mpos",
        "mover",
        "shelf_errors",
        "shelf_count",
        "sort",
        "loaded_service",
        "onshelf",
        "skidmoreStack",
        "status",
        "services",
        "corrections",
        "api_services",
        "sort_schemes",
        "sortSchemeId",
        "apiServiceId",
        "sortSchemeName",
        "libraryApiServices",
        "statusAlert",
        "mains",
        "errors",
	"unloadedService"
    ],

    data() {
        return {
            barcode: "",
            confirmingUserDeletion: false,
            confirmingCorrectLocation: false,
            currentItem: "",
            currentItemNumber: "",
            form: this.$inertia.form({
                barcode: "",
                demo_barcode: "",
                sort: "",
            }),
            isOpen: false,
            items: [],
        };
    },

    mounted() {
        let book_count = this.shelf.length;

        this.nowFocusInput();
        this.scrollToEnd();
    },

    methods: {
        postBarcode() {
            this.$inertia.post("process-barcode", {
                barcode: this.form.barcode,
                service: this.loaded_service,
                sortSchemeId: this.sortSchemeId,
                ignoreLocation: 0,
                onSuccess: (this.form.barcode = ""),
            });
                this.nowFocusInput;
        },

        deleteItem(barcode) {
            this.$inertia.post("delete.item", {
                barcode: barcode,
            });
        },

        bookInfo(index) {
            this.confirmingUserDeletion = true;
            this.currentItem = this.shelf[index];
            this.currentItemNumber = index + 1;
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

        pageLoadScroll() {
            // Scroll right when shelf is loaded

            let shelfLength = this.currentShelfCount;

            let content = this.$refs.shelfDig;

            // Check for accuracy as shelf gets larger
            let scroll_distance = 58 * shelfLength - 18 * 58;

            content.scrollLeft += scroll_distance;
        },

        emptyTables() {
            this.$inertia.post("empty_tables");
        },

        addItem: function () {
            this.items.push("Item #" + this.items.length);
            this.scrollToEnd();
        },
        scrollToEnd: function () {
            var corntainer = document.querySelector(".corntainer");
            corntainer.scrollLeft = corntainer.scrollWidth;
        },
        populate: function () {
            for (var i = 0; i < 100; i++) {
                this.items.push("Item #" + i);
            }
        },
    },

    computed: {
        currentShelf() {
            return this.shelf;
        },

        currentStatus() {
            return this.status;
        },

        currentShelfCount() {
            return this.shelf_count;
        },

        currentService() {
            return this.loaded_service;
        },
    },

    watch: {
        currentShelfCount(to, from) {
            let book_count = this.shelf.length;
            this.scrollToEnd();
        },

        currentShelf(to, from) {
            this.$refs.shelfDig;
        },

        currentService(to, from) {
            this.nowFocusInput();
        },
        currentSkidmoreStack(to, from) {
            this.skidmoreStack();
        },
    },
};
</script>

<style>
.up {
    transform: /* Magic Numbers */ translate(25px, 51px)
        /* 45 is really 360 - 45 */ rotate(45deg);
}

td.rotate {
    /* Something you can count on */
    height: 140px;
    white-space: nowrap;
}

td.rotate > div {
    transform: /* Magic Numbers */ translate(25px, 51px)
        /* 45 is really 360 - 45 */ rotate(90deg);
    width: 20px;
}

td.rotate > div > span {
    text-align: center;
}

td.rotate45 {
    /* Something you can count on */
    height: 140px;
    white-space: nowrap;
}

td.rotate45 > div {
    transform: /* Magic Numbers */ translate(25px, 51px)
        /* 45 is really 360 - 45 */ rotate(40deg);
    width: 20px;
}

td.rotate45 > div > span {
}

.blink {
    text-decoration: none;
    color: maroon;
}

.shelf {
    display: flex;
    height: 520px;
    width: 100%;
}

.shelf-book {
    display: flex;

    min-width: 0;
}

.shelf-book span {
    display: flex-inline-block;
    width: 70px;
    justify-content: center;
    align-items: center;
    flex-direction: row;
    min-height: 10em;
    line-height: 2em;
    height: 480px;
    font-size: 2.1em;
    border: 1px solid;
    -ms-writing-mode: bt-rl;
    writing-mode: vertical-rl;
    -webkit-writing-mode: vertical-rl;
    -moz-writing-mode: vertical-rl;
    white-space: nowrap;
    padding: 5px;
}

.call {
    height: 45px;
    width: 100%;
}

.call-book {
    display: flex;

    min-width: 0;
}

.call-book span {
    transform: /* Magic Numbers */ translate(25px, 51px)
        /* 45 is really 360 - 45 */ rotate(30deg);
    display: flex-inline-block;
    width: 70px;
    font-size: 1.5em;
    white-space: nowrap;
    text-align: left;
    vertical-align: top;
    padding: 5px;
}

.turn {
    display: inline-block;
    width: 60px;
    line-height: 55px;
    -ms-writing-mode: bt-rl;
    writing-mode: vertical-rl;
    -webkit-writing-mode: vertical-rl;
    -moz-writing-mode: vertical-rl;
    white-space: nowrap;
    background-image: url("/assets/images/jspine.jpeg");
    text-align: left;
    height: 395px;
    font-size: 1.7em;
    padding-top: 20px;
}

.unturn {
    		-ms-writing-mode: lr-tb;
		writing-mode: horizontal-tb;
    		-webkit-writing-mode: horizontal-tb;
   		-moz-writing-mode: horizontal-tb;
	}

.callnum {
    display: inline-block;
    transform: /* Magic Numbers */ translate(25px, 51px)
        /* 45 is really 360 - 45 */ rotate(30deg);
    white-space: nowrap;
    text-align: left;
    font-size: 1.5em;
    width: 60px;
    line-height: 60px;
    font-weight: bold;
}

.qanda {
    margin-left: 100px;
    min-height: 20px;
    font-size: 1.3em;
    width: 65%;
}

.qanda span {
    min-height: 25px;
    font-size: 1.1em;
    width: 100%;
    color: maroon;
}

.ask {
}

.response {
    margin-left: 150px;
}

#sbar::-webkit-scrollbar {
    display: none;
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
