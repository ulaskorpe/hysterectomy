<template>
    
    <div>

        <Dialog v-model:visible="dialogVisible" modal @hide="props.onUpdateDone" header="Ürün Kategorisi Güncelle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update">

                <div v-if="form.product_type_id">

                    <Divider />

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
                        <label class="form-label">SLUG</label>
                        <InputText v-model="form.slug" class="w-100" />
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
    selectedCategory:Object,
    onUpdateDone:{
        type:Function,
        required:true
    }
});


const dialogVisible = ref(true);
const categories = ref([]);
const listingTemplates = ref([]);

const form = useForm({
    name:props.selectedCategory.name,
    slug:props.selectedCategory.slug,
    language:props.selectedCategory.language,
    parent_id:props.selectedCategory.parent_id,
    summary:props.selectedCategory.summary,
    description:props.selectedCategory.description,
    product_type_id:props.selectedCategory.product_type_id,
    is_published:props.selectedCategory.is_published,
    media_objects:{
        mainImage:props.selectedCategory.media_objects.mainImage,
        headerImage:props.selectedCategory.media_objects.mainImage,
        mainVideo:props.selectedCategory.media_objects.mainVideo,
        headerImage:props.selectedCategory.media_objects.headerImage,
        galleryImages:props.selectedCategory.media_objects.galleryImages,
    },
    seo:{
        title:props.selectedCategory.seo.title,
        description:props.selectedCategory.seo.description
    },
});

const cancel = () => {
    form.reset();
    props.onUpdateDone();
};

const setMainImage = (media) => {
    form.media_objects.mainImage = media;
}

const setHeaderImage = (media) => {
    form.media_objects.headerImage = media;
}

const getCategories = () => {

    categories.value = [];

    fetch(route('product-categories.index',{productType:form.product_type_id,language:props.selectedCategory.language}),{
        headers: {
            'Accept': 'application/json',
        }
    }).then(function (response) {
        return response.json();
    }).then((json) => {
        categories.value = json;
    });

}

const update = () => {
    form.put(route('product-categories.update',[props.selectedCategory,{productType:form.product_type_id,language:props.selectedCategory.language}]),{
        onSuccess: () => {
            form.reset();
            props.onUpdateDone();
        },
    });
}

onMounted(() => {
    getCategories();
});

</script>