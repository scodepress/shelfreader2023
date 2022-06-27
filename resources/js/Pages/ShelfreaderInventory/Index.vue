<template>
    <div><layout></layout></div>
    <br />
    <div>
    	Hey
    </div>
    <div style="margin-top: 30px; margin-right: 30px; margin-left: 30px;">
        <div class="grid grid-flow-col auto-cols-max-content">
            <div>
                <a href="#" @click="emptyTables">Clear the Shelf</a>
            </div>
            <div>Corrections: {{ corrections }}</div>
            

            <div>
                <form @change="postBarcode">
                    <div class="max-w-md grid grid-cols-1 gap-1">
                        <select
                            class="block w-full mt-1 form-input rounded-md shadow-sm"
                            v-model="form.barcode"
                        >
			<option value="" disabled>Scan a Book</option>
                            <option
                                v-for="(item, index) in inventory"
                                :key="index"
                                :value="item.barcode"
                                >{{ item.title.substring(0, 20) }}</option
                            >
                        </select>
                    </div>
                </form>
            </div>
            <div>
                <form @change="postCorrection">
                    <div class="max-w-md grid grid-cols-1 gap-1">
                        <select
                            class="block w-full mt-1 form-input rounded-md shadow-sm"
                            v-model="form.barcode"
                        >
			<option value="" disabled>Make a  Correction</option>
	                            <option
				    v-if="movers"
                                v-for="(item, index) in movers.slice(0,1)"
                                :key="index"
                                :value="item.barcode"
                                >{{ item.title.substring(0, 20) }}</option
                            >
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div
            v-if="status != 'Available' && status != 'None'"
            key="av_1"
            style="
                font-size: 2em;
                background: red;
                color: white;
                padding: 5px;
                text-align: center;
            "
        >
            {{ status }}
        </div>
        <div
            v-if="shelf[0] && shelf[0].status != 'Available'"
            key="av_2"
            style="
                font-size: 2em;
                background: red;
                color: white;
                padding: 5px;
                text-align: center;
            "
        >
            {{ shelf[0].status }}
        </div>
    </div>

    <div
        id="sbar"
        style="
            width: 100%;
            height: 750px;
            overflow: auto;
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 15px;
        "
        ref="shelfDig"
    >
        <table width="900000px" border="0">
            <tr>
                <td>
                    <div
                        v-for="(book, index) in shelf"
                        :key="index"
                        class="turn"
                        ref="reshelve"
                    >
                        <!-- Right move -->
                        <div
                            v-if="book.shelf_position == mpos && mover < mpos"
                            key="pos"
                            style="
                                background-image: none;
                                background-color: #00cc00;
                                width: 7px;
                            "
                        ></div>

                        <div v-if="book.shelf_position == mpos" key="pos2">
                            <div
                                style="
                                    margin-left: -10px;
                                    text-align: left;
                                    display: inline-block;
                                    width: 60px;
                                "
                            >
                                <span style="font-weight: bold;"
                                    >{{ index + 1 }} &nbsp;&nbsp;{{
                                        book.title.slice(0, 20)
                                    }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-else
                            style="
                                text-align: left;
                                display: inline-block;
                                width: 60px;
                            "
                        >
                            <span
                                v-if="mover == book.shelf_position"
                                key="pos3"
                                style="color: blue; font-weight: bold;"
                            >
                                {{ index + 1 }}&nbsp;&nbsp;{{
                                    book.title.slice(0, 20)
                                }}
                            </span>
                            <span v-else style="font-weight: bold;">
                                {{ index + 1 }} &nbsp;&nbsp;{{
                                    book.title.slice(0, 20)
                                }}
                            </span>
                        </div>

                        <!-- Left move -->
                        <div
                            v-if="book.shelf_position == mpos && mover > mpos"
                            key="pos4"
                            style="
                                background-image: none;
                                background-color: #00cc00;
                                width: 7px;
                            "
                        ></div>
                    </div>
                </td>
            </tr>

            <tr>
                <td v-for="(book, index) in shelf" :key="index" class="callnum">
                    <span>{{ book.callnumber }}</span>
                </td>
            </tr>
        </table>
    </div>
</template>



<script>
import Layout from "@/Layouts/Layout";

export default {
    components: {
        Layout,
        ConfirmationModal,
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
        "loaded_ service",
        "onshelf",
        "skidmoreStack",
        "status",
        "services",
        "corrections",
        "api_services",
        "inventory",
    ],

    data() {
        return {
            barcode: "",
	    confirmingUserDeletion: true,
            form: this.$inertia.form({
                barcode: "",
            }),
        };
    },

    mounted() {
        let book_count = this.shelf.length;

        if (book_count > 18) {
            this.pageLoadScroll();
        }

        //this.nowFocusInput();
    },

    methods: {
        postBarcode() {
            this.$inertia.post("process-folio-inventory", {
                barcode: this.form.barcode,
                service: "inventory",
                onSuccess: this.form.reset("barcode"),
            });
        },

        postCorrection() {
            this.$inertia.post("inventory-correction", {
                barcode: this.form.barcode,
                service: "inventory",
                onSuccess: this.form.reset("barcode"),
            });
        },

        chooseService(service) {
            this.$inertia.post("set_service", {
                service: service,
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
            this.$inertia.post("empty_inventory_tables");
        },
    },

    computed: {
        currentShelf() {
            return this.shelf;
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

            this.$refs.shelfDig;

            if (book_count > 18) {
                this.scrollRight();
            }
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
    transform: 
	/* Magic Numbers */ translate(25px, 51px)
        /* 45 is really 360 - 45 */ rotate(45deg);
}

td.rotate {
    /* Something you can count on */
    height: 140px;
    white-space: nowrap;
}

td.rotate > div {
    transform: 
	/* Magic Numbers */ translate(25px, 51px)
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
    transform: 
	/* Magic Numbers */ translate(25px, 51px)
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
    transform: 
	/* Magic Numbers */ translate(25px, 51px)
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

.callnum {
    display: inline-block;
    transform: 
	/* Magic Numbers */ translate(25px, 51px)
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
