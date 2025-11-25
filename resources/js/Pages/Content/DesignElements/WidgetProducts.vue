<template>
    
    <div>
        <h4 class="text-dark">{{ elemDetails.data.name }}</h4>
        <Divider class="mb-10"/>

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">ALL PRODUCT TYPES</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.all_product_types" />
            </div>
        </div>

        <Divider />

        <div v-if="!elemDetails.data.all_product_types">
            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">PRODUCT TYPES</label>
                <div class="col-md-9">
                    <MultiSelect v-model="elemDetails.data.product_types" :options="$page.props.segments" optionGroupLabel="name" optionLabel="name" optionValue="id" optionGroupChildren="product_types" placeholder="Select Product Types" class="w-100" />
                </div>
            </div>

            <Divider />
        </div>

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">FEATURED ONLY</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.featured_only" />
            </div>
        </div>
        
        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">HAS DISCOUNTS ONLY</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.discounts_only" />
            </div>
        </div>
        
        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">ALL ITEMS</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.all_items" />
            </div>
        </div>

        <Divider />

        <div v-if="!elemDetails.data.all_items">
            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">ITEMS COUNT</label>
                <div class="col-md-9">
                    <InputText v-model.number="elemDetails.data.itemCount" class="w-100" />
                    <Slider v-model="elemDetails.data.itemCount" class="w-100" :min="1" :max="24"/>
                </div>
            </div>

            <Divider />
        </div>

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">TYPE</label>
            <div class="col-md-9">
                <Dropdown v-model="elemDetails.data.type" :options="galleryTypes" class="w-100" />
            </div>
        </div>

        <OrderByOptions :order-by="elemDetails.data.orderBy"/>

        <div v-if="elemDetails.data.type == 'carousel'">
            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">PAGINATION</label>
                <div class="col-md-9">
                    <InputSwitch v-model="elemDetails.data.pagination" />
                </div>
            </div>
        </div>

        <div v-if="elemDetails.data.type == 'carousel'">
            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">NAVIGATION</label>
                <div class="col-md-9">
                    <InputSwitch v-model="elemDetails.data.navigation" />
                </div>
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">OPEN PRODUCTS IN POUP</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.popup" />
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">CARD TYPE</label>
            <div class="col-md-9">
                <Dropdown v-model="elemDetails.data.cardType" :options="['default','custom']" class="w-100" />
            </div>
        </div>

        <div v-if="elemDetails.data.cardType == 'custom'">
            <Divider />
            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">CUSTOM LAYOUT</label>
                <div class="col-md-9">
                    <Dropdown v-model="elemDetails.data.customCardLayout" :options="card_layouts" optionLabel="name" optionValue="id" class="w-100" placeholder="Select card layout"/>
                </div>
            </div>
        </div>


        <ColumnCounts :column-counts="elemDetails.data.columnCounts"/>

        <BaseDesignOptions :base-design-options="elemDetails.data.baseDesignOptions" :anim-options="elemDetails.data.animOptions"/>

        <Divider class="my-10" />

        <Button label="Tamam" @click="prepareElement"/>

    </div>

</template>

<script setup>

import {ref,onMounted} from "vue";
import Divider from "primevue/divider";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import InputSwitch from "primevue/inputswitch";
import Dropdown from "primevue/dropdown";
import MultiSelect from "primevue/multiselect";
import BaseDesignOptions from "./BaseDesignOptions";
import ColumnCounts from "./ColumnCounts";
import OrderByOptions from "./OrderByOptions";
import Slider from 'primevue/slider';


const props = defineProps({
    elem:{
        type:Object,
        default:null,
    },
    onDone:Function
});

let emptyElem = {
    id:"elem_" + Date.now(),
    type:'products',
    widget:true,
    data:{
        name:"Ürünler",
        prevHtml:"",
        elemHtml:"",
        all_product_types:true,
        product_types:[],
        featured_only:false,
        discounts_only:false,
        all_items:false,
        itemCount:6,
        type:"grid",
        orderBy:{
            label:"Default (ID DESC)",
            value:"id DESC"
        },
        pagination:true,
        navigation:false,
        swiperOptions:"",
        popup:false,
        cardType:"default",
        customCardLayout:0,
        columnCounts:{
            sm:1,
            md:1,
            lg:1,
            xl:1
        },
        baseDesignOptions:{
            class: "",
            position: "position-relative",
            padding: {
                top: "",
                right: "",
                bottom: "",
                left: ""
            },
            margin: {
                top: "",
                right: "",
                bottom: "",
                left: ""
            }
        },
        animOptions:{
            use: false,
            class: "",
            delay: ""
        }
    }
};


let galleryTypes = ['grid','carousel'];

const elemDetails = ref(emptyElem);
if( props.elem ){
    Object.entries(emptyElem.data).forEach(([key, val]) => {
        if(!props.elem.data.hasOwnProperty(key)){
            props.elem.data[key] = val;
        }
        elemDetails.value = Object.assign(emptyElem,props.elem);
    });
}

const card_layouts = ref(null);

const prepareElement = async () => {

    let promise = new Promise(function(resolve, reject) {

        if( elemDetails.value.data.type == 'carousel' ) {

            const swiperOptionsData = {
                lazy: true,
            };

            if( elemDetails.value.data.pagination ){

                swiperOptionsData.pagination = {
                    el: ".swiper-pagination",
                    clickable:true
                };

            }

            if( elemDetails.value.data.navigation ){

                swiperOptionsData.navigation = {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                };

            }

            swiperOptionsData.slidesPerView = 1;
            swiperOptionsData.spaceBetween = 15;

            swiperOptionsData.breakpoints = {
                400: {
                    slidesPerView: elemDetails.value.data.columnCounts.sm,
                },
                768: {
                    slidesPerView: elemDetails.value.data.columnCounts.md,
                },
                998: {
                    slidesPerView: elemDetails.value.data.columnCounts.lg,
                },
                1200: {
                    slidesPerView:  elemDetails.value.data.columnCounts.xl,
                }
            };

            elemDetails.value.data.swiperOptions = JSON.stringify(swiperOptionsData);
        
        }

        elemDetails.value.data.prevHtml = 'Ürünler Widget';

        resolve(true);
    });

    const isElementReady = await promise;
    if(isElementReady){
        props.onDone(elemDetails.value);
    }
}

onMounted(() => {
   
   fetch(route('card-layouts.index'),{
       headers:{
           'Accept':'application/json'
       }
   }).then(function(response) {
       return response.json();
   }).then((json) => {
       card_layouts.value = json;
   });
   
});

</script>