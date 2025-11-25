<template>
    <Head head-key="title" title="Kargo Tipleri" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Kargo Yönetimi
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <NewCargo />
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="card">
                <div class="card-body p-0">

                    <DataTable :value="props.cargos.data" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column field="name" header="İsim"></Column>
                        <Column field="fixed" header="Sabit Fiyat"></Column>
                        <Column field="fixed_price" header="Sabit Fiyat Tutarı"></Column>
                        <Column field="free_after" header="Ücretsiz Sepet Tutarı"></Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <EditCargo :cargo="data" v-tooltip.top="'Güncelle'"/>
                                    <DeletePrompt :uri="route('cargos.delete',data.id)" :model="data"/>
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

import NewCargo from "./NewCargo";
import EditCargo from "./EditCargo";
import DeletePrompt from "../../Components/DeletePrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';

const props = defineProps({
    cargos:Object
});

</script>