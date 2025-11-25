<template>
    
    <div>
        <h4 class="text-dark">{{ elemDetails.data.name }}</h4>
        <Divider class="mb-10"/>

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">GALLERY ID</label>
            <div class="col-md-9">
                <InputText v-model="elemDetails.data.swiperId" class="w-100" />
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">SELECT IMAGES</label>
            <div class="col-md-9">
                <div class="row g-3 row-cols-3 row-cols-lg-6 my-2">
                    <div class="col" v-for="(media,m) in elemDetails.data.medias" :key="m">
                        <div class="overlay overflow-hidden rounded">
                            <div class="overlay-wrapper">
                                <img :src="media.preview_url" class="img-fluid rounded cursor-pointer" />
                            </div>
                            <div class="overlay-layer bg-dark bg-opacity-25">
                                <button type="button" class="btn btn-light-danger btn-sm btn-icon" @click="removeFromGallery(m)"><i class="bi bi-x fs-3"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <PopupMediaLibrary :on-select="setGalleryImages" :multiple="true" :button-text="'Select Images'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">IMAGE SIZE</label>
            <div class="col-md-9">
                <Dropdown v-model="elemDetails.data.conversion" :options="conversions" optionValue="value" optionLabel="label" class="w-100" />
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">TYPE</label>
            <div class="col-md-9">
                <Dropdown v-model="elemDetails.data.type" :options="galleryTypes" class="w-100" />
            </div>
        </div>

        <div v-if="elemDetails.data.type == 'grid'">
            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">MASONRY LAYOUT</label>
                <div class="col-md-9">
                    <InputSwitch v-model="elemDetails.data.masonry" />
                </div>
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">LIGHTBOX</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.lightbox" />
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">IMG ROUNDED</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.imgRounded" />
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">IMG SHADOW</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.imgShadow" />
            </div>
        </div>

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

            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">NAVIGATION POSITION</label>
                <div class="col-md-9">
                    <Dropdown v-model="elemDetails.data.navigationPosition" :options="['navigation-default','navigation-bottom-right','navigation-top-right']" class="w-100" />
                </div>
            </div>

        </div>

        <div v-if="elemDetails.data.type == 'carousel' || elemDetails.data.type == 'infinitie-loop'">
            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">SLIDES BETWEEN</label>
                <div class="col-md-9">
                    <Dropdown v-model="elemDetails.data.spaceBetween" :options="[0,15,30,50,75,100]" class="w-100" />
                </div>
            </div>
        </div>

        <div v-if="elemDetails.data.type == 'infinitie-loop'">
            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">REVERSE DIRECTION</label>
                <div class="col-md-9">
                    <InputSwitch v-model="elemDetails.data.reverseDirection" />
                </div>
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">SLIDES HAS SHADOW</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.slidesShadow" />
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">SLIDES ROUNDED</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.slidesRounded" />
            </div>
        </div>

        <Divider />

        <div class="row align-items-center">
            <label class="col-md-3 fw-bold text-uppercase">SLIDES BORDERED</label>
            <div class="col-md-9">
                <InputSwitch v-model="elemDetails.data.slidesBordered" />
            </div>
        </div>

        <div v-if="elemDetails.data.type == 'carousel'">
            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">SLIDES PER VIEW AUTO</label>
                <div class="col-md-9">
                    <InputSwitch v-model="elemDetails.data.slidesPerViewAuto" />
                </div>
            </div>
        </div>

        <div v-if="elemDetails.data.slidesPerViewAuto">
            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">SLIDES HEIGHT (MOBILE)</label>
                <div class="col-md-9">
                    <Dropdown v-model="elemDetails.data.slidesMobileHeight" :options="['h-100px','h-150px','h-200px','h-250px','h-300px','h-350px','h-400px','h-450px','h-500px','h-600px','h-700px']" class="w-100" />
                </div>
            </div>

            <Divider />

            <div class="row align-items-center">
                <label class="col-md-3 fw-bold text-uppercase">SLIDES HEIGHT (DESKTOP)</label>
                <div class="col-md-9">
                    <Dropdown v-model="elemDetails.data.slidesHeight" :options="['h-xl-100px','h-xl-150px','h-xl-200px','h-xl-250px','h-xl-300px','h-xl-350px','h-xl-400px','h-xl-450px','h-xl-500px','h-xl-600px','h-xl-700px']" class="w-100" />
                </div>
            </div>
        </div>

        <RowCounts :row-counts="elemDetails.data.rowCounts" v-if="elemDetails.data.type == 'carousel' && !elemDetails.data.slidesPerViewAuto"/>

        <ColumnCounts :column-counts="elemDetails.data.columnCounts" v-if="elemDetails.data.type != 'infinitie-loop' && !elemDetails.data.slidesPerViewAuto"/>

        <BaseDesignOptions :base-design-options="elemDetails.data.baseDesignOptions" :anim-options="elemDetails.data.animOptions"/>

        <Divider class="my-10" />

        <Button label="Tamam" @click="prepareElement"/>

    </div>

