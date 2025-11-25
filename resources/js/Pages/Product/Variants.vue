<template>
    <Head head-key="title" :title="`${props.product.name} varyasyon listesi`" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ props.product.name }} vasyasyon listesi
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link :href="route('products.index',{productType:props.product.product_type.id,language:$page.props.current_language})" class="btn fw-bold btn-sm btn-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left fs-4"></i>
                    <span class="ms-1 lh-1">Geri</span>
                </Link>
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="card">

                <div class="card-header px-4">
                    <div class="card-toolbar w-100 justify-content-between gap-2">
                        <div><VariantsNew :product="props.product"/></div>
                        <Link :href="route('product-variants.reorder',props.product.id)" class="btn fw-bold btn-sm btn-light d-flex align-items-center">
                            <i class="bi bi-sort-down fs-4"></i>
                            <span class="ms-1 lh-1">SIRALA</span>
                        </Link>
                    </div>
                </div>

                <div class="card-body p-0">

                    <DataTable :value="props.product.product_variants" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column v-for="(head,h) in props.product.product_type.option_group.options" :key="h">
                            <template #header>
                                <span>{{ head.name }}</span>
                            </template>
                            <template #body="{data}">
                                <span>{{ getHeadValue(data.option_values,head.id) }}</span>
                            </template>
                        </Column>
                        <Column header="Fiyat" v-if="props.product.product_type.option_group && props.product.product_type.option_group.has_own_price">
                            <template #body="{ data }">
                                <span>{{ data.price ? data.price.final_price : 'NA' }}</span>
                            </template>
                        </Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="selectedProductVariant = data"/>
                                    <DeletePrompt :delete-done="resetSelectedItems" :uri="route('product-variants.delete',[props.product.id,data.id])" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
            </div>

        </div>

    </div>

    <VariantsEdit :product="props.product" :variant="selectedProductVariant" :on-update-done="resetSelectedProductVariant" v-if="selectedProductVariant" />

</template>

<script setup>

import {ref} from "vue";
import DeletePrompt from "../../Components/DeletePrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Button from "primevue/button";
import VariantsNew from "./VariantsNew";
import VariantsEdit from "./VariantsEdit";

const props = defineProps({
    product:Object,
});

const selectedItems = ref([]);
const metaKey = ref(true);
const selectedProductVariant = ref(null);

const resetSelectedItems = () => {
    selectedItems.value = [];
}

const resetSelectedProductVariant = () => {
    selectedProductVariant.value = null;
}

const getHeadValue = (option_values,option_id) => {

    const op_value = option_values.find((x) => x.id === option_id);
    if( op_value ){
        return op_value.value;
    }
    return "NA";
}

const formatDateToMask = (date) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();

    return day + '.' + month + '.' + year;
}

</script>