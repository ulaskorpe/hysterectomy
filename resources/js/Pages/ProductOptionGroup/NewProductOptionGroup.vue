<template>
    
    <div>
        <button type="button" :class="props.buttonClass" @click="dialogVisible = true" v-html="props.buttonText"></button>

        <Dialog v-model:visible="dialogVisible" modal header="Yeni Ürün Seçenek Grubu Ekle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="create">

                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center mb-1">
                        <span class="fw-semibold">İsim</span>
                        <div class="d-flex align-items-center ms-2">
                            <img :src="`/dmn/media/flags/${props.language}.svg`" class="me-2" style="width: 14px" />
                            <span class="text-uppercase">{{ props.language }}</span>
                        </div>
                    </div>
                    <InputText v-model="form.name" class="w-100" />
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-md-6 form-label">Listeleme Tipi</label>
                    <div class="col-md-6">
                        <Dropdown v-model="form.variant_select_type" :options="$page.props.enums.variant_select_types" optionValue="value" optionLabel="label" placeholder="Seçiniz" class="w-100" />
                    </div>
                </div>

                <Divider />

                <div class="row g-5">
                    <div class="col-lg-4 d-flex flex-row">
                        <InputSwitch v-model="form.has_own_price" />
                        <label class="fw-bold ms-2">Kendi fiyatı var</label>
                    </div>
                    <div class="col-lg-4 d-flex flex-row">
                        <InputSwitch v-model="form.stock_usage" />
                        <label class="fw-bold ms-2">Stok kullanımı</label>
                    </div>
                </div>
                <div class="separator my-10"></div>
                <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                <FormErrors :form="form"/>

            </form>
        </Dialog>
    </div>

</template>

<script setup>

import {ref} from "vue";
import { useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import Divider from "primevue/divider";
import Dropdown from "primevue/dropdown";

const props = defineProps({
    productTypes:Object,
    language:{
        type:String,
        default:"tr"
    },
    fromGroup:{
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

const form = useForm({
    name:props.fromGroup ? props.fromGroup.name+' - '+props.language : "",
    language:props.language,
    has_own_price:props.fromGroup ? props.fromGroup.has_own_price : false,
    stock_usage:props.fromGroup ? props.fromGroup.stock_usage : false,
    variant_select_type:props.fromGroup ? props.fromGroup.variant_select_type : "",
    uuid:props.fromGroup ? props.fromGroup.uuid : "",
});

const create = () => {
    form.post(route('product-option-groups.store'),{
        onSuccess: () => {
            form.reset();
            dialogVisible.value = false;
        },
    });
}

</script>