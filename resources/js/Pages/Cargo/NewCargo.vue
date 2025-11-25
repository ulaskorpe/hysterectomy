<template>
    
    <div>
        <button type="button" class="btn fw-bold btn-sm btn-success d-flex align-items-center" @click="dialogVisible = true"><i class="bi bi-person-plus fs-4"></i><span class="ms-1 lh-1">EKLE</span></button>

        <Dialog v-model:visible="dialogVisible" modal header="Yeni Kargo Ekle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="create">

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
import Divider from "primevue/divider";
import InputText from 'primevue/inputtext';
import InputNumber from "primevue/inputnumber";
import InputSwitch from 'primevue/inputswitch';

const dialogVisible = ref(false);

const form = useForm({
    name:"",
    fixed:false,
    fixed_price:0,
    free_after:0,
    default_product_desi:3,
});

const create = () => {
    form.post(route('cargos.store'),{
        onSuccess: () => {
            form.reset();
            dialogVisible.value = false;
        },
    });
}

</script>