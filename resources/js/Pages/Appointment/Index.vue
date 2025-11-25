<template>
    <Head head-key="title" title="Randevu Talepleri" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Randevu Talepleri
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="card">

                <div class="card-header px-4">
                    <Filter :filters="props.filters" :uri="route('appointments.index')" />
                </div>
                <div class="card-body p-0">

                    <DataTable :value="props.appointments.data" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column field="status" header="Durum">
                            <template #body="{ data }">
                                <span v-if="data.status == 'canceled'">İPTAL</span>
                                <span v-if="data.status == 'success'">ONAYLANDI</span>
                                <span v-if="data.status == 'new'">YENİ</span>
                            </template>
                        </Column>
                        <Column field="name" header="İsim"></Column>
                        <Column field="phone" header="Telefon"></Column>
                        <Column field="email" header="Email"></Column>
                        <Column field="appointment_date" header="Tarih">
                            <template #body="{ data }">
                                <span v-html="formatDateToMask(data.appointment_date)"></span>
                            </template>
                        </Column>
                        <Column field="appointment_time" header="Saat"></Column>
                        <Column field="email" header="Email"></Column>
                        <Column field="json_data" header="Lokasyon">
                            <template #body="{ data }">
                                <span>{{ data.json_data?.location }}</span>
                            </template>
                        </Column>
                        <Column field="cancel_reason" header="İptal Sebebi"></Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <Button icon="bi bi-pen" text rounded severity="info" @click="selectedAnnouncement = data" v-tooltip.top="'Görüntüle & Güncelle'"/>
                                    <DeletePrompt :uri="route('appointments.delete',[data.id])" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
                <div class="card-footer">
                    <Pagination :links="props.appointments.links" :only="['appointments','flash']"/>
                </div>
            </div>

        </div>

    </div>

    <EditAppointment :announcement="selectedAnnouncement" v-tooltip.top="'Güncelle'" :on-hide="resetSelectedAnnouncement" v-if="selectedAnnouncement"/>

</template>

<script setup>

import { ref } from "vue";

import EditAppointment from "./EditAppointment";
import Pagination from "../../Components/Pagination";
import DeletePrompt from "../../Components/DeletePrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Button from 'primevue/button'
import Filter from "../../Components/Filter";

const props = defineProps({
    appointments:Object,
    filters:Object,
    sortables:{
        type:Array,
        default:[]
    }
});

const selectedAnnouncement = ref(null);

const resetSelectedAnnouncement = () => {
    selectedAnnouncement.value = null;
}

const formatDateToMask = (date) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();

    return day + '.' + month + '.' + year;
}

</script>