<template>
    
    <div>
        <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="visible = true"/>

        <Dialog v-model:visible="visible" modal header="İçerik Tipi Güncelle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update" class="d-flex flex-column h-100">
                <div class="row g-5 align-items-center">
                    <label class="col-lg-6 fw-bold">Kupon Grubu</label>
                    <div class="col-lg-6">
                        <Dropdown v-model="form.coupon_group_id" :options="props.groups" optionLabel="name" optionValue="id" placeholder="Lütfen seçiniz" class="w-100" />
                    </div>
                </div>

                <div v-if="form.coupon_group_id">

                    <Divider />

                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">İndirim Çeki Olarak Kullan</label>
                        <div class="col-lg-6">
                            <InputSwitch v-model="form.as_voucher" />
                        </div>
                    </div>

                    <Divider />
                    
                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">Kod</label>
                        <div class="col-lg-6">
                            <InputText placeholder="Kod" v-model="props.coupon.code" disabled/>
                        </div>
                    </div>

                    <Divider />
                    
                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">İndirim Tipi</label>
                        <div class="col-lg-6">
                            <Dropdown v-model="form.type" :options="discount_types" optionLabel="label" optionValue="value" placeholder="Lütfen seçiniz" class="w-100" />
                        </div>
                    </div>

                    <Divider />
                    
                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">İndirim Tutarı</label>
                        <div class="col-lg-6">
                            <InputNumber v-model="form.discount" class="w-100" />
                        </div>
                    </div>

                    <Divider />

                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">Kaç Kez Kullanılabilir?</label>
                        <div class="col-lg-6">
                            <InputNumber v-model="form.usage_count" class="w-100" />
                        </div>
                    </div>

                    <Divider />

                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">Tüm Sepette Geçerli</label>
                        <div class="col-lg-6">
                            <InputSwitch v-model="form.apply_cart" />
                        </div>
                    </div>

                    <div v-if="!form.apply_cart">
                        <Divider />
                        <div class="row g-5 align-items-center">
                            <label class="col-lg-6 fw-bold">Hangi Ürün Tiplerinde Geçerli?</label>
                            <div class="col-lg-6">
                                <MultiSelect v-model="form.product_types" :options="props.productTypes" optionGroupLabel="name" optionLabel="name" optionValue="id" optionGroupChildren="product_types" placeholder="Lütfen seçiniz" class="w-100" />
                            </div>
                        </div>
                    </div>

                    <Divider />

                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">Başlangıç Tarihi</label>
                        <div class="col-lg-6">
                            <InputMask v-model="form.start_date" :class="{'p-invalid':form.errors.start_date}" mask="99.99.9999" placeholder="GG.AA.YYYY"/>
                        </div>
                    </div>

                    <Divider />

                    <div class="row g-5 align-items-center">
                        <label class="col-lg-6 fw-bold">Bitiş Tarihi</label>
                        <div class="col-lg-6">
                            <InputMask v-model="form.end_date" :class="{'p-invalid':form.errors.start_date}" mask="99.99.9999" placeholder="GG.AA.YYYY"/>
                        </div>
                    </div>

                    <div class="separator my-10"></div>
                    <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                    <FormErrors :form="form"/>
                </div>
            </form>
        </Dialog>
    </div>

</template>

<script setup>

import {ref, onMounted} from "vue";
import { useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import InputText from 'primevue/inputtext';
import InputNumber from "primevue/inputnumber";
import InputSwitch from 'primevue/inputswitch';
import Divider from 'primevue/divider';
import Dropdown from "primevue/dropdown";
import Button from "primevue/button";
import MultiSelect from "primevue/multiselect";
import InputMask from 'primevue/inputmask';

const props = defineProps({
    coupon:Object,
    groups:Object,
    productTypes:Object
});

const visible = ref(false);

const form = useForm({
    coupon_group_id:props.coupon.coupon_group_id,
    type:props.coupon.type,
    discount:props.coupon.discount,
    start_date:"",
    end_date:"",
    usage_count:props.coupon.usage_count,
    apply_cart:props.coupon.apply_cart,
    count:props.coupon.count,
    product_types:props.coupon.product_types ? props.coupon.product_types.map((row) => row.id) : [],
    as_voucher:props.coupon.as_voucher
});

const discount_types = [
    {
        label:"Yüzde",
        value:"percentage"
    },
    {
        label:"Tutar",
        value:"fixed"
    }
];

const update = () => {

    if (form.as_voucher && form.type == 'percentage') {
        alert("İndirim çekinde kupon tipi Yüzde olamaz");
        return;
    }

    form.put(route('coupons.update',props.coupon),{
        preserveScroll: true,
        onSuccess: (page) => {
            visible.value = false;
        }
    });
}

const formatDateToMask = (date) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();

    return day + '.' + month + '.' + year;
}

onMounted(() => {
    form.start_date = formatDateToMask(props.coupon.start_date);
    form.end_date = formatDateToMask(props.coupon.end_date);
});

</script>