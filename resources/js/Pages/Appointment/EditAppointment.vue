<template>
    
    <div>
        <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="visible = true"/>

        <Dialog @hide="hide" v-model:visible="visible" modal :header="`Randevu güncelle: ${props.announcement.name}`" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update" class="d-flex flex-column h-100">
                
                <div class="row g-5 align-items-center">
                    <label class="col-lg-6 fw-bold">İSİM</label>
                    <div class="col-lg-6">{{ props.announcement.name }}</div>
                </div>

                <Divider />

                <div class="row g-5 align-items-center">
                    <label class="col-lg-6 fw-bold">EMAIL</label>
                    <div class="col-lg-6">{{ props.announcement.email }}</div>
                </div>

                <Divider />

                <div class="row g-5 align-items-center">
                    <label class="col-lg-6 fw-bold">TELEFON</label>
                    <div class="col-lg-6">{{ props.announcement.phone }}</div>
                </div>

                <Divider />

                <div class="row g-5 align-items-center">
                    <label class="col-lg-6 fw-bold">LOKASYON</label>
                    <div class="col-lg-6">{{ props.announcement.json_data?.location }}</div>
                </div>

                <Divider />

                <div class="row g-5 align-items-center">
                    <label class="col-lg-6 fw-bold">MEDIKAL</label>
                    <div class="col-lg-6">{{ props.announcement.message }}</div>
                </div>

                <Divider />

                <div class="row g-5 align-items-center">
                    <label class="col-lg-6 fw-bold">DURUM</label>
                    <div class="col-lg-6">
                        <Dropdown v-model="form.status" :options="statuses" optionLabel="label" optionValue="value" placeholder="Lütfen seçiniz" class="w-100" />
                    </div>
                </div>

                <div v-if="form.status == 'canceled'">
                    <Divider />

                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">İPTAL SEBEBİ</label>
                        <div class="col-lg-6">
                            <Textarea autoResize rows="1" cols="30" class="w-100" v-model="form.cancel_reason" />
                        </div>
                    </div>
                </div>

                <div v-if="props.announcement.status != 'canceled'">
                    <div class="separator my-10"></div>
                    <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                    <button type="button" class="btn btn-light ms-5" @click="cancel()">Vazgeç</button>
                    <FormErrors :form="form"/>
                </div>

            </form>
        </Dialog>
    </div>

</template>

<script setup>

import {ref} from "vue";
import { useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import Divider from 'primevue/divider';
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import Textarea from "primevue/textarea";

const props = defineProps({
    announcement:Object,
    onHide:{
        type:Function,
        default:() => {}
    }
});

const statuses = ref([
        {
            label:"YENİ",
            value:"new"
        },
        {
            label:"ONAYLANDI",
            value:"success"
        },
        {
            label:"İPTAL",
            value:"canceled"
        }
]);

const visible = ref(true);

const form = useForm({
    status:props.announcement.status,
    cancel_reason:props.announcement.cancel_reason,
});

const cancel = () => {
    visible.value = false;
    form.reset();
};

const hide = () => {
    visible.value = false;
    props.onHide();
}

const update = () => {

    form.put(route('appointments.update',[props.announcement,{language:props.announcement.language}]),{
        preserveScroll: true,
        onSuccess: (page) => {
            visible.value = false;
        }
    });
}

const formatDateToMask = (date) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();

    return day + '.' + month + '.' + year;
}

</script>