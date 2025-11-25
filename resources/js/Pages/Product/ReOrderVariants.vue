<template>
    <Head head-key="title" title="Varyasyonları Sırala" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ props.product.name }} varyasyonları sırala
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link :href="route('products.index',{productType:props.product.product_type_id,language:$page.props.current_language})" class="btn fw-bold btn-sm btn-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left fs-4"></i>
                    <span class="ms-1 lh-1">Geri</span>
                </Link>
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-xxl h-100">
            
            <div class="card h-100">

                <div class="card-body p-0" ref="reorderCardBody">

                    <DataTable :value="variantsRef" @rowReorder="onRowReorder" stripedRows scrollable :scrollHeight="scrollHeight" dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column rowReorder headerStyle="width: 3rem" :reorderableColumn="false" />
                        <Column header="Şube" v-if="props.product.product_type.option_group && props.product.product_type.option_group.use_team">
                            <template #body="{ data }">
                                <span>{{ data.team ? data.team.name : 'NA' }}</span>
                            </template>
                        </Column>
                        <Column header="Varyasyon Detayları">
                            <template #body="{ data }">
                                <div class="vstack gap-1">
                                    <div v-for="(val,v) in data.option_values" :key="v">
                                        <span>{{ val.name }}:</span>
                                        <strong>{{ val.value }}</strong>
                                    </div>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>

                <div class="card-footer">
                    <button type="button" class="btn fw-bold btn-success ms-auto" :class="{ 'opacity-25': reOrderForm.processing }" :disabled="reOrderForm.processing" @click="reorder">Kaydet</button>
                </div>
            </div>

        </div>

    </div>

</template>

<script setup>

import {ref,onMounted} from "vue";
import {useForm} from "@inertiajs/vue3";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';

const props = defineProps({
    product:Object
});

const reOrderForm = useForm({
    order_data:[]
});


const variantsRef = ref();
const reorderCardBody = ref();
const scrollHeight = ref("500px");

const onRowReorder = (event) => {
    variantsRef.value = event.value;
}

const reorder = () => {

    reOrderForm.transform((data) => ({
        ...data,
        order_data: variantsRef.value.map((row) => row.id),
    })).post(route('product-variants.reorder',props.product.id),{
        onSuccess: () => {
            
        },
    });

}

onMounted(() => {

    scrollHeight.value = reorderCardBody.value.offsetHeight + "px";

    variantsRef.value = props.product.product_variants;
});

</script>