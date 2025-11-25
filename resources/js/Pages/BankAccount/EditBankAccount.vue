<template>
    
    <div>
        <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="visible = true"/>

        <Dialog v-model:visible="visible" modal header="Banka Hesabı Güncelle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update" class="d-flex flex-column h-100">

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

const props = defineProps({
    account:Object,
});

const visible = ref(false);

const form = useForm({
    bank:props.account.bank,
    holder:props.account.holder,
    iban:props.account.iban,
    currency:props.account.currency,
});

const cancel = () => {
    visible.value = false;
    form.reset();
};

const update = () => {
    form.put(route('bank-accounts.update',props.account),{
        preserveScroll: true,
        onSuccess: (page) => {
            visible.value = false;
        }
    });
}


</script>