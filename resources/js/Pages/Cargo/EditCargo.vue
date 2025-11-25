<template>
    
    <div>
        <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="visible = true"/>

        <Dialog v-model:visible="visible" modal header="Kargo Güncelle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update" class="d-flex flex-column h-100">

                <div class="row align-items-center">
                    <label class="col-lg-6">İsim</label>
                    <div class="col-lg-6">
                        <InputText v-model="form.name" class="w-100" />
                    </div>
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-lg-6">Sabit Fiyat</label>
                    <div class="col-lg-6">
                        <InputSwitch v-model="form.fixed" />
                    </div>
                </div>

                <div v-if="form.fixed">
                    <Divider />
                    <div class="row align-items-center">
                        <label class="col-lg-6">Sabit Fiyat Tutarı</label>
                        <div class="col-lg-6">
                            <InputNumber v-model="form.fixed_price" class="w-100"/>
                        </div>
                    </div>
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-lg-6">Ücretsiz Sepet Tutarı</label>
                    <div class="col-lg-6">
                        <InputNumber v-model="form.free_after" class="w-100"/>
                    </div>
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-lg-6">Varsayılan Toplam Desi</label>
                    <div class="col-lg-6">
                        <InputNumber v-model="form.default_product_desi" class="w-100"/>
                    </div>
                </div>

                <div class="separator my-7"></div>

                <div class="">
                    <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                    <button type="button" class="btn btn-light ms-5" @click="cancel()">Vazgeç</button>
                </div>
                <FormErrors :form="form"/>
            </form>
        </Dialog>
    </div>

</template>

<script setup>

import {ref} from "vue";
import { useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import Divider from "primevue/divider";
import InputText from 'primevue/inputtext';
import InputNumber from "primevue/inputnumber";
import InputSwitch from 'primevue/inputswitch';

const props = defineProps({
    cargo:Object,
});

const visible = ref(false);

const form = useForm({
    name:props.cargo.name,
    fixed:props.cargo.fixed,
    fixed_price:props.cargo.fixed_price,
    free_after:props.cargo.free_after,
    default_product_desi:props.cargo.default_product_desi,
});

const cancel = () => {
    visible.value = false;
    form.reset();
};

const update = () => {
    form.put(route('cargos.update',props.cargo),{
        preserveScroll: true,
        onSuccess: (page) => {
            visible.value = false;
        }
    });
}


</script>