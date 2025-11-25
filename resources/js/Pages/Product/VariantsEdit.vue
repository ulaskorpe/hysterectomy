<template>
    
    <div>
        <Dialog v-model:visible="dialogVisible" modal @hide="props.onUpdateDone" header="Varyasyon Güncelle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update" v-if="props.product.product_type.option_group && props.product.product_type.option_group.options">

                <div class="position-relative">

                    <div class="row g-4 g-xl-7">

                        <div class="col-xl-6">
                            <div class="" v-for="(option,o) in form.option_values.filter(x => props.product.additional.options_for_use.includes(x.id))" :key="`group-option-${o}`">
                                <Divider v-if="o != 0"/>
                                <div class="row g-3 align-items-center">
                                    <label class="col-md-3 fw-bold text-uppercase">{{ option.name }}</label>
                                    <div class="col-md-9">
                                        <InputMask v-if="option.option_type == 'time'" v-model="option.value" mask="99:99"/>
                                        <InputMask v-if="option.option_type == 'date'" v-model="option.value" mask="99.99.9999" placeholder="GG.AA.YYYY"/>
                                        <InputMask v-if="option.option_type == 'datetime'" v-model="option.value" mask="99.99.9999 99:99" placeholder="GG.AA.YYYY HH:SS"/>
                                        <Dropdown v-if="option.option_type == 'select' || option.option_type == 'radio'" v-model="option.value" :options="option.values" optionLabel="name" optionValue="name" placeholder="Lütfen seçiniz"/>
                                        <MultiSelect v-if="option.option_type == 'multiselect'" v-model="option.value" :options="option.values" optionLabel="name" optionValue="name" placeholder="Lütfen seçiniz"/>
                                        <div class="d-flex flex-row align-items-center" v-if="option.option_type == 'file'">
                                            <PopupMediaLibrary v-if="!option.value" @click="optionMediaObjectIndex = o" :on-select="setOptionGroupOptionMedia" :form-model="option" :button-text="'Seç'" :mime-type="'application/'" :key="Math.floor(Math.random() * 100000)"/>
                                            <div class="d-flex flex-" v-if="option.value">
                                                <a target="_blank" :href="option.value" class="btn btn-sm btn-light-success">Göster</a>
                                                <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" @click="option.value = null">
                                                    <i class="bi bi-x fs-3"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row flex-wrap align-items-center" v-if="option.option_type == 'image'">
                                            <img :src="option.value" v-if="option.value" class="img-thumbnail" width="100"/>
                                            <div class="d-flex flex-row align-items-center">
                                                <PopupMediaLibrary v-if="!option.value" @click="optionMediaObjectIndex = o" :on-select="setOptionGroupOptionMedia" :form-model="option"  :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                                <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" v-if="option.value" @click="option.value = null">
                                                    <i class="bi bi-x fs-3"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <OptionType :option="option" v-model="option.value" v-else />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="rounded bg-gray-100 border p-4 h-100">

                                <div class="">
                                    <div class="d-flex flex-row flex-wrap align-items-end">
                                        <div class="symbol symbol-100px me-2">
                                            <img :src="form.media_objects.mainImage.preview_url" v-if="form.media_objects.mainImage"/>
                                            <div class="symbol-label bg-white" v-else>
                                                <i class="bi bi-image fs-2x"></i>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row align-items-center">
                                            <PopupMediaLibrary v-if="!form.media_objects.mainImage" :on-select="setOptionRowMedia" :form-model="form.media_objects.mainImage" :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                            <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" @click="form.media_objects.mainImage = null" v-if="form.media_objects.mainImage">
                                                <i class="bi bi-x fs-3"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="props.product.product_type.option_group.stock_usage">
                                    <Divider />
                                    <div class="">
                                        <label class="fw-bold text-uppercase">STOK</label>
                                        <div class="">
                                            <InputNumber class="w-100" v-model="form.stock" :inputId="`stock-${props.product.id}`" :useGrouping="false"/>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div class="" v-if="props.product.product_type.option_group.has_own_price">
                                    <Divider />
                                    <label class="fw-bold text-uppercase">fiyat</label>
                                    <div class="">
                                        <InputNumber class="w-100" mode="currency" currency="TRY" locale="tr-TR" v-model="form.price" :inputId="`price-${props.product.id}`"/>
                                    </div>
                                    <Divider />
                                    <div class="hstack align-items-center gap-2">
                                        <InputSwitch v-model="form.has_discount"/>
                                        <span class="fw-bold text-uppercase">İndirim</span>
                                    </div>
                                    <div v-if="form.has_discount">
                                        <Divider />
                                        <div class="d-flex flex-row align-items-center justify-content-between">
                                            <label class="fw-bold text-uppercase">indirim tipi</label>
                                            <Dropdown v-model="form.discount_type" :options="['percentage','fixed']"/>
                                        </div>
                                        <Divider />
                                        <div class="d-flex flex-row align-items-center justify-content-between">
                                            <label class="fw-bold text-uppercase">değer</label>
                                            <InputNumber v-model="form.discount_value" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="separator my-10"></div>
                <div>
                    <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                    <button type="button" class="btn btn-light ms-5" @click="cancel()">Vazgeç</button>
                </div>

                <FormErrors :form="form"/>

            </form>
        </Dialog>
    </div>

</template>

<script setup>

import {ref} from "vue";
import { useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import InputMask from "primevue/inputmask";
import InputNumber from "primevue/inputnumber";
import MultiSelect from "primevue/multiselect";
import InputSwitch from 'primevue/inputswitch';
import Divider from "primevue/divider";
import Dropdown from "primevue/dropdown";
import OptionType from "../../Components/OptionType";
import PopupMediaLibrary from "../MediaLibrary/PopupMediaLibrary";

const props = defineProps({
    product:Object,
    variant:Object,
    onUpdateDone:{
        type:Function,
        required:true
    }
});

const dialogVisible = ref(true);
const optionMediaObjectIndex = ref(null);

//option values icin mevcuttaki degerleri ekleyecegiz.
const option_group_options = ref(props.product.product_type.option_group.options);

props.variant.option_values.forEach(option => {
    //guncel optionlar icindeki karsiligini bul.
    let optionInGroup = option_group_options.value.find((x) => x.id === option.id);
    if( optionInGroup ){
        optionInGroup.value = option.value;
    }
});

const form = useForm({
    option_values:option_group_options.value,
    media_objects:{
        mainImage:props.variant.main_image && props.variant.main_image.length > 0 ? props.variant.main_image[0] : null,
    },
    stock:props.variant.stock,
    price:props.variant.price ? props.variant.price.price : null,
    has_discount:props.variant.has_discount,
    discount_type:props.variant.price && props.variant.price.discount ? props.variant.price.discount.discount_type : "percentage",
    discount_value:props.variant.price && props.variant.price.discount ? props.variant.price.discount.value : 0,
});

const cancel = () => {
    form.reset();
    props.onUpdateDone();
};

const setOptionRowMedia = (media) => {
    form.media_objects.mainImage = media;
}

const setOptionGroupOptionMedia = (media, option) => {
    form.option_values[optionMediaObjectIndex.value].value = media.original_url;
}

const update = () => {
    form.put(route('product-variants.update',[props.product.id, props.variant.id]),{
        onSuccess: () => {
            form.reset();
            props.onUpdateDone();
        },
    });
}

</script>