<template>
    
    <div>
        <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="visible = true"/>

        <Dialog v-model:visible="visible" modal :header="`Güncelle: ${props.cargo.day_name}`" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update" class="d-flex flex-column h-100">

                <div class="row align-items-center">
                    <label class="col-lg-6">Aktif</label>
                    <div class="col-lg-6">
                        <InputSwitch v-model="form.is_active" />
                    </div>
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-lg-6">Başlangıç</label>
                    <div class="col-lg-6">
                        <InputMask mask="99:99" v-model="form.start_time" />
                    </div>
                </div>

                <Divider />

                <div class="row align-items-center">
                    <label class="col-lg-6">Bitiş</label>
                    <div class="col-lg-6">
                        <InputMask mask="99:99" v-model="form.end_time" />
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
import InputMask from 'primevue/inputmask';
import InputSwitch from 'primevue/inputswitch';

const props = defineProps({
    cargo:Object,
});

const visible = ref(false);

const form = useForm({
    is_active:props.cargo.is_active,
    start_time:props.cargo.start_time,
    end_time:props.cargo.end_time,
});

const cancel = () => {
    visible.value = false;
    form.reset();
};

const update = () => {
    form.put(route('working-hours.update',props.cargo),{
        preserveScroll: true,
        onSuccess: (page) => {
            visible.value = false;
        }
    });
}


</script>