</template>

<script setup>

import {ref} from "vue";
import Divider from "primevue/divider";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import InputSwitch from "primevue/inputswitch";
import Dropdown from "primevue/dropdown";
import BaseDesignOptions from "./BaseDesignOptions";
import ColumnCounts from "./ColumnCounts";
import RowCounts from "./RowCounts";
import PopupMediaLibrary from "../../MediaLibrary/PopupMediaLibrary";


const props = defineProps({
    elem:{
        type:Object,
        default:null,
    },
    onDone:Function
});

const editor = ref(null);

let emptyElem = {
    id:"elem_" + Date.now(),
    type:'gallery',
    render_blade:true,
    blade:'gallery',
    data:{
        name:"IMAGE GALLERY",
        swiperId:"",
        medias:[],
        prevHtml:"",
        elemHtml:"",
        lightbox:false,
        conversion:"",
        type:"grid",
        masonry:false,
        pagination:true,
        navigation:false,
        navigationPosition:"navigation-default",
        reverseDirection:false,
        slidesRounded:false,
        slidesShadow:false,
        slidesBordered:false,
        slidesPerViewAuto:false,
        spaceBetween:15,
        slidesHeight:"h-xl-300px",
        slidesMobileHeight:"h-200px",
        imgRounded:false,
        imgShadow:false,
        rowCounts:{
            sm:1,
            md:1,
            lg:1,
            xl:1
        },
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

let conversions = [
    {
        label:"Default Size",
        value:""
    },
    {
        label:"Optimize",
        value:"op_url"
    },
    {
        label:"Thumbnail",
        value:"th_url"
    },
    {
        label:"992x500",
        value:"992x500_url"
    },
    {
        label:"500x500",
        value:"500x500_url"
    },
    {
        label:"300x300",
        value:"300x300_url"
    },
    {
        label:"1200x1200",
        value:"1200x1200_url"
    },
    {
        label:"100x100",
        value:"100x100_url"
    }
]

let galleryTypes = ['grid','carousel','infinitie-loop','cloud'];

const elemDetails = ref(emptyElem);
if( props.elem ){
    Object.entries(emptyElem.data).forEach(([key, val]) => {
        if(!props.elem.data.hasOwnProperty(key)){
            props.elem.data[key] = val;
        }
        elemDetails.value = Object.assign(emptyElem,props.elem);
    });
}

const setGalleryImages = (medias) => {
    elemDetails.value.data.medias = [...elemDetails.value.data.medias, ...medias];
}

const removeFromGallery = (index) => {
    elemDetails.value.data.medias.splice(index,1);
}

const prepareElement = async () => {

    let promise = new Promise(function(resolve, reject) {

        if( elemDetails.value.data.medias.length == 0 ){
            resolve(false);
        }
        
        let prev_html = '<div class="hstack gap-3 flex-wrap">';
            
            elemDetails.value.data.medias.forEach(med => {
                prev_html += '<img src="'+med.preview_url+'" width="50" />'; 
            });

        prev_html += '</div>';

        elemDetails.value.data.prevHtml = prev_html;

        resolve(true);
    });

    const isElementReady = await promise;
    if(isElementReady){
        props.onDone(elemDetails.value);
    } else {
        alert("Gorseller zorunludur.!");
    }
    
    
}

</script>