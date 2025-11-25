<template>
    
    <div>
        <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="visible = true"/>

        <Dialog v-model:visible="visible" modal header="İçerik Tipi Güncelle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update" class="d-flex flex-column h-100">
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
import InputText from 'primevue/inputtext';
import Divider from 'primevue/divider';
import Button from 'primevue/button';
import InputSwitch from 'primevue/inputswitch';

const props = defineProps({
    group:Object,
});

const visible = ref(false);

const form = useForm({
    name:props.group.name,
    is_active:props.group.is_active,
});

const cancel = () => {
    visible.value = false;
    form.reset();
};

const update = () => {
    form.put(route('coupon-groups.update',props.group),{
        preserveScroll: true,
        preserveState:false,
        onSuccess: () => {
            cancel();
        }
    });
}


</script>