<template>
    <Head head-key="title" title="Kampanya Ekle" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Yeni Kampanya
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link :href="route('campaigns.index',{language:$page.props.current_language})" class="btn fw-bold btn-sm btn-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left fs-4"></i>
                    <span class="ms-1 lh-1">Geri</span>
                </Link>
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <FormErrors :form="form"/>

            <form @submit.prevent="create" class="row g-4 g-lg-6 g-xl-10">

                <div class="col-lg-9">

                    <div class="card mb-4 mb-xl-10">
                        <div class="card-body">

                            <h5 class="card-title text-muted">Genel Bilgiler</h5>
                            <Divider class="mb-10"/>

                            <div class="row align-items-center mt-5">
                                <label class="col-md-3 fw-bold text-uppercase">İsim</label>
                                <div class="col-md-9">
                                    <InputText class="w-100" v-model="form.name" :class="{'p-invalid':form.errors.name}"/>
                                </div>
                            </div>

                            <Divider />

                            <div class="row align-items-center mt-5">
                                <label class="col-md-3 fw-bold text-uppercase">etiketler</label>
                                <div class="col-md-9">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="flex-grow-1 me-2">
                                            <MultiSelect v-model="form.tags" display="chip" filter :options="optionTags" optionLabel="name" optionValue="id" :virtualScrollerOptions="{ itemSize: 44 }" :maxSelectedLabels="3" placeholder="Lütfen seçiniz" class="w-100"/>
                                        </div>
                                        <NewTag :render-as="'fetch'" :on-done="addNewTagToSelected"/>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-4 mb-xl-10">
                        <div class="card-body">
                            <h5 class="card-title text-muted">SEO Bilgileri</h5>
                            <Divider class="mb-10"/>

                            <div class="row align-items-center">
                                <label class="col-md-3 fw-bold text-uppercase">SEO Başlık</label>
                                <div class="col-md-9">
                                    <InputText class="w-100" v-model="form.seo.title" :class="{'p-invalid':form.errors['seo.title']}"/>
                                </div>
                            </div>

                            <div class="row align-items-center mt-5">
                                <label class="col-md-3 fw-bold text-uppercase">SEO Açıklama</label>
                                <div class="col-md-9">
                                    <Textarea autoResize rows="1" cols="30" class="w-100" v-model="form.seo.description" :class="{'p-invalid':form.errors['seo.description']}" />
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-4 mb-xl-10">
                        <div class="card-body">

                            <h5 class="card-title text-muted">Özet Bilgi</h5>
                            <Divider class="mb-10"/>

                            <TextEditor v-model="form.summary" />

                        </div>
                    </div>

                    <ContentArea :form="form"/>

                    <div class="card mb-4 mb-xl-10">
                        <div class="card-body">

                            <h5 class="card-title text-muted">Sayfa Özellikleri</h5>
                            <Divider class="mb-10"/>

                            <div class="row align-items-center">
                                <label class="col-md-6 fw-bold text-uppercase">BAŞLIK ALANI GİZLE</label>
                                <div class="col-md-6">
                                    <InputSwitch v-model="form.additional.hideHeader" />
                                </div>
                            </div>

                            <Divider />

                            <div v-if="!form.additional.hideHeader">
                                <div class="row align-items-center">
                                    <label class="col-md-6 fw-bold text-uppercase">BAŞLIK YAZISINI DEĞİŞTİR</label>
                                    <div class="col-md-6">
                                        <InputSwitch v-model="form.additional.customTitle" />
                                    </div>
                                </div>
                                <Divider />
                                <div v-if="form.additional.customTitle">
                                    <div class="row align-items-center">
                                        <label class="col-md-6 fw-bold text-uppercase">ÖZEL BAŞLIK YAZISI</label>
                                        <div class="col-md-6">
                                            <InputText class="w-100" v-model="form.additional.customTitleText" />
                                        </div>
                                    </div>
                                    <Divider />
                                </div>
                                <div class="row align-items-center">
                                    <label class="col-md-6 fw-bold text-uppercase">ALT BAŞLIK</label>
                                    <div class="col-md-6">
                                        <InputText class="w-100" v-model="form.additional.subTitle" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-5 mb-xl-10">
                        <div class="card-body">

                            <h5 class="card-title text-muted">Özellikler</h5>
                            <Divider class="mb-10"/>

                            <div class="row align-items-center">
                                <label class="col-6 form-label">Üyelere Özel</label>
                                <div class="col-6">
                                    <InputSwitch v-model="form.users_only" />
                                </div>
                            </div>

                            <Divider />

                            <div v-if="form.users_only">
                                <div class="row align-items-center">
                                    <label class="col-6 form-label">Üye Kaç Kez Faydalanılabilir?</label>
                                    <div class="col-6">
                                        <InputNumber v-model="form.user_usage_count" :useGrouping="false" />
                                    </div>
                                </div>

                                <Divider />
                            </div>

                            <div class="row align-items-center">
                                <label class="col-6 form-label">İndirim Tipi</label>
                                <div class="col-6">
                                    <Dropdown v-model="form.type" :options="discount_types" optionLabel="label" optionValue="value" placeholder="Lütfen seçiniz" class="w-100" />
                                </div>
                            </div>

                            <Divider />

                            <div class="row g-5 align-items-center">
                                <label class="col-lg-6 fw-bold">İndirim Tutarı</label>
                                <div class="col-lg-6">
                                    <InputNumber v-model="form.discount" class="w-100" />
                                </div>
                            </div>

                            <Divider />

                            <div class="row g-5 align-items-center">
                                <label class="col-lg-6 fw-bold">Tüm Sepette Geçerli</label>
                                <div class="col-lg-6">
                                    <InputSwitch v-model="form.apply_cart" />
                                </div>
                            </div>

                            <div v-if="!form.apply_cart">
                                <Divider />
                                <div class="row g-5 align-items-center">
                                    <label class="col-lg-6 fw-bold">Hangi Ürün Tiplerinde Geçerli?</label>
                                    <div class="col-lg-6">
                                        <MultiSelect v-model="form.product_types" :options="$page.props.segments" optionGroupLabel="name" optionLabel="name" optionValue="id" optionGroupChildren="product_types" placeholder="Lütfen seçiniz" class="w-100" />
                                    </div>
                                </div>

                            </div>

                            <Divider />

                            <div class="row align-items-center">
                                <label class="col-6 form-label">Min. Sepet Tutaru</label>
                                <div class="col-6">
                                    <InputNumber v-model="form.min_cart_amount" :useGrouping="false" />
                                </div>
                            </div>

                            <Divider />

                            <div class="row g-5 align-items-center">
                                <label class="col-lg-6 fw-bold">Başlangıç Tarihi</label>
                                <div class="col-lg-6">
                                    <InputMask v-model="form.start_date" :class="{'p-invalid':form.errors.start_date}" mask="99.99.9999" placeholder="GG.AA.YYYY"/>
                                </div>
                            </div>

                            <Divider />

                            <div class="row g-5 align-items-center">
                                <label class="col-lg-6 fw-bold">Bitiş Tarihi</label>
                                <div class="col-lg-6">
                                    <InputMask v-model="form.end_date" :class="{'p-invalid':form.errors.start_date}" mask="99.99.9999" placeholder="GG.AA.YYYY"/>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-4 mb-xl-10">
                        <div class="card-body">

                            <h5 class="card-title text-muted">Header Scripts</h5>
                            <Divider class="mb-10"/>

                            <AceEditor
                                v-model:codeContent="form.additional.headerScripts" 
                                v-model:editor="editor"
                                :options="{'showPrintMargin': false}"
                                theme="chrome"
                                lang="html"
                                width="100%" 
                                height="200px" 
                            />

                        </div>
                    </div>

                    <div class="card mb-4 mb-xl-10">
                        <div class="card-body">

                            <h5 class="card-title text-muted">Footer Scripts</h5>
                            <Divider class="mb-10"/>

                            <AceEditor
                                v-model:codeContent="form.additional.footerScripts" 
                                v-model:editor="editor"
                                :options="{'showPrintMargin': false}"
                                theme="chrome"
                                lang="html"
                                width="100%" 
                                height="200px" 
                            />

                        </div>
                    </div>

                </div>

                <div class="col-lg-3">

                    <div class="card mb-4 mb-xl-10">
                        <div class="card-body">

                            <div>
                                <h5 class="card-title text-muted">Öne Çıkan Görsel</h5>
                                <Divider class="mb-5"/>
                                <div class="mb-4 mb-xl-10">
                                    <div class="overlay overflow-hidden" v-if="form.media_objects.mainImage">
                                        <div class="overlay-wrapper">
                                            <img :src="form.media_objects.mainImage.original_url" class="img-fluid" />
                                        </div>
                                        <div class="overlay-layer bg-dark bg-opacity-25">
                                            <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" v-if="form.media_objects.mainImage" @click="form.media_objects.mainImage = null">
                                                <i class="bi bi-x fs-3"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <PopupMediaLibrary v-if="!form.media_objects.mainImage" :on-select="setMainImage" :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                </div>
                            </div>

                            <div>
                                <h5 class="card-title text-muted">Başlık Alanı Görsel</h5>
                                <Divider class="mb-5"/>
                                <div class="mb-4 mb-xl-10">
                                    <div class="overlay overflow-hidden" v-if="form.media_objects.headerImage">
                                        <div class="overlay-wrapper">
                                            <img :src="form.media_objects.headerImage.original_url" class="img-fluid" />
                                        </div>
                                        <div class="overlay-layer bg-dark bg-opacity-25">
                                            <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" v-if="form.media_objects.headerImage" @click="form.media_objects.headerImage = null">
                                                <i class="bi bi-x fs-3"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <PopupMediaLibrary v-if="!form.media_objects.headerImage" :on-select="setHeaderImage" :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                </div>
                            </div>

                            <div>
                                <h5 class="card-title text-muted">Başlık Alanı Video</h5>
                                <Divider class="mb-5"/>
                                <div class="d-flex flex-column mb-4 mb-xl-10">
                                    <div class="bg-light p-3 mb-3" v-if="form.media_objects.mainVideo">
                                        <video controls class="w-100">
                                            <source :src="form.media_objects.mainVideo.original_url" type="video/mp4">
                                        </video>
                                    </div>
                                    <div class="d-flex flex-row align-items-center">
                                        <PopupMediaLibrary :on-select="setMainVideo" :button-text="'Seç'" :mime-type="'video/'" :key="Math.floor(Math.random() * 100000)"/>
                                        <button type="button" class="btn btn-sm btn-light-danger ms-2" v-if="form.media_objects.mainVideo" @click="form.media_objects.mainVideo = null">Kaldır</button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h5 class="card-title text-muted">Galeri</h5>
                                <Divider class="mb-5"/>
                                <div>
                                    
                                    <div class="row g-3 row-cols-3 mb-5" v-if="form.media_objects.galleryImages">
                                        <div class="col" v-for="(media,m) in form.media_objects.galleryImages" :key="m">
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
                                    <PopupMediaLibrary :on-select="setGalleryImages" :multiple="true" :button-text="'Görselleri Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4 mb-xl-10">
                        <InputSwitch v-model="form.is_published" />
                        <label class="ms-2 fw-bold text-uppercase">YAYINLA</label>
                    </div>

                    <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                </div>

            </form>
        </div>
    </div>
    
