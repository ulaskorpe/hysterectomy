<template>
    <Head head-key="title" title="Satış Raporları" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    {{props.product_type.name}} satış raporları
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="card">
                <div class="card-header px-4">
                    <Filter :filters="props.filters" :uri="route('product-types.sales',props.product_type.id)" :has-export="true"/>
                </div>
                <div class="card-body p-0">

                    <DataTable :value="props.sales.data" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column field="order_id" header="Sipariş ID" headerStyle="width: 4rem"></Column>
                        <Column field="price" header="Fiyat"></Column>
                        <Column header="Ödenen">
                            <template #body="{ data }">
                                <span>{{ data.discount ? data.price - data.discount : data.price }}</span>
                            </template>
                        </Column>
                        <Column field="created_at" header="Satış Tarihi">
                            <template #body="{ data }">
                                <span>{{ formatDateToMask(data.created_at, true) }}</span>
                            </template>
                        </Column>
                        <Column header="İsim">
                            <template #body="{ data }">
                                <span v-if="data.product">{{ data.product.name }}</span>
                                <span v-else class="text-danger">Ürün Bulunamadı!</span>
                            </template>
                        </Column>
                        <Column v-for="(col,c) in optionColumns" :key="c">
                            <template #header>
                                <span>{{ col.name }}</span>
                            </template>
                            <template #body="{ data }">
                                <span v-if="data.product_variant">{{ getHeadValue(data.product_variant.option_values,col.id) }}</span>
                                <span v-else class="text-danger">Varyasyon Bulunamadı!</span>
                            </template>
                        </Column>
                        <Column field="count" header="Adet"></Column>
                        <Column header="Kupon">
                            <template #body="{ data }">
                                <span>{{ data.order.coupon && data.discount > 0 ? data.order.coupon.code : '' }}</span>
                            </template>
                        </Column>
                        <Column header="Ad Soyad">
                            <template #body="{ data }">
                                <span>{{ data.order.cart_details.extra_info.billing ? data.order.cart_details.extra_info.billing.name : 'NA' }}</span>
                            </template>
                        </Column>
                        <Column header="Email">
                            <template #body="{ data }">
                                <span>{{ data.order.cart_details.extra_info.billing ? data.order.cart_details.extra_info.billing.email : 'NA' }}</span>
                            </template>
                        </Column>
                        <Column header="Telefon">
                            <template #body="{ data }">
                                <span>{{ data.order.cart_details.extra_info.billing ? data.order.cart_details.extra_info.billing.phone : 'NA' }}</span>
                            </template>
                        </Column>
                        <Column header="İl / İlçe">
                            <template #body="{ data }">
                                <span>{{ data.order.cart_details.extra_info.billing ? `${data.order.cart_details.extra_info.billing.county} / ${data.order.cart_details.extra_info.billing.state}` : 'NA' }}</span>
                            </template>
                        </Column>
                    </DataTable>

                </div>
                <div class="card-footer">
                    <Pagination :links="props.sales.links" :only="['sales','flash']"/>
                </div>
            </div>

        </div>

    </div>

</template>

<style>
    .p-datatable-thead,
    .p-datatable-tbody {
        white-space: nowrap;
    }
</style>

<script setup>

import {onBeforeMount, ref} from "vue";
import Pagination from "../../Components/Pagination";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Filter from "../../Components/Filter";

const props = defineProps({
    sales:Object,
    product_type:Object,
    filters:Object,
    sortables:{
        type:Array,
        default:[]
    }
});

const metaKey = ref(true);
const optionColumns = ref([]);

const getHeadValue = (option_values,option_id) => {

    const op_value = option_values.find((x) => x.id === option_id);
    if( op_value ){
        return op_value.value;
    }
    return "NA";
}

const formatDateToMask = (date, withHour = false) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toLocaleString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();
    var hour = String(originalDate.getHours()).padStart(2, '0');
    var min = String(originalDate.getMinutes()).padStart(2, '0');
    var sec = String(originalDate.getSeconds()).padStart(2, '0');

    if( withHour ){
        return day + '.' + month + '.' + year + ' ' + hour + ':' + min + ':' + sec;
    }
    return day + '.' + month + '.' + year;
}

onBeforeMount(() => {

    if( props.product_type.option_group ){

        props.product_type.option_group.options.forEach(option => {
            optionColumns.value.push(option);
        });

    }

});

</script>