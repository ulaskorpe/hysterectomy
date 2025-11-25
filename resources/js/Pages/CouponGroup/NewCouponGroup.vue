<template>
    
    <div>
        <button type="button" class="btn fw-bold btn-sm btn-success d-flex align-items-center" @click="dialogVisible = true"><i class="bi bi-person-plus fs-4"></i><span class="ms-1 lh-1">EKLE</span></button>

        <Dialog v-model:visible="dialogVisible" modal header="Yeni İçerik Tipi Ekle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="create">

                <div class="d-flex flex-column">
                    <label class="form-label">İsim</label>
                    <span class="p-input-icon-left">
                        <i class="bi bi-person" />
                        <InputText v-model="form.name" class="w-100" />
                    </span>
                </div>

                <Divider />

                <div class="row g-5">
                    <div class="col-lg-4 d-flex flex-row">
                        <InputSwitch v-model="form.is_active" />
                        <label class="fw-bold ms-2">Yayınla</label>
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
import Divider from 'primevue/divider';

const dialogVisible = ref(false);

const form = useForm({
    name:"",
    is_active:false
});

const create = () => {
    form.post(route('coupon-groups.store'),{
        onSuccess: () => {
            form.reset();
            dialogVisible.value = false;
        },
    });
}

</script>