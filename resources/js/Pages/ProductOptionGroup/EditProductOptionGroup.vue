<template>
    
    <div>
        <form @submit.prevent="update" class="d-flex flex-column h-100">
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center mb-1">
                    <span class="fw-semibold">İsim</span>
                    <div class="d-flex align-items-center ms-2">
                        <img :src="`/dmn/media/flags/${props.item.language}.svg`" class="me-2" style="width: 14px" />
                        <span class="text-uppercase">{{ props.item.language }}</span>
                    </div>
                </div>
                <InputText v-model="form.name" class="w-100" />
            </div>

            <Divider />

            <div class="d-flex flex-column mb-5">
                <label class="form-label">Slug</label>
                <span class="p-input-icon-left">
                    <i class="bi bi-person" />
                    <InputText v-model="form.slug" class="w-100" />
                </span>
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

            <div class="separator my-7"></div>

            <div class="">
                <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                <button type="button" class="btn btn-light ms-5" @click="cancel()">Vazgeç</button>
            </div>
            <FormErrors :form="form"/>
        </form>
    </div>

</template>

<script setup>

import { useForm } from "@inertiajs/vue3";
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import Divider from "primevue/divider";
import Dropdown from "primevue/dropdown";

const props = defineProps({
    item:Object,
    productTypes:Object,
    onUpdateDone:{
        type:Function,
        required:true
    }
});

const form = useForm({
    name:props.item.name,
    language:props.item.language,
    slug:props.item.slug,
    has_own_price:props.item.has_own_price,
    stock_usage:props.item.stock_usage,
    variant_select_type:props.item.variant_select_type,
});

const update = () => {
    form.put(route('product-option-groups.update',[props.item,{language:props.item.language}]),{
        preserveScroll: true,
        onSuccess: (page) => {
            props.onUpdateDone();
        }
    });
}


</script>