<template>
    
    <div>
        <button type="button" class="btn fw-bold btn-sm btn-success d-flex align-items-center" @click="dialogVisible = true"><i class="bi bi-person-plus fs-4"></i><span class="ms-1 lh-1">EKLE</span></button>

        <Dialog v-model:visible="dialogVisible" modal header="Yeni Banka Hesabı Ekle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="create">

                <div class="row align-items-center">
                    <label class="col-lg-6">Banka Adı</label>
                    <div class="col-lg-6">
                        <InputText v-model="form.bank" class="w-100" />
                    </div>
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-lg-6">Hesap Sahibi Adı</label>
                    <div class="col-lg-6">
                        <InputText v-model="form.holder" class="w-100" />
                    </div>
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-lg-6">IBAN</label>
                    <div class="col-lg-6">
                        <InputText v-model="form.iban" class="w-100" />
                    </div>
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-lg-6">Para Birimi</label>
                    <div class="col-lg-6">
                        <InputText v-model="form.currency" class="w-100" />
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

const dialogVisible = ref(false);

const form = useForm({
    bank:"",
    holder:"",
    iban:"",
    currency:"",
});

const create = () => {
    form.post(route('bank-accounts.store'),{
        onSuccess: () => {
            form.reset();
            dialogVisible.value = false;
        },
    });
}

</script>