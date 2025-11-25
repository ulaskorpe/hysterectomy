<template>

    <div class="row g-4 g-xl-10">

        <div class="col-lg-8 col-xl-9">

            <div class="card mb-5 mb-xl-10">
                <div class="card-body">
                    <h5 class="card-title text-muted">Ürün Listesi</h5>
                    <Divider class="mb-5"/>

                    <DataTable :value="Object.values(props.order.cart_details.items)" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <Column field="title" header="Ürün">
                            <template #body="{ data }">
                                <div class="vstack gap-2">
                                    <span class="h4 mb-0">{{ data.title }}</span>
                                    <div v-if="data.extra_info.variant_details" class="vstack">

                                        <span v-for="(option,key) in data.extra_info.variant_details.option_values" :key="key">
                                            <span>{{ option.name }}: </span>
                                            <span class="fw-bold" v-if="option.option_type == 'multiselect'">
                                                {{ option.value[0] }}
                                            </span>
                                            <span class="fw-bold" v-else>
                                                {{ option.value }}
                                            </span>
                                        </span>
                                    </div>
                                    <div v-if="data.applied_actions" class="vstack">
                                        <span v-for="(action,key) in getProductAppliedActions(data.applied_actions)" :key="key">
                                            <span>{{ action.title }}: </span>
                                            <span class="fw-bold">
                                                {{ action.amount }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column field="quantity" header="Adet"></Column>
                        <Column field="price" header="Birim Fiyat"></Column>
                        <Column field="subtotal" header="Toplam Fiyat"></Column>
                    </DataTable>
                </div>
            </div>

            <div class="card mb-5 mb-xl-10" v-if="props.order.cart_details.extra_info.notes">
                <div class="card-body">
                    <h5 class="card-title text-muted">Müşteri Notu</h5>
                    <Divider class="mb-5"/>

                    <div v-html="props.order.cart_details.extra_info.notes"></div>
                </div>
            </div>

            <div class="row mb-5 mb-xl-10">
                <div class="col-lg-6">
                    <div class="card position-relative">
                        <div class="position-absolute bottom-0 end-0 opacity-10 d-flex align-items-center me-5">
                            <i class="bi bi-credit-card" style="font-size: 9em"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-muted">Fatura Bilgileri</h5>
                            <Divider class="mb-5"/>
                            <div class="vstack">
                                <span><b>{{ props.order.cart_details.extra_info.billing.name }}</b></span>
                                <span>{{ props.order.cart_details.extra_info.billing.phone }}</span>
                                <span>{{ props.order.cart_details.extra_info.billing.email }}</span>
                                <span>{{ props.order.cart_details.extra_info.billing.address }}</span>
                                <div class="vstack mt-3" v-if="props.order.cart_details.extra_info.billing_extra">
                                    <span><b>Fatura Tipi:</b> {{ props.order.cart_details.extra_info.billing_extra.billing_type ?? '' }}</span>
                                    <div v-if="props.order.cart_details.extra_info.billing_extra.billing_type == 'Bireysel'">
                                        <span><b>TC NO:</b> {{ props.order.cart_details.extra_info.billing_extra.tc_no ?? '' }}</span>
                                    </div>
                                    <div v-if="props.order.cart_details.extra_info.billing_extra.billing_type == 'Kurumsal'" class="vstack">
                                        <span><b>Firma Adı:</b> {{ props.order.cart_details.extra_info.billing_extra.company_name ?? '' }}</span>
                                        <span><b>Vergi Dairesi:</b> {{ props.order.cart_details.extra_info.billing_extra.vd ?? '' }}</span>
                                        <span><b>Vergi No:</b> {{ props.order.cart_details.extra_info.billing_extra.vkn ?? '' }}</span>
                                        <span><b>E-Fatura:</b> {{ props.order.cart_details.extra_info.billing_extra.e_fatura ? 'Evet' : 'Hayır' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card position-relative h-100">
                        <div class="position-absolute bottom-0 end-0 opacity-10 d-flex align-items-center me-5">
                            <i class="bi bi-truck" style="font-size: 9em"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-muted">Teslimat Bilgileri</h5>
                            <Divider class="mb-5"/>
                            <div class="vstack">
                                <span><b>{{ props.order.cart_details.extra_info.shipping.name }}</b></span>
                                <span>{{ props.order.cart_details.extra_info.shipping.phone }}</span>
                                <span>{{ props.order.cart_details.extra_info.shipping.email }}</span>
                                <span>{{ props.order.cart_details.extra_info.shipping.address }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4 col-xl-3">
            <div class="card mb-5 mb-xl-10" v-if="props.order.coupon">
                <div class="card-body">
                    <h5 class="card-title text-muted">Kupon Detayları</h5>
                    <Divider class="mb-5"/>
                    <div class="vstack gap-1">
                        <div class="hstack justify-content-between align-items-center">
                            <span>Kupon Kodu:</span>
                            <span class="fw-bold">{{ props.order.coupon.code }}</span>
                        </div>
                        <div class="hstack justify-content-between align-items-center">
                            <span>Şirket Satış Tutarı:</span>
                            <span class="fw-bold">{{ props.order.coupon.company_amount }}</span>
                        </div>
                        <div class="hstack justify-content-between align-items-center">
                            <span>İndirim Tipi:</span>
                            <span class="fw-bold">{{ props.order.coupon.type == 'percentage' ? '%' : 'TL' }}</span>
                        </div>
                        <div class="hstack justify-content-between align-items-center">
                            <span>İndirim Miktarı:</span>
                            <span class="fw-bold">{{ props.order.coupon.discount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-5 mb-xl-10">
                <div class="card-body">
                    <h5 class="card-title text-muted">Sipariş Özeti</h5>
                    <Divider class="mb-5"/>
                    <div class="vstack gap-1">
                        <div class="hstack justify-content-between align-items-center">
                            <span>Ara Toplam:</span>
                            <span class="fw-bold">{{ props.order.cart_details.items_subtotal }}</span>
                        </div>
                        <div v-if="props.order.cart_details.applied_actions">
                            <div v-for="(action,a) in props.order.cart_details.applied_actions" :key="a">
                                <div class="hstack justify-content-between align-items-center">
                                    <span>{{ action.title }}:</span>
                                    <span class="fw-bold">{{ action.amount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="hstack justify-content-between align-items-center">
                            <span>TOPLAM:</span>
                            <span class="fw-bold">{{ props.order.cart_details.total }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <form class="card mb-5 mb-xl-10" @submit.prevent="updateStatus">
                <div class="card-body">
                    <h5 class="card-title text-muted">Sipariş Durumu</h5>
                    <Divider class="mb-5"/>
                    <div>
                        <Dropdown v-model="updateStatusForm.order_status_id" :options="props.orderStatuses" optionLabel="name" optionValue="id" class="w-100"/>
                    </div>
                    <Divider />
                    <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': updateStatusForm.processing }" :disabled="updateStatusForm.processing || !updateStatusForm.isDirty">Güncelle</button>
                </div>
            </form>
        </div>

    </div>

</template>

<script setup>

import { useForm, usePage } from "@inertiajs/vue3";
import Divider from "primevue/divider";
import Dropdown from "primevue/dropdown";
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const props = defineProps({
    order:Object,
    orderStatuses:Object,
    onUpdateDone:{
        type:Function,
        default:() => {}
    }
});


const updateStatusForm = useForm({
    order_status_id:props.order.order_status_id,
});


const getProductAppliedActions = (actions) => {

    let applied_actions = [];

    for (const [key, value] of Object.entries(actions)) {
        applied_actions.push(value);
    }

    return applied_actions;

}

const updateStatus = () => {

    if( !updateStatusForm.isDirty ){
        return;
    }

    updateStatusForm.put(route('orders.updateStatus',props.order),{
        preserveScroll:true,
        onSuccess: () => {
            props.onUpdateDone();
        },
    });
}

const formatDateToMask = (date) => {

    var originalDate = new Date(date);

    if (originalDate.getHours() == "21:00:00") {
        const trDate = originalDate.toLocaleString('tr-TR', { timeZone: 'Europe/Istanbul' });
        originalDate = trDate;
    }

    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();
    // var hour = originalDate.getHours();
    // var min = originalDate.getMinutes();
    // var sec = originalDate.getSeconds();

    return day + '.' + month + '.' + year;
}

</script>