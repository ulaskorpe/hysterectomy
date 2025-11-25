<template>
    <Head head-key="title" title="Siparişler" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Siparişler
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
                    <Filter :filters="props.filters" :uri="route('orders.index')" :has-export="true"/>
                </div>
                <div class="card-header px-4" v-if="selectedItems.length > 0">
                    <h5 class="card-title fs-6"><i class="bi bi-person me-2 fs-2"></i>{{ selectedItems.length }}</h5>
                    <div class="card-toolbar">
                        
                    </div>
                </div>
                <div class="card-body p-0">

                    <DataTable v-model:selection="selectedOrder" :value="props.orders.data" stripedRows selectionMode="single" dataKey="id" tableStyle="min-width: 50rem" @rowSelect="onRowSelect">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column field="user_id" header="Üye" headerStyle="width: 4rem">
                            <template #body="{ data }">
                                <span class="text-success" v-if="data.user_id"><i class="bi bi-check fs-3"></i></span>
                                <span class="text-danger" v-else><i class="bi bi-x fs-3"></i></span>
                            </template>
                        </Column>
                        <Column field="created_at" header="Tarih">
                            <template #body="{ data }">
                                <span>{{ formatDateToMask(data.created_at, true) }}</span>
                            </template>
                        </Column>
                        <Column field="order_status_id" header="Durumu">
                            <template #body="{ data }">
                                <span>{{ data.status.name }}</span>
                            </template>
                        </Column>
                        <Column field="name" header="İsim">
                            <template #body="{ data }">
                                <span>{{ data.cart_details.extra_info.billing ? data.cart_details.extra_info.billing.name : '' }}</span>
                            </template>
                        </Column>
                        <Column field="email" header="E-Posta">
                            <template #body="{ data }">
                                <span>{{ data.cart_details.extra_info.billing ? data.cart_details.extra_info.billing.email : '' }}</span>
                            </template>
                        </Column>
                        <Column field="total" header="Toplam"></Column>
                        <Column field="coupon_id" header="Kupon">
                            <template #body="{ data }">
                                <span>{{ data.coupon ? data.coupon.code : 'NA' }}</span>
                            </template>
                        </Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <button type="button" class="btn btn-sm btn-light-primary" @click="selectedOrder = data; sideBarVisible = true">Detay</button>
                            </template>
                        </Column>
                    </DataTable>

                </div>
                <div class="card-footer">
                    <Pagination :links="props.orders.links" :only="['orders','flash']"/>
                </div>
            </div>

        </div>

    </div>

    <Sidebar v-model:visible="sideBarVisible" :header="`#${selectedOrder ? selectedOrder.code : ''} sipariş detayları`" position="right" @hide="resetSelectedOrder" class="w-100 w-xxl-90">
        <OrderDetails v-if="selectedOrder" :order="selectedOrder" :order-statuses="props.order_statuses" :on-update-done="resetSelectedOrder"/>
    </Sidebar>

</template>

<script setup>

import {ref} from "vue";
import Pagination from "../../Components/Pagination";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Filter from "../../Components/Filter";
import Sidebar from "primevue/sidebar";
import OrderDetails from "./OrderDetails";

const props = defineProps({
    orders:Object,
    order_statuses:Object,
    filters:Object,
    sortables:{
        type:Array,
        default:[]
    }
});

const selectedItems = ref([]);
const metaKey = ref(true);

const sideBarVisible = ref(false);
const selectedOrder = ref(null);

const resetSelectedItems = () => {
    selectedItems.value = [];
}

const resetSelectedOrder = () => {
    selectedOrder.value = null;
    sideBarVisible.value = false;
}

const onRowSelect = (event) => {
    sideBarVisible.value = true;
};

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

</script>