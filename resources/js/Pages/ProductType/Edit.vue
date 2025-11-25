<template>
    <Head head-key="title" :title="props.productType.name" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Güncelle: {{ props.productType.name }}
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link :href="route('product-types.index')" class="btn fw-bold btn-sm btn-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left fs-4"></i>
                    <span class="ms-1 lh-1">Geri</span>
                </Link>
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <FormErrors :form="form"/>

            <form @submit.prevent="update">

                <div class="row g-4 g-lg-6 g-xl-10">
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

                                <div class="row align-items-center">
                                    <label class="col-md-3 form-label">SEÇENEK GRUBU KULLAN</label>
                                    <div class="col-md-9">
                                        <Dropdown v-model="form.product_option_group_id" :options="props.optionGroups" showClear optionValue="id" optionLabel="name" placeholder="Seçiniz" class="w-100" />
                                    </div>
                                </div>

                                <Divider />

                                <div class="row align-items-center">
                                    <label class="col-md-3 form-label">TAXONOMY</label>
                                    <div class="col-md-9">
                                        <InputText v-model="form.taxonomy" class="w-100" />
                                    </div>
                                </div>

                                <Divider />

                                <div class="row align-items-center">
                                    <label class="col-md-3 form-label">SATINALMA SONRASI İŞLEM</label>
                                    <div class="col-md-9">
                                        <Dropdown v-model="form.after_order_type" :options="$page.props.enums.after_order_type" optionValue="value" optionLabel="label" placeholder="Seçiniz" class="w-100" />
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

                                <div class="row align-items-center">
                                    <label class="col-6 form-label">Vergi Kullanımı</label>
                                    <div class="col-6">
                                        <InputSwitch v-model="form.taxable" />
                                    </div>
                                </div>

                                <div v-if="form.taxable">
                                    <Divider />
                                        <div class="row align-items-center">
                                        <label class="col-6 form-label">Vergi Sınıfı</label>
                                        <div class="col-6">
                                            <Dropdown v-model="form.tax_id" :options="props.taxes" showClear optionValue="id" optionLabel="name" placeholder="Seçiniz" class="w-100" />
                                        </div>
                                    </div>
                                </div>

                                <Divider />

                                <div class="row align-items-center">
                                    <label class="col-6 form-label">Stok Kullanımı</label>
                                    <div class="col-6">
                                        <InputSwitch v-model="form.stock_usage" />
                                    </div>
                                </div>

                                <Divider />

                                <div class="row align-items-center">
                                    <label class="col-6 form-label">Sepete Eklenebilir</label>
                                    <div class="col-6">
                                        <InputSwitch v-model="form.is_cartable" />
                                    </div>
                                </div>

                                <Divider />

                                <div class="row align-items-center">
                                    <label class="col-6 form-label">İletişim Kurulabilir</label>
                                    <div class="col-6">
                                        <InputSwitch v-model="form.is_contactable" />
                                    </div>
                                </div>

                                <div v-if="form.is_contactable">
                                    <Divider />
                                    <div class="row align-items-center">
                                        <label class="col-6 form-label">İletişim Formu</label>
                                        <div class="col-6">
                                            <Dropdown v-model="form.contact_form_id" :options="props.forms" showClear optionValue="id" optionLabel="name" placeholder="Seçiniz" class="w-100" />
                                        </div>
                                    </div>
                                </div>

                                <Divider />

                                <div class="row align-items-center">
                                    <label class="col-6 form-label">Ürünleri Popup Aç</label>
                                    <div class="col-6">
                                        <InputSwitch v-model="form.popup_products" />
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="col-lg-3">

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                    <InputSwitch v-model="form.is_published" />
                                    <label class="ms-2 fw-bold text-uppercase">YAYINLA</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    
</template>

<script setup>

import {ref} from "vue";
import { useForm } from "@inertiajs/vue3";
import Divider from "primevue/divider";
import InputText from "primevue/inputtext";
import InputSwitch from "primevue/inputswitch";
import TextEditor from "../../Components/TextEditor";
import Dropdown from "primevue/dropdown";
import PopupMediaLibrary from "../MediaLibrary/PopupMediaLibrary";

const props = defineProps({
    productType:Object,
    language:{
        type:String,
        default:"tr"
    },
    optionGroups:{
        type:Object,
        default:null
    },
    forms:Object,
    taxes:Object
});

const editor = ref(null);

const form = useForm({
    name:props.productType.name,
    taxonomy:props.productType.taxonomy.name,
    slug:props.productType.slug,
    summary:props.productType.summary,
    taxable:props.productType.taxable,
    tax_rate:props.productType.tax_rate,
    stock_usage:props.productType.stock_usage,
    is_cartable:props.productType.is_cartable,
    is_contactable:props.productType.is_contactable,
    is_published:props.productType.is_published,
    product_option_group_id:props.productType.product_option_group_id,
    after_order_type:props.productType.after_order_type,
    popup_products:props.productType.popup_products,
    contact_form_id:props.productType.contact_form_id,
    tax_id:props.productType.tax_id,
    users_only:props.productType.users_only
});

const update = () => {
    form.put(route('product-types.update',[props.productType,{language:props.productType.language}]),{
        onSuccess: () => {
            
        },
    });
}

</script>