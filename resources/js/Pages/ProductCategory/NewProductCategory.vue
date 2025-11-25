<template>
    
    <div>
        <button type="button" :class="props.buttonClass" @click="dialogVisible = true" v-html="props.buttonText"></button>

        <Dialog v-model:visible="dialogVisible" modal header="Yeni Ürün Kategorisi Ekle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="create">

                <div>

                    <div class="row align-items-center">
                        <label class="col-md-6 form-label">ÜST KATEGORİ</label>
                        <div class="col-md-6">
                            <Dropdown v-model="form.parent_id" :options="categories" filter showClear optionValue="id" optionLabel="depth_name" placeholder="Root" class="w-100" />
                        </div>
                    </div>

                    <Divider />

                    <div class="row align-items-center">
                        <label class="col-6 form-label">ANA GÖRSEL</label>
                        <div class="col-6">
                            <div class="d-flex flex-row align-items-end">
                            <div class="symbol symbol-100px">
                                <img :src="form.media_objects.mainImage.preview_url" v-if="form.media_objects.mainImage"/>
                                <div class="symbol-label" v-else>
                                    <i class="bi bi-person fs-2x"></i>
                                </div>
                            </div>
                            <div class="ms-3 d-flex flex-row align-items-center">
                                <PopupMediaLibrary :on-select="setMainImage" :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                <button type="button" class="btn btn-sm btn-light-danger ms-2" v-if="form.media_objects.mainImage" @click="form.media_objects.mainImage = null">Kaldır</button>
                            </div>
                        </div>
                        </div>
                    </div>

                    <Divider />

                    <div class="row align-items-center">
                        <label class="col-6 form-label">HEADER GÖRSEL</label>
                        <div class="col-6">
                            <div class="d-flex flex-row align-items-end">
                            <div class="symbol symbol-100px">
                                <img :src="form.media_objects.headerImage.preview_url" v-if="form.media_objects.headerImage"/>
                                <div class="symbol-label" v-else>
                                    <i class="bi bi-person fs-2x"></i>
                                </div>
                            </div>
                            <div class="ms-3 d-flex flex-row align-items-center">
                                <PopupMediaLibrary :on-select="setHeaderImage" :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                <button type="button" class="btn btn-sm btn-light-danger ms-2" v-if="form.media_objects.headerImage" @click="form.media_objects.headerImage = null">Kaldır</button>
                            </div>
                        </div>
                        </div>
                    </div>

                    <Divider />

                    <div class="d-flex flex-column">
                        <label class="form-label">İSİM</label>
                        <InputText v-model="form.name" class="w-100" />
                    </div>

                    <Divider />

                    <div class="d-flex flex-column">
                        <label class="form-label">SEO BAŞLIK</label>
                        <InputText v-model="form.seo.title" class="w-100" />
                    </div>

                    <Divider />

                    <div class="d-flex flex-column">
                        <label class="form-label">SEO AÇIKLAMA</label>
                        <Textarea autoResize rows="1" cols="30" class="w-100" v-model="form.seo.description" />
                    </div>

                    <Divider />

                    <div class="d-flex flex-column">
                        <label class="form-label">Kısa Açıklama</label>
                        <TextEditor v-model="form.summary" />
                    </div>

                    <Divider />

                    <div class="row g-5">
                        <div class="col-lg-4 d-flex flex-row">
                            <InputSwitch v-model="form.is_published" />
                            <label class="fw-bold ms-2">Yayınla</label>
                        </div>
                    </div>
                    <div class="separator my-10"></div>
                    <div>
                        <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                        <button type="button" class="btn btn-light ms-5" @click="cancel()">Vazgeç</button>
                    </div>
                </div>

                <FormErrors :form="form"/>

            </form>
        </Dialog>
    </div>

</template>

<script setup>

import {onMounted, ref} from "vue";
import { useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import TextEditor from "../../Components/TextEditor";
import Textarea from "primevue/textarea";
import Dropdown from 'primevue/dropdown';
import Divider from "primevue/divider";
import PopupMediaLibrary from "../MediaLibrary/PopupMediaLibrary";

const props = defineProps({
    language:{
        type:String,
        default:"tr"
    },
    selectedProductTypeId:{
        type:Number,
        default:null
    },
    fromCategory:{
        type:Object,
        default:null
    },
    buttonClass:{
        type:String,
        default:'btn fw-bold btn-sm btn-success d-flex align-items-center'
    },
    buttonText:{
        type:String,
        default:'<i class="bi bi-person-plus fs-4"></i><span class="ms-1 lh-1">EKLE</span>'
    },
});


const dialogVisible = ref(false);
const categories = ref([]);
const listingTemplates = ref([]);

const form = useForm({
    name:props.fromCategory ? props.fromCategory.name+' - '+props.language : "",
    language:props.language,
    parent_id:"",
    summary:props.fromCategory ? props.fromCategory.summary : "",
    description:props.fromCategory ? props.fromCategory.description : "",
    product_type_id:props.selectedProductTypeId,
    is_published:false,
    media_objects:props.fromCategory ? props.fromCategory.media_objects : {
        mainImage:null,
        headerImage:null,
        mainVideo:null,
        galleryImages:[],
    },
    seo:{
        title:props.fromCategory && props.fromCategory.seo ? props.fromCategory.seo.title : "",
        description:props.fromCategory && props.fromCategory.seo ? props.fromCategory.seo.description : ""
    },
    uuid:props.fromCategory ? props.fromCategory.uuid : ""
});

const cancel = () => {
    dialogVisible.value = false;
    form.reset();
};

const setMainImage = (media) => {
    form.media_objects.mainImage = media;
}

const setHeaderImage = (media) => {
    form.media_objects.headerImage = media;
}

const getCategories = () => {

    categories.value = [];

    fetch(route('product-categories.index',{productType:form.product_type_id,language:props.language}),{
        headers: {
            'Accept': 'application/json',
        }
    }).then(function (response) {
        return response.json();
    }).then((json) => {
        categories.value = json;
    });

}

const create = () => {
    form.post(route('product-categories.store',{productType:props.selectedProductTypeId}),{
        onSuccess: () => {
            form.reset();
            dialogVisible.value = false;
        },
    });
}

onMounted(() => {
    if(props.selectedProductTypeId){
        getCategories();
    }
});

</script>