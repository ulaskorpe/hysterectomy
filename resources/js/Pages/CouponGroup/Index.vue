<template>
    <Head head-key="title" title="Kupon Grupları" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Kupon Grupları
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <NewCouponGroup />
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="card">
                <div class="card-body p-0">

                    <DataTable :value="props.groups" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column field="name" header="İsim"></Column>
                        <Column field="for_gifts" header="Hediye Kategorisi">
                            <template #body="{ data }">
                                <span>{{ data.for_gifts ? 'Evet' : 'Hayır' }}</span>
                            </template>
                        </Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <EditCouponGroup :group="data" v-tooltip.top="'Güncelle'"/>
                                    <DeletePrompt :uri="route('coupon-groups.delete',data.id)" :model="data"/>
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

import NewCouponGroup from "./NewCouponGroup";
import EditCouponGroup from "./EditCouponGroup";
import DeletePrompt from "../../Components/DeletePrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';

const props = defineProps({
    groups:Object
});

</script>