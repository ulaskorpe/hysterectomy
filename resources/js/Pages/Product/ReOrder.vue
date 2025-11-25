<template>
    <Head head-key="title" title="Ürünleri Sırala" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ props.product_type.name }} listesini sırala
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link :href="route('products.index',{productType:props.product_type.id,language:$page.props.current_language})" class="btn fw-bold btn-sm btn-secondary d-flex align-items-center">
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

                    <DataTable :value="productsRef" @rowReorder="onRowReorder" stripedRows scrollable :scrollHeight="scrollHeight" dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column rowReorder headerStyle="width: 3rem" :reorderableColumn="false" />
                        <Column field="name" header="Başlık"></Column>
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
    product_type:Object,
    products:Object
});

const reOrderForm = useForm({
    order_data:[]
});


const productsRef = ref();
const reorderCardBody = ref();
const scrollHeight = ref("500px");

const onRowReorder = (event) => {
    productsRef.value = event.value;
}

const reorder = () => {

    reOrderForm.transform((data) => ({
        ...data,
        order_data: productsRef.value.map((row) => row.id),
    })).post(route('products.reorder', {productType:props.product_type.id}),{
        onSuccess: () => {
            
        },
    });

}

onMounted(() => {

    scrollHeight.value = reorderCardBody.value.offsetHeight + "px";

    productsRef.value = props.products;
});

</script>