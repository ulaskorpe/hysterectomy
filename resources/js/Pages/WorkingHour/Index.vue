<template>
    <Head head-key="title" title="Çalışma Saatleri" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Çalışma Saatleri Yönetimi
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">

            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="card">
                <div class="card-body p-0">

                    <DataTable :value="props.working_hours" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column field="day_name" header="Gün"></Column>
                        <Column field="is_active" header="Aktif?">
                            <template #body="{ data }">
                                <span class="bi bi-check-circle text-success fs-3" v-if="data.is_active"></span>
                                <span class="bi bi-x-lg text-danger" v-else></span>
                            </template>
                        </Column>
                        <Column field="start_time" header="Başlangıç"></Column>
                        <Column field="end_time" header="Bitiş"></Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <EditWorkingHour :cargo="data" v-tooltip.top="'Güncelle'"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
            </div>

        </div>

    </div>

</template>

<script setup>

import EditWorkingHour from "./EditWorkingHour";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';

const props = defineProps({
    working_hours:Object
});

</script>