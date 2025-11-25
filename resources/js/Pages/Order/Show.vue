<template>
    <Head head-key="title" title="Siparişler" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Sipariş: #{{ props.order.code }}
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link :href="route('orders.index')" class="btn fw-bold btn-sm btn-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left fs-4"></i>
                    <span class="ms-1 lh-1">Geri</span>
                </Link>
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <OrderDetails :order="props.order" :order-statuses="props.order_statuses"/>

        </div>

    </div>

</template>

<script setup>

import { useForm, usePage } from "@inertiajs/vue3";
import OrderDetails from "./OrderDetails";

const props = defineProps({
    order:Object,
    order_statuses:Object
});

const updateStatusForm = useForm({
    order_status_id:props.order.order_status_id,
});

const getProductGiftCoupons = (p_id) => {

    return props.order.gift_coupons.filter((x) => x.product_id == p_id);

}

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
        onSuccess: () => {
            
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