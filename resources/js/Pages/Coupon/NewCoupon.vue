<template>
    
    <div>
        <button type="button" class="btn fw-bold btn-sm btn-success d-flex align-items-center" @click="dialogVisible = true"><i class="bi bi-person-plus fs-4"></i><span class="ms-1 lh-1">EKLE</span></button>

        <Dialog v-model:visible="dialogVisible" modal header="Yeni Kupon Ekle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="create">

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
                        <label class="col-lg-6 fw-bold">Adet</label>
                        <div class="col-lg-6">
                            <InputNumber v-model="form.count" class="w-100" />
                        </div>
                    </div>

                    <div v-if="form.count === 1">
                        <Divider />
                        <div class="row g-5 align-items-center">
                            <label class="col-lg-6 fw-bold">Kod</label>
                            <div class="col-lg-6">
                                <div class="p-inputgroup flex-1">
                                    <InputText placeholder="Kod" v-model="form.code"/>
                                    <Button label="Üret" severity="warning" @click="generateCode"/>
                                </div>
                            </div>
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

import {ref} from "vue";
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
    groups:Object,
    productTypes:Object
});

const dialogVisible = ref(false);

const form = useForm({
    coupon_group_id:"",
    type:"",
    discount:0,
    start_date:"",
    end_date:"",
    usage_count:1,
    apply_cart:true,
    code:"",
    count:1,
    product_types:[],
    as_voucher:false
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

const create = () => {

    if( form.as_voucher && form.type == 'percentage' ){
        alert("İndirim çekinde kupon tipi Yüzde olamaz");
        return;
    }

    form.post(route('coupons.store'),{
        onSuccess: () => {
            form.reset();
            dialogVisible.value = false;
        },
    });
}

const generateCode = () => {

    fetch(route('coupons.generate'),{
        headers:{
            'Accept':'application/json',
        }
    }).then(function(response){
        return response.json()
    }).then((json) => {
        form.code = json.code
    });

}

</script>