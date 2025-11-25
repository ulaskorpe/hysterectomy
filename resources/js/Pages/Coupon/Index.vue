<template>
    <Head head-key="title" title="Kuponlar" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Kuponlar
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <NewCoupon :groups="props.groups" :product-types="props.product_types"/>
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="card">
                <div class="card-header px-4">
                    <Filter :filters="props.filters" :uri="route('coupons.index')"  :has-export="true"/>
                </div>
                <div class="card-body p-0">

                    <DataTable :value="props.coupons.data" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column field="code" header="Kod"></Column>
                        <Column field="discount" header="İndirim">
                            <template #body="{ data }">
                                <div class="d-flex gap-1 align-items-center">
                                    <span>{{ data.discount }}</span>
                                    <span v-if="data.type == 'fixed'">TRY</span>
                                    <span v-else>%</span>
                                </div>
                            </template>
                        </Column>
                        <Column field="group.name" header="Grup"></Column>
                        <Column field="usage_count" header="Kullanım Hakkı"></Column>
                        <Column field="used_count" header="Kullanılan Adet"></Column>
                        <Column field="apply_cart" header="Sepette Geçerli"></Column>
                        <Column field="product_types" header="Geçerli Ürün Tipleri">
                            <template #body="{ data }">
                                <div class="vstack" v-if="!data.apply_cart">
                                    <span v-for="(ptype,p) in data.product_types" :key="p">{{ ptype.name }}</span>
                                </div>
                                <span v-if="data.apply_cart">Tüm Ürün Tipleri</span>
                            </template>
                        </Column>
                        <Column field="as_voucher" header="Çek Olarak Kullanım"></Column>
                        <Column field="start_date" header="Başlangıç">
                            <template #body="{ data }">
                                <span v-html="formatDateToMask(data.start_date)"></span>
                            </template>
                        </Column>
                        <Column field="end_date" header="Bitiş">
                            <template #body="{ data }">
                                <span v-html="formatDateToMask(data.end_date)"></span>
                            </template>
                        </Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <EditCoupon :coupon="data" :groups="props.groups" :product-types="props.product_types" v-tooltip.top="'Güncelle'"/>
                                    <DeletePrompt :uri="route('coupons.delete',data.id)" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
                <div class="card-footer">
                    <Pagination :links="props.coupons.links" :only="['coupons','flash']"/>
                </div>
            </div>

        </div>


    </div>

</template>

<script setup>

import NewCoupon from "./NewCoupon";
import EditCoupon from "./EditCoupon";
import Pagination from "../../Components/Pagination";
import DeletePrompt from "../../Components/DeletePrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Filter from "../../Components/Filter";

const props = defineProps({
    coupons:Object,
    groups:Object,
    filters:Object,
    product_types:Object,
    sortables:{
        type:Array,
        default:[]
    }
});

const formatDateToMask = (date) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();

    return day + '.' + month + '.' + year;
}

</script>