</template>

<script setup>

import {ref,onMounted} from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Divider from "primevue/divider";
import InputText from "primevue/inputtext";
import InputSwitch from "primevue/inputswitch";
import InputNumber from "primevue/inputnumber";
import TextEditor from "../../Components/TextEditor";
import Textarea from "primevue/textarea";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import MultiSelect from "primevue/multiselect";
import PopupMediaLibrary from "../MediaLibrary/PopupMediaLibrary";
import InputMask from 'primevue/inputmask';

import AceEditor from "ace-editor-vue3";
import "brace/mode/html";
import "brace/theme/chrome";

import ContentArea from '../Content/DesignElements/ContentArea';
import NewTag from '../Tag/NewTag';

const props = defineProps({
    copy_content:Object,
});

const editor = ref(null);
const optionTags = ref([]);

let curr_language = usePage().props.current_language;

const discount_types = [
    {
        label:"Yüzde",
        value:"percentage"
    },
    {
        label:"Tutar",
        value:"fixed"
    }
];

const form = useForm({
    language:curr_language,
    name:props.copy_content ? props.copy_content.name+' - '+curr_language : "",
    summary:props.copy_content ? props.copy_content.summary : "",
    media_objects:props.copy_content ? props.copy_content.media_objects : {
        mainImage:null,
        headerImage:null,
        mainVideo:null,
        galleryImages:[],
    },
    seo:{
        title:props.copy_content && props.copy_content.seo ? props.copy_content.seo.title : "",
        description:props.copy_content && props.copy_content.seo ? props.copy_content.seo.description : ""
    },
    content:props.copy_content ? props.copy_content.content : [],
    additional:{
        footerScripts:props.copy_content && props.copy_content.additional.footerScripts ? props.copy_content.additional.footerScripts : ``,
        headerScripts:props.copy_content && props.copy_content.additional.headerScripts ? props.copy_content.additional.headerScripts : ``,
        hideHeader:props.copy_content ? props.copy_content.additional.hideHeader : false,
        customTitle:props.copy_content ? props.copy_content.additional.customTitle : false,
        customTitleText:props.copy_content ? props.copy_content.additional.customTitleText : null,
        subTitle:props.copy_content ? props.copy_content.additional.subTitle : null,
    },
    tags:[],
    is_published:false,
    type:props.copy_content ? props.copy_content.type : "",
    discount:props.copy_content ? props.copy_content.discount : 0,
    start_date:"",
    end_date:"",
    user_usage_count:props.copy_content ? props.copy_content.user_usage_count : 1,
    users_only:props.copy_content ? props.copy_content.users_only : false,
    apply_cart:props.copy_content ? props.copy_content.apply_cart : true,
    product_types:[],
    min_cart_amount:props.copy_content ? props.copy_content.min_cart_amount : 0,
    uuid:props.copy_content ? props.copy_content.uuid : ""
});

const setMainImage = (media) => {
    form.media_objects.mainImage = media;
}

const setHeaderImage = (media) => {
    form.media_objects.headerImage = media;
}

const setMainVideo = (media) => {
    form.media_objects.mainVideo = media;
}

const setGalleryImages = (medias) => {
    form.media_objects.galleryImages = [...form.media_objects.galleryImages, ...medias];
}

const removeFromGallery = (index) => {
    form.media_objects.galleryImages.splice(index,1);
}


const create = () => {
    form.post(route('campaigns.store'),{
        onSuccess: () => {
            
        },
    });
}

const getTags = () => {
    fetch(route('tags.index',{language:curr_language}),{
       headers: {
           'Accept': 'application/json',
       }
   }).then(function (response) {
       return response.json();
   }).then((json) => {
       optionTags.value = json;
   });
}

const addNewTagToSelected = (tag) => {
    getTags();
    form.tags.push(tag.id);
}

const formatDateToMask = (date) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();

    return day + '.' + month + '.' + year;
}

onMounted(() => {
   
    getTags();   

   form.start_date = props.copy_content ? formatDateToMask(props.copy_content.start_date) : "";
    form.end_date = props.copy_content ? formatDateToMask(props.copy_content.end_date) : "";
});

</